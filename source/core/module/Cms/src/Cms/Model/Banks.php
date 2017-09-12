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

class Banks implements InputFilterAwareInterface{
    public $banks_id;
    public $website_id;
    public $banks_title;
    public $banks_description;
    public $is_published;
    public $is_delete;
    public $date_create;
    public $thumb_image;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->banks_id            = (!empty($data['banks_id'])) ? $data['banks_id'] : NULL;
        $this->website_id            = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->banks_title          = (!empty($data['banks_title'])) ? $data['banks_title'] : NULL;
        $this->banks_description         = (!empty($data['banks_description'])) ? $data['banks_description'] : NULL;
        $this->is_published         = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete         = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->date_create         = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->thumb_image         = (!empty($data['thumb_image'])) ? $data['thumb_image'] : NULL;
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
                'name' => 'banks_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'banks_title',
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