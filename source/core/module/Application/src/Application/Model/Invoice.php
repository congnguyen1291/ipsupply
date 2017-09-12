<?php


namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Invoice  implements InputFilterAwareInterface{

    public $invoice_id;
    public $website_id;
    public $first_name;
    public $last_name;
    public $full_name;
    public $phone;
    public $email;
    public $type_address_delivery;
    public $country_id;
    public $address;
    public $address01;
    public $city;
    public $state;
    public $suburb;
    public $region;
    public $province;
    public $zipcode;
    public $cities_id;
    public $districts_id;
    public $wards_id;
    public $users_id;
    public $transportation_id;
    public $shipping_id;
    public $transport_type;
    public $is_free_shipping;
    public $ships_fee;
    public $invoice_title;
    public $invoice_description;
    public $ship_to_different_address;
    public $ships_first_name;
    public $ships_last_name;
    public $ships_full_name;
    public $ships_email;
    public $ships_phone;
    public $ships_country_id;
    public $ships_address;
    public $ships_address01;
    public $ships_city;
    public $ships_state;
    public $ships_suburb;
    public $ships_region;
    public $ships_province;
    public $ships_zipcode;
    public $ships_cities_id;
    public $ships_districts_id;
    public $ships_wards_id;
    public $from_currency;
    public $to_currency;
    public $rate_exchange;
    public $total_converter;
    public $is_published;
    public $is_delete;
    public $payment;
    public $delivery;
    public $date_create;
    public $date_update;
    public $promotion;
    public $total;
    public $value_ship;
    public $content;
    public $company_name;
    public $company_tax_code;
    public $company_address;
    public $email_subscription;
    public $payment_id;
    public $payment_code;
    public $pay_date;
    public $transactionid;
    public $is_view;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->invoice_id = (!empty($data['invoice_id'])) ? $data['invoice_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : null;
        $this->first_name = (!empty($data['first_name'])) ? $data['first_name'] : '';
        $this->last_name = (!empty($data['last_name'])) ? $data['last_name'] : '';
        $this->full_name = (!empty($data['full_name'])) ? $data['full_name'] : '';
        $this->email = (!empty($data['email'])) ? $data['email'] : '';
        $this->type_address_delivery = (!empty($data['type_address_delivery'])) ? $data['type_address_delivery'] : 0;
        $this->country_id = (!empty($data['country_id'])) ? $data['country_id'] : 0;
        $this->address = (!empty($data['address'])) ? $data['address'] : '';
        $this->address01 = (!empty($data['address01'])) ? $data['address01'] : '';
        $this->city = (!empty($data['city'])) ? $data['city'] : '';
        $this->state = (!empty($data['state'])) ? $data['state'] : '';
        $this->suburb = (!empty($data['suburb'])) ? $data['suburb'] : '';
        $this->region = (!empty($data['region'])) ? $data['region'] : '';
        $this->province = (!empty($data['province'])) ? $data['province'] : '';
        $this->zipcode = (!empty($data['zipcode'])) ? $data['zipcode'] : '';
        $this->cities_id = (!empty($data['cities_id'])) ? $data['cities_id'] : 0;
        $this->districts_id = (!empty($data['districts_id'])) ? $data['districts_id'] : 0;
        $this->wards_id = (!empty($data['wards_id'])) ? $data['wards_id'] : 0;
        $this->users_id = (!empty($data['users_id'])) ? $data['users_id'] : 0;
        $this->transportation_id = (!empty($data['transportation_id'])) ? $data['transportation_id'] : 0;
        $this->shipping_id = (!empty($data['shipping_id'])) ? $data['shipping_id'] : 0;
        $this->transport_type = (!empty($data['transport_type'])) ? $data['transport_type'] : 0;
        $this->is_free_shipping = (!empty($data['is_free_shipping'])) ? $data['is_free_shipping'] : 0;
        $this->ships_fee = (!empty($data['ships_fee'])) ? $data['ships_fee'] : 0;
        $this->invoice_title = (!empty($data['invoice_title'])) ? $data['invoice_title'] : ('HD'.time());
        $this->invoice_description = (!empty($data['invoice_description'])) ? $data['invoice_description'] : '';
        $this->ship_to_different_address = (!empty($data['ship_to_different_address'])) ? $data['ship_to_different_address'] : 0;
        $this->ships_first_name = (!empty($data['ships_first_name'])) ? $data['ships_first_name'] : '';
        $this->ships_last_name = (!empty($data['ships_last_name'])) ? $data['ships_last_name'] : '';
        $this->ships_full_name = (!empty($data['ships_full_name'])) ? $data['ships_full_name'] : '';
        $this->ships_email = (!empty($data['ships_email'])) ? $data['ships_email'] : '';
        $this->ships_phone = (!empty($data['ships_phone'])) ? $data['ships_phone'] : '';
        $this->ships_country_id = (!empty($data['ships_country_id'])) ? $data['ships_country_id'] : 0;
        $this->ships_address = (!empty($data['ships_address'])) ? $data['ships_address'] : '';
        $this->ships_address01 = (!empty($data['ships_address01'])) ? $data['ships_address01'] : '';
        $this->ships_city = (!empty($data['ships_city'])) ? $data['ships_city'] : '';
        $this->ships_state = (!empty($data['ships_state'])) ? $data['ships_state'] : '';
        $this->ships_suburb = (!empty($data['ships_suburb'])) ? $data['ships_suburb'] : '';
        $this->ships_region = (!empty($data['ships_region'])) ? $data['ships_region'] : '';
        $this->ships_province = (!empty($data['ships_province'])) ? $data['ships_province'] : '';
        $this->ships_zipcode = (!empty($data['ships_zipcode'])) ? $data['ships_zipcode'] : '';
        $this->ships_cities_id = (!empty($data['ships_cities_id'])) ? $data['ships_cities_id'] : 0;
        $this->ships_districts_id = (!empty($data['ships_districts_id'])) ? $data['ships_districts_id'] : 0;
        $this->ships_wards_id = (!empty($data['ships_wards_id'])) ? $data['ships_wards_id'] : 0;
        $this->from_currency = (!empty($data['from_currency'])) ? $data['from_currency'] : '';
        $this->to_currency = (!empty($data['to_currency'])) ? $data['to_currency'] : '';
        $this->rate_exchange = (!empty($data['rate_exchange'])) ? $data['rate_exchange'] : 1;
        $this->total_converter = (!empty($data['total_converter'])) ? $data['total_converter'] : 0;
        $this->is_published = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->payment = (!empty($data['payment'])) ? $data['payment'] : 'unpaid';
        $this->delivery = (!empty($data['delivery'])) ? $data['delivery'] : 'no_delivery';
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->promotion = (!empty($data['promotion'])) ? $data['promotion'] : '';
        $this->total = (!empty($data['total'])) ? $data['total'] : 0;
        $this->value_ship = (!empty($data['value_ship'])) ? $data['value_ship'] : 0;
        $this->content = (!empty($data['content'])) ? $data['content'] : '';
        $this->company_name = (!empty($data['company_name'])) ? $data['company_name'] : '';
        $this->company_tax_code = (!empty($data['company_tax_code'])) ? $data['company_tax_code'] : '';
        $this->company_address = (!empty($data['company_address'])) ? $data['company_address'] : '';
        $this->email_subscription = (!empty($data['email_subscription'])) ? $data['email_subscription'] : '';
        $this->payment_id = (!empty($data['payment_id'])) ? $data['payment_id'] : 0;
        $this->payment_code = (!empty($data['payment_code'])) ? $data['payment_code'] : 0;
        $this->pay_date = (!empty($data['pay_date'])) ? $data['pay_date'] : date('Y-m-d H:i:s');
        $this->transactionid = (!empty($data['transactionid'])) ? $data['transactionid'] : 0;
        $this->is_view = (!empty($data['is_view'])) ? $data['is_view'] : 0;
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
                'name'     => 'invoice_title',
                'required' => false,
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
                'name'     => 'email',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'email_subscription',
                'required' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}