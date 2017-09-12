<?php

namespace Application\Model;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class WholesaleProducts{

    public $wholesale_products_id;
    public $products_id;
    public $products_type_id;
    public $wholesale_id;
    public $is_published;
    public $is_delete;
    public $promotion;
    public $price;
    public $price_sale;
    public $quantity;
    public $total_price_extention;
    public $vat;
    public $total;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->wholesale_products_id     = (isset($wholesale_products_id['wholesale_id'])) ? $data['wholesale_products_id'] : null;
        $this->products_id = (isset($data['products_id'])) ? $data['products_id'] : null;
        $this->products_type_id  = (isset($data['products_type_id'])) ? $data['products_type_id'] : 0;
        $this->wholesale_id  = (isset($data['wholesale_id'])) ? $data['wholesale_id'] : null;
        $this->is_published  = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete  = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->promotion  = (!empty($data['promotion'])) ? $data['promotion'] : '';
        $this->price  = (!empty($data['price'])) ? $data['price'] : 0;
        $this->price_sale  = (!empty($data['price_sale'])) ? $data['price_sale'] : 0;
        $this->quantity  = (!empty($data['quantity'])) ? $data['quantity'] : 0;
        $this->total_price_extention  = (!empty($data['total_price_extention'])) ? $data['total_price_extention'] : 0;
        $this->vat  = (!empty($data['vat'])) ? $data['vat'] : 0;
        $this->total = (!empty($data['total'])) ? $data['total'] : 0;
    }
    
}
