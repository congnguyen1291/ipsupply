<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 10:11 AM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class CategoryTemplate implements InputFilterAwareInterface{
    public $categories_template_id;
    public $parent_id;
    public $categories_title;
    public $categories_alias;
    public $is_published;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->categories_template_id            = (!empty($data['categories_template_id'])) ? $data['categories_template_id'] : NULL;
        $this->parent_id            = (!empty($data['parent_id'])) ? $data['parent_id'] : 0;
        $this->categories_title            = (!empty($data['categories_title'])) ? $data['categories_title'] : '';
        $this->categories_alias            = (!empty($data['categories_alias'])) ? $data['categories_alias'] : '';
        $this->is_published            = (!empty($data['is_published'])) ? $data['is_published'] : 0;
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
                'name' => 'categories_template_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'parent_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'categories_title',
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