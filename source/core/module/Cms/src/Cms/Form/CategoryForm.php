<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

use Zend\Form\Form;

class CategoryForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('categories', 'categories_id');
        $this->add(array(
            'name' => 'parent_id',
            'type' => 'Select',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'parent_id',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'categories_title',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'categories_title',
                'placeholder' => 'Tiêu đề',
                'class'       => 'form-control input-sm',
                'onblur'      => 'javascript:locdau(this.value, \'.categories_alias\');',
            ),
        ));
        $this->add(array(
            'name' => 'categories_alias',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'categories_alias',
                'placeholder' => 'Đường dẫn',
                'class'       => 'form-control input-sm categories_alias',

            ),
        ));
        $this->add(array(
            'name' => 'seo_keywords',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'seo_keywords',
                'placeholder' => 'SEO Keywords',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'seo_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'seo_description',
                'placeholder' => 'SEO Description',
                'class'       => 'form-control input-sm',
                'rows'        => 4
            ),
        ));
        $this->add(array(
            'name' => 'seo_title',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'seo_title',
                'placeholder' => 'SEO Title',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'is_published',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'class'       => 'form-control input-sm',
            ),
        ));
		$this->add(array(
            'name' => 'is_static',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_static',
                'class'       => 'form-control input-sm',
            ),
        ));
		$this->add(array(
            'name' => 'categories_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'categories_description',
                'placeholder' => 'Mô tả:',
                'class'       => 'form-control ckeditor',
                'rows'         => 3,
            ),
        ));
        $this->add(array(
            'name' => 'ordering',
            'type' => 'Text',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'ordering',
                'class'       => 'form-control input-sm numberInput input-ordering',
                'value'       => '0'
            ),
        ));
    }
}