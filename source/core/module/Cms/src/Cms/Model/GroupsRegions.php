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

class GroupsRegions implements InputFilterAwareInterface{
    public $group_regions_id;
    public $website_id;
    public $country_id;
    public $is_rest_world;
    public $group_regions_name;
    public $regions;//danh sach id, cach nhau bang day ,
    public $group_regions_type;//kieu la thanh pho hay quan/huyen
    public $is_published;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->group_regions_id            = (!empty($data['group_regions_id'])) ? $data['group_regions_id'] : NULL;
        $this->website_id            = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->country_id            = (!empty($data['country_id'])) ? $data['country_id'] : '';
        $this->is_rest_world            = (!empty($data['is_rest_world'])) ? $data['is_rest_world'] : 0;
        $this->group_regions_name          = (!empty($data['group_regions_name'])) ? $data['group_regions_name'] : '';
        $this->regions         = (!empty($data['regions'])) ? $data['regions'] : '';
        $this->group_regions_type         = (!empty($data['group_regions_type'])) ? $data['group_regions_type'] : 0;
        $this->is_published         = (!empty($data['is_published'])) ? $data['is_published'] : 0;
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
                'name' => 'group_regions_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'group_regions_name',
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