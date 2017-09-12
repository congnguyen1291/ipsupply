<?php

namespace Application\Model;

class Websites {

    public $website_id;
    public $template_id;
    public $website_name;
    public $website_domain;
    public $website_alias;
    public $website_domain_refer;
    public $logo;
    public $facebook_id;
    public $google_client_id;
    public $seo_title;
    public $seo_keywords;
    public $seo_description;
    public $websites_dir;
    public $websites_folder;
    public $website_email_admin;
    public $website_email_customer;
    public $website_name_business;
    public $website_phone;
    public $website_address;
    public $website_city;
    public $website_contries;
    public $website_city_name;
    public $website_contries_name;
    public $website_timezone;
    public $website_currency;
    public $website_currency_format;
    public $website_currency_decimals;
    public $website_currency_decimalpoint;
    public $website_currency_separator;
    public $website_order_code_prefix;
    public $website_order_code_suffix;
    public $website_ga_code;
    public $website_min_value_slider;
    public $website_max_value_slider;
    public $css;
    public $javascript;
    public $url_twister;
    public $url_google_plus;
    public $url_facebook;
    public $url_pinterest;
    public $url_houzz;
    public $url_instagram;
    public $url_rss;
    public $url_youtube;

    public $default_access_token;
    public $long_access_token;
    public $default_page_access_token;
    public $long_page_access_token;
    public $page_id;
    public $page_tab;
    public $date_access_token;
    public $date_sync;
    public $app_facebook_has_configed;

    public $date_create;
    public $is_master;
    public $is_try;
    public $templete_buy;
    public $is_local;
    public $is_multilanguage;
    public $has_login_facebook;
    public $has_login_google;
    public $has_login_twister;
    public $version_cart;
    public $type_crop_image;
    public $confirm_location;
    public $is_published;

