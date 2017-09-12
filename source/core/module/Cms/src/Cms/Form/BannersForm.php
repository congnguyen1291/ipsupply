<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

class BannersForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('banners', 'banners_id');
        $this->add(array(
            'name' => 'banners_title',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Title',
                'class' => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'position_id',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'position_id',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'banners_description',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'banners_description',
                'class'       => 'form-control input-sm ckeditor',
                'rows'        => '10',
            ),
        ));
        $this->add(array(
            'name'       => 'code',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'code',
                'class'       => 'form-control input-sm',
                'rows'        => '10',
            ),
        ));
        $this->add(array(
            'name'       => 'file',
            'type'       => 'file',
            'attributes' => array(
                'id'          => 'file',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'date_show',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'date_show',
                'class'       => 'form-control input-sm date-time-input',
				'value'		  => date('Y-m-d H:i:s'),
            ),
        ));
        $this->add(array(
            'name'       => 'date_hide',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'date_hide',
                'class'       => 'form-control input-sm date-time-input',
				'value'		  => date('Y-m-d H:i:s'),
            ),
        ));
        $this->add(array(
            'name'       => 'link',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'link',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'status',
            'type'       => 'Hidden',
            'attributes' => array(
                'id'          => 'status',
                'class'       => 'form-control input-sm',
                'value'       => 0
            ),
        ));
        $this->add(array(
            'name'       => 'is_published',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'class'       => 'form-control input-sm',
            ),
        ));
    }
}