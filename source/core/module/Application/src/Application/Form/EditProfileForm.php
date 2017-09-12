<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/18/14
 * Time: 3:43 PM
 */

namespace Application\Form;


use Zend\Form\Form;

class EditProfileForm extends Form{
    public function __construct($params){
    	//var_dump($params);die;
        parent::__construct('editprofile');
        $this->add(array(
            'name' => 'full_name',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Full name',
                'class' => 'form-control',
                'id' => 'full_name',
                'onblur' => 'javascript:locdau(this.value,\'.users_alias\');',
            	'value'=>$params['full_name'],
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
        /*
        $this->add(array(
            'name' => 'user_name',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Email',
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
        */
        $this->add(array(
            'name' => 'phone',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Phone',
                'class' => 'form-control',
                'id' => 'phone',
            	'value'=>$params['phone'],
            ),
        ));
        $this->add(array(
            'name' => 'birthday',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Birthday',
                'class' => 'form-control date-input',
                'id' => 'birthday',
            	'value'=>date('d/m/Y', strtotime($params['birthday'])) ,
            ),
        ));
        $this->add(array(
            'name' => 'address',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Address',
                'class' => 'form-control',
                 'id' => 'address',
            		'value'=>$params['address'],
            ),
        ));
        $this->add(array(
            'name' => 'cities_id',
            'type' => 'select',
            'attributes' => array(
                'class' => 'form-control',
                'onchange' => 'loadDistrict(this.value,-1,-1)',
                'id' => 'cities',
            	'value'=>$params['cities_id'],
            ),
        ));
       $this->add(array(
           'name' => 'districts_id',
           'type' => 'select',
           'attributes' => array(               
               	'class' => 'form-control',
               	'id' => 'districts',
               	'onchange' => 'loadWard(this.value)',
           		'disable_inarray_validator' => true,
				'value'=>$params['districts_id'],
           ),
       ));
       
       $this->add(array(
       		'name' => 'wards_id',
       		'type' => 'select',
       		'attributes' => array(
       				'class' => 'form-control',
       				'id' => 'wards',
       				'disable_inarray_validator' => true,
       				'value'=> isset($params['wards_id']) ? $params['wards_id'] : 0,
       		),
       ));
    }
}