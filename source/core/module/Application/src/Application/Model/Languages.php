<?php

namespace Application\Model;

class Languages {
    
    public $languages_id;
    public $languages_name;
    public $languages_file;
    public $is_published;
    public $date_create;
    public $date_update;

    public function exchangeArray($data)
    {
        $this->languages_id     = (!empty($data['languages_id'])) ? $data['languages_id'] : null;
        $this->languages_name = (!empty($data['languages_name'])) ? $data['languages_name'] : '';
        $this->languages_file  = (!empty($data['languages_file'])) ? $data['languages_file'] : '';
        $this->is_published = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update = (!empty($data['date_update'])) ? $data['date_update'] : date('Y-m-d H:i:s');
    }

}