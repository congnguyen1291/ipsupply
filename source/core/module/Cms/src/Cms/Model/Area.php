<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 10:49 AM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Area implements InputFilterAwareInterface
{
    public $area_id;
    public $country_id;
    public $area_title;
    public $area_alias;
    public $is_published;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->area_id   = (!empty($data['area_id'])) ? $data['area_id'] : NULL;
        $this->country_id   = (!empty($data['country_id'])) ? $data['country_id'] : NULL;
        $this->area_title = (!empty($data['area_title'])) ? $data['area_title'] : NULL;
        $this->area_alias   = (!empty($data['area_alias'])) ? $data['area_alias'] : '';
        $this->is_published   = (!empty($data['is_published'])) ? $data['is_published'] : 0;
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
                'name' => 'area_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'country_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'area_title',
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
                            'max' => 100,
                        ),
                    ),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}