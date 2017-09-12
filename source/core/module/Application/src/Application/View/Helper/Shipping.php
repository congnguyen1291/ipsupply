<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Shipping extends App
{

    public function getShipping($shipping_id)
    {
        $shipping = $this->getModelTable('ShippingTable')->getShipping($shipping_id);
        return $shipping;
    }
}
