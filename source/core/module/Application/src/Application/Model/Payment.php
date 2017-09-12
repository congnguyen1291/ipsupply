<?php

namespace Application\Model;

class Payment {
    public $payment_id;
    public $website_id;
    public $payment_name;
    public $payment_description;
    public $code;
    public $sale_account;
    public $image;
    public $is_published;
    public $is_delete;
    public $date_create;
    public $date_update;

    public function exchangeArray($data)
    {
        $this->payment_id              = (!empty($data['payment_id'])) ? $data['payment_id'] : NULL;
        $this->website_id              = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->payment_name            = (!empty($data['payment_name'])) ? $data['payment_name'] : '';
        $this->payment_description     = (!empty($data['payment_description'])) ? $data['payment_description'] : '';
        $this->code                     = (!empty($data['code'])) ? $data['code'] : 'HOME';
        $this->sale_account             = (!empty($data['sale_account'])) ? $data['sale_account'] : 0;
        $this->image                    = (!empty($data['image'])) ? $data['image'] : '';
        $this->is_published           = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete           = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->date_create           = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d');
        $this->date_update           = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d');
    }
}
