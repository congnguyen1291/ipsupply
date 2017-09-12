<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 10:49 AM
 */

namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Pack implements InputFilterAwareInterface
{
    public $pack_id;
    public $pack_name;
    public $pack_description;
    public $pack_price;
    public $time;
    public $time_bonus;
    public $products;
    public $bandwidth;
    public $storage;
    public $domain;
    public $edit_seo;
    public $chat_live;
    public $shop;
    public $email_marketing;
    public $responsive;
    public $payment_online;
    public $multi_language;
    public $edit_layout;
    public $is_published;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->pack_id   = (!empty($data['pack_id'])) ? $data['pack_id'] : NULL;
        $this->pack_name = (!empty($data['pack_name'])) ? $data['pack_name'] : NULL;
        $this->pack_description   = (!empty($data['pack_description'])) ? $data['pack_description'] : NULL;
        $this->pack_price   = (!empty($data['pack_price'])) ? $data['pack_price'] : 0;
        $this->time   = (!empty($data['time'])) ? $data['time'] : 0;
        $this->time_bonus   = (!empty($data['time_bonus'])) ? $data['time_bonus'] : 0;
        $this->products   = (!empty($data['products'])) ? $data['products'] : 0;
        $this->bandwidth   = (!empty($data['bandwidth'])) ? $data['bandwidth'] : 0;
        $this->storage   = (!empty($data['storage'])) ? $data['storage'] : 0;
        $this->domain   = (!empty($data['domain'])) ? $data['domain'] : 0;
        $this->edit_seo   = (!empty($data['edit_seo'])) ? $data['edit_seo'] : 0;
        $this->chat_live   = (!empty($data['chat_live'])) ? $data['chat_live'] : 0;
        $this->shop   = (!empty($data['shop'])) ? $data['shop'] : 0;
        $this->email_marketing   = (!empty($data['email_marketing'])) ? $data['email_marketing'] : 0;
        $this->responsive   = (!empty($data['responsive'])) ? $data['responsive'] : 0;
        $this->payment_online   = (!empty($data['payment_online'])) ? $data['payment_online'] : 0;
        $this->multi_language   = (!empty($data['multi_language'])) ? $data['multi_language'] : 0;
        $this->edit_layout   = (!empty($data['edit_layout'])) ? $data['edit_layout'] : 0;
        $this->is_published   = (!empty($data['is_published'])) ? $data['is_published'] : 0;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'pack_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'pack_name',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}