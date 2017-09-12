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

class Groups implements InputFilterAwareInterface
{
    public $groups_id;
    public $website_id;
    public $groups_name;
    public $groups_description;
    public $is_published;
    public $date_create;
    public $date_update;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->groups_id   = (!empty($data['groups_id'])) ? $data['groups_id'] : NULL;
        $this->website_id   = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->groups_name = (!empty($data['groups_name'])) ? $data['groups_name'] : '';
        $this->groups_description   = (!empty($data['groups_description'])) ? $data['groups_description'] : '';
        $this->is_published   = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->date_create   = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update   = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
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
                'name' => 'groups_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'groups_name',
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