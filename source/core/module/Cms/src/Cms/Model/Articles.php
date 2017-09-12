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

class Articles implements InputFilterAwareInterface{
    public $articles_id;
    public $users_id;
    public $website_id;
    public $users_fullname;
    public $categories_articles_id;
    public $articles_title;
    public $articles_alias;
    public $articles_content;
    public $title_seo;
    public $keyword_seo;
    public $description_seo;
    public $is_new;
    public $is_hot;
    public $is_published;
    public $articles_sub_content;
    public $is_delete;
    public $date_create;
    public $date_update;
    public $ordering;
    public $number_views;
    public $thumb_images;
    public $is_faq;
    public $is_static;
    public $language;
    protected $inputFilter;
    public $tags;

    public function exchangeArray($data)
    {
        $this->articles_id            = (!empty($data['articles_id'])) ? $data['articles_id'] : NULL;
        $this->website_id            = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->users_id          = (!empty($data['users_id'])) ? $data['users_id'] : NULL;
        $this->users_fullname         = (!empty($data['users_fullname'])) ? $data['users_fullname'] : NULL;
        $this->categories_articles_id         = (!empty($data['categories_articles_id'])) ? $data['categories_articles_id'] : 0;
        $this->articles_title         = (!empty($data['articles_title'])) ? $data['articles_title'] : NULL;
        $this->articles_alias         = (!empty($data['articles_alias'])) ? $data['articles_alias'] : NULL;
        $this->articles_content   = (!empty($data['articles_content'])) ? $data['articles_content'] : NULL;
        $this->title_seo              = (!empty($data['title_seo']))  ? $data['title_seo']  : '';
        $this->keyword_seo              = (!empty($data['keyword_seo']))  ? $data['keyword_seo']  : '';
        $this->description_seo  = (!empty($data['description_seo']))  ? $data['description_seo']  : '';
        $this->is_new     = (!empty($data['is_new']))  ? $data['is_new']  : 0;
        $this->is_hot     = (!empty($data['is_hot']))  ? $data['is_hot']  : 0;
        $this->is_published     = (!empty($data['is_published']))  ? $data['is_published']  : 0;
        $this->articles_sub_content             = (!empty($data['articles_sub_content'])) ? $data['articles_sub_content'] : NULL;
        $this->is_delete = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->date_create    = (!empty($data['date_create']))  ? $data['date_create']  : date('Y-m-d H:i:s');
        $this->date_update             = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
        $this->ordering = (!empty($data['ordering'])) ? $data['ordering'] : 0;
        $this->number_views    = (!empty($data['number_views']))  ? $data['number_views']  : 0;
        $this->thumb_images             = (!empty($data['thumb_images'])) ? $data['thumb_images'] : NULL;
        $this->is_faq             = (!empty($data['is_faq'])) ? $data['is_faq'] : 0;
        $this->is_static             = (!empty($data['is_static'])) ? $data['is_static'] : 0;
        $this->tags                     = (!empty($data['tags'])) ? $data['tags'] : '';
        $this->language                     = (!empty($data['language'])) ? $data['language'] : 0;
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
                'name' => 'articles_id',
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

            $inputFilter->add(array(
                'name' => 'keyword_seo',
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
                            'max' => 250,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'description_seo',
                'required' => FALSE,
            ));

            $inputFilter->add(array(
                'name' => 'articles_alias',
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
                'name' => 'articles_content',
                'required' => TRUE,
            ));

            $inputFilter->add(array(
                'name' => 'is_faq',
                'required' => FALSE
            ));

            $inputFilter->add(array(
                'name' => 'is_static',
                'required' => FALSE
            ));

            $inputFilter->add(array(
                'name' => 'thumb_images',
                'required' => FALSE
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
} 