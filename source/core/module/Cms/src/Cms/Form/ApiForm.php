<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;


class ApiForm extends GeneralForm
{
    public function __construct($name = null)
    {
        parent::__construct('api', 'api_id');
        $this->add(array(
            'name' => 'api_module',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'api_module',
                'placeholder' => 'Tên module',
                'class' => 'form-control',

            ),
        ));
        $this->add(array(
            'name' => 'api_class',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'api_class',
                'placeholder' => 'Tên class',
                'class' => 'form-control',

            ),
        ));
        $this->add(array(
            'name' => 'api_function',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'api_function',
                'placeholder' => 'Tên function',
                'class' => 'form-control',

            ),
        ));
        $this->add(array(
            'name'       => 'is_published',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'class'       => 'form-control'
            ),
        ));

    }
}