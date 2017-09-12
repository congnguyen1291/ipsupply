<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/10/14
 * Time: 11:25 AM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class User  implements InputFilterAwareInterface
{
    public $users_id;
    public $website_id;
    public $parent_id;
    public $create_by;
    public $user_name;
    public $password;
    public $first_name;
    public $last_name;
    public $full_name;
    public $users_alias;
    public $birthday;
    public $phone;
    public $address;
    public $address_full;
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
    public $is_delete;
    public $date_create;
    public $date_update;
    public $type;
    public $is_administrator;
    public $groups_id;
    public $merchant_id;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->users_id     = (!empty($data['users_id'])) ? $data['users_id'] : null;
        $this->website_id     = (!empty($data['website_id'])) ? $data['website_id'] : 0;
        $this->parent_id     = (!empty($data['parent_id'])) ? $data['parent_id'] : 0;
        $this->create_by     = (!empty($data['create_by'])) ? $data['create_by'] : 0;
        $this->user_name     = (!empty($data['user_name'])) ? $data['user_name'] : null;
        $this->password = (!empty($data['password'])) ? $data['password'] : null;
        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : '';
        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : '';
        $this->full_name = (!empty($data['full_name'])) ? $data['full_name'] : null;
        $this->users_alias = (!empty($data['users_alias'])) ? $data['users_alias'] : null;
        $this->birthday = (!empty($data['birthday'])) ? ($data['birthday'].':00') : null;
        $this->phone = (!empty($data['phone'])) ? $data['phone'] : null;
        $this->address = (!empty($data['address'])) ? $data['address'] : null;
        $this->address_full = (!empty($data['address_full'])) ? $data['address_full'] : null;
        $this->address01 = (!empty($data['address01'])) ? $data['address01'] : null;
        $this->zipcode = (!empty($data['zipcode'])) ? $data['zipcode'] : null;
        $this->longitude = (!empty($data['longitude'])) ? $data['longitude'] : null;
        $this->latitude = (!empty($data['latitude'])) ? $data['latitude'] : null;
        $this->country_id = (!empty($data['country_id'])) ? $data['country_id'] : null;
        $this->city = (!empty($data['city'])) ? $data['city'] : null;
        $this->state = (!empty($data['state'])) ? $data['state'] : null;
        $this->suburb = (!empty($data['suburb'])) ? $data['suburb'] : null;
        $this->region = (!empty($data['region'])) ? $data['region'] : null;
        $this->province = (!empty($data['province'])) ? $data['province'] : null;
        $this->cities_id = (!empty($data['cities_id'])) ? $data['cities_id'] : null;
        $this->cities_id = (!empty($data['cities_id'])) ? $data['cities_id'] : null;
        $this->districts_id = (!empty($data['districts_id'])) ? $data['districts_id'] : null;
        $this->wards_id = (!empty($data['wards_id'])) ? $data['wards_id'] : null;
        $this->is_published = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete = 0;
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
        $this->type = (!empty($data['type'])) ? $data['type'] : 'user';
        $this->is_administrator = (!empty($data['is_administrator'])) ? $data['is_administrator'] : 0;
        $this->groups_id = (!empty($data['groups_id'])) ? $data['groups_id'] : NULL;
        $this->merchant_id = (!empty($data['merchant_id'])) ? $data['merchant_id'] : 0;
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
                'name'     => 'users_id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'user_name',
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

            $inputFilter->add(array(
                'name'     => 'full_name',
                'required' => FALSE,
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
                'name'     => 'users_alias',
                'required' => FALSE,
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
                'name'     => 'is_administrator',
                'required' => FALSE,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}