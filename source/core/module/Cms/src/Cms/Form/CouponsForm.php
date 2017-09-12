<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;


class CouponsForm extends GeneralForm
{
    public function __construct($name = null)
    {
        parent::__construct('coupons', 'coupons_id');
        $this->add(array(
            'name' => 'coupons_code',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'coupons_code',
                'placeholder' => 'Mã coupons',
                'class' => 'form-control  input-sm',

            ),
        ));
        $this->add(array(
            'name'       => 'coupons_type',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'coupons_type',
                'class'       => 'form-control  input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'coupons_max_use',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'coupons_max_use',
                'class'       => 'form-control  input-sm numberInput input-ordering',
                'value'       => 1
            ),
        ));
        $this->add(array(
            'name'       => 'start_date',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'start_date',
                'class'       => 'date-time-input form-control  input-sm',
                'value'       => date('Y-m-d H:m:s'),
            ),
        ));
        $this->add(array(
            'name'       => 'expire_date',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'expire_date',
                'class'       => 'date-time-input form-control  input-sm',
                'value'       => date('Y-m-d H:m:s'),
            ),
        ));
        $this->add(array(
            'name'       => 'min_price_use',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'min_price_use',
                'class'       => 'moneyInput form-control  input-sm',
                'placeholder'       => 'Nhỏ nhất',
            ),
        ));
        $this->add(array(
            'name'       => 'max_price_use',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'max_price_use',
                'class'       => 'moneyInput form-control  input-sm',
                'placeholder'       => 'Lớn nhất',
            ),
        ));
        $this->add(array(
            'name'       => 'coupon_price',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'coupon_price',
                'class'       => 'moneyInput form-control  input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'coupon_percent',
            'type'       => 'Hidden',
            'attributes' => array(
                'id'          => 'coupon_percent',
                'class'       => 'numberInput form-control  input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'is_published',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'class'       => 'form-control  input-sm',
            ),
        ));
    }
}