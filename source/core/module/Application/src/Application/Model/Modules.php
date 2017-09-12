<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 11:54 AM
 */
namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Modules implements InputFilterAwareInterface
{
    public $module_id;
    public $module_name;
    public $module_description;
    public $price;
    public $is_default;
    public $date_create;
    public $date_update;
    protected $inputFilter;
    public function exchangeArray($data)
    {
        $this->module_id = (!empty($data['module_id'])) ? $data['module_id'] : NULL;
        $this->module_name = (!empty($data['module_name'])) ? $data['module_name'] : NULL;
        $this->module_description = (!empty($data['module_description'])) ? $data['module_description'] : NULL;
        $this->price = (!empty($data['price'])) ? $data['price'] : 0;
        $this->is_default = (!empty($data['is_default'])) ? $data['is_default'] : date('Y-m-d H:i:s');
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
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
                'name' => 'module_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'module_name',
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