    public function exchangeArray($data)
    {
        $this->website_id     = (isset($data['website_id'])) ? $data['website_id'] : null;
        $this->template_id = (isset($data['template_id'])) ? $data['template_id'] : null;
        $this->website_name  = (isset($data['website_name'])) ? $data['website_name'] : null;
        $this->website_domain  = (isset($data['website_domain'])) ? $data['website_domain'] : null;
        $this->website_alias  = (isset($data['website_alias'])) ? $data['website_alias'] : null;
        $this->website_domain_refer  = (isset($data['website_domain_refer'])) ? $data['website_domain_refer'] : null;
        $this->logo  = (isset($data['logo'])) ? $data['logo'] : '';
        $this->facebook_id  = (isset($data['facebook_id'])) ? $data['facebook_id'] : '';
        $this->google_client_id  = (isset($data['google_client_id'])) ? $data['google_client_id'] : '';
        $this->seo_title  = (isset($data['seo_title'])) ? $data['seo_title'] : '';
        $this->seo_keywords  = (isset($data['seo_keywords'])) ? $data['seo_keywords'] : '';
        $this->seo_description  = (isset($data['seo_description'])) ? $data['seo_description'] : '';
        $this->websites_dir  = (isset($data['websites_dir'])) ? $data['websites_dir'] : '';
        $this->websites_folder  = (isset($data['websites_folder'])) ? $data['websites_folder'] : '';
        $this->website_email_admin  = (isset($data['website_email_admin'])) ? $data['website_email_admin'] : '';
        $this->website_email_customer  = (isset($data['website_email_customer'])) ? $data['website_email_customer'] : '';
        $this->website_name_business  = (isset($data['website_name_business'])) ? $data['website_name_business'] : '';
        $this->website_phone  = (isset($data['website_phone'])) ? $data['website_phone'] : '';
        $this->website_address  = (isset($data['website_address'])) ? $data['website_address'] : '';
        $this->website_city  = (isset($data['website_city'])) ? $data['website_city'] : 0;
        $this->website_contries  = (isset($data['website_contries'])) ? $data['website_contries'] : 0;
        $this->website_city_name  = (isset($data['website_city_name'])) ? $data['website_city_name'] : '';
        $this->website_contries_name  = (isset($data['website_contries_name'])) ? $data['website_contries_name'] : '';
        $this->website_timezone  = (isset($data['website_timezone'])) ? $data['website_timezone'] : '';
        $this->website_currency  = (isset($data['website_currency'])) ? $data['website_currency'] : '';
        $this->website_currency_format  = (isset($data['website_currency_format'])) ? $data['website_currency_format'] : '';
        $this->website_currency_decimals  = (isset($data['website_currency_decimals'])) ? $data['website_currency_decimals'] : 2;
        $this->website_currency_decimalpoint  = (isset($data['website_currency_decimalpoint'])) ? $data['website_currency_decimalpoint'] : '.';
        $this->website_currency_separator  = (isset($data['website_currency_separator'])) ? $data['website_currency_separator'] : ',';
        $this->website_order_code_prefix  = (isset($data['website_order_code_prefix'])) ? $data['website_order_code_prefix'] : '';
        $this->website_order_code_suffix  = (isset($data['website_order_code_suffix'])) ? $data['website_order_code_suffix'] : '';
        $this->website_ga_code  = (isset($data['website_ga_code'])) ? $data['website_ga_code'] : '';
        $this->website_min_value_slider  = (isset($data['website_min_value_slider'])) ? $data['website_min_value_slider'] : 0;
        $this->website_max_value_slider  = (isset($data['website_max_value_slider'])) ? $data['website_max_value_slider'] : 0;
        $this->javascript  = (isset($data['javascript'])) ? $data['javascript'] : '';
        $this->css  = (isset($data['css'])) ? $data['css'] : '';
        $this->url_twister  = (isset($data['url_twister'])) ? $data['url_twister'] : '';
        $this->url_google_plus  = (isset($data['url_google_plus'])) ? $data['url_google_plus'] : '';
        $this->url_facebook  = (isset($data['url_facebook'])) ? $data['url_facebook'] : '';
        $this->url_pinterest  = (isset($data['url_pinterest'])) ? $data['url_pinterest'] : '';
        $this->url_houzz  = (isset($data['url_houzz'])) ? $data['url_houzz'] : '';
        $this->url_instagram  = (isset($data['url_instagram'])) ? $data['url_instagram'] : '';
        $this->url_rss  = (isset($data['url_rss'])) ? $data['url_rss'] : '';
        $this->url_youtube  = (isset($data['url_youtube'])) ? $data['url_youtube'] : '';
        
        $this->default_access_token  = (isset($data['default_access_token'])) ? $data['default_access_token'] : '';
        $this->long_access_token  = (isset($data['long_access_token'])) ? $data['long_access_token'] : '';
        $this->default_page_access_token  = (isset($data['default_page_access_token'])) ? $data['default_page_access_token'] : '';
        $this->long_page_access_token  = (isset($data['long_page_access_token'])) ? $data['long_page_access_token'] : '';
        $this->page_id  = (isset($data['page_id'])) ? $data['page_id'] : '';
        $this->page_tab  = (isset($data['page_tab'])) ? $data['page_tab'] : '';
        $this->date_access_token  = (isset($data['date_access_token'])) ? $data['date_access_token'] : '';
        $this->date_sync  = (isset($data['date_sync'])) ? $data['date_sync'] : '';
        $this->app_facebook_has_configed  = (isset($data['app_facebook_has_configed'])) ? $data['app_facebook_has_configed'] : '';

        $this->date_create  = (isset($data['date_create'])) ? $data['date_create'] : date("d-m-Y");
        $this->is_master  = (isset($data['is_master'])) ? $data['is_master'] : 0;
        $this->is_try  = (isset($data['is_try'])) ? $data['is_try'] : 0;
        $this->templete_buy  = (isset($data['templete_buy'])) ? $data['templete_buy'] : '';
        $this->is_local  = (isset($data['is_local'])) ? $data['is_local'] : 0;
        $this->is_multilanguage  = (isset($data['is_multilanguage'])) ? $data['is_multilanguage'] : 0;
        $this->has_login_facebook  = (isset($data['has_login_facebook'])) ? $data['has_login_facebook'] : 0;
        $this->has_login_google  = (isset($data['has_login_google'])) ? $data['has_login_google'] : 0;
        $this->has_login_twister  = (isset($data['has_login_twister'])) ? $data['has_login_twister'] : 0;
        $this->version_cart  = (isset($data['version_cart'])) ? $data['version_cart'] : '';
        $this->type_crop_image  = (isset($data['type_crop_image'])) ? $data['type_crop_image'] : 0;
        $this->confirm_location  = (isset($data['confirm_location'])) ? $data['confirm_location'] : 0;
        $this->is_published  = (isset($data['is_published'])) ? $data['is_published'] : 0;
    }
    
}
