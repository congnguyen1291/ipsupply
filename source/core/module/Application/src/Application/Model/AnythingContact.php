<?php
namespace Application\Model;

class AnythingContact
{
    /*must*/
    public $website_id;
	public $email;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $fullname;
    public $title;
    public $type;

    /*not must*/
    public $telephone;
    public $description;
    public $product_id;
    public $product_name;
    public $link;

    public $utm_source;
    public $utm_campaign;
    public $utm_medium;

    public $users_id;
    public $service_id;
    public $total;
    public $payment_id;
    public $from_currency;
    public $to_currency;
    public $rate_exchange;
    public $total_converter;
    public $payment_code;
    public $payment;

    public $date_send;
    public $readed;
    public $reply;

    public function bin( $datas )
    {
        foreach ($datas as $key => $data) {
            if( array_key_exists($key, $this) ){
                $this->{$key} = $data;
            }
        }
        return $this;
    }

    public function exchangeArray($data)
    {
        $this->website_id               = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->email                    = (!empty($data['email'])) ? $data['email'] : '';
        $this->first_name               = (!empty($data['first_name'])) ? $data['first_name'] : '';
        $this->middle_name              = (!empty($data['middle_name'])) ? $data['middle_name'] : '';
        $this->last_name                = (!empty($data['last_name'])) ? $data['last_name'] : '';
        $this->fullname                 = (!empty($data['fullname'])) ? $data['fullname'] : '';
        $this->title                    = (!empty($data['title'])) ? $data['title'] : '';
        $this->type                     = (!empty($data['type'])) ? $data['type'] : 'contact';
        
        $this->telephone                = (!empty($data['telephone'])) ? $data['telephone'] : '';
        $this->description              = (!empty($data['description'])) ? $data['description'] : '';
        $this->product_id               = (!empty($data['product_id'])) ? $data['product_id'] : '';
        $this->product_name             = (!empty($data['product_name'])) ? $data['product_name'] : '';
        $this->link                     = (!empty($data['link'])) ? $data['link'] : '';
        $this->utm_source               = (!empty($data['utm_source'])) ? $data['utm_source'] : '';
        $this->utm_campaign             = (!empty($data['utm_campaign'])) ? $data['utm_campaign'] : '';
        $this->utm_medium               = (!empty($data['utm_medium'])) ? $data['utm_medium'] : '';
        $this->total                    = (!empty($data['total'])) ? $data['total'] : 0;
        $this->payment_id               = (!empty($data['payment_id'])) ? $data['payment_id'] : 0;
        $this->from_currency            = (!empty($data['from_currency'])) ? $data['from_currency'] : '';
        $this->to_currency              = (!empty($data['to_currency'])) ? $data['to_currency'] : '';
        $this->rate_exchange            = (!empty($data['rate_exchange'])) ? $data['rate_exchange'] : 1;
        $this->total_converter          = (!empty($data['total_converter'])) ? $data['total_converter'] : 0;
        $this->payment_code             = (!empty($data['payment_code'])) ? $data['payment_code'] : '';
        $this->payment                  = (!empty($data['payment'])) ? $data['payment'] : '';
        $this->service_id               = (!empty($data['service_id'])) ? $data['service_id'] : 0;
        $this->date_send                = (!empty($data['date_send'])) ? $data['date_send'] : '';
        $this->readed                   = (!empty($data['readed'])) ? $data['readed'] : '';
        $this->reply                    = (!empty($data['reply'])) ? $data['reply'] : '';
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
