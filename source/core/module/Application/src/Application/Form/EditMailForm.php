<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/18/14
 * Time: 3:43 PM
 */

namespace Application\Form;


use Zend\Form\Form;

class EditMailForm extends Form{
    public function __construct($params){
    	//var_dump($params);die;
        parent::__construct('editpassword');
        $this->add(array(
            'name' => 'user_name',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'user_name',
            	'value'=>$params['user_name'],
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