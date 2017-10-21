<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/23/14
 * Time: 2:46 PM
 */
namespace Cms\Form;

class PaymentMethodForm extends GeneralForm{
    public function __construct(){
        parent::__construct('payment_name','payment_id');
        $this->add(array(
            'name' => 'payment_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'payment_name',
                'placeholder' => 'Tên phương thức thanh toán',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'code',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'code',
                'placeholder' => 'Mã nhận dạng phương thức',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'payment_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'payment_description',
                'placeholder' => 'Mô tả',
                'class'       => 'form-control ckeditor input-sm',
                'rows'        => '3'
            ),
        ));
        $this->add(array(
            'name' => 'is_published',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'class'       => 'form-control input-sm',
            ),
        ));
		$this->add(array(
            'name' => 'is_sandbox',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_sandbox',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'sale_account',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'sale_account',
                'class'       => 'form-control input-sm',
                'placeholder' => 'Tài khoản nhận tiền'
            ),
        ));		
        $this->add(array(
            'name' => 'api_username',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'api_username',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'api_password',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'api_password',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'api_signature',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'api_signature',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'image',
            'type' => 'File',
            'attributes' => array(
                'id'          => 'image',
                'class'       => 'form-control input-sm',
                'placeholder' => 'Hình đại diện'
            ),
        ));
		$this->add(array(
            'name' => 'vpc_merchant',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'vpc_merchant',
                'class'       => 'form-control input-sm',
            ),
        ));
		$this->add(array(
            'name' => 'vpc_hashcode',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'vpc_hashcode',
                'class'       => 'form-control input-sm',
            ),
        ));
		$this->add(array(
            'name' => 'vpc_accesscode',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'vpc_accesscode',
                'class'       => 'form-control input-sm',
            ),
        ));
		$this->add(array(
            'name' => 'vnp_merchant',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'vnp_merchant',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'vnp_tmncode',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'vnp_tmncode',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'vnp_hashsecret',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'vnp_hashsecret',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'is_local',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_local',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'is_sandbox',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_sandbox',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'ordering',
            'type' => 'Text',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'ordering',
                'class'       => 'form-control  input-sm numberInput input-ordering',
                'value'       => '0'
            ),
        ));
		//End form
    }
}