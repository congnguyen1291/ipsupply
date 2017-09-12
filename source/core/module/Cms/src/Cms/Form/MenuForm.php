<?php

/**
 * Created by PhpStorm.
 * User: tientv
 * Date: 08/19/14
 * Time: 02:22 PM
 */

namespace Cms\Form;

class MenuForm extends GeneralForm{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('menus', 'id');
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'title',
                'placeholder' => 'Tiêu đề',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'link',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'link',
                'placeholder' => 'Liên kết',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'is_new',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_new',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'is_hot',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_hot',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'in_page',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'in_page',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'is_remove_icon',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_remove_icon',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'file_icon',
            'type' => 'File',
            'attributes' => array(
                'id'          => 'file_icon',
                'placeholder' => 'Icon',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'icon',
            'type' => 'Hidden',
        ));
		
        $this->add(array(
            'name' => 'ordering',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'ordering',
                'placeholder' => '',
                'class'       => 'form-control numberInput',
            ),
        ));
    }
}