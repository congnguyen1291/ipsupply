<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/16/14
 * Time: 2:04 PM
 */
namespace Application\Model;

class Banner{
    public $banners_id;
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

    public function exchangeArray($data){
        $this->banners_id = (!empty($data['banners_id'])) ? $data['banners_id'] : NULL;
        $this->banners_title = (!empty($data['banners_title'])) ? $data['banners_title'] : NULL;
        $this->position_id = (!empty($data['position_id'])) ? $data['position_id'] : NULL;
        $this->type_banners = (!empty($data['type_banners'])) ? $data['type_banners'] : NULL;
        $this->size_id = (!empty($data['size_id'])) ? $data['size_id'] : NULL;
        $this->code = (!empty($data['code'])) ? $data['code'] : NULL;
        $this->file = (!empty($data['file'])) ? $data['file'] : NULL;
        $this->status = (!empty($data['status'])) ? $data['status'] : NULL;
        $this->date_show = (!empty($data['date_show'])) ? $data['date_show'] : NULL;
        $this->date_hide = (!empty($data['date_hide'])) ? $data['date_hide'] : NULL;
        $this->link = (!empty($data['link'])) ? $data['link'] : NULL;
    }

}