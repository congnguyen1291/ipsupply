<?php
namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Payment implements InputFilterAwareInterface
{
    public $payment_id;
    public $website_id;
    public $payment_name;
    public $code;
    public $payment_description;

    public $sale_account;
    public $api_username;
    public $api_password;
    public $api_signature;

    public $vpc_merchant;
    public $vpc_accesscode;
    public $vpc_hashcode;

    public $vnp_merchant;
    public $vnp_tmncode;
    public $vnp_hashsecret;

    public $is_local;
    public $is_sandbox;

    public $ordering;
    public $image;
    public $is_published;
    public $is_delete;

    protected $inputFilter;
    public function exchangeArray($data)
    {
        $this->payment_id     = (!empty($data['payment_id'])) ? $data['payment_id'] : null;
        $this->website_id     = (!empty($data['website_id'])) ? $data['website_id'] : null;
        $this->payment_name     = (!empty($data['payment_name'])) ? $data['payment_name'] : null;
        $this->code = (!empty($data['code'])) ? $data['code'] : 'HOME';
        $this->payment_description = (!empty($data['payment_description'])) ? $data['payment_description'] : '';

        $this->sale_account = (!empty($data['sale_account'])) ? $data['sale_account'] : '';
        $this->api_username = (!empty($data['api_username'])) ? $data['api_username'] : '';
        $this->api_password = (!empty($data['api_password'])) ? $data['api_password'] : '';
        $this->api_signature = (!empty($data['api_signature'])) ? $data['api_signature'] : '';

        $this->vpc_merchant = (!empty($data['vpc_merchant'])) ? $data['vpc_merchant'] : '';
        $this->vpc_accesscode = (!empty($data['vpc_accesscode'])) ? $data['vpc_accesscode'] : '';
        $this->vpc_hashcode = (!empty($data['vpc_hashcode'])) ? $data['vpc_hashcode'] : '';

        $this->vnp_merchant = (!empty($data['vnp_merchant'])) ? $data['vnp_merchant'] : '';
        $this->vnp_tmncode = (!empty($data['vnp_tmncode'])) ? $data['vnp_tmncode'] : '';
        $this->vnp_hashsecret = (!empty($data['vnp_hashsecret'])) ? $data['vnp_hashsecret'] : '';

        $this->image = (!empty($data['image'])) ? $data['image'] : '';

        $this->is_local = (!empty($data['is_local'])) ? $data['is_local'] : 0;
        $this->is_sandbox = (!empty($data['is_sandbox'])) ? $data['is_sandbox'] : 0;
        
        $this->is_published = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->ordering = (!empty($data['ordering'])) ? $data['ordering'] : 0;
		
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
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
                'name'     => 'payment_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'payment_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'code',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}