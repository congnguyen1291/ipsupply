<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 3:12 PM
 */

namespace Cms\Form;

use Zend\Form\Form;

class InvoiceForm extends GeneralForm{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('invoice', 'invoice_id');
        $this->add(array(
            'name' => 'shipping_id',
            'type' => 'Hidden',
            'attributes' => array(
                'id'          => 'shipping_id',
                'placeholder' => 'Dịch vụ',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'invoice_title',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'invoice_title',
                'placeholder' => 'Tiều đề',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'invoice_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'invoice_description',
                'placeholder' => 'Mô tả',
                'class'       => 'form-control',
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
        $this->add(array(
            'name' => 'payment',
            'type' => 'Select',
            'options' => array(
                'value_options' => array(
                    'unpaid' => 'Chưa thanh toán',
                    'paid' => 'Đã thanh toán',
                ),
            ),
            'attributes' => array(
                'id'          => 'payment',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'delivery',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'delivery',
                'class'       => 'form-control input-sm',
            ),
            'options' => array(
                'value_options' => array(
                    'pending' => 'pending',
                    'processing' => 'processing',
                    'delivered' => 'delivered',
                    'updated' => 'updated',
                    'cancel' => 'cancel',
                    'accept' => 'accept',
                    'finish' => 'finish',
                ),
            ),
        ));
        $this->add(array(
            'name' => 'users_id',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'users_id',
                'onchange'    => 'filluser(this.value)'
            ),
        ));

        $this->add(array(
            'name' => 'full_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'full_name',
                'class'       => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'phone',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'phone',
                'class'       => 'form-control numberInput',
            ),
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Email',
            'attributes' => array(
                'id'          => 'email',
                'class'       => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'address',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'address',
                'class'       => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'company_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'company_name',
                'class'       => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'company_tax_code',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'company_tax_code',
                'class'       => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'company_address',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'company_address',
                'class'       => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'email_subscription',
            'type' => 'Email',
            'attributes' => array(
                'id'          => 'email_subscription',
                'class'       => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'promotion',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'promotion',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name' => 'total',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'total',
                'class'       => 'form-control numberInput',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id'    => 'submitbutton',
                'class' => 'btn btn-primary',
            ),
        ));
    }
} 