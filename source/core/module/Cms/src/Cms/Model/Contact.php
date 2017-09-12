<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 3:17 PM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Contact
{
    public $id;
    public $website_id;
    public $product_id;
    public $product_name;
    public $fullname;
    public $email;
    public $telephone;
    public $description;
    public $link;
    public $utm_source;
    public $utm_campaign;
    public $utm_medium;
    public $date_send;
    public $readed;
    public $reply;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id   = (!empty($data['id'])) ? $data['id'] : NULL;
        $this->website_id   = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->product_id = (!empty($data['product_id'])) ? $data['product_id'] : NULL;
        $this->product_name   = (!empty($data['product_name'])) ? $data['product_name'] : '';
        $this->fullname   = (!empty($data['fullname'])) ? $data['fullname'] : '';
        $this->email   = (!empty($data['email'])) ? $data['email'] : '';
        $this->telephone   = (!empty($data['telephone'])) ? $data['telephone'] : '';
        $this->description   = (!empty($data['description'])) ? $data['description'] : '';
        $this->link   = (!empty($data['link'])) ? $data['link'] : '';
        $this->utm_source   = (!empty($data['utm_source'])) ? $data['utm_source'] : '';
        $this->utm_campaign    = (!empty($data['utm_campaign'])) ? $data['utm_campaign'] : '';
        $this->utm_medium    = (!empty($data['utm_medium'])) ? $data['utm_medium'] : '';
        $this->date_send    = (!empty($data['date_send'])) ? $data['date_send'] : '';
        $this->readed    = (!empty($data['readed'])) ? $data['readed'] : '';
        $this->reply    = (!empty($data['reply'])) ? $data['reply'] : '';
    }
    
}