<?php
namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Shipping
{
    public $shipping_id;
    public $website_id;
    public $group_regions_id;
    public $shipping_title;
    public $shipping_type;
    public $shipping_from;
    public $shipping_to;
    public $shipping_value;
    public $shipping_fast_value;
    public $shipping_time;
    public $is_published;
    protected $inputFilter;
    public function exchangeArray($data)
    {
        $this->shipping_id = (!empty($data['shipping_id'])) ? $data['shipping_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : $_SESSION['CMSMEMBER']['website_id'];
        $this->group_regions_id = (!empty($data['group_regions_id'])) ? $data['group_regions_id'] : 0;
        $this->shipping_title = (!empty($data['shipping_title'])) ? $data['shipping_title'] : '';
        $this->shipping_type = (!empty($data['shipping_type'])) ? $data['shipping_type'] : 0;
        $this->shipping_from = (!empty($data['shipping_from'])) ? $data['shipping_from'] : 0;
        $this->shipping_to = (!empty($data['shipping_to'])) ? $data['shipping_to'] : 0;
        $this->shipping_value = (!empty($data['shipping_value'])) ? $data['shipping_value'] : 0;
        $this->shipping_fast_value = (!empty($data['shipping_fast_value'])) ? $data['shipping_fast_value'] : 0;
        $this->shipping_time = (!empty($data['shipping_time'])) ? $data['shipping_time'] : 0;
        $this->is_published = (!empty($data['is_published'])) ? $data['is_published'] : 0;
    }
}