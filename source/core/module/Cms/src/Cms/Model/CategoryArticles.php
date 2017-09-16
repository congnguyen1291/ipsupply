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

class CategoryArticles implements InputFilterAwareInterface{
    public $categories_articles_id;
    public $website_id;
    public $parent_id;
    public $categories_articles_title;
    public $categories_articles_alias;
    public $seo_title;
    public $seo_keywords;
    public $seo_description;
    public $is_published;
    public $is_delete;
    public $is_faq;
    public $is_technical_category;
    public $date_create;
    public $date_update;
    public $ordering;
    public $is_static;
	public $language;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->categories_articles_id            = (!empty($data['categories_articles_id'])) ? $data['categories_articles_id'] : NULL;
        $this->website_id            = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->parent_id            = (!empty($data['parent_id'])) ? $data['parent_id'] : 0;
        $this->categories_articles_title            = (!empty($data['categories_articles_title'])) ? $data['categories_articles_title'] : array();
        $this->categories_articles_alias            = (!empty($data['categories_articles_alias'])) ? $data['categories_articles_alias'] : NULL;
        $this->seo_title            = (!empty($data['seo_title'])) ? $data['seo_title'] : '';
        $this->seo_keywords            = (!empty($data['seo_keywords'])) ? $data['seo_keywords'] : '';
        $this->seo_description            = (!empty($data['seo_description'])) ? $data['seo_description'] : '';
        $this->is_published            = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete            = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->is_technical_category          = (!empty($data['is_technical_category']))  ? $data['is_technical_category']  : 0;
        $this->is_faq            = (!empty($data['is_faq'])) ? $data['is_faq'] : 0;
        $this->date_create            = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update            = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
        $this->ordering            = (!empty($data['ordering'])) ? $data['ordering'] : 0;
        $this->is_static            = (!empty($data['is_static'])) ? $data['is_static'] : 0;
		$this->language            = (!empty($data['language'])) ? $data['language'] : 0;
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
                'name' => 'categories_articles_id',
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
                'name' => 'is_technical_category',
                'required' => FALSE,
            ));
            $inputFilter->add(array(
                'name' => 'is_faq',
                'required' => FALSE,
            ));
            $inputFilter->add(array(
                'name' => 'categories_articles_title',
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
                'name' => 'categories_articles_alias',
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