<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

use Zend\Form\Form;

class BlogForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('blog', 'id');
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Title',
                'class' => 'form-control',

            ),
        ));
        $this->add(array(
            'name' => 'content',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'content',
                'placeholder' => 'Content',
                'class'       => 'form-control ckeditor',
                'rows'         => 3,
            ),
        ));
        $this->add(array(
            'name'       => 'catid',
            'type'       => 'Select',
            'options'    => array(
                'empty_option' => '-- Please Choose --',
            ),
            'attributes' => array(
                'id'          => 'catid',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id'    => 'submitbutton',
                'class' => 'btn btn-primary',
            ),
        ));
        $this->add(array(
            'name' => 'cancel',
            'type' => 'button',
            'attributes' => array(
                'value' => 'Cancel',
                'class' => 'btn btn-danger',
            ),
        ));
    }
}