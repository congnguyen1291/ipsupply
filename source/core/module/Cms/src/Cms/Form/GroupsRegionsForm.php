<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

class GroupsRegionsForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('group_regions', 'group_regions_id');
        $this->add(array(
            'name' => 'group_regions_name',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Title',
                'class' => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'country_id',
            'type'       => 'Hidden',
            'attributes' => array(
                'id'          => 'country_id',
                'class'       => 'form-control country_id input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'regions',
            'type'       => 'Hidden',
            'attributes' => array(
                'id'          => 'regions',
                'class'       => 'form-control citi_ajax_select input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'group_regions_type',
            'type'       => 'Hidden',
            'attributes' => array(
                'id'          => 'group_regions_type',
                'class'       => 'form-control input-sm',
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