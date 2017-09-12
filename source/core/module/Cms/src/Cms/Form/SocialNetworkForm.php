<?php

/**
 * Created by PhpStorm.
 * User: tientv
 * Date: 08/19/14
 * Time: 02:22 PM
 */

namespace Cms\Form;

class SocialNetworkForm extends GeneralForm{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('connect_socials', 'id');
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
            'type' => 'Url',
            'attributes' => array(
                'id'          => 'link',
                'placeholder' => 'Liên kết',
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
            'name' => 'is_published',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'placeholder' => '',
                'class'       => 'form-control',
            ),
        ));
    }
}