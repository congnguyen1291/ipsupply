<?php

/**
 * Created by PhpStorm.
 * User: tientv
 * Date: 08/19/14
 * Time: 02:48 PM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Menu implements InputFilterAwareInterface{
	
	public $title;
	public $id;
	public $icon;
	public $link;
	public $ordering;
	public $is_new;
	public $is_hot;
	public $in_page;
	protected $inputFilter;

	public function exchangeArray($data)
    {
        $this->id            = (!empty($data['id'])) ? $data['id'] : time();
        $this->title          = (!empty($data['title'])) ? $data['title'] : NULL;
        $this->icon         = (!empty($data['icon'])) ? $data['icon'] : NULL;
        $this->link         = (!empty($data['link'])) ? $data['link'] : NULL;
        $this->ordering         = (!empty($data['ordering'])) ? $data['ordering'] : 0;
        $this->is_new         = (!empty($data['is_new'])) ? $data['is_new'] : 0;
        $this->is_hot         = (!empty($data['is_hot'])) ? $data['is_hot'] : 0;
        $this->in_page         = (!empty($data['in_page'])) ? $data['in_page'] : 0;
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
                'name' => 'title',
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
            $inputFilter->add(array(
                'name' => 'link',
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