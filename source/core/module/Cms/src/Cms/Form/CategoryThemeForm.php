<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 10:32 AM
 */

namespace Cms\Form;


class CategoryThemeForm extends GeneralForm{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('categories_theme', 'categories_template_id');
        $this->add(array(
            'name' => 'parent_id',
            'type' => 'Select',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'parent_id',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'categories_title',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'categories_title',
                'placeholder' => 'Tên sản phẩm',
                'class'       => 'form-control',
                'onblur'      => 'javascript:locdau(this.value, \'.categories_alias\');',
            ),
        ));
        $this->add(array(
            'name' => 'categories_alias',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'categories_alias',
                'placeholder' => 'Url',
                'class'       => 'form-control categories_alias',

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