<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

class BanksForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('banks', 'banks_id');
        $this->add(array(
            'name' => 'banks_title',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Title',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name'       => 'banks_description',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'banks_description',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name'       => 'is_published',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'class'       => 'form-control',
            ),
        ));
    }
}