<?php

namespace Application\Model;

class TraGop{
    public $tra_gop_id;
    public $products_id;
    public $cities_id;
    public $total_month;
    public $banks_id;
	public $full_name;
	public $phone;
	public $users_id;
	public $date_create;
	public $total_pay;
	public $first_pay;
	public $month_pay;

    public function exchangeArray($data)
    {
        $this->tra_gop_id     = (isset($data['tra_gop_id'])) ? $data['tra_gop_id'] : null;
        $this->products_id = (isset($data['products_id'])) ? $data['products_id'] : null;
        $this->cities_id  = (isset($data['cities_id'])) ? $data['cities_id'] : null;
        $this->total_month  = (isset($data['total_month'])) ? $data['total_month'] : null;
        $this->banks_id  = (isset($data['banks_id'])) ? $data['banks_id'] : null;
		$this->full_name  = (isset($data['full_name'])) ? $data['full_name'] : null;
		$this->phone  = (isset($data['phone'])) ? $data['phone'] : null;
		$this->users_id  = (isset($data['users_id'])) ? $data['users_id'] : null;
		$this->date_create  = (isset($data['date_create'])) ? $data['date_create'] : null;
		$this->total_pay  = (isset($data['total_pay'])) ? $data['total_pay'] : null;
		$this->first_pay  = (isset($data['first_pay'])) ? $data['first_pay'] : null;
		$this->month_pay  = (isset($data['month_pay'])) ? $data['month_pay'] : null;
    }
}
