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

class SocialNetwork implements InputFilterAwareInterface{
	
	public $title;
	public $id;
	public $icon;
	public $link;
	public $is_published;
	public $date_create;
	protected $inputFilter;

	public function exchangeArray($data)
    {
        $this->id            = (!empty($data['id'])) ? $data['id'] : NULL;
        $this->title          = (!empty($data['title'])) ? $data['title'] : NULL;
        $this->icon         = (!empty($data['icon'])) ? $data['icon'] : NULL;
        $this->link         = (!empty($data['link'])) ? $data['link'] : NULL;
        $this->is_published         = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->date_create         = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
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