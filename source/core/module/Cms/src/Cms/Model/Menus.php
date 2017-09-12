<?php

namespace Cms\Model;

class Menus {
    public $menus_id;
    public $website_id;
    public $parent_id;
    public $menus_reference_id;
    public $menus_reference_name;
    public $menus_reference_url;
    public $menus_description;
    public $menus_name;
    public $menus_alias;
    public $menus_type;
    public $menus_location;
    public $is_root;
    public $is_published;

    public function exchangeArray($data)
    {
        $this->menus_id = (!empty($data['menus_id'])) ? $data['menus_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->parent_id = (!empty($data['parent_id'])) ? $data['parent_id'] : 0;
        $this->menus_reference_id = (!empty($data['menus_reference_id'])) ? $data['menus_reference_id'] : 0;
        $this->menus_reference_name = (!empty($data['menus_reference_name'])) ? $data['menus_reference_name'] : '';
        $this->menus_reference_url = (!empty($data['menus_reference_url'])) ? $data['menus_reference_url'] : '';
        $this->menus_description = (!empty($data['menus_description'])) ? $data['menus_description'] : '';
        $this->menus_name = (!empty($data['menus_name'])) ? $data['menus_name'] : '';
        $this->menus_alias = (!empty($data['menus_alias'])) ? $data['menus_alias'] : '';
        $this->menus_type                = (!empty($data['menus_type']))  ? $data['menus_type']  : 'frontpage';
        $this->menus_location             = (!empty($data['menus_location']))  ? $data['menus_location']  : '';
        $this->is_root          = (!empty($data['is_root']))  ? $data['is_root']  : 0;
        $this->is_published          = (!empty($data['is_published']))  ? $data['is_published']  : 0;
    }
}
