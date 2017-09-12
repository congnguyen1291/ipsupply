<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

class GoldTimerForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('goldtimer', 'gold_timer_id');
        $this->add(array(
            'name' => 'gold_timer_title',
            'type' => 'Text',
            'attributes' => array(
                'placeholder' => 'Title',
                'class' => 'form-control',
            ),
        ));
        $this->add(array(
            'name'       => 'date_start',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'date_start',
                'value'       => date('Y-m-d'),
                'class'       => 'form-control date-input',
            ),
        ));
        $this->add(array(
            'name'       => 'date_end',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'date_end',
                'value'       => date('Y-m-d'),
                'class'       => 'form-control date-input',
            ),
        ));
        $this->add(array(
            'name'       => 'time_start',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'time_start',
                'class'       => 'form-control time-input',
            ),
        ));
        $this->add(array(
            'name'       => 'time_end',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'time_end',
                'class'       => 'form-control time-input',
            ),
        ));

    }
}