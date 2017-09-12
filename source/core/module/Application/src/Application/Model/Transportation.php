<?php

namespace Application\Model;

class Transportation {
    public $transportation_id;
    public $website_id;
    public $transportation_type;
    public $transportation_title;
    public $transportation_description;
    public $price;
    public $is_published;
    public $is_delete;

    public function exchangeArray($data)
    {
        $this->transportation_id              = (!empty($data['transportation_id'])) ? $data['transportation_id'] : NULL;
        $this->website_id              = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->transportation_type            = (!empty($data['transportation_type'])) ? $data['transportation_type'] : 0;
        $this->transportation_title            = (!empty($data['transportation_title'])) ? $data['transportation_title'] : '';
        $this->transportation_description     = (!empty($data['transportation_description'])) ? $data['transportation_description'] : '';
        $this->price                     = (!empty($data['price'])) ? $data['price'] : 0;
        $this->is_published           = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete           = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
    }
}
