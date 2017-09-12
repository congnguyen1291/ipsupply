<?php

namespace Application\Model;

class RegisterMailProduct {
    public $register_mail_id;
    public $products_id;
    public $name;
    public $phone;
    public $email;
	public $date_create;

    public function exchangeArray($data)
    {
        $this->register_mail_id     = (isset($data['register_mail_id'])) ? $data['register_mail_id'] : null;
        $this->products_id = (isset($data['products_id'])) ? $data['products_id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->phone = (isset($data['phone'])) ? $data['phone'] : null;
        $this->email  = (isset($data['email'])) ? $data['email'] : null;
		$this->date_create  = (isset($data['date_create'])) ? $data['date_create'] : null;
    }
}
