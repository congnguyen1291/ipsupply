<?php

namespace Application\Model;

class Manufacturers {
    public $manufacturers_id;
    public $website_id;
    public $manufacturers_name;
    public $is_published;
    public $is_delete;
    public $date_create;
    public $date_update;
    public $ordering;
    public $is_value;
    public $thumb_image;
    public $description;

    public function exchangeArray($data)
    {
        $this->manufacturers_id     = (isset($data['manufacturers_id'])) ? $data['manufacturers_id'] : null;
        $this->website_id     = (isset($data['website_id'])) ? $data['website_id'] : null;
        $this->manufacturers_name = (isset($data['manufacturers_name'])) ? $data['manufacturers_name'] : null;
        $this->is_published  = (isset($data['is_published'])) ? $data['is_published'] : null;
        $this->is_delete  = (isset($data['is_delete'])) ? $data['is_delete'] : null;
        $this->date_create  = (isset($data['date_create'])) ? $data['date_create'] : null;
        $this->date_update  = (isset($data['date_update'])) ? $data['date_update'] : null;
        $this->ordering  = (isset($data['ordering'])) ? $data['ordering'] : null;
        $this->is_value  = (isset($data['is_value'])) ? $data['is_value'] : null;
        $this->thumb_image  = (isset($data['thumb_image'])) ? $data['thumb_image'] : null;
        $this->description  = (isset($data['description'])) ? $data['description'] : null;
    }
}
