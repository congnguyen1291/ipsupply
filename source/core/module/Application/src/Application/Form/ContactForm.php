<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/18/14
 * Time: 3:43 PM
 */

namespace Application\Form;

use Zend\Form\Form;

class ContactForm extends Form
{
    public function __construct($params = null)
    {
        parent::__construct('contactform');
        $this->add(array(
            'name' => 'title',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'Title',
                'value' => isset($params['title']) ? $params['title'] : '',
            ),
        ));
        $this->add(array(
            'name' => 'content',
            'type' => 'textarea',
            'attributes' => array(
                'placeholder' => 'Content',
                'class' => 'form-control',
                'id' => 'content',                
                'rows' => 4,
            ),
        ));

        $this->add(array(
            'name' => 'fullname',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Full name',
                'class' => 'form-control',
                'id' => 'fullname',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'text',
            'attributes' => array(
                'placeholder' => 'Email',
                'class' => 'form-control',
                'id' => 'email',
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
            'name' => 'copy',
            'type' => 'checkbox',
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'copy',
            )
        ));
    }
}