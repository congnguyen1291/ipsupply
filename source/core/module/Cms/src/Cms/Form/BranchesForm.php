<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 11:06 AM
 */
namespace Cms\Form;

class BranchesForm extends GeneralForm{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('branches', 'branches_id');
        $this->add(array(
            'name' => 'branches_title',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'branches_title',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'address',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'address',
                'class'       => 'form-control input-sm'
            ),
        ));
        $this->add(array(
            'name' => 'phone',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'phone',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'website',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'website',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'email',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'email',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'description',
                'class'       => 'form-control ckeditor input-sm',

            ),
        ));
        $this->add(array(
            'name' => 'is_published',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'class'       => 'form-control input-sm',

            ),
        ));

        $this->add(array(
            'name' => 'is_default',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_default',
                'class'       => 'form-control input-sm',

            ),
        ));
    }
}