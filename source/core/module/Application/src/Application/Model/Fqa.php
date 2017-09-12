<?php

namespace Application\Model;

class Fqa {
    public $id;
    public $products_id;
    public $id_parent;
	public $email;
	public $users_id;
	public $tieu_de;
	public $noi_dung;
	public $date_create;
	public $is_published;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->products_id = (isset($data['products_id'])) ? $data['products_id'] : null;
        $this->id_parent  = (isset($data['id_parent'])) ? $data['id_parent'] : null;
		$this->email  = (isset($data['email'])) ? $data['email'] : null;
		$this->users_id  = (isset($data['users_id'])) ? $data['users_id'] : null;
		$this->tieu_de  = (isset($data['tieu_de'])) ? $data['tieu_de'] : null;
		$this->noi_dung  = (isset($data['noi_dung'])) ? $data['noi_dung'] : null;
		$this->date_create  = (isset($data['date_create'])) ? $data['date_create'] : null;
		$this->is_published  = (isset($data['is_published'])) ? $data['is_published'] : null;
    }
}
