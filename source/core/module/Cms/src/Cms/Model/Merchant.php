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

class Merchant  implements InputFilterAwareInterface
{
    public $merchant_id;
    public $website_id;
    public $merchant_type;
    public $merchant_name;
    public $merchant_alias;
    public $merchant_phone;
    public $merchant_email;
    public $merchant_fax;
    public $merchant_avatar;
    public $merchant_note;
    public $address;
    public $address01;
    public $zipcode;
    public $longitude;
    public $latitude;
    public $country_id;
    public $city;
    public $state;
    public $suburb;
    public $region;
    public $province;
    public $cities_id;
    public $districts_id;
    public $wards_id;
    public $is_published;
    public $date_create;
    public $date_update;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->merchant_id   = (!empty($data['merchant_id'])) ? $data['merchant_id'] : NULL;
        $this->website_id   = (!empty($data['website_id'])) ? $data['website_id'] : 0;
        $this->merchant_type = (!empty($data['merchant_type'])) ? $data['merchant_type'] : 0;
        $this->merchant_name   = (!empty($data['merchant_name'])) ? $data['merchant_name'] : '';
        $this->merchant_alias   = (!empty($data['merchant_alias'])) ? $data['merchant_alias'] : '';
        $this->merchant_phone   = (!empty($data['merchant_phone'])) ? $data['merchant_phone'] : '';
        $this->merchant_email   = (!empty($data['merchant_email'])) ? $data['merchant_email'] : '';
        $this->merchant_fax   = (!empty($data['merchant_fax'])) ? $data['merchant_fax'] : '';
        $this->merchant_avatar   = (!empty($data['merchant_avatar'])) ? $data['merchant_avatar'] : '';
        $this->merchant_note   = (!empty($data['merchant_note'])) ? $data['merchant_note'] : '';
        $this->address   = (!empty($data['address'])) ? $data['address'] : '';
        $this->address01   = (!empty($data['address01'])) ? $data['address01'] : '';
        $this->zipcode   = (!empty($data['zipcode'])) ? $data['zipcode'] : '';
        $this->longitude   = (!empty($data['longitude'])) ? $data['longitude'] : '';
        $this->latitude   = (!empty($data['latitude'])) ? $data['latitude'] : '';
        $this->country_id   = (!empty($data['country_id'])) ? $data['country_id'] : 0;
        $this->city   = (!empty($data['city'])) ? $data['city'] : '';
        $this->state   = (!empty($data['state'])) ? $data['state'] : '';
        $this->suburb   = (!empty($data['suburb'])) ? $data['suburb'] : '';
        $this->region   = (!empty($data['region'])) ? $data['region'] : '';
        $this->province   = (!empty($data['province'])) ? $data['province'] : '';
        $this->cities_id   = (!empty($data['cities_id'])) ? $data['cities_id'] : 0;
        $this->districts_id   = (!empty($data['districts_id'])) ? $data['districts_id'] : 0;
        $this->wards_id   = (!empty($data['wards_id'])) ? $data['wards_id'] : 0;
        $this->is_published   = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->date_create   = (!empty($data['date_create'])) ? $data['date_create'] : '';
        $this->date_update   = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:m:s');
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
                'name'     => 'merchant_name',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}