<?php

namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Facebook{

    public $facebook_id_a;
    public $facebook_id;
    public $users_id;
    public $website_id;
    public $email;
    public $first_name;
    public $last_name;
    public $name;
    public $name_format;
    public $gender;
    public $locale;
    public $link;
    public $timezone;
    public $cover;
    public $currency;
    public $date_create;
    public $date_update;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->facebook_id_a     = (isset($data['facebook_id_a'])) ? $data['facebook_id_a'] : null;
        $this->facebook_id = (isset($data['facebook_id'])) ? $data['facebook_id'] : null;
        $this->users_id = (isset($data['users_id'])) ? $data['users_id'] : 0;
        $this->website_id = (isset($data['website_id'])) ? $data['website_id'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : '';
        $this->first_name = (isset($data['first_name'])) ? $data['first_name'] : '';
        $this->last_name = (isset($data['last_name'])) ? $data['last_name'] : '';
        $this->name = (isset($data['name'])) ? $data['name'] : '';
        $this->name_format = (isset($data['name_format'])) ? $data['name_format'] : '';
        $this->gender = (isset($data['gender'])) ? $data['gender'] : '';
        $this->locale = (isset($data['locale'])) ? $data['locale'] : '';
        $this->link = (isset($data['link'])) ? $data['link'] : '';
        $this->timezone = (isset($data['timezone'])) ? $data['timezone'] : 0;
        $this->cover = (isset($data['cover'])) ? $data['cover'] : '';
        $this->currency = (isset($data['currency'])) ? $data['currency'] : '';
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
    }
    
}
