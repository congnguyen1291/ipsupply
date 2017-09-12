<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;


class BannerSizeForm extends GeneralForm
{
    public function __construct($name = null)
    {
        parent::__construct('id', 'id');
        $this->add(array(
            'name' => 'size',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'size',
                'placeholder' => 'Tên size',
                'class' => 'form-control',

            ),
        ));
        $this->add(array(
            'name' => 'width',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'width',
                'placeholder' => 'width',
                'class' => 'form-control',

            ),
        ));
        $this->add(array(
            'name' => 'height',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'height',
                'placeholder' => 'width',
                'class' => 'form-control',

            ),
        ));

    }
}