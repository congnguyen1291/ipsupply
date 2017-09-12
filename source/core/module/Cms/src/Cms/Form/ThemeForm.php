<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

class ThemeForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('template_id', 'template_id');
        $this->add(array(
            'name' => 'categories_template_id',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'categories_template_id',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'template_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'template_name',
                'placeholder' => 'Tên templet',
                'class'       => 'form-control',
                'onblur'      => 'javascript:locdau(this.value, \'.template_alias\');',
            ),
        ));
        $this->add(array(
            'name'       => 'template_alias',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'template_alias',
                'class'       => 'form-control template_alias',
            ),
        ));
        $this->add(array(
            'name' => 'template_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'template_description',
                'placeholder' => 'Mô tả:',
                'class'       => 'form-control ckeditor',
                'rows'         => 3,
            ),
        ));
        
        $this->add(array(
            'name'       => 'template_dir',
            'type'       => 'Hidden',
            'attributes' => array(
                'id'          => 'template_dir',
                'class'       => 'form-control',
            ),
        ));

        $this->add(array(
            'name'       => 'template_folder',
            'type'       => 'Hidden',
            'attributes' => array(
                'id'          => 'template_folder',
                'class'       => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'template_status',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'template_status',
                'class'       => 'form-control',

            ),
        ));

        $this->add(array(
            'name'       => 'templete_price',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'templete_price',
                'class'       => 'form-control',
            ),
        ));


    }
}