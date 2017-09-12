<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 11:06 AM
 */
namespace Cms\Form;

class MerchanForm extends GeneralForm{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('merchan', 'merchan_id');
        $this->add(array(
            'name' => 'merchan_type',
            'type' => 'Select',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'merchan_type',
                'class'       => 'form-control input-sm',
                'options'     => array(
                            1 => 'Đại lý cấp 1',
                            2 => 'Đại lý cấp 2',
                            0 => 'Đại lý cấp 3'
                    )
            ),
        ));

        $this->add(array(
            'name' => 'merchan_alias',
            'type' => 'Hidden',
            'attributes' => array(
                'id'          => 'merchan_alias'
            ),
        ));

        $this->add(array(
            'name' => 'merchan_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'merchan_name',
                'placeholder' => 'Name',
                'class'       => 'form-control input-sm'
            ),
        ));

        $this->add(array(
            'name' => 'merchan_phone',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'merchan_phone',
                'placeholder' => 'Phone',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'merchan_email',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'merchan_email',
                'placeholder' => 'Email',
                'class'       => 'form-control input-sm',

            ),
        ));

        $this->add(array(
            'name' => 'merchan_fax',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'merchan_fax',
                'placeholder' => 'Fax',
                'class'       => 'form-control input-sm'

            ),
        ));

        $this->add(array(
            'name' => 'merchan_note',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'merchan_note',
                'class'       => 'form-control input-sm',
                'rows'         => 3,
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

    }
}