<?php

namespace PHPPaypal;

/**
 * Sitemap
 *
 * This class used for generating Google Sitemap files
 *
 * @package    Sitemap
 * @author     Osman Üngür <osmanungur@gmail.com>
 * @copyright  2009-2015 Osman Üngür
 * @license    http://opensource.org/licenses/MIT MIT License
 * @link       http://github.com/o/sitemap-php
 */
//use Zend\Crypt\Password\Bcrypt;

class Paypal {

	const 	vpc_Version = '2';//Phiên bản modul (cố định)
	const 	vpc_Url_Sandbox = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	const 	vpc_Url = 'https://www.paypal.com/cgi-bin/webscr';
	private $is_sandbox = TRUE;
	private $cmd = '_cart';
	private $charset = 'utf-8';
	private $upload = 1;
	private $invoice = '';
	private $business = '';

	private $address_override = '';
	private $first_name = '';
	private $last_name = '';
	private $email = '';
	private $address1 = '';
	private $city = '';
	private $state = '';
	private $zip = '';

	private $tax = 0;
	private $shipping = 0;
	private $shipping2 = 0;
	private $handling = 0;
	private $handling_cart = 0;

	private $item_name = '';
	private $amount = 0;
	private $products = array();

	private $currency_code = '';
	private $return = '';
	private $shopping_url = '';
	private $cancel_return = '';
	private $notify_url = '';
	private $rm = '';
	private $lc = '';
	private $cbt = '';
	private $rate_exchange = 1;

	public function __construct( $data = array() ) {
	}

	private function getVersion() {
		return self::vpc_Version;
	}

	private function getPaypalUrl() {
		if( $this->getIsSandbox() ){
			return self::vpc_Url_Sandbox;
		}
		return self::vpc_Url;
	}

	public function setIsSandbox($is_sandbox) {
		$this->is_sandbox = $is_sandbox;
		return $this;
	}

	private function getIsSandbox() {
		return $this->is_sandbox;
	}

	public function setCmd($cmd) {
		$this->cmd = $cmd;
		return $this;
	}

	private function getCmd() {
		return $this->cmd;
	}

	public function setCharset($charset) {
		$this->charset = $charset;
		return $this;
	}

	private function getCharset() {
		return $this->charset;
	}

	public function setUpload($upload) {
		$this->upload = $upload;
		return $this;
	}

	private function getUpload() {
		return $this->upload;
	}

	public function setInvoice($invoice) {
		$this->invoice = $invoice;
		return $this;
	}

	private function getInvoice() {
		return $this->invoice;
	}

	public function setBusiness( $business ) {
		$this->business = $business;
		return $this;
	}

	private function getBusiness() {
		return $this->business;
	}

	public function setAddressOverride( $address_override ) {
		$this->address_override = $address_override;
		return $this;
	}

	private function getAddressOverride() {
		return $this->address_override;
	}

	public function setFirstName( $first_name ) {
		$this->first_name = $first_name;
		return $this;
	}

	private function getFirstName() {
		return $this->first_name;
	}

	public function setLastName( $last_name ) {
		$this->last_name = $last_name;
		return $this;
	}

	private function getLastName() {
		return $this->last_name;
	}

	public function setEmail( $email ) {
		$this->email = $email;
		return $this;
	}

	private function getEmail() {
		return $this->email;
	}

	public function setAddress1( $address1 ) {
		$this->address1 = $address1;
		return $this;
	}

	private function getAddress1() {
		return $this->address1;
	}

	public function setCity( $city ) {
		$this->city = $city;
		return $this;
	}

	private function getCity() {
		return $this->city;
	}

	public function setState( $state ) {
		$this->state = $state;
		return $this;
	}

	private function getState() {
		return $this->state;
	}

	public function setZip( $zip ) {
		$this->zip = $zip;
		return $this;
	}

	private function getZip() {
		return $this->zip;
	}

	public function setTax( $tax ) {
		$this->tax = $tax;
		return $this;
	}

	private function getTax() {
		return $this->tax;
	}

	public function setShipping( $shipping ) {
		$this->shipping = $shipping;
		return $this;
	}

	private function getShipping() {
		return $this->shipping;
	}

	public function setShipping2( $shipping2 ) {
		$this->shipping2 = $shipping2;
		return $this;
	}

	private function getShipping2() {
		return $this->shipping2;
	}

	public function setHandling( $handling ) {
		$this->handling = $handling;
		return $this;
	}

	private function getHandling() {
		return $this->handling;
	}

