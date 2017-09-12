<?php

namespace Application\Model;

class Coupons {
    public $coupons_id;
    public $website_id;
    public $coupons_code;
    public $coupons_type;
    public $start_date;
    public $expire_date;
    public $min_price_use;
    public $max_price_use;
    public $coupon_price;
    public $coupon_percent;
    public $is_published;

    public function exchangeArray($data)
    {
        $this->coupons_id = (!empty($data['coupons_id'])) ? $data['coupons_id'] : NULL;
        $this->website_id = (!empty($data['website_id'])) ? $data['website_id'] : NULL;
        $this->coupons_code = (!empty($data['coupons_code'])) ? $data['coupons_code'] : 0;
        $this->coupons_type = (!empty($data['coupons_type'])) ? $data['coupons_type'] : 0;
        $this->start_date = (!empty($data['start_date'])) ? $data['start_date'] : '';
        $this->expire_date = (!empty($data['expire_date'])) ? $data['expire_date'] : '';
        $this->min_price_use    = (!empty($data['min_price_use']))  ? $data['min_price_use']  : 0;
        $this->max_price_use    = (!empty($data['max_price_use']))  ? $data['max_price_use']  : 0;
        $this->coupon_price          = (!empty($data['coupon_price']))  ? $data['coupon_price']  : 0;
        $this->coupon_percent          = (!empty($data['coupon_percent']))  ? $data['coupon_percent']  : 0;
        $this->is_published          = (!empty($data['is_published']))  ? $data['is_published']  : 0;
    }
}
