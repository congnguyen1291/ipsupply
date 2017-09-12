<?php

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Category extends MultiLevel implements InputFilterAwareInterface
{
    public $categories_id;
    public $website_id;
    public $categories_title;
    public $categories_alias;
    public $seo_keywords;
    public $seo_description;
    public $categories_description;
    public $seo_title;
    public $icon;
    public $template_id;
	public $is_static;
	public $language;
    public function exchangeArray($data)
    {
        parent::exchange($data);
		//var_dump($data);
		//die();
        $this->categories_id = (!empty($data['categories_id'])) ? $data['categories_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->categories_title = (!empty($data['categories_title'])) ? $data['categories_title'] : NULL;
        $this->categories_alias = (!empty($data['categories_alias'])) ? $data['categories_alias'] : NULL;
        $this->seo_title = (!empty($data['seo_title']))  ? $data['seo_title']  : NULL;
        $this->seo_keywords = (!empty($data['seo_keywords']))  ? $data['seo_keywords']  : NULL;
        $this->seo_description = (!empty($data['seo_description']))  ? $data['seo_description']  : NULL;
        $this->icon = (!empty($data['icon']))  ? $data['icon']  : NULL;
		$this->categories_description = (!empty($data['categories_description']))  ? $data['categories_description']  : NULL;
        $this->template_id          = (!empty($data['template_id']))  ? $data['template_id']  : 0;
		$this->is_static          = (!empty($data['is_static']))  ? $data['is_static']  : 0;
		$this->language          = (!empty($data['language']))  ? $data['language']  : 1;
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
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'categories_id',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
			$inputFilter->add(array(
                'name' => 'categories_description',
                'required' => false,
            ));
            $inputFilter->add(array(
                'name' => 'categories_title',
                'required' => false,
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
                'name' => 'categories_alias',
                'required' => false,
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