	public function setHandlingCart( $handling_cart ) {
		$this->handling_cart = $handling_cart;
		return $this;
	}

	private function getHandlingCart() {
		return $this->handling_cart;
	}

	public function setProducts( $products ) {
		$this->products = $products;
		return $this;
	}

	private function getProducts() {
		return $this->products;
	}

	public function setCurrencyCode( $currency_code ) {
		$this->currency_code = $currency_code;
		return $this;
	}

	private function getCurrencyCode() {
		return $this->currency_code;
	}

	public function setReturn( $return ) {
		$this->return = $return;
		return $this;
	}

	private function getReturn() {
		return $this->return;
	}

	public function setShoppingUrl( $shopping_url ) {
		$this->shopping_url = $shopping_url;
		return $this;
	}

	private function getShoppingUrl() {
		return $this->shopping_url;
	}

	public function setCancelReturn( $cancel_return ) {
		$this->cancel_return = $cancel_return;
		return $this;
	}

	private function getCancelReturn() {
		return $this->cancel_return;
	}

	public function setNotifyUrl( $notify_url ) {
		$this->notify_url = $notify_url;
		return $this;
	}

	private function getNotifyUrl() {
		return $this->notify_url;
	}

	public function setRm( $rm ) {
		$this->rm = $rm;
		return $this;
	}

	private function getRm() {
		return $this->rm;
	}

	public function setLc( $lc ) {
		$this->lc = $lc;
		return $this;
	}

	private function getLc() {
		return $this->lc;
	}

	public function setCbt( $cbt ) {
		$this->cbt = $cbt;
		return $this;
	}

	private function getCbt() {
		return $this->cbt;
	}

	public function setRateExchange( $rate_exchange ) {
		$this->rate_exchange = $rate_exchange;
		return $this;
	}

	private function getRateExchange() {
		return $this->rate_exchange;
	}

	public function setItemName( $item_name ) {
		$this->item_name = $item_name;
		return $this;
	}

	private function getItemName() {
		return $this->item_name;
	}

	public function setAmount( $amount ) {
		$this->amount = $amount;
		return $this;
	}

	private function getAmount() {
		return $this->amount;
	}

	/*
	$paypal = new Paypal( );
    $paypal->setIsSandbox( TRUE );
    $paypal->setCmd( '_cart' );
    $paypal->setCharset( 'utf-8' );
    $paypal->setUpload( 1 );
    $paypal->setInvoice( '29744' );
    $paypal->setBusiness( 'oshopvn@gmail.com' );
    $paypal->setCurrencyCode( 'USD' );
    $paypal->setReturn( '/pay/return' );
    $paypal->setCancelReturn( '/pay/cancel' );
    $paypal->setNotifyUrl( '/pay/notifi' );
    $paypal->setRm( 2 );
    $paypal->setLc( 2 );
    $paypal->setCbt( 'tiep tuc' );
    $products = array( array('title' => 'tai nghe AA', 'type_name' => 'mau vang', 'quantity' => 10, 'price_total' => 20000),
        array('title' => 'tai nghe BB', 'type_name' => 'mau xanh', 'quantity' => 5, 'price_total' => 10000));
    $paypal->setProducts( $products );
    //$paypal->setItemName( 'test mua hang paypal' );
    //$paypal->setAmount( 30000 );
    $paypal->setRateExchange( 1 );
    $url = $paypal->getUrlPay();
    https://www.paypal.com/cgi-bin/webscr?cmd=_cart&charset=utf-8&upload=1&business=sales%40ipsupply.com.au&invoice=49&currency_code=AUD&return=http%3A%2F%2Fipsupply.coz.vn%2Fau%2Fpaypal%2Freturn%2F49&cancel_return=http%3A%2F%2Fipsupply.coz.vn%2Fau%2Fpaypal%2Fcancel%2F49¬ify_url=http%3A%2F%2Fipsupply.coz.vn%2Fau%2Fpaypal%2Fnotifi%2F49&rm=2&lc=2&cbt=Continue+Paypal+payments&item_name_1=Cisco+1800+Series+Integrated+Router%2C+Model+1811%2FK9%28%29&quantity_1=1&amount_1=135.95&shipping_1=15&tax_1=20
	*/
        
