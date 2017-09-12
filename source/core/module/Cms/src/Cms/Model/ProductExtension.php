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

class ProductExtension implements InputFilterAwareInterface
{
    public $id;
    public $ext_id;
    public $products_id;
    public $ext_name;
    public $ext_require;
    public $price;
    public $ext_description;
    public $quantity;
    public $is_available;
    public $is_always;
    public $type;
    public $refer_product_id;
    public $icons;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id   = (!empty($data['id'])) ? $data['id'] : NULL;
        $this->ext_id   = (!empty($data['ext_id'])) ? $data['ext_id'] : NULL;
        $this->products_id = (!empty($data['products_id'])) ? $data['products_id'] : NULL;
        $this->ext_name   = (!empty($data['ext_name'])) ? $data['ext_name'] : '';
        $this->ext_require   = (!empty($data['ext_require'])) ? $data['ext_require'] : 0;
        $this->price   = (!empty($data['price'])) ? $data['price'] : 0;
        $this->ext_description   = (!empty($data['ext_description'])) ? $data['ext_description'] : '';
        $this->quantity   = (!empty($data['quantity'])) ? $data['quantity'] : 0;
        $this->is_available   = (!empty($data['is_available'])) ? $data['is_available'] : 0;
        $this->is_always   = (!empty($data['is_always'])) ? $data['is_always'] : 0;
        $this->type   = (!empty($data['type'])) ? $data['type'] : 'default';
        $this->refer_product_id   = (!empty($data['refer_product_id'])) ? $data['refer_product_id'] : 0;
        $this->icons   = (!empty($data['icons'])) ? $data['icons'] : 0;
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
                'name' => 'ext_name',
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