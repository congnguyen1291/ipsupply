<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

use Zend\Form\Form;

class ManufacturersForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('manufacturers', 'manufacturers_id');
        $this->add(array(
            'name' => 'manufacturers_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'manufacturers_name',
                'placeholder' => 'Tên nhà sản xuất',
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
            'name' => 'description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'description',
                'class'       => 'form-control input-sm',
                'rows'        => 3

            ),
        ));
        $this->add(array(
            'name' => 'warranty_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'warranty_description',
                'class'       => 'form-control ckeditor input-sm',
                'rows'        => 3

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
                'class'       => 'form-control  input-sm numberInput input-ordering',
                'value'       => '0'
            ),
        ));
    }
}