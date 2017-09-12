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

class CategoriesArticlesLanguage implements InputFilterAwareInterface{
    public $id;
    public $categories_articles_id;
    public $languages_id;
    public $categories_articles_title;
    public $categories_articles_alias;
    public $seo_keywords;
    public $seo_description;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id            = (!empty($data['id'])) ? $data['id'] : NULL;
        $this->categories_articles_id          = (!empty($data['categories_articles_id'])) ? $data['categories_articles_id'] : NULL;
        $this->languages_id         = (!empty($data['languages_id'])) ? $data['languages_id'] : NULL;
        $this->categories_articles_title         = (!empty($data['categories_articles_title'])) ? $data['categories_articles_title'] : '';
        $this->categories_articles_alias         = (!empty($data['categories_articles_alias'])) ? $data['categories_articles_alias'] : '';
        $this->seo_keywords         = (!empty($data['seo_keywords'])) ? $data['seo_keywords'] : '';
        $this->seo_description         = (!empty($data['seo_description'])) ? $data['seo_description'] : '';
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
                'name' => 'id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'categories_articles_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'languages_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
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