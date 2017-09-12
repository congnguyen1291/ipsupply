<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;


class PackForm extends GeneralForm
{
    public function __construct($name = null)
    {
        parent::__construct('pack', 'pack_id');
        $this->add(array(
            'name' => 'pack_name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'pack_name',
                'placeholder' => 'Tên park',
                'class' => 'form-control',

            ),
        ));
        $this->add(array(
            'name'       => 'pack_description',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'pack_description',
                'class'       => 'form-control ckeditor',
                'placeholder' => 'Mô tả'
            ),
        ));
        $this->add(array(
            'name' => 'pack_price',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'pack_price',
                'placeholder' => 'price',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name'       => 'time',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'time',
                'class'       => 'form-control',
                'placeholder' => 'time'
            ),
        ));
        $this->add(array(
            'name'       => 'time_bonus',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'time_bonus',
                'class'       => 'form-control',
                'placeholder' => 'time bonus'
            ),
        ));
        $this->add(array(
            'name'       => 'products',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'products',
                'class'       => 'form-control',
                'placeholder' => 'so san pham'
            ),
        ));
        $this->add(array(
            'name'       => 'bandwidth',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'bandwidth',
                'class'       => 'form-control',
                'placeholder' => 'so san pham'
            ),
        ));
        $this->add(array(
            'name'       => 'storage',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'storage',
                'class'       => 'form-control',
                'placeholder' => 'storage'
            ),
        ));
        $this->add(array(
            'name'       => 'domain',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'domain',
                'class'       => 'form-control'
            ),
        ));
        $this->add(array(
            'name' => 'edit_seo',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'edit_seo',
                'class'       => 'form-control',

            ),
        ));
        $this->add(array(
            'name' => 'chat_live',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'chat_live',
                'class'       => 'form-control',

            ),
        ));
        $this->add(array(
            'name' => 'shop',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'shop',
                'class'       => 'form-control',

            ),
        ));
        $this->add(array(
            'name'       => 'email_marketing',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'email_marketing',
                'class'       => 'form-control',
                'placeholder' => 'email marketing'
            ),
        ));
        $this->add(array(
            'name'       => 'responsive',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'responsive',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name'       => 'payment_online',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'payment_online',
                'class'       => 'form-control'
            ),
        ));
        $this->add(array(
            'name'       => 'multi_language',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'multi_language',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name'       => 'edit_layout',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'edit_layout',
                'class'       => 'form-control',
            ),
        ));
        $this->add(array(
            'name'       => 'is_published',
            'type'       => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'class'       => 'form-control'
            ),
        ));

    }
}