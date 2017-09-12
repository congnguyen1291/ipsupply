<?php

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Branches implements InputFilterAwareInterface{
    public $branches_id;
    public $website_id;
    public $branches_title;
    public $phone;
    public $website;
    public $email;
    public $description;
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
    public $is_default;
    public $is_published;
    public $ordering;
    public $is_delete;
    public $date_create;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->branches_id     = (!empty($data['branches_id'])) ? $data['branches_id'] : null;
        $this->website_id     = (!empty($data['website_id'])) ? $data['website_id'] : null;
        $this->branches_title = (!empty($data['branches_title'])) ? $data['branches_title'] : null;
        $this->phone  = (!empty($data['phone'])) ? $data['phone'] : null;
        $this->website = (!empty($data['website'])) ? $data['website'] : NULL;
        $this->email = (!empty($data['email'])) ? $data['email'] : NULL;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
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
        $this->is_published = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_default = (!empty($data['is_default'])) ? $data['is_default'] : 0;
        $this->is_delete = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->ordering = (!empty($data['ordering'])) ? $data['ordering'] : 0;
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
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
                'name'     => 'branches_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'branches_title',
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
                            'max'      => 250,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'phone',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 10,
                            'max'      => 40,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'website',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 250,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'email',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 250,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'description',
                'required' => false
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}