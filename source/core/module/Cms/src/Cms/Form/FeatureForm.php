<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

use Zend\Form\Form;

class FeatureForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('feature', 'feature_id');
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
            'name' => 'website_id',
            'type' => 'Hidden',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'website_id',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'feature_title',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'feature_title',
                'placeholder' => 'Name',
                'class'       => 'form-control input-sm',
                'onblur'      => 'javascript:locdau(this.value, \'.feature_alias\');',
            ),
        ));

        $this->add(array(
            'name' => 'feature_alias',
            'type' => 'Hidden',
            'attributes' => array(
                'id'          => 'feature_alias',
                'placeholder' => 'Đường dẫn',
                'class'       => 'form-control input-sm feature_alias',

            ),
        ));

        $this->add(array(
            'name' => 'is_published',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'class'       => 'form-control input-sm',
				'checked'	=> 'checked',
            ),
        ));

        $this->add(array(
            'name' => 'is_value',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'is_value',
                'class'       => 'form-control input-sm',
            ),
            'options' => array(
                'value_options' => array(
                    0 => 'Color',
                    1 => 'Icon',
                ),
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
                'class'       => 'form-control input-sm numberInput input-ordering' ,
                'value'       => '0',
            ),
        ));

        $this->add(array(
            'name' => 'feature_type',
            'type' => 'Select',
            'options' => array(
                'value_options' => array(
                    0 => 'Bình thường',
                    1 => 'Màu sắc',
                ),
            ),
            'attributes' => array(
                'id'          => 'feature_type',
                'class'       => 'form-control input-sm feature_type',
            ),
        ));

        $this->add(array(
            'name' => 'feature_color',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'feature_color',
                'value'       => '#00aabb',
                'class'       => 'form-control input-sm feature_file',

            ),
        ));
        $this->add(array(
            'name' => 'feature_file',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'feature_file',
                'placeholder' => 'Đường dẫn hình',
                'class'       => 'form-control input-sm feature_file',

            ),
        ));
    }

}