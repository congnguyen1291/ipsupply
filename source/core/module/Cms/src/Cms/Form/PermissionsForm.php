<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;


class PermissionsForm extends GeneralForm
{
    public function __construct($name = null)
    {
        parent::__construct('permissions', 'permissions_id');
        $this->add(array(
            'name' => 'permissions_name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'permissions_name',
                'placeholder' => 'Tên quyền',
                'class' => 'form-control input-sm',

            ),
        ));
        $this->add(array(
            'name' => 'controller',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'controller',
                'placeholder' => 'Controller',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'module',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'module',
                'placeholder' => 'Module',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'action',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'action',
                'class'       => 'form-control input-sm',
                'placeholder' => 'Action'
            ),
        ));
        $this->add(array(
            'name'       => 'description',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'description',
                'class'       => 'form-control input-sm',
                'placeholder' => 'Mô tả'
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