<?php

namespace PHPVnpay;

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

class Vnpay {

	const 	vnp_Version = '2.0.0';
	const 	vnp_Url_Sandbox = 'http://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
	const 	vnp_Url = 'https://pay.vnpay.vn/vpcpay.html';
	private $is_sandbox = TRUE;
	private $vnp_TmnCode = '';
	private $vnp_Amount = 0;
	private $vnp_Command = 'pay';
	private $vnp_CreateDate = '';
	private $vnp_CurrCode = 'VND';
	private $vnp_IpAddr = '';
	private $vnp_UserAgent = '';
	private $vnp_Locale = '';
	private $vnp_Merchant = '';
	private $vnp_OrderInfo = '';
	private $vnp_OrderType = '';
	private $vnp_ReturnUrl = '';
	private $vnp_TxnRef = '';
	private $returnurl = '';
	private $website_id = 0;
	private $vnp_BankCode = '';
	private $vnp_HashSecret = '';
	//private $vnp_SecureHashType = 'BCRYPT';
	private $vnp_SecureHashType = 'MD5';

	public function __construct( $data = array() ) {
		$this->setVnpIpAddr( $_SERVER['REMOTE_ADDR'] );
		$this->setVnpUserAgent( $_SERVER['HTTP_USER_AGENT'] );
		$this->setVnpCreateDate( date('YmdHis') );
	}

	private function getVersion() {
		return self::vnp_Version;
	}

	private function getVnpUrl() {
		if( $this->getIsSandbox() ){
			return self::vnp_Url_Sandbox;
		}
		return self::vnp_Url;
	}

	public function setIsSandbox($is_sandbox) {
		$this->is_sandbox = $is_sandbox;
		return $this;
	}

	private function getIsSandbox() {
		return $this->is_sandbox;
	}

	public function setVnpTmnCode($vnp_TmnCode) {
		$this->vnp_TmnCode = $vnp_TmnCode;
		return $this;
	}

	private function getVnpTmnCode() {
		return $this->vnp_TmnCode;
	}

	public function setVnpAmount($vnp_Amount) {
		$this->vnp_Amount = $vnp_Amount;
		return $this;
	}

	private function getVnpAmount() {
		return $this->vnp_Amount;
	}

	public function setVnpCommand($vnp_Command) {
		$this->vnp_Command = $vnp_Command;
		return $this;
	}

	private function getVnpCommand() {
		return $this->vnp_Command;
	}

	public function setVnpCreateDate($vnp_CreateDate) {
		$this->vnp_CreateDate = $vnp_CreateDate;
		return $this;
	}

	private function getVnpCreateDate() {
		return $this->vnp_CreateDate;
	}

	public function setVnpCurrCode($vnp_CurrCode) {
		$this->vnp_CurrCode = $vnp_CurrCode;
		return $vnp_CurrCode;
	}

	private function getVnpCurrCode() {
		return $this->vnp_CurrCode;
	}

	public function setVnpIpAddr($vnp_IpAddr) {
		$this->vnp_IpAddr = $vnp_IpAddr;
		return $this;
	}

	private function getVnpIpAddr() {
		return $this->vnp_IpAddr;
	}

	public function setVnpUserAgent($vnp_UserAgent) {
		$this->vnp_UserAgent = $vnp_UserAgent;
		return $this;
	}

	private function getVnpUserAgent() {
		return $this->vnp_UserAgent;
	}

	public function setVnpLocale($vnp_Locale) {
		$this->vnp_Locale = $vnp_Locale;
		return $this;
	}

	private function getVnpLocale() {
		return $this->vnp_Locale;
	}

	public function setVnpMerchant($vnp_Merchant) {
		$this->vnp_Merchant = $vnp_Merchant;
		return $this;
	}

	private function getVnpMerchant() {
		return $this->vnp_Merchant;
	}

	public function setVnpOrderInfo($vnp_OrderInfo) {
		$this->vnp_OrderInfo = $vnp_OrderInfo;
		return $this;
	}

	private function getVnpOrderInfo() {
		return $this->vnp_OrderInfo;
	}

	public function setVnpOrderType($vnp_OrderType) {
		$this->vnp_OrderType = $vnp_OrderType;
		return $this;
	}

	private function getVnpOrderType() {
		return $this->vnp_OrderType;
	}

	public function setVnpReturnUrl($vnp_ReturnUrl) {
		$this->vnp_ReturnUrl = $vnp_ReturnUrl;
		return $this;
	}

	private function getVnpReturnUrl() {
		return $this->vnp_ReturnUrl;
	}

	public function setVnpTxnRef($vnp_TxnRef) {
		$this->vnp_TxnRef = $vnp_TxnRef;
		return $this;
	}

	private function getVnpTxnRef() {
		return $this->vnp_TxnRef;
	}

	public function setReturnUrl($returnurl) {
		$this->returnurl = $returnurl;
		return $this;
	}

	private function getReturnUrl() {
		return $this->returnurl;
	}

	public function setVnpBankCode($vnp_BankCode) {
		$this->vnp_BankCode = $vnp_BankCode;
		return $this;
	}

