<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 3:17 PM
 */

namespace Application\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class User implements InputFilterAwareInterface{
    public $users_id;
    public $website_id;
    public $user_name;
    public $password;
    public $full_name;
    public $users_alias;
    public $gender;
    public $birthday;
    public $phone;
    public $country_id;
    public $address;
    public $address01;
    public $address_full;
    public $city;
    public $state;
    public $suburb;
    public $region;
    public $province;
    public $zipcode;
    public $cities_id;
    public $districts_id;
    public $wards_id;
    public $is_published;
    public $is_delete;
    public $date_create;
    public $date_update;
    public $type;
    public $is_administrator;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->users_id     = (isset($data['users_id'])) ? $data['users_id'] : null;
        $this->website_id     = (isset($data['website_id'])) ? $data['website_id'] : null;
        $this->user_name     = (isset($data['user_name'])) ? $data['user_name'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->full_name = (isset($data['full_name'])) ? $data['full_name'] : null;
        $this->users_alias = (isset($data['users_alias'])) ? $data['users_alias'] : null;
        $this->gender = (isset($data['gender'])) ? $data['gender'] : 0;
        $this->birthday = (isset($data['birthday'])) ? $data['birthday'] : null;
        $this->phone = (isset($data['phone'])) ? $data['phone'] : null;
        $this->country_id = (isset($data['country_id'])) ? $data['country_id'] : 0;
        $this->address = (isset($data['address'])) ? $data['address'] : '';
        $this->address_full = (isset($data['address_full'])) ? $data['address_full'] : '';
        $this->address01 = (isset($data['address01'])) ? $data['address01'] : '';
        $this->city = (isset($data['city'])) ? $data['city'] : '';
        $this->state = (isset($data['state'])) ? $data['state'] : '';
        $this->suburb = (isset($data['suburb'])) ? $data['suburb'] : '';
        $this->region = (isset($data['region'])) ? $data['region'] : '';
        $this->province = (isset($data['province'])) ? $data['province'] : '';
        $this->cities_id = (isset($data['cities_id'])) ? $data['cities_id'] : 0;
        $this->zipcode = (isset($data['zipcode'])) ? $data['zipcode'] : 0;
        $this->districts_id = (isset($data['districts_id'])) ? $data['districts_id'] : 0;
        $this->wards_id = (isset($data['wards_id'])) ? $data['wards_id'] : 0;
        
        $this->is_published = 1;
        $this->is_delete = 0;
        $this->date_create = (isset($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update = (isset($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
        $this->type = (isset($data['type']) && !empty($data['type'])) ? $data['type'] : 'user'; 
        $this->is_administrator = (!empty($data['is_administrator'])) ? $data['is_administrator'] : 0;
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
                'name'     => 'cities_id',
                'required' => false,
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
                'name'     => 'password',
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
                            'min'      => 1,
                            'max'      => 250,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'users_alias',
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
                            'min'      => 1,
                            'max'      => 250,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
    public function getInputFilterEditProfile()
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
    				'name'     => 'full_name',
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
    										'min'      => 1,
    										'max'      => 250,
    								),
    						),
    				),
    		));
    		$inputFilter->add(array(
    				'name'     => 'users_alias',
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
    		$this->inputFilter = $inputFilter;
    	}
    	return $this->inputFilter;
    }
    
    public function getInputFilterEditPassword()
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
    				'name'     => 'full_name',
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
    				'name'     => 'users_alias',
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
    		$this->inputFilter = $inputFilter;
    	}
    	return $this->inputFilter;
    }
    
} 
