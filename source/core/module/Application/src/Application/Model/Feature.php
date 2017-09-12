<?php

namespace Application\Model;

class Feature {
    public $feature_id;
    public $website_id;
    public $parent_id;
    public $feature_title;
    public $feature_alias;
    public $is_published;
    public $is_value;
    public $is_delete;
    public $date_create;
    public $date_update;
    public $ordering;

    public function exchangeArray($data)
    {
        $this->feature_id = (!empty($data['feature_id'])) ? $data['feature_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->parent_id = (!empty($data['parent_id'])) ? $data['parent_id'] : 0;
        $this->feature_title                = (!empty($data['feature_title']))  ? $data['feature_title']  : '';
        $this->feature_alias             = (!empty($data['feature_alias']))  ? $data['feature_alias']  : '';
        $this->is_published          = (!empty($data['is_published']))  ? $data['is_published']  : 0;
        $this->is_value          = (!empty($data['is_value']))  ? $data['is_value']  : 0;
        $this->is_delete          = (!empty($data['is_delete']))  ? $data['is_delete']  : 0;
        $this->date_create          = (!empty($data['date_create']))  ? $data['date_create']  : date('Y-m-d');
        $this->date_update          = (!empty($data['date_update']))  ? $data['date_update']  : date('Y-m-d');
        $this->ordering          = (!empty($data['ordering']))  ? $data['ordering']  : 0;
    }
}
