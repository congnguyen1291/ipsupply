<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/16/14
 * Time: 2:04 PM
 */
namespace Application\Model;

class Branches{
    public $branches_id;
    public $website_id;
    public $branches_title;
    public $address;
    public $phone;
    public $website;
    public $email;
    public $description;
    public $is_published;
    public $is_delete;
    public $date_create;
    public $latitude;
    public $longitude;
    public $cities_id;
    public $districts_id;
    public $wards_id;
    public $full_address;

    public function exchangeArray($data){
        $this->branches_id = (!empty($data['branches_id'])) ? $data['branches_id'] : 0;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : 0;
        $this->branches_title = (!empty($data['branches_title'])) ? $data['branches_title'] : '';
        $this->address = (!empty($data['address'])) ? $data['address'] : '';
        $this->phone = (!empty($data['phone'])) ? $data['phone'] : '';
        $this->website = (!empty($data['website'])) ? $data['website'] : '';
        $this->email = (!empty($data['email'])) ? $data['email'] : '';
        $this->description = (!empty($data['description'])) ? $data['description'] : '';
        $this->is_published = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d');
        $this->latitude = (!empty($data['latitude'])) ? $data['latitude'] : 0;
        $this->longitude = (!empty($data['longitude'])) ? $data['longitude'] : 0;
        $this->cities_id = (!empty($data['cities_id'])) ? $data['cities_id'] : 0;
        $this->districts_id = (!empty($data['districts_id'])) ? $data['districts_id'] : 0;
        $this->full_address = (!empty($data['full_address'])) ? $data['full_address'] : '';
    }

}