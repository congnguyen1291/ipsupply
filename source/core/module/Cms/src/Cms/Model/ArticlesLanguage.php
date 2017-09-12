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

class ArticlesLanguage implements InputFilterAwareInterface{
    public $articles_languages_id;
    public $articles_id;
    public $languages_id;
    public $articles_title;
    public $articles_alias;
    public $articles_sub_content;
    public $articles_content;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->articles_languages_id            = (!empty($data['articles_languages_id'])) ? $data['articles_languages_id'] : NULL;
        $this->articles_id          = (!empty($data['articles_id'])) ? $data['articles_id'] : NULL;
        $this->languages_id         = (!empty($data['languages_id'])) ? $data['languages_id'] : NULL;
        $this->articles_title         = (!empty($data['articles_title'])) ? $data['articles_title'] : '';
        $this->articles_alias         = (!empty($data['articles_alias'])) ? $data['articles_alias'] : '';
        $this->articles_sub_content         = (!empty($data['articles_sub_content'])) ? $data['articles_sub_content'] : '';
        $this->articles_content         = (!empty($data['articles_content'])) ? $data['articles_content'] : '';
        $this->keyword_seo         = (!empty($data['keyword_seo'])) ? $data['keyword_seo'] : '';
        $this->description_seo         = (!empty($data['description_seo'])) ? $data['description_seo'] : '';
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
                'name' => 'articles_languages_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'articles_id',
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
                'name' => 'articles_title',
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