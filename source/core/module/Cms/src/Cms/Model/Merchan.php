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

class Merchan  implements InputFilterAwareInterface
{
    public $merchan_id;
    public $website_id;
    public $merchan_type;
    public $merchan_name;
    public $merchan_alias;
    public $merchan_phone;
    public $merchan_email;
    public $merchan_fax;
    public $merchan_avatar;
    public $merchan_note;
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
        $this->merchan_id   = (!empty($data['merchan_id'])) ? $data['merchan_id'] : NULL;
        $this->website_id   = (!empty($data['website_id'])) ? $data['website_id'] : 0;
        $this->merchan_type = (!empty($data['merchan_type'])) ? $data['merchan_type'] : 0;
        $this->merchan_name   = (!empty($data['merchan_name'])) ? $data['merchan_name'] : '';
        $this->merchan_alias   = (!empty($data['merchan_alias'])) ? $data['merchan_alias'] : '';
        $this->merchan_phone   = (!empty($data['merchan_phone'])) ? $data['merchan_phone'] : '';
        $this->merchan_email   = (!empty($data['merchan_email'])) ? $data['merchan_email'] : '';
        $this->merchan_fax   = (!empty($data['merchan_fax'])) ? $data['merchan_fax'] : '';
        $this->merchan_avatar   = (!empty($data['merchan_avatar'])) ? $data['merchan_avatar'] : '';
        $this->merchan_note   = (!empty($data['merchan_note'])) ? $data['merchan_note'] : '';
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
                'name'     => 'merchan_name',
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