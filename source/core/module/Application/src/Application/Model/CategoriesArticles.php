<?php

namespace Application\Model;

class CategoriesArticles {
    public $categories_articles_id;
    public $website_id;
    public $parent_id;
    public $categories_articles_title;
    public $categories_articles_alias;
    public $seo_keywords;
    public $seo_description;
    public $is_published;
    public $is_delete;
    public $is_technical_category;
    public $date_create;
    public $date_update;
    public $ordering;
    public $is_faq;

    public function exchangeArray($data)
    {
        $this->categories_articles_id = (!empty($data['categories_articles_id'])) ? $data['categories_articles_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->parent_id = (!empty($data['parent_id'])) ? $data['parent_id'] : NULL;
        $this->categories_articles_title                = (!empty($data['categories_articles_title']))  ? $data['categories_articles_title']  : NULL;
        $this->categories_articles_alias             = (!empty($data['categories_articles_alias']))  ? $data['categories_articles_alias']  : NULL;
        $this->seo_keywords          = (!empty($data['seo_keywords']))  ? $data['seo_keywords']  : NULL;
        $this->seo_description          = (!empty($data['seo_description']))  ? $data['seo_description']  : NULL;
        $this->is_published          = (!empty($data['is_published']))  ? $data['is_published']  : NULL;
        $this->is_delete          = (!empty($data['is_published']))  ? $data['is_delete']  : NULL;
        $this->is_technical_category          = (!empty($data['is_technical_category']))  ? $data['is_technical_category']  : NULL;
        $this->date_create          = (!empty($data['date_create']))  ? $data['date_create']  : date('Y-m-d');
        $this->date_update          = (!empty($data['date_update']))  ? $data['date_update']  : date('Y-m-d');
        $this->ordering          = (!empty($data['ordering']))  ? $data['ordering']  : 0;
        $this->is_faq          = (!empty($data['is_faq']))  ? $data['is_faq']  : 0;
    }
}
