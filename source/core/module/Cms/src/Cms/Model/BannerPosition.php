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

class BannerPosition implements InputFilterAwareInterface
{
    public $position_id;
    public $website_id;
    public $position_name;
    public $position_alias;
    public $image_preview;
    public $date_create;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->website_id   = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->position_id   = (!empty($data['position_id'])) ? $data['position_id'] : NULL;
        $this->position_name = (!empty($data['position_name'])) ? $data['position_name'] : '';
        $this->position_alias   = (!empty($data['position_alias'])) ? $data['position_alias'] : '';
        $this->image_preview   = (!empty($data['image_preview'])) ? $data['image_preview'] : '';
        $this->date_create   = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d');
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
                'name' => 'position_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'position_name',
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
                'name' => 'position_alias',
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
                            'max' => 256,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'image_preview',
                'required' => FALSE,
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}