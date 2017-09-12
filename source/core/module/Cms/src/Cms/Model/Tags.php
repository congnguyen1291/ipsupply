<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 10:49 AM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Tags implements InputFilterAwareInterface
{
    public $tags_id;
    public $website_id;
    public $tags_name;
    public $tags_alias;
    public $date_create;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->tags_id   = (!empty($data['tags_id'])) ? $data['tags_id'] : NULL;
        $this->website_id   = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->tags_name = (!empty($data['tags_name'])) ? $data['tags_name'] : NULL;
        $this->tags_alias   = (!empty($data['tags_alias'])) ? $data['tags_alias'] : '';
        $this->date_create   = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d');
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
                'name' => 'tags_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'tags_name',
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