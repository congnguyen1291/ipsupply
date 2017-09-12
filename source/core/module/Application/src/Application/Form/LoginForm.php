<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/18/14
 * Time: 3:43 PM
 */

namespace Application\Form;


use Zend\Form\Form;

class LoginForm extends Form{
    public function __construct(){
        parent::__construct('register');
        $this->add(array(
            'name' => 'user_name',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Email',
                'class' => 'form-control',
                    'value' => ''
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'password',
            'attributes' => array(
                'placeholder' => 'Password',
                'class' => 'form-control',
                    'value' => ''
            ),
        ));


    }
}