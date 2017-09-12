<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 11:54 AM
 */
namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Coupons implements InputFilterAwareInterface
{
    public $coupons_id;
    public $website_id;
    public $coupons_code;
    public $coupons_type;
    public $coupons_max_use;
    public $start_date;
    public $expire_date;
    public $is_published;
    public $min_price_use;
    public $max_price_use;
    public $coupon_price;
    public $coupon_percent;
    protected $inputFilter;
    public function exchangeArray($data)
    {
        $this->coupons_id = (!empty($data['coupons_id'])) ? $data['coupons_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->coupons_code = (!empty($data['coupons_code'])) ? $data['coupons_code'] : NULL;
        $this->coupons_type = (!empty($data['coupons_type'])) ? $data['coupons_type'] : 0;
        $this->coupons_max_use = (!empty($data['coupons_max_use'])) ? $data['coupons_max_use'] : 0;
        $this->start_date = (!empty($data['start_date'])) ? $data['start_date'] : date('Y-m-d H:i:s');
        $this->expire_date = (!empty($data['expire_date'])) ? $data['expire_date'] : date('Y-m-d H:i:s');
        $this->is_published = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->min_price_use = (!empty($data['min_price_use'])) ? $data['min_price_use'] : 0;
        $this->max_price_use = (!empty($data['max_price_use'])) ? $data['max_price_use'] : 0;
        $this->coupon_price = (!empty($data['coupon_price'])) ? $data['coupon_price'] : 0;
        $this->coupon_percent = (!empty($data['coupon_percent'])) ? $data['coupon_percent'] : 0;
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
                'name' => 'coupons_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'coupons_code',
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
                'name' => 'start_date',
                'required' => TRUE,
            ));
            $inputFilter->add(array(
                'name' => 'expire_date',
                'required' => TRUE,
            ));
            $inputFilter->add(array(
                'name' => 'min_price_use',
                'required' => TRUE,
            ));
            $inputFilter->add(array(
                'name' => 'max_price_use',
                'required' => TRUE,
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}