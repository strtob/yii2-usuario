<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use dmstr\widgets\Alert;

/**
 * @var yii\web\View               $this
 * @var yii\widgets\ActiveForm     $form
 * @var \Da\User\Form\SettingsForm $model
 */

$this->title = Yii::t('usuario', 'Account settings');
$this->params['breadcrumbs'][] = $this->title;

/** @var \Da\User\Module $module */
$module = Yii::$app->getModule('user');

$token = Yii::$app->user->identity->socialNetworkAccounts['LinkedIn']->access_token;

$imgUrl = (new \Da\User\Helper\LinkedInHelper($token))->pictureUrl(Yii::$app->user->identity->socialNetworkAccounts['LinkedIn']->client_id);

?>
<img src="<?= $imgUrl ?>">
<div class="clearfix"></div>

<?= $this->render('/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('/settings/_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(
                    [
                        'id' => $model->formName(),
                        'options' => ['class' => 'form-horizontal'],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                            'labelOptions' => ['class' => 'col-lg-3 control-label'],
                        ],
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                    ]
                ); ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'new_password')->passwordInput() ?>

                <hr/>

                <?= $form->field($model, 'current_password')->passwordInput() ?>

                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?= Html::submitButton(Yii::t('usuario', 'Save'), ['class' => 'btn btn-block btn-success']) ?>
                        <br>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <?php if ($module->enableTwoFactorAuthentication): ?>
            <div class="modal fade" id="tfmodal" tabindex="-1" role="dialog" aria-labelledby="tfamodalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">
                                <?= Yii::t('usuario', 'Two Factor Authentication (2FA)') ?></h4>
                        </div>
                        <div class="modal-body">
                            ...
                        </div>
                        <div class="modal-footer">                            
                            <button type="button" class="btn btn-default" data-dismiss="modal" onClick='window.location.reload();'>
                                <?= Yii::t('usuario', 'Close') ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('usuario', 'Two Factor Authentication (2FA)') ?></h3>
                </div>
                <div class="panel-body">
                    <p>
                        <?= Yii::t('usuario', 'Two factor authentication protects you in case of stolen credentials') ?>.
                    </p>
                    <?php if (!$model->getUser()->auth_tf_enabled):  
                        $validators = $module->twoFactorAuthenticationValidators;
                        $theFirstFound = false; 
                        $checked = '';
                        foreach( $validators as $name => $validator ) {
                            if($validator[ "enabled" ]){
                                // I want to check in the radio field the first validator I get
                                if(!$theFirstFound){
                                    $checked = 'checked';
                                    $theFirstFound = true; 
                                }
                                $description = $validator[ "description" ];
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="2famethod" id="<?= $name?>" value="<?= $name?>" <?= $checked?>>
                                    <label class="form-check-label" for="<?= $name?>">
                                        <?= $description?>
                                    </label>
                                    </div>
                                <?php
                                $checked = '';
                            }
                        } ;
                    ?>
                        <?= Html::a(
                            Yii::t('usuario', 'Enable two factor authentication'),
                            '#tfmodal',
                            [
                                'id' => 'enable_tf_btn',
                                'class' => 'btn btn-info',
                                'data-toggle' => 'modal',
                                'data-target' => '#tfmodal'
                            ]
                        ) ?>
                    <?php else:
                         ?>
                            <p>
                                <?php 
                                    $method = $model->getUser()->auth_tf_type;
                                    $message = '';
                                    switch ($method) {
                                        case 'email':
                                            $message = Yii::t('usuario', 'The email address set is: "{0}".', [ $model->getUser()->email] );
                                            break;
                                        case 'sms':
                                            $message = Yii::t('usuario', 'The phone number set is: "{0}".', [ $model->getUser()->auth_tf_mobile_phone]);
                                            break;
                                    }
                                ?>
                                <?= Yii::t('usuario', 'Your two factor authentication method is based on "{0}".', [$method] ) .' ' . $message ?>
                            </p>
                            <div class="text-right">
                            <?= Html::a(
                                Yii::t('usuario', 'Disable two factor authentication'),
                                ['two-factor-disable', 'id' => $model->getUser()->id],
                                [
                                    'id' => 'disable_tf_btn',
                                    'class' => 'btn btn-warning ',
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('usuario', 'This will disable two factor authentication. Are you sure?'),
                                ]
                            ) ?>
                           </div>
                    <?php
                    endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($model->module->allowAccountDelete): ?>
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('usuario', 'Delete account') ?></h3>
                </div>
                <div class="panel-body">
                    <p>
                        <?= Yii::t('usuario', 'Once you delete your account, there is no going back') ?>.
                        <?= Yii::t('usuario', 'It will be deleted forever') ?>.
                        <?= Yii::t('usuario', 'Please be certain') ?>.
                    </p>
                    <div class="text-right">
                        <?= Html::a(
                            Yii::t('usuario', 'Delete account'),
                            ['delete'],
                            [
                                'class' => 'btn btn-danger',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('usuario', 'Are you sure? There is no going back'),
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>
<?php if ($module->enableTwoFactorAuthentication): ?>

    <?php
    // This script should be in fact in a module as an external file
    // consider overriding this view and include your very own approach
    $uri = Url::to(['two-factor', 'id' => $model->getUser()->id]);
    $verify = Url::to(['two-factor-enable', 'id' => $model->getUser()->id]);
    $mobilePhoneRegistration = Url::to(['two-factor-mobile-phone', 'id' => $model->getUser()->id]);
    $js = <<<JS
    var choice = ''; 
    $('#tfmodal')
    .on('show.bs.modal', function(){
        var element = document.getElementsByName('2famethod');
        for(i = 0; i < element.length; i++) {
            if(element[i].checked)
                choice = element[i].value;
        }
        if(!$('img#qrCode').length) {
            $(this).find('.modal-body').load('{$uri}', {choice: choice});
        } else {
            $('input#tfcode').val('');
        }
    });

    

$(document)
    .on('click', '.btn-submit-code', function(e) {
        e.preventDefault();
        var btn = $(this);
        btn.prop('disabled', true);
        var choice = ''; 
        var element = document.getElementsByName('2famethod');
        for(i = 0; i < element.length; i++) {
            if(element[i].checked)
                choice = element[i].value;
        }
        
        $.getJSON('{$verify}', {code: $('#tfcode').val(), choice: choice}, function(data){
            btn.prop('disabled', false);
            if(data.success) {
                $('#enable_tf_btn, #disable_tf_btn').toggleClass('hide');
                $('#tfmessage').removeClass('alert-danger').addClass('alert-success').find('p').text(data.message);
                setTimeout(function() { $('#tfmodal').modal('hide'); }, 2000);
                window.location.reload();
            } else {
                $('input#tfcode').val('');
                $('#tfmessage').removeClass('alert-info').addClass('alert-danger').find('p').text(data.message);
            }
        }).fail(function(){ btn.prop('disabled', false); });
    })
    .on('click', '.btn-submit-mobile-phone', function(e) {
        e.preventDefault();
        var btn = $(this);
        btn.prop('disabled', true);

        $.getJSON('{$mobilePhoneRegistration}', {mobilephone: $('#mobilephone').val()}, function(data){
            btn.prop('disabled', false);
            if(data.success) {
                btn.prop('disabled', true);
                $('#smssection').toggleClass('hide');
                $('#sendnewcode').toggleClass('hide');
                $('#tfmessagephone').removeClass('alert-danger').addClass('alert-success').find('p').text(data.message);
            } else {
                $('input#phonenumber').val('');
                $('#tfmessagephone').removeClass('alert-info').addClass('alert-danger').find('p').text(data.message);
            }
        }).fail(function(){ btn.prop('disabled', false); });
       
    })
JS;

    $this->registerJs($js);
    ?>
<?php endif; ?>
