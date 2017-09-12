<?php
namespace Google;
require_once 'Google_Client.php';
require_once 'contrib/Google_Oauth2Service.php';

class Api {

    public $facebook;
    public function __construct() {
    }

    /**
     * get me
     *
     * @author Thanhn Ngo
     */
    public function getMe() {
        $client = new \Google_Client();
        $client->setApplicationName("mytopcare");
        // Visit https://code.google.com/apis/console?api=plus to generate your
        // oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
        $client->setClientId(GOOGLE_CLIENT_ID);
        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
//         var_dump($_SERVER);die;
        $redirect = 'http://' . $_SERVER['HTTP_HOST'] . '/login';
        $client->setRedirectUri($redirect);
        // $client->setDeveloperKey('insert_your_developer_key');
        $oauth2 = new \Google_Oauth2Service($client);

        if (isset($_REQUEST['error'])) {
            return 'cancel';
        }

        if (isset($_GET['code'])) {
            $client->authenticate($_GET['code']);
            $_SESSION['token'] = $client->getAccessToken();
            $redirect = 'http://' . $_SERVER['HTTP_HOST'] . '/login';
            header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
            return;
        }

        if (isset($_SESSION['token'])) {
            $client->setAccessToken($_SESSION['token']);
        }

        if (isset($_REQUEST['logout'])) {
            unset($_SESSION['token']);
            $client->revokeToken();
            return 'cancel';
        }

        if ($client->getAccessToken()) {
            $user = $oauth2->userinfo->get();
            $_SESSION['token'] = $client->getAccessToken();
            return $user;
        } else {
            $authUrl = $client->createAuthUrl();
            return $authUrl;
        }
    }
}