<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Cart extends App
{
    public function getCart()
    {
        if( !empty($_SESSION['cart']) ){
        	$carts = $_SESSION['cart'];
        	if( $this->hasCoupon() ){
                unset($carts['coupon']);
            }
            if( $this->hasShipping() ){
        		unset($carts['shipping']);
        	}
            return $carts;
        }
        return array();
    }

    public function hasShipping()
    {
        if( !empty($_SESSION['cart'])
            && !empty($_SESSION['cart']['shipping']) ){
            return TRUE;
        }
        return FALSE;
    }

    public function getShipping()
    {
        if( $this->hasShipping() ){
            return $_SESSION['cart']['shipping'];
        }
        return array();
    }

    public function getFeeShipping()
    {
        $fee = 0;
        if( $this->hasShipping() ){
            $fee = $this->getShipping()['fee'];
        }
        return $fee;
    }

    public function getIsFreeShipping()
    {
        $is_free = FALSE;
        if( $this->hasShipping() ){
            $is_free = $this->getShipping()['is_free'];
        }
        return $is_free;
    }

    public function getNoShipping()
    {
        $no_shipping = FALSE;
        if( $this->hasShipping() ){
            $no_shipping = $this->getShipping()['no_shipping'];
        }
        return $no_shipping;
    }

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
        if( !empty($_SESSION['cart'])
        	&& !empty($_SESSION['cart']['coupon']) ){
            return $_SESSION['cart']['coupon'];
        }
        return array();
    }

    public function getNumberProductInCart()
    {
        $number = 0;
        $carts = $this->getCart();
        if( !empty($carts) ){
			foreach ($carts as $products_id => $product) {
				foreach ($product['product_type'] as $product_type_id => $product_type) {
					$number += $product_type['quantity'];
				}
			}
		}
		return $number;
    }

    public function sumSubTotalPriceInCart(){
        $price_total = 0;
        $price_total_old = 0;
        $price_total_orig = 0;
        $price_total_tax = 0;
        $price_total_old_tax = 0;
        $price_total_orig_tax = 0;
        $carts = $this->getCart();
        if( !empty($carts) ){
            foreach ($carts as $products_id => $product) {
                foreach ($product['product_type'] as $product_type_id => $product_type) {
                    $price_total += $product_type['price_total'];
                    $price_total_old += $product_type['price_total_old'];
                    $vat = 0;
                    if( !empty($product_type['vat']) ){
                        $vat = $product_type['vat'];
                    }
                    $price_total_tax += $product_type['price_total'] + ($product_type['price_total']*$vat/100);
                    $price_total_old_tax += $product_type['price_total_old'] + ($product_type['price_total_old']*$vat/100);
                }
            }
            $price_total_orig = $price_total;
            $price_total_orig_tax = $price_total_tax;
            if( $this->hasCoupon() ){
                $coupon = $this->getCoupon();
                $price_total =  $price_total > $coupon['coupon_price'] ? ($price_total - $coupon['coupon_price']) : 0;
                $price_total_old =  $price_total_old > $coupon['coupon_price'] ? ($price_total_old - $coupon['coupon_price']) : 0;
                
                $price_total_tax =  $price_total_tax > $coupon['coupon_price'] ? ($price_total_tax - $coupon['coupon_price']) : 0;
                $price_total_old_tax =  $price_total_old_tax > $coupon['coupon_price'] ? ($price_total_old_tax - $coupon['coupon_price']) : 0;
            }
        }
        return array(
            'price_total' => $price_total,
            'price_total_old' => $price_total_old,
            'price_total_orig' => $price_total_orig,
            'price_total_tax' => $price_total_tax,
            'price_total_old_tax' => $price_total_old_tax,
            'price_total_orig_tax' => $price_total_orig_tax,
        );
    }

    public function sumSubTotalPriceFromArray($cart){
		$price_total = 0;
        $price_total_old = 0;
        $price_total_orig = 0;
        $price_total_tax = 0;
		$price_total_old_tax = 0;
		$price_total_orig_tax = 0;
        if( !empty($cart) ){
			foreach ($cart as $products_id => $product) {
                if($products_id == 'coupon'  || $products_id == 'shipping'  ) continue;
				foreach ($product['product_type'] as $product_type_id => $product_type) {
					$price_total += $product_type['price_total'];
                    $price_total_old += $product_type['price_total_old'];
                    $vat = 0;
                    if( !empty($product_type['vat']) ){
                        $vat = $product_type['vat'];
                    }
                    $price_total_tax += $product_type['price_total'] + ($product_type['price_total']*$vat/100);
					$price_total_old_tax += $product_type['price_total_old'] + ($product_type['price_total_old']*$vat/100);
				}
			}
            $price_total_orig = $price_total;
			$price_total_orig_tax = $price_total_tax;
			if( isset($cart['coupon']) ){
				$coupon = $cart['coupon'];
				$price_total =  $price_total > $coupon['coupon_price'] ? ($price_total - $coupon['coupon_price']) : 0;
                $price_total_old =  $price_total_old > $coupon['coupon_price'] ? ($price_total_old - $coupon['coupon_price']) : 0;

                $price_total_tax =  $price_total_tax > $coupon['coupon_price'] ? ($price_total_tax - $coupon['coupon_price']) : 0;
				$price_total_old_tax =  $price_total_old_tax > $coupon['coupon_price'] ? ($price_total_old_tax - $coupon['coupon_price']) : 0;
			}
		}
		return array(
			'price_total' => $price_total,
            'price_total_old' => $price_total_old,
            'price_total_orig' => $price_total_orig,
            'price_total_tax' => $price_total_tax,
			'price_total_old_tax' => $price_total_old_tax,
			'price_total_orig_tax' => $price_total_orig_tax,
		);
	}

	public function getUrlCart(){
		return $this->getUrlPrefixLang().'/cart';
	}

	public function getUrlPayment(){
		$carts = $this->getCart();
        if( !empty($carts) ){
        	return $this->getUrlPrefixLang().'/cart/payment';
        }
		return $this->getUrlPrefixLang().'/cart';
	}

	public function getUrlAuth(){
        $carts = $this->getCart();
        if( !empty($carts) ){
            return $this->getUrlPrefixLang().'/cart/auth';
        }
        return $this->getUrlPrefixLang().'/cart';
    }

    public function getUrlDel($p){
        if( !empty($p)
            && isset($p['products_id']) 
            && isset($p['products_type_id']) ){
            return FOLDERWEB.$this->getUrlPrefixLang().'/cart/remove?products_id=' .$p['products_id']. '&product_type=' .$p['products_type_id'];
        }
        return '';
    }

    public function getUrlEditProduct($p){
        if( !empty($p)
            && isset($p['products_id']) 
            && isset($p['products_type_id']) ){
        	return FOLDERWEB.$this->getUrlPrefixLang().'/cart/popEditProduct?products_id=' .$p['products_id']. '&product_type=' .$p['products_type_id'];
        }
		return '';
	}

    public function getProductsQuantity($p)
    {
        $quantity = 0;
        if( !empty($p) && !empty($p['quantity']) ){
            $quantity = $p['quantity'];
        }
        return $quantity;
    }

    public function getProductsPriceTotal($p)
    {
        $price_total = 0;
        if( !empty($p) && !empty($p['price_total']) ){
            $price_total = $p['price_total'];
        }
        return $price_total;
    }

    public function getMessageError( $error )
    {
        $translator = $this->getTranslator ();
        $msg = '';
        if( !empty($error['vnp_ResponseCode']) ){
            switch ( $error['vnp_ResponseCode'] ) {
                case '01':
                    $msg = $translator->translate('txt_error_giao_dich_da_ton_tai');
                    break;
                case '97':
                    $msg = $translator->translate('txt_error_chu_ki_khong_hop_le');
                    break;
            }
        }
        return $msg;
    }
	
}
