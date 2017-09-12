<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;


class GroupsForm extends GeneralForm
{
    public function __construct($name = null)
    {
        parent::__construct('groups', 'groups_id');
        $this->add(array(
            'name' => 'groups_name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'groups_name',
                'placeholder' => 'Nhóm người dùng',
                'class' => 'form-control input-sm',

            ),
        ));

        $this->add(array(
            'name'       => 'groups_description',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'groups_description',
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