<?php

namespace Application\Model;

class Tags {
    public $tags_id;
    public $website_id;
    public $tags_name;
    public $tags_alias;
    public $date_create;

    public function exchangeArray($data)
    {
        $this->tags_id = (!empty($data['tags_id'])) ? $data['tags_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->tags_name = (!empty($data['tags_name'])) ? $data['tags_name'] : 0;
        $this->tags_alias = (!empty($data['tags_alias'])) ? $data['tags_alias'] : 0;
        $this->date_create = (!empty($data['date_create'])) ? $data['date_create'] : '';
    }
}
