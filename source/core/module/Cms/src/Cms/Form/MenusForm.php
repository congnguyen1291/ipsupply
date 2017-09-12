<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

class MenusForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('websites', 'website_id');
        $this->add(array(
            'name' => 'template_id',
            'type' => 'hidden'
        ));


    }
}