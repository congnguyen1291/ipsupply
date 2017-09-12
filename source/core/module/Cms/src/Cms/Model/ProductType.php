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

class ProductType implements InputFilterAwareInterface
{
    public $products_type_id;
    public $products_id;
    public $type_name;
    public $price;
    public $price_sale;
    public $quantity;
    public $is_available;
    public $thumb_image;
    public $is_default;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->products_type_id   = (!empty($data['products_type_id'])) ? $data['products_type_id'] : NULL;
        $this->products_id   = (!empty($data['products_id'])) ? $data['products_id'] : NULL;
        $this->type_name = (!empty($data['type_name'])) ? $data['type_name'] : '';
        $this->price   = (!empty($data['price'])) ? $data['price'] : 0;
        $this->price_sale   = (!empty($data['price_sale'])) ? $data['price_sale'] : 0;
        $this->quantity   = (!empty($data['quantity'])) ? $data['quantity'] : 0;
        $this->is_available   = (!empty($data['is_available'])) ? $data['is_available'] : 0;
        $this->thumb_image   = (!empty($data['thumb_image'])) ? $data['thumb_image'] : '';
        $this->is_default   = (!empty($data['is_default'])) ? $data['is_default'] : 0;
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
                'name' => 'products_type_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'type_name',
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