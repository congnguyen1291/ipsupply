<?php
namespace Openid\Facebook;

require_once('facebook.php');

class Api {

    public $facebook;
    public function __construct() {
        $config = array(
            'appId'              => FACEBOOK_APP_ID,
            'secret'             => FACEBOOK_APP_SECRET,
            'cookie'             => true,
            'allowSignedRequest' => false // optional but should be set to false for non-canvas apps
        );
        $this->facebook = new \Facebook($config);
    }

    /**
     * get me
     *
     * @author Giau Le
     */
    public function getMe() {
        $userId = $this->facebook->getUser();

        if($userId) {
            try {
                $data = $this->facebook->api('/me','GET');
            } catch(FacebookApiException $e) {
                $data = $this->facebook->getLoginUrl(array('display' => 'popup', 'scope' => 'publish_actions,user_birthday,user_location,email'));
            }
        } else {
            $data = $this->facebook->getLoginUrl(array('display' => 'popup', 'scope' => 'publish_actions,user_birthday,user_location,email'));
        }
        return $data;
    }
}