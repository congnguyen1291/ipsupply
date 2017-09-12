<?php
namespace Application\Model;

class ShippingWard
{
    public $shipping_ward_id;
    public $website_id;
    public $districts_id;
    public $shipping_id;
    public $shipping_fixed_value;
    public $no_shipping;
    protected $inputFilter;
    public function exchangeArray($data)
    {
        $this->shipping_ward_id = (!empty($data['shipping_ward_id'])) ? $data['shipping_ward_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : $_SESSION['CMSMEMBER']['website_id'];
        $this->districts_id = (!empty($data['districts_id'])) ? $data['districts_id'] : 0;
        $this->shipping_id = (!empty($data['shipping_id'])) ? $data['shipping_id'] : 0;
        $this->shipping_fixed_value = (!empty($data['shipping_fixed_value'])) ? $data['shipping_fixed_value'] : 0;
        $this->shipping_fixed_time = (!empty($data['shipping_fixed_time'])) ? $data['shipping_fixed_time'] : '';
        $this->no_shipping = (!empty($data['no_shipping'])) ? $data['no_shipping'] : 0;
    }
}