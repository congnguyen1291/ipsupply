<?php

namespace Application\Model;

class Products {
    public $products_id;
    public $website_id;
    public $products_code;
    public $categories_id;
    public $manufacturers_id;
    public $users_id;
    public $users_fullname;
    public $products_title;
    public $products_alias;
    public $products_description;
    public $products_longdescription;
    public $promotion;
    public $promotion_description;
    public $promotion_ordering;
    public $promotion1;
    public $promotion1_description;
    public $promotion1_ordering;
    public $promotion2;
    public $promotion2_description;
    public $promotion2_ordering;
    public $promotion3;
    public $promotion3_description;
    public $promotion3_ordering;
    public $seo_keywords;
    public $seo_description;
    public $seo_title;
    public $is_published;
    public $is_delete;
    public $is_new;
    public $is_available;
    public $is_hot;
    public $date_create;
    public $date_update;
    public $price;
    public $price_sale;
    public $ordering;
    public $quantity;
    public $thumb_image;
    public $list_thumb_image;
    public $number_views;
    public $vat;
    public $tags;
    public $type_view;

    public function exchangeArray($data)
    {
        $this->products_id              = (!empty($data['products_id'])) ? $data['products_id'] : NULL;
        $this->website_id              = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->products_code            = (!empty($data['products_code'])) ? $data['products_code'] : NULL;
        $this->categories_id            = (!empty($data['categories_id'])) ? $data['categories_id'] : NULL;
        $this->manufacturers_id         = (!empty($data['manufacturers_id'])) ? $data['manufacturers_id'] : NULL;
        $this->users_id                 = (!empty($data['users_id'])) ? $data['users_id'] : NULL;
        $this->users_fullname           = (!empty($data['users_fullname'])) ? $data['users_fullname'] : NULL;
        $this->products_title           = (!empty($data['products_title'])) ? $data['products_title'] : NULL;
        $this->products_alias           = (!empty($data['products_alias'])) ? $data['products_alias'] : NULL;
        $this->products_description     = (!empty($data['products_description'])) ? $data['products_description'] : NULL;
        $this->products_longdescription = (!empty($data['products_longdescription'])) ? $data['products_longdescription'] : NULL;
        $this->promotion                = (!empty($data['promotion']))  ? $data['promotion']  : NULL;
        $this->promotion_description    = (!empty($data['promotion_description']))  ? $data['promotion_description']  : NULL;
        $this->promotion_ordering       = (!empty($data['promotion_ordering']))  ? $data['promotion_ordering']  : NULL;
        $this->promotion1               = (!empty($data['promotion1'])) ? $data['promotion1'] : NULL;
        $this->promotion1_description   = (!empty($data['promotion1_description'])) ? $data['promotion1_description'] : NULL;
        $this->promotion1_ordering      = (!empty($data['promotion1_ordering']))  ? $data['promotion1_ordering']  : NULL;
        $this->promotion2               = (!empty($data['promotion2'])) ? $data['promotion2'] : NULL;
        $this->promotion2_description   = (!empty($data['promotion2_description'])) ? $data['promotion2_description'] : NULL;
        $this->promotion2_ordering      = (!empty($data['promotion2_ordering']))  ? $data['promotion2_ordering']  : NULL;
        $this->promotion3               = (!empty($data['promotion3'])) ? $data['promotion3'] : NULL;
        $this->promotion3_description   = (!empty($data['promotion3_description'])) ? $data['promotion3_description'] : NULL;
        $this->promotion3_ordering      = (!empty($data['promotion3_ordering']))  ? $data['promotion3_ordering']  : NULL;
        $this->seo_title                = (!empty($data['seo_title']))  ? $data['seo_title']  : NULL;
        $this->seo_keywords             = (!empty($data['seo_keywords']))  ? $data['seo_keywords']  : NULL;
        $this->seo_description          = (!empty($data['seo_description']))  ? $data['seo_description']  : NULL;
        $this->is_published             = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete                = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->is_new                   = (!empty($data['is_new'])) ? $data['is_new'] : 0;
        $this->is_available             = (!empty($data['is_available'])) ? $data['is_available'] : 0;
        $this->is_hot                   = (!empty($data['is_hot'])) ? $data['is_hot'] : 0;
        $this->date_create              = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update              = (!empty($data['date_update'])) ? $data['date_update'] : NULL;
        $this->price                    = (!empty($data['price'])) ? $data['price'] : NULL;
        $this->price_sale               = (!empty($data['price_sale'])) ? $data['price_sale'] : NULL;
        $this->ordering                 = (!empty($data['ordering'])) ? $data['ordering'] : 0;
        $this->quantity                 = (!empty($data['quantity'])) ? $data['quantity'] : NULL;
        $this->thumb_image              = (!empty($data['thumb_image'])) ? $data['thumb_image'] : '';
        $this->list_thumb_image              = (!empty($data['list_thumb_image'])) ? $data['list_thumb_image'] : '';
        $this->number_views             = (!empty($data['number_views'])) ? $data['number_views'] : 0;
        $this->vat                      = (!empty($data['vat'])) ? $data['vat'] : NULL;
        $this->tags                     = (!empty($data['tags'])) ? $data['tags'] : NULL;
        $this->type_view                = (!empty($data['type_view'])) ? $data['type_view'] : NULL;
    }
}
