<?php

namespace Cms\Model;


class MultiLevel{
    public $parent_id;
    public $is_published;
    public $is_delete;
    public $date_create;
    public $date_update;
    public $ordering;
    protected $inputFilter;

    protected function exchange($data){
        $this->parent_id = (!empty($data['parent_id'])) ? $data['parent_id'] : 0;
        $this->is_published = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
        $this->ordering = (!empty($data['ordering'])) ? $data['ordering'] : 0;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}