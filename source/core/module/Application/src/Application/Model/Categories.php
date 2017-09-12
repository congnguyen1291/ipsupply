<?php

namespace Application\Model;

class Categories {
    public $categories_id;
    public $categories_title;
    public $categories_alias;
    public $seo_keywords;
    public $seo_description;
    public $seo_title;

    public function exchangeArray($data)
    {
        $this->categories_id = (!empty($data['categories_id'])) ? $data['categories_id'] : NULL;
        $this->categories_title = (!empty($data['categories_title'])) ? $data['categories_title'] : NULL;
        $this->categories_alias = (!empty($data['categories_alias'])) ? $data['categories_alias'] : NULL;
        $this->seo_title                = (!empty($data['seo_title']))  ? $data['seo_title']  : NULL;
        $this->seo_keywords             = (!empty($data['seo_keywords']))  ? $data['seo_keywords']  : NULL;
        $this->seo_description          = (!empty($data['seo_description']))  ? $data['seo_description']  : NULL;
    }
}
