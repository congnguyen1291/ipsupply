<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 12:06 PM
 */

namespace Cms\Form;

use Zend\Form\Form;

class TransportationForm extends GeneralForm{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('transportation', 'transportation_id');
        $this->add(array(
            'name' => 'shipping_class',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'shipping_class',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'transportation_type',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'transportation_type',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'transportation_title',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'transportation_title',
                'placeholder' => 'Tiêu đề',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'transportation_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'transportation_description',
                'placeholder' => 'Mô tả',
                'class'       => 'form-control ckeditor',
            ),
        ));
        $this->add(array(
            'name' => 'is_published',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'placeholder' => 'Hiển thị',
                'class'       => 'form-control',
            ),
        ));
    }
} 