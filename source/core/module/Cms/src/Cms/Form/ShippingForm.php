<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

class ShippingForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('shipping', 'shipping_id');
        $this->add(array(
            'name' => 'shipping_title',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Title',
                'class' => 'form-control shipping_title input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'group_regions_id',
            'type'       => 'Select',
            'attributes' => array(
                'id'          => 'group_regions_id',
                'class'       => 'form-control group_regions_id input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'shipping_type',
            'type'       => 'Select',
            'attributes' => array(
                'id'          => 'shipping_type',
                'class'       => 'form-control shipping_type input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'shipping_from',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'shipping_from',
                'class'       => 'form-control shipping_from moneyInput input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'shipping_to',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'shipping_to',
                'class'       => 'form-control shipping_to moneyInput input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'shipping_value',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'shipping_value',
                'class'       => 'form-control shipping_value moneyInput input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'shipping_fast_value',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'shipping_fast_value',
                'class'       => 'form-control shipping_fast_value moneyInput input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'shipping_time',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'shipping_time',
                'class'       => 'form-control shipping_time input-sm',
            ),
        ));
    }
}