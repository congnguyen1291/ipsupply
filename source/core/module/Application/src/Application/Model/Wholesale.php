<?php

namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Wholesale{

    public $wholesale_id;
    public $website_id;
    public $users_id;
    public $transportation_id;
    public $wholesale_title;
    public $full_name;
    public $phone;
    public $email;
    public $address;
    public $wholesale_description;
    public $is_published;
    public $is_delete;
    public $payment;
    public $delivery;
    public $date_create;
    public $date_update;
    public $total;
    public $content;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->wholesale_id     = (isset($data['wholesale_id'])) ? $data['wholesale_id'] : null;
        $this->website_id = (isset($data['website_id'])) ? $data['website_id'] : null;
        $this->users_id = (isset($data['users_id'])) ? $data['users_id'] : 0;
        $this->transportation_id  = (isset($data['transportation_id'])) ? $data['transportation_id'] : null;
        $this->full_name  = (isset($data['full_name'])) ? $data['full_name'] : '';
        $this->phone  = (isset($data['phone'])) ? $data['phone'] : '';
        $this->email  = (isset($data['email'])) ? $data['email'] : '';
        $this->address  = (isset($data['address'])) ? $data['address'] : '';
        $this->wholesale_description          = (!empty($data['wholesale_description']))  ? $data['wholesale_description']  : '';
        $this->is_published  = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete  = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->payment  = (!empty($data['payment'])) ? $data['payment'] : NULL;
        $this->delivery  = (!empty($data['delivery'])) ? $data['delivery'] : NULL;
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
        $this->total = (!empty($data['total'])) ? $data['total'] : 0;
        $this->content = (!empty($data['content'])) ? $data['content'] : NULL;
    }
    
}
