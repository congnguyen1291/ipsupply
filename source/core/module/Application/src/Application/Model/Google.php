<?php

namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Google{

    public $google_id_a;
    public $google_id;
    public $users_id;
    public $website_id;
    public $email;
    public $first_name;
    public $last_name;
    public $name;
    public $cover;
    public $date_create;
    public $date_update;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->google_id_a     = (isset($data['google_id_a'])) ? $data['google_id_a'] : null;
        $this->google_id = (isset($data['google_id'])) ? $data['google_id'] : null;
        $this->users_id = (isset($data['users_id'])) ? $data['users_id'] : 0;
        $this->website_id  = (isset($data['website_id'])) ? $data['website_id'] : null;
        $this->email  = (isset($data['email'])) ? $data['email'] : '';
        $this->first_name  = (isset($data['first_name'])) ? $data['first_name'] : '';
        $this->last_name  = (isset($data['last_name'])) ? $data['last_name'] : '';
        $this->name  = (isset($data['name'])) ? $data['name'] : '';
        $this->cover  = (isset($data['cover'])) ? $data['cover'] : '';
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
    }
    
}
