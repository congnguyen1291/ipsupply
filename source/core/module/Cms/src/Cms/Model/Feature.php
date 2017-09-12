<?php

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Feature extends MultiLevel implements InputFilterAwareInterface
{
    public $feature_id;
    public $website_id;
    public $feature_title;
    public $feature_alias;
    public $is_value;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        parent::exchange($data);
        $this->feature_id = (!empty($data['feature_id'])) ? $data['feature_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->feature_title = (!empty($data['feature_title'])) ? $data['feature_title'] : NULL;
        $this->feature_alias = (!empty($data['feature_alias'])) ? $data['feature_alias'] : NULL;
        $this->feature_type = (!empty($data['feature_type'])) ? $data['feature_type'] : NULL;
        $this->feature_color = (!empty($data['feature_color'])) ? $data['feature_color'] : NULL;
        $this->feature_file = (!empty($data['feature_file'])) ? $data['feature_file'] : NULL;
        $this->is_value = (!empty($data['is_value'])) ? $data['is_value'] : 0;
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
                'name' => 'parent_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'feature_id',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'feature_type',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'is_value',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'feature_color',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'StripTags'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'feature_file',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'StripTags'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'feature_title',
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

            $inputFilter->add(array(
                'name' => 'feature_alias',
                'required' => FALSE,
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