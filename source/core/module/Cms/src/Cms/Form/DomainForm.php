<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;


class DomainForm extends GeneralForm
{
    public function __construct($name = null)
    {
        parent::__construct('domain_id', 'domain_id');
        $this->add(array(
            'name' => 'domain_name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'domain_name',
                'placeholder' => 'Domain',
                'class' => 'form-control',

            ),
        ));
        $this->add(array(
            'name' => 'domain_price',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'domain_price',
                'class' => 'form-control',

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