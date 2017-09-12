<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 11:06 AM
 */
namespace Cms\Form;

class LanguagesForm extends GeneralForm{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('languages', 'languages_id');
        $this->add(array(
            'name' => 'languages_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'languages_name',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'languages_file',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'languages_file',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'is_published',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'class'       => 'form-control',
            ),
        ));
    }
}