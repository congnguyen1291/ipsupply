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

class Extension implements InputFilterAwareInterface{
    public $ext_id;
    public $website_id;
    public $ext_name;
    public $ext_description;
    public $is_published;
    public $is_delete;
    public $date_create;
    public $date_update;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->ext_id            = (!empty($data['ext_id'])) ? $data['ext_id'] : NULL;
        $this->website_id            = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->ext_name          = (!empty($data['ext_name'])) ? $data['ext_name'] : NULL;
        $this->ext_description         = (!empty($data['ext_description'])) ? $data['ext_description'] : NULL;
        $this->is_published     = (!empty($data['is_published']))  ? $data['is_published']  : 0;
        $this->is_delete = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->date_create    = (!empty($data['date_create']))  ? $data['date_create']  : date('Y-m-d H:i:s');
        $this->date_update             = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
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
                'name' => 'ext_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'ext_name',
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