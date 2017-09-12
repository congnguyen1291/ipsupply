<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/23/14
 * Time: 2:46 PM
 */
namespace Cms\Form;

class UsersLevelForm extends GeneralForm{
    public function __construct(){
        parent::__construct('users_level','users_level_id');
        $this->add(array(
            'name' => 'users_level_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'users_level_name',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'users_level_longdescription',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'users_level_longdescription',
                'class'       => 'form-control ckeditor input-sm',
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
            'name' => 'users_level_decrease',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'users_level_decrease',
                'class'       => 'form-control numberInput input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'min_buy',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'min_buy',
                'class'       => 'form-control moneyInput input-sm',
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
            'name' => 'users_level_icon',
            'type' => 'Hidden'
        ));
    }
}