<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;


class ModulesForm extends GeneralForm
{
    public function __construct($name = null)
    {
        parent::__construct('modules', 'module_id');
        $this->add(array(
            'name' => 'module_name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'module_name',
                'placeholder' => 'module name',
                'class' => 'form-control',

            ),
        ));
        $this->add(array(
            'name'       => 'module_description',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'module_description',
                'class'       => 'form-control',
                'placeholder' => 'Mô tả'
            ),
        ));
        $this->add(array(
            'name'       => 'price',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'price',
                'class'       => 'form-control',
                'placeholder' => 'price'
            ),
        ));
        $this->add(array(
            'name'       => 'is_default',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_default',
                'class'       => 'form-control'
            ),
        ));

    }
}