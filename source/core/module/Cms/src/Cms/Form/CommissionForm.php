<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

use Zend\Form\Form;

class CommissionForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('commission', 'commission_id');

        $this->add(array(
            'name' => 'merchant_id',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'merchant_id',
                'class' => 'form-control merchant_id input-sm input-select2',
            ),
        ));

        $this->add(array(
            'name'       => 'rate',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'rate',
                'class'       => 'form-control rate input-sm numberInput',
            ),
        ));

        $this->add(array(
            'name' => 'start_date',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'start_date',
                'placeholder' => "Start date",
                'class'       => 'form-control date-input input-sm',
                'value'       => date('Y-m-d H:m:s'),
            ),
        ));

        $this->add(array(
            'name' => 'end_date',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'end_date',
                'placeholder' => "End date",
                'class'       => 'form-control date-input input-sm',
                'value'       => date('Y-m-d H:m:s'),
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