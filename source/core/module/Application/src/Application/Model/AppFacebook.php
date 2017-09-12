<?php

namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class AppFacebook{

    public $app_facebook_id;
    public $website_id;
    public $app_name;
    public $app_id;
    public $app_secret;
    public $is_published;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->app_facebook_id     = (isset($data['app_facebook_id'])) ? $data['app_facebook_id'] : null;
        $this->website_id = (isset($data['website_id'])) ? $data['website_id'] : null;
        $this->app_name = (isset($data['app_name'])) ? $data['app_name'] : '';
        $this->app_id = (isset($data['app_id'])) ? $data['app_id'] : '';
        $this->app_secret = (isset($data['app_secret'])) ? $data['app_secret'] : '';
        $this->is_published = (isset($data['is_published'])) ? $data['is_published'] : 0;
    }
    
}