	public function getUrlPay() {
		$row = array(
			"cmd" => $this->getCmd(),
		    "charset" => $this->getCharset(),
		    "upload" => $this->getUpload(),
		    "business" => $this->getBusiness(),
		    "invoice" => $this->getInvoice(),
		    "currency_code" => $this->getCurrencyCode(),
		    "return" => $this->getReturn(),
		    "cancel_return" => $this->getCancelReturn(),
		    "notify_url" => $this->getNotifyUrl(),
		    "rm" => $this->getRm(),
		    "lc" => $this->getLc(),
		    "cbt" => $this->getCbt(),
		);
		$products = $this->getProducts();
		$shipping = $this->getShipping();
		$shipping = $shipping/$this->getRateExchange();
		$tax = $this->getTax();
		$tax = $tax/$this->getRateExchange();
		if( !empty($products) ){
			$i=1;
			foreach ( $products as $key => $p) {
	            $row['item_name_'.$i] = $p['title'].'('.$p['type_name'].')';
	            $row['quantity_'.$i] = $p['quantity'];
	            $price_usd = $p['price_total']/$this->getRateExchange();
	            $row['amount_'.$i] = number_format((float)$price_usd, 2, '.', '');
	            $i++;
	        }
	        if( !empty($shipping) ){
	        	$row['shipping_1'] = $shipping;
	        }
	        if( !empty($tax) ){
	        	$row['tax_1'] = $tax;
	        }
	    }else{
	    	$row['item_name'] = $this->getItemName();
	    	$price_usd = $this->getAmount()/$this->getRateExchange();
	    	$row['amount'] = number_format((float)$price_usd, 2, '.', '');
	    	if( !empty($shipping) ){
	        	$row['shipping'] = $shipping;
	        }
	        if( !empty($tax) ){
	        	$row['tax'] = $tax;
	        }
	    }
	    $query = http_build_query($row);
		$url = '';
		if( !empty($query) ){
			$url = $this->getPaypalUrl() . "?" . $query;
		}
		return $url;
	}

	// Function to convert NTP string to an array
    public function NVPToArray($NVPString)
    {
        $proArray = array();
        while(strlen($NVPString))
        {
            // name
            $keypos= strpos($NVPString,'=');
            $keyval = substr($NVPString,0,$keypos);
            // value
            $valuepos = strpos($NVPString,'&') ? strpos($NVPString,'&'): strlen($NVPString);
            $valval = substr($NVPString,$keypos+1,$valuepos-$keypos-1);
            // decoding the respose
            $proArray[$keyval] = urldecode($valval);
            $NVPString = substr($NVPString,$valuepos+1,strlen($NVPString));
        }
        return $proArray;
    }

