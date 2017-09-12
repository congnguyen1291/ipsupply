<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Coupons extends App
{
	public function hasCoupon()
    {
        if( !empty($_SESSION['cart'])
        	&& !empty($_SESSION['cart']['coupon']) ){
            return TRUE;
        }
        return FALSE;
    }

	public function getCoupon()
    {
        if( $this->hasCoupon() ){
            $carts = $_SESSION['cart'];
            return $carts['coupon'];
        }
        return array();
    }

    public function getPrice()
    {
        if( $this->hasCoupon() ){
            $coupon = $this->getCoupon();
            return $coupon['coupon_price'];
        }
        return '';
    }

    public function getPriceWithCurrency()
    {
        if( $this->hasCoupon() ){
            $coupon = $this->getCoupon();
            return $this->view->Currency()->fomatCurrency($coupon['coupon_price']);
        }
        return '';
    }

    public function isAvaliable()
    {
    	$calculate = $this->view->Cart()->sumSubTotalPriceInCart();
        $price_total = $calculate['price_total'];
        $price_total_old = $calculate['price_total_old'];
        $price_total_orig = $calculate['price_total_orig'];
        $coupon = $this->getCoupon();
        if( empty($coupon) 
            || !($price_total_orig >= $coupon['min_price_use'] 
            && $price_total_orig <= $coupon['max_price_use']) ){
            return FALSE;
        }
        return TRUE;
    }
}
