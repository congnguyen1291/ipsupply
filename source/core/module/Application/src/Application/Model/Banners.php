<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/16/14
 * Time: 2:04 PM
 */
namespace Application\Model;

class Banners{
    public $banners_id;
    public $website_id;
    public $banners_title;
    public $position_id;
    public $type_banners;
    public $size_id;
    public $code;
    public $file;
    public $status;
    public $date_show;
    public $date_hide;
    public $link;
    public $background;

    public function exchangeArray($data){
        $this->banners_id = (!empty($data['banners_id'])) ? $data['banners_id'] : 0;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : 0;
        $this->banners_title = (!empty($data['banners_title'])) ? $data['banners_title'] : '';
        $this->position_id = (!empty($data['position_id'])) ? $data['position_id'] : 0;
        $this->type_banners = (!empty($data['type_banners'])) ? $data['type_banners'] : 0;
        $this->size_id = (!empty($data['size_id'])) ? $data['size_id'] : 0;
        $this->code = (!empty($data['code'])) ? $data['code'] : '';
        $this->file = (!empty($data['file'])) ? $data['file'] : '';
        $this->status = (!empty($data['status'])) ? $data['status'] : 0;
        $this->date_show = (!empty($data['date_show'])) ? $data['date_show'] : date('Y-m-d');
        $this->date_hide = (!empty($data['date_hide'])) ? $data['date_hide'] : date('Y-m-d');
        $this->link = (!empty($data['link'])) ? $data['link'] : '';
        $this->background = (!empty($data['background'])) ? $data['background'] : '';
    }

}