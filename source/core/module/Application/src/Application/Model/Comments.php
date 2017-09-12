<?php

namespace Application\Model;

class Comments {
    public $id;
    public $content;
    public $member;
	public $product;
	public $parent;
	public $type;
	public $number;
	public $date_crerate;
	public $status;
	public $rating;

    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->content = (isset($data['content'])) ? $data['content'] : null;
        $this->member  = (isset($data['member'])) ? $data['member'] : null;
		$this->product  = (isset($data['product'])) ? $data['product'] : null;
		$this->parent  = (isset($data['parent'])) ? $data['parent'] : null;
		$this->type  = (isset($data['type'])) ? $data['type'] : null;
		$this->number  = (isset($data['number'])) ? $data['number'] : null;
		$this->datecrerate  = (isset($data['datecrerate'])) ? $data['datecrerate'] : null;
		$this->status  = (isset($data['status'])) ? $data['status'] : null;
		$this->rating  = (isset($data['rating'])) ? $data['rating'] : null;
    }
}
