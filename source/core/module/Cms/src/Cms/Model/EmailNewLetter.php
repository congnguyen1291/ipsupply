<?php

namespace Cms\Model;

class EmailNewLetter {
    public $email_id;
    public $email;
	public $date_create;

    public function exchangeArray($data)
    {
        $this->email_id     = (isset($data['email_id'])) ? $data['email_id'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
		$this->date_create  = (isset($data['date_create'])) ? $data['date_create'] : null;
    }
}
