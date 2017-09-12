<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/10/14
 * Time: 11:44 AM
 */

namespace Cms\Form;

use Zend\Form\Form;

class LoginForm extends Form{
    public function __construct(){

        parent::__construct('login');
        $this->attributes = array(
            'role'   => 'form',
            'method' => 'post'
        );
        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'username',
                'placeholder' => 'Username',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
            'attributes' => array(
                'id'          => 'password',
                'placeholder' => 'Password',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'rememberme',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'rememberme',
                'placeholder' => 'Remember me',
                'class'       => '',
            ),
        ));
    }
} 