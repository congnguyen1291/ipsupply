<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class User extends App
{
    public function hasLogin()
    {
        if( !empty($_SESSION['MEMBER']) 
            && !empty($_SESSION['MEMBER']['full_name']) ){
            return TRUE;
        }
        return false;
    }

    public function getFullName()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['full_name']) ){
            return $_SESSION['MEMBER']['full_name'];
        }
        return '';
    }

    public function getAddress()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['address']) ){
            return $_SESSION['MEMBER']['address'];
        }
        return '';
    }

    public function getStringGender()
    {
        $translator = $this->getTranslator ();
        $gender = '';
        if( $this->hasLogin()
            && isset($_SESSION['MEMBER']['gender']) ){
            return $translator->translate('txt_gender_'.$_SESSION['MEMBER']['gender']);
        }
        return $gender;
    }

    public function getGender()
    {
        $gender = 0;
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['gender']) ){
            return $_SESSION['MEMBER']['gender'];
        }
        return $gender;
    }

    public function getUsersId()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['users_id']) ){
            return $_SESSION['MEMBER']['users_id'];
        }
        return '';
    }

    public function getMerchantId()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['merchant_id']) ){
            return $_SESSION['MEMBER']['merchant_id'];
        }
        return '';
    }

    public function getUserName()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['user_name']) ){
            return $_SESSION['MEMBER']['user_name'];
        }
        return '';
    }

    public function getEmail()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['email']) ){
            return $_SESSION['MEMBER']['email'];
        }
        return '';
    }

    public function getUrlTwitter()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['url_twitter']) ){
            return $_SESSION['MEMBER']['url_twitter'];
        }
        return '';
    }

    public function getUrlGooglePlus()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['url_google_plus']) ){
            return $_SESSION['MEMBER']['url_google_plus'];
        }
        return '';
    }

    public function getUrlFacebook()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['url_facebook']) ){
            return $_SESSION['MEMBER']['url_facebook'];
        }
        return '';
    }

    public function getFacebookId()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['facebookId']) ){
            return $_SESSION['MEMBER']['facebookId'];
        }
        return '';
    }

    public function getBirthday()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['birthday']) ){
            return $_SESSION['MEMBER']['birthday'];
        }
        return date('Y-m-d', strtotime('-4745 days'));
    }

    public function getDayBirthday()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['birthday']) ){
            $date = \DateTime::createFromFormat("Y-m-d", $_SESSION['MEMBER']['birthday']);
            return $date->format("d");
        }
        return 0;
    }

    public function getMonthBirthday()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['birthday']) ){
            $date = \DateTime::createFromFormat("Y-m-d", $_SESSION['MEMBER']['birthday']);
            return $date->format("m");
        }
        return 0;
    }

    public function getYearBirthday()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['birthday']) ){
            $date = \DateTime::createFromFormat("Y-m-d", $_SESSION['MEMBER']['birthday']);
            return $date->format("Y");
        }
        return 0;
    }

    public function getPhone()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['phone']) ){
            return $_SESSION['MEMBER']['phone'];
        }
        return '';
    }

    public function getCover()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['cover']) ){
            return $_SESSION['MEMBER']['cover'];
        }
        return '';
    }

    public function getAvatar()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['avatar']) ){
            return $_SESSION['MEMBER']['avatar'];
        }
        return '';
    }

    public function getCountryId()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['country_id']) ){
            return $_SESSION['MEMBER']['country_id'];
        }
        return '';
    }

    public function getDistrictsId()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['districts_id']) ){
            return $_SESSION['MEMBER']['districts_id'];
        }
        return '';
    }

    public function getWardsId()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['wards_id']) ){
            return $_SESSION['MEMBER']['wards_id'];
        }
        return '';
    }

    public function getDateCreate()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['date_create']) ){
            return $_SESSION['MEMBER']['date_create'];
        }
        return '';
    }

    public function getDateUpdate()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['date_update']) ){
            return $_SESSION['MEMBER']['date_update'];
        }
        return '';
    }

    public function getLongitude()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['longitude']) ){
            return $_SESSION['MEMBER']['longitude'];
        }
        return '';
    }

    public function getLatitude()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['latitude']) ){
            return $_SESSION['MEMBER']['latitude'];
        }
        return '';
    }

    public function getZipcode()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['zipcode']) ){
            return $_SESSION['MEMBER']['zipcode'];
        }
        return '';
    }

    public function getSubAddress()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['address01']) ){
            return $_SESSION['MEMBER']['address01'];
        }
        return '';
    }

    public function getLastName()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['last_name']) ){
            return $_SESSION['MEMBER']['last_name'];
        }
        return '';
    }

    public function getFirstName()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['first_name']) ){
            return $_SESSION['MEMBER']['first_name'];
        }
        return '';
    }

    public function isMerchant()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['is_merchant']) ){
            return TRUE;
        }
        return FALSE;
    }

    public function isAffiliate()
    {
        if( $this->hasLogin()
            && !empty($_SESSION['MEMBER']['is_affiliate']) ){
            return TRUE;
        }
        return FALSE;
    }

    public function getMember()
    {
        if( $this->hasLogin() ){
            return $_SESSION['MEMBER'];
        }
        return array();
    }
	
}