	private function getVnpSecureHashType() {
		return $this->vnp_SecureHashType;
	}

	public function setVnpSecureHashType($vnp_SecureHashType) {
		$this->vnp_SecureHashType = $vnp_SecureHashType;
		return $this;
	}

	private function getVnpBankCode() {
		return $this->vnp_BankCode;
	}

	public function bcryptHashSecret( $str ) {
		//$bcrypt = new Bcrypt();
		//$hash_secret = $bcrypt->create($str);
		$hash_secret = md5($str);
		return $hash_secret;
	}

	public function setHashSecret($vnp_HashSecret) {
		$this->vnp_HashSecret = $vnp_HashSecret;
		return $this;
	}

	private function getHashSecret() {
		return $this->vnp_HashSecret;
	}

	public function setWebsiteId($website_id) {
		$this->website_id = $website_id;
		return $this;
	}

	private function getWebsiteId() {
		return $this->website_id;
	}

	/*	$vnpay = new Vnpay( );
        $vnpay->setIsSandbox( TRUE );
        $vnpay->setVnpMerchant( 'VNPAY' );
        $vnpay->setVnpTmnCode( 'VNSKY001' );
        $vnpay->setVnpAmount( 30000000 );
        $vnpay->setVnpCommand( 'pay' );
        $vnpay->setVnpCurrCode( 'VND' );
        $vnpay->setVnpLocale( 'vn' );
        $vnpay->setVnpOrderInfo( 'giao hang dung hen' );
        $vnpay->setVnpOrderType( 'billpayment' );
        $vnpay->setVnpReturnUrl( '/vnpay/return' );
        $vnpay->setVnpTxnRef( 12 );
        $vnpay->setVnpBankCode( 'NCB' );
        $vnpay->setHashSecret( 'BOFPOCIYWQWDGVWDPKLRZHJEAYYTKHJC' );
        $url = $vnpay->getUrlPay();
        echo $url;die();*/
        
	public function getUrlPay() {
		$row = array(
		    "vnp_TmnCode" => $this->getVnpTmnCode(), //Tham so nay lay tu VNPAY, cua cong ty 
		    "vnp_Amount" =>  $this->getVnpAmount()*100,//so tien don hang, nhan voi 100
		    "vnp_Command" =>  $this->getVnpCommand(),//ma api su dung, de thanh toan dung 'pay', Yêu cầu truy vấn giao dịch (vnp_Command=querydr)
		    "vnp_CreateDate" =>  $this->getVnpCreateDate(),//ngay tao
		    "vnp_CurrCode" =>  $this->getVnpCurrCode(),//loai currency VND, USD, AUS...
		    "vnp_IpAddr" =>  $this->getVnpIpAddr(),//dia chi IP nguoi mua
		    "vnp_Locale" =>  $this->getVnpLocale(),//ngon ngu vn, en
		    "vnp_Merchant" =>  $this->getVnpMerchant(),//viet tat cua cong ty
		    //"vnp_OrderInfo" =>  $this->getVnpOrderInfo(),//noi dung muon noi khi thanh toan
		    "vnp_OrderType" =>  $this->getVnpOrderType(),//mot trong 3 gia tri ( topup :Nạp tiền điện thoại, billpayment: Thanh toán hóa đơn, fashion: Thời trang)
		    "vnp_ReturnUrl" =>  $this->getVnpReturnUrl(),//callback khi giao dich hoan thanh
		    "vnp_TxnRef" =>  $this->getVnpTxnRef(),//order id ben coz
		    "vnp_Version" =>  $this->getVersion(),//phien ban API
		    //"vnp_BankCode" =>  $this->getVnpBankCode(),//ma ngan hang
		    //"vnp_SecureHashType" =>  $this->getVnpSecureHashType(),//kieu ma hoa key
		    //"vnp_SecureHash" =>  $this->bcryptHashSecret($this->getHashSecret()),//key cua ung dung
		    //"website_id" =>  $this->getWebsiteId(),//key cua ung dung
		);
		if( !empty($this->getVnpOrderInfo()) ){
			$row['vnp_OrderInfo'] = $this->getVnpOrderInfo();
		}
		if( !empty($this->getVnpBankCode()) ){
			$row['vnp_BankCode'] = $this->getVnpBankCode();
		}
		ksort($row);
		$query = '';
		$hashdata = '';
		foreach ($row as $key => $value) {
			$hashdata 	.= (!empty($hashdata) ? '&' : '' ) . $key . "=" . $value;
		    $query 		.= (!empty($query) ? '&' : '' ) . urlencode($key) . "=" . urlencode($value);
		}
		$url = '';
		if( !empty($query) ){
			$url = $this->getVnpUrl() . "?" . $query;
			if ( !empty($this->getHashSecret()) ) {
			    $vnpSecureHash = $this->bcryptHashSecret($this->getHashSecret() . $hashdata);
			    $url .= '&vnp_SecureHashType='.$this->getVnpSecureHashType().'&vnp_SecureHash=' . $vnpSecureHash;
			}
		}
		return $url;
	}

}
