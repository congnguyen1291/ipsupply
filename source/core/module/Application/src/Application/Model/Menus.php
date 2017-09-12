<?php

namespace Application\Model;

class Menus {
    public $menus_id;
    public $website_id;
    public $parent_id;
    public $menus_reference;
    public $menus_name;
    public $menus_alias;
    public $menus_type;
    public $menus_location;
    public $is_root;

    public function exchangeArray($data)
    {
        $this->menus_id = (!empty($data['menus_id'])) ? $data['menus_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->parent_id = (!empty($data['parent_id'])) ? $data['parent_id'] : 0;
        $this->menus_reference = (!empty($data['menus_reference'])) ? $data['menus_reference'] : 0;
        $this->menus_name = (!empty($data['menus_name'])) ? $data['menus_name'] : '';
        $this->menus_alias = (!empty($data['menus_alias'])) ? $data['menus_alias'] : '';
        $this->menus_type                = (!empty($data['menus_type']))  ? $data['menus_type']  : 'frontpage';
        $this->menus_location             = (!empty($data['menus_location']))  ? $data['menus_location']  : '';
        $this->is_root          = (!empty($data['is_root']))  ? $data['is_root']  : 0;
    }
}
