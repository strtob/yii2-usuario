<?php

namespace Da\User\Helper;

use yii\httpclient\Client;

class LinkedInHelper {
    
    private $profilePictureUrn = 'https://api.linkedin.com/v2/me?projection=(id,profilePicture(displayImage~digitalmediaAsset:playableStreams))&oauth2_access_token=';
    
    private $client = null;

    public function __construct($token) {
        $this->client = new Client();

        
        // attach token
        $this->profilePictureUrn .= $token;
    }

    public function request($url, $method = 'GET') {
        $response = $this->client->createRequest()
                ->setMethod($method)
                ->setUrl($url)
                //->addHeaders(['Authorization' => 'Bearer xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'])
                ->send();
        if ($response->isOk) {
            return $response->content;
        } else
            throw new \Exception('Could not complete request: '
                            . $response->getStatusCode()
                            . ': '
                            . implode(', ', $response->getData())
                            . ' Url used: '
                            . $url
            );
    }

    public function pictureUrl($id) {

        $url = str_replace('(id', '(' . $id, $this->profilePictureUrn);

        $urn = json_decode($this->request($url), true);       
        return $urn['profilePicture']['displayImage~']['elements'][2]['identifiers'][0]['identifier'];
                
    }

}
