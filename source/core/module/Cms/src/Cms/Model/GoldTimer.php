<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 9:22 AM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class GoldTimer implements InputFilterAwareInterface{
    public $gold_timer_id;
    public $website_id;
    public $gold_timer_title;
    public $time_start;
    public $time_end;
    public $date_start;
    public $date_end;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->gold_timer_id            = (!empty($data['gold_timer_id'])) ? $data['gold_timer_id'] : NULL;
        $this->website_id            = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->gold_timer_title          = (!empty($data['gold_timer_title'])) ? $data['gold_timer_title'] : NULL;
        $this->time_start         = (!empty($data['time_start'])) ? $data['time_start'] : '00:00:00';
        $this->time_end         = (!empty($data['time_end'])) ? $data['time_end'] : '00:00:00';
        $this->date_start         = (!empty($data['date_start'])) ? $data['date_start'] : date('Y-m-d');
        $this->date_end         = (!empty($data['date_end'])) ? $data['date_end'] : date('Y-m-d');
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'gold_timer_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'gold_timer_title',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 250,
                        ),
                    ),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
} 