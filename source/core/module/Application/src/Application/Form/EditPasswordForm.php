<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/18/14
 * Time: 3:43 PM
 */

namespace Application\Form;


use Zend\Form\Form;

class EditPasswordForm extends Form{
    public function __construct($params){
    	//var_dump($params);die;
        parent::__construct('editpassword');
        $this->add(array(
            'name' => 'user_name',
            'type' => 'hidden',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'user_name',
            	'value'=>$params['user_name'],
            ),
        ));
		$this->add(array(
            'name' => 'old_password',
            'type' => 'password',
            'attributes' => array(
                'placeholder' => 'Old password',
                'class' => 'form-control',
            	'autocomplete'=>'off',
                'id' => 'old_password',

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
            'name' => 'users_alias',
            'type' => 'Hidden',
            'attributes' => array(
                'class' => 'form-control users_alias',
                'id' => 'users_alias',
            	'value'=>$params['users_alias'],
            ),
        ));
		$this->add(array(
        		'name' => 'users_id',
        		'type' => 'Hidden',
        		'attributes' => array(
        				'class' => 'form-control',
        				'id' => 'users_id',
        				'value'=>$params['users_id'],
        		),
        ));
    }
}