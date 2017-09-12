<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/23/14
 * Time: 2:46 PM
 */
namespace Cms\Form;

class UserForm extends GeneralForm{
    public function __construct(){
        parent::__construct('users','users_id');
        $this->add(array(
            'name' => 'last_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'last_name',
                'placeholder' => 'Last name',
                'class'       => 'form-control input-sm'
            ),
        ));
        $this->add(array(
            'name' => 'first_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'first_name',
                'placeholder' => 'First name',
                'class'       => 'form-control input-sm'
            ),
        ));
        $this->add(array(
            'name' => 'full_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'full_name',
                'placeholder' => 'Họ và tên',
                'class'       => 'form-control input-sm'
            ),
        ));
        $this->add(array(
            'name' => 'user_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'user_name',
                'placeholder' => 'Tên đăng nhập',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'attributes' => array(
                'id'          => 'password',
                'placeholder' => 'Mật khẩu',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'repassword',
            'type' => 'Password',
            'attributes' => array(
                'id'          => 'repassword',
                'placeholder' => 'Xác nhận mật khẩu',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'users_alias',
            'type' => 'Hidden',
            'attributes' => array(
                'id'          => 'users_alias',
                'class'       => 'users_alias',
            ),
        ));
        $this->add(array(
            'name' => 'birthday',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'birthday',
                'placeholder' => "Birthday",
                'class'       => 'form-control date-input input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'phone',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'phone',
                'placeholder' => "Phone",
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'address',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'address',
                'placeholder' => "Address",
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'is_administrator',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_administrator',
                'class'       => 'form-control input-sm',
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
            'name' => 'merchant_id',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'merchant_id',
                'class'       => 'form-control input-sm select-multiple-publisher'
            ),
        ));
    }
}