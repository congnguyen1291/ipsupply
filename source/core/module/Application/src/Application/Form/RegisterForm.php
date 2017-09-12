<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/18/14
 * Time: 3:43 PM
 */

namespace Application\Form;


use Zend\Form\Form;

class RegisterForm extends Form{
    public function __construct(){
        parent::__construct('register');
        $this->add(array(
            'name' => 'full_name',
            'type' => 'Text',
            'attributes' => array(
                
                'class' => 'form-control',
                'id' => 'full_name',
                'onblur' => 'javascript:locdau(this.value,\'.users_alias\');'
            ),
        ));
        $this->add(array(
            'name' => 'users_alias',
            'type' => 'Hidden',
            'attributes' => array(
                'class' => 'form-control users_alias',
                 'id' => 'users_alias',
            ),
        ));
        $this->add(array(
            'name' => 'user_name',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Email',
                'class' => 'form-control',
                'id' => 'user_name',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'attributes' => array(
                'placeholder' => 'Password',
                'class' => 'form-control',
                'id' => 'password',
            ),
        ));
        $this->add(array(
            'name' => 'repassword',
            'type' => 'password',
            'attributes' => array(
                'placeholder' => 'Confirm password',
                'class' => 'form-control',
                'id' => 'repassword',
            ),
        ));
        $this->add(array(
            'name' => 'phone',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Phone',
                'class' => 'form-control',
                'id' => 'phone',
            ),
        ));
        $this->add(array(
            'name' => 'birthday',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Birthday',
                'class' => 'form-control date-input',
                 'id' => 'birthday',
            ),
        ));
        $this->add(array(
            'name' => 'address',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Address',
                'class' => 'form-control',
                 'id' => 'address',
            ),
        ));
        $this->add(array(
            'name' => 'cities_id',
            'type' => 'select',
            'attributes' => array(
                'placeholder' => 'Tỉnh/Thành phố',
                'class' => 'form-control',
                'onchange' => 'loadDistrict(this.value,-1,-1)',
                'id' => 'cities_id',
            ),
        ));
//        $this->add(array(
//            'name' => 'districts_id',
//            'type' => 'select',
//            'attributes' => array(
//                'placeholder' => 'Quận/Huyện',
//                'class' => 'form-control',
//                'id' => 'districts',
//                'onchange' => 'loadWard(this.value)',
//            ),
//        ));
    }
}