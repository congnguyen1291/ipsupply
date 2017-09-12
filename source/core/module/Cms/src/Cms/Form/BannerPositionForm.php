<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;


class BannerPositionForm extends GeneralForm
{
    public function __construct($name = null)
    {
        parent::__construct('position', 'position_id');
        $this->add(array(
            'name' => 'position_name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'position_name',
                'placeholder' => 'Tên vị trí',
                'class' => 'form-control input-sm',

            ),
        ));
        $this->add(array(
            'name' => 'position_alias',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'position_alias',
                'placeholder' => 'Alias vị trí',
                'class' => 'form-control input-sm',

            ),
        ));

    }
}