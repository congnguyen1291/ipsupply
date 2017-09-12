<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/28/14
 * Time: 11:16 AM
 */
namespace Cms\Form;

use Zend\Form\Form;

class GeneralForm extends Form
{
    public function __construct($name = null, $id = 'id'){
        parent::__construct($name);
        $this->attributes = array(
            'role'   => 'form',
            'method' => 'post'
        );
        $this->add(array(
            'name' => $id,
            'type' => 'Hidden',
            'filters'  => array(
                array('name' => 'Int'),
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