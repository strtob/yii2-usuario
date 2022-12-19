<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View                        $this
 * @var yii\widgets\ActiveForm              $form
 * @var \Da\User\Model\User                 $model
 * @var \Da\User\Model\SocialNetworkAccount $account
 */
$this->title = Yii::t('usuario', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="auth-wrapper">
    <div class="auth-content">


        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= Html::encode($this->title) ?>
                </h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-info">
                    <p>
                        <?= Yii::t('usuario', 'In order to finish your registration, we need you to enter following fields') ?>:
                    </p>
                </div>
                <?php
                $form = ActiveForm::begin(
                                [
                                    'id' => $model->formName(),
                                ]
                );
                ?>

                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'email') ?>


                <?=
                $form->field($model, 'tbl_title_user_id')->widget(\kartik\widgets\Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map(\common\models\UserTitle::find()
                                    ->orderBy('uid')->asArray()->all(), 'uid', 'name'),
                    'options' => ['placeholder' => 'Choose...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>

                <?= $form->field($model, 'first_name') ?>

                <?= $form->field($model, 'last_name') ?>

                <?= Html::submitButton(Yii::t('usuario', 'Continue'), ['class' => 'btn btn-success btn-block']) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <p class="text-center">
            <?=
            Html::a(Yii::t('usuario', 'If you already registered, sign in and connect this account on settings page?'),
                    ['/user/settings/networks'])
            ?>.
        </p>


    </div>
</div>