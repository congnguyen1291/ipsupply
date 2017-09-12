<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 11:06 AM
 */
namespace Cms\Form;

class ExtensionForm extends GeneralForm{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('extension', 'ext_id');
        $this->add(array(
            'name' => 'ext_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'ext_name',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'ext_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'ext_description',
                'class' => 'form-control',
                'rows'  => '3',
            ),
        ));
        $this->add(array(
            'name' => 'is_published',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'is_published',
            ),
        ));

    }
}