    public function processCreditCardPaypal($nvp_string){
    	$api='https://api-3t.paypal.com/nvp';
    	if( $this->getIsSandbox() ){
    		$api='https://api-3t.sandbox.paypal.com/nvp';
    	}
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $api);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $nvp_string);
        $result = curl_exec($curl);//print curl_error($request);     
        curl_close($curl);
        return $result;
    }

    public function getQueryStringCreditCardPaypal($carts){
        $query = array();
        $query['METHOD'] = 'DoDirectPayment';
        $query['USER'] = $carts['username'];
        $query['PWD'] = $carts['password'];
        $query['SIGNATURE'] = $carts['signature'];
        $query['VERSION'] = '85.0';
        $query['PAYMENTACTION'] = 'Sale';
        $query['IPADDRESS'] = $_SERVER['REMOTE_ADDR'];

        $query['CREDITCARDTYPE'] = $carts['creditcardtype'];
        $query['ACCT'] = $carts['acct'];
        $query['EXPDATE'] = $carts['expdate'];
        $query['CVV2'] = $carts['cvv'];

        if( !empty($carts['first_name']) ){
	        $query['FIRSTNAME'] = $carts['first_name'];
	    }

	    if( !empty($carts['last_name']) ){
	        $query['LASTNAME'] = $carts['last_name'];
	    }

	    if( !empty($carts['email']) ){
	        $query['EMAIL'] = $carts['email'];
	    }

	    if( !empty($carts['desc']) ){
	        $query['DESC'] = $carts['desc'];
	    }

	    if( !empty($carts['street']) ){
	        $query['STREET'] = $carts['street'];
	    }

	    if( !empty($carts['city']) ){
	        $query['CITY'] = $carts['city'];
	    }

	    if( !empty($carts['state']) ){
	        $query['STATE'] = $carts['state'];
	    }

	    if( !empty($carts['country_code']) ){
	        $query['COUNTRYCODE'] = $carts['country_code'];
	    }

	    if( !empty($carts['zipcode']) ){
	        $query['ZIP'] = $carts['zipcode'];
	    }

        $query['AMT'] = $carts['amount'];
        $query['CURRENCYCODE'] = $carts['currency'];

        $query_string = http_build_query($query);
        return $query_string;
    }

    /*

    $paypal = new Paypal( );
    $is_sandbox = TRUE;
    $paypal->setIsSandbox( $is_sandbox );
    $creditCard = array(
            'username' => 'dieplac7494_api1.gmail.com',
            'password' => '55W7C6BF8YKF4ZCU',
            'signature' => 'AKrx410tk16ZxXUkAA4vgll9MiDPAjLEsZ9cuTQxNSYj2ZEYa3TPoHc4',
            'creditcardtype' => 'Visa',//Visa, MasterCard, và Discover, American Express
            'acct' => '4032035898982020',
            'expdate' => '082020',
            'cvv' => '020',
            'first_name' => 'Yang',
            'last_name' => 'Ling',
            //'email' => 'test@coz.vn',
            'street' => '1 Main St',
            'city' => 'San Jose',
            'state' => 'CA',
            'country_code' => 'US',
            'zipcode' => '95131',
            'amount' => '10.00',
            'currency' => 'USD',
            'desc' => 'Testing Payments Pro'
        );
    $query_string = $paypal->getQueryStringCreditCardPaypal($creditCard);
    $result = $paypal->processCreditCardPaypal($query_string);
    $result = $paypal->NVPToArray($result);
    print_r($result);
    if( !empty($result) 
        && !empty($result['ACK'])
        && strtolower($result['ACK']) == 'success' ){
        die('thanh cong');
    }
    die('that bai');

	case 'ATM':
        {
            $rate_exchange = 1;
            if( strtolower($_SESSION['website']['website_currency']) != 'usd' ){
                $rate_exchange = $this->currencyConvert('USD',$_SESSION['website']['website_currency']);
            }
            $amount = $total_tax/$rate_exchange;
            $query_string = $this->getQueryStringCreditCardPaypal($payment, $dataPayment, $amount, 'USD', $dataPayment['desc']);
			if( $payment["testing"]==1){
				$api='https://api.sandbox.paypal.com/nvp';
			}else{
				$api='https://api.paypal.com/nvp';
			}
            $result = $this->processCreditCardPaypal($api, $query_string);
            $result_array = $this->NVPToArray($result);
            if(!empty($result_array) 
                && !empty($result_array['ACK'])
                && strtolower($result_array['ACK']) == 'success' ){
                $row = array('payment' => 'paid',
                            'from_currency' => $_SESSION['website']['website_currency'],
                            'to_currency' => 'USD',
                            'rate_exchange' => $rate_exchange,
                            'total_converter' => $amount,
                            'payment_code' => $payment_type);
                $this->getModelTable('InvoiceTable')->updateData($row, $id);
            }

            $_SESSION['invoice_id'] = $id;
            return $this->redirect()->toRoute('cart', array('action' => 'success'));
            break;
        }
            
        case 'PAYPAL':
        {
            $rate_exchange = 1;
            if( strtolower($_SESSION['website']['website_currency']) != 'usd' ){
                $rate_exchange = $this->currencyConvert('USD',$_SESSION['website']['website_currency']);
            }
            $id_sha1 = sha1($id);
            $amount = $total_tax/$rate_exchange;
            $cb_return = $this->baseUrl.'/pay/return/'.$id_sha1;
            $cb_cancel = $this->baseUrl.'/pay/cancel/'.$id_sha1;
            $cb_notifi = $this->baseUrl.'/pay/notifi/'.$id_sha1;
            $query_string = $this->getQueryStringPaypal($id_sha1, $payment, $rate_exchange, $dataCart,
                                                        $dataPayment, 'USD', $cb_return, $cb_cancel, $cb_notifi);
            $row = array('from_currency' => $_SESSION['website']['website_currency'],
                            'to_currency' => 'USD',
                            'rate_exchange' => $rate_exchange,
                            'total_converter' => $amount,
                            'payment_code' => $payment_type);
            try{
                $invoice = $this->getModelTable('InvoiceTable')->getInvoiceByTitle($id);
                $this->getModelTable('InvoiceTable')->updateData($row, $invoice->invoice_id);
            }catch(\Exception $ex){}
            if( $payment["testing"]==1){
				$redirectlink='https://www.sandbox.paypal.com/cgi-bin/webscr?'.$query_string;
			}else{
				$redirectlink='https://www.paypal.com/cgi-bin/webscr?'.$query_string;
			}
            return $this->redirect()->toUrl($redirectlink);
            break;
        }
    }
    */

}
