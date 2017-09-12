<?php

namespace PHPOnepay;

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

class Onepay {

	const 	vpc_Version = '2';//Phiên bản modul (cố định)
	const 	vpc_Url_Sandbox = 'http://mtf.onepay.vn/onecomm-pay/vpc.op';
	//const 	vpc_Url_Sandbox = 'http://mtf.onepay.vn/vpcpay/vpcpay.op';
	const 	vpc_Url = 'https://onepay.vn/vpcpay/vpcpay.op';
	private $is_sandbox = TRUE;
	private $vpc_Merchant = '';//Được cấp bởi OnePAY
	private $vpc_AccessCode = '';//Được cấp bởi OnePAY
	private $vpc_MerchTxnRef = '';//ID giao dịch, giá trị phải khác nhau trong mỗi lần thanh(tối đa 40 ký tự) toán
	private $vpc_OrderInfo = '';//id hóa đơn - (tối đa 34 ký tự)
	private $vpc_ReturnURL = '';//Url nhận kết quả trả về sau khi giao dịch hoàn thành.
	private $Title = '';
	private $vpc_Command = 'pay';//Loại request (cố định)
	private $vpc_CreateDate = '';
	private $vpc_Locale = 'vn';//Ngôn ngữ hiện thị trên cổng (vn/en)
	private $vpc_TicketNo = '';//IP khách hàng
	private $vpc_UserAgent = '';
	private $vpc_SHIP_Street01 = '';//Địa chỉ gửi hàng
	private $vpc_SHIP_Provice = '';//Quận Huyện(địa chỉ gửi hàng) Thu Duc
	private $vpc_SHIP_City = '';//Tỉnh/thành phố (địa chỉ khách hàng) Ho Chi Minh
	private $vpc_SHIP_Country = '';//Quốc gia(địa chỉ khách hàng) VN
	private $vpc_Customer_Phone = '';//Số điện thoại khách hàng
	private $vpc_Customer_Email = '';//Địa chỉ hòm thư của khách hàng
	private $vpc_Customer_Id = '';//Tên tài khoản khách hàng trên hệ thống thanhvt
	private $vpc_Amount = 0;//Số tiền cần thanh toán,Chua được nhân với 100
	private $vpc_Currency = 'VND';//Loại tiền tệ VND
	private $vpc_HashSecret = '';
	private $vpc_SecureHashType = 'SHA256';

	private $AgainLink = '';
	private $AVS_Street01 = '';//Địa chỉ Phát Ngân Hàng phát hành
	private $AVS_City = '';//Thành phố Ngân hàng phát hành
	private $AVS_StateProv = '';//Quận Huyện ngân hàng phát hành
	private $AVS_PostCode = '';//Mã vùng ngân hàng phát hành
	private $AVS_Country = '';//Country
	private $display = '';//Hiện thị trên thiết bị Desktop hay Mobile , macdinh la rong, mobile la mobile

	private $is_local = TRUE;//là thẻ nội địa

	public function __construct( $data = array() ) {
		$this->setVpcTicketNo( $_SERVER['REMOTE_ADDR'] );
		$this->setVpcUserAgent( $_SERVER['HTTP_USER_AGENT'] );
		$this->setVpcCreateDate( date('YmdHis') );
	}

	private function getVersion() {
		return self::vpc_Version;
	}

	private function getVnpUrl() {
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

	public function setVpcMerchant($vpc_Merchant) {
		$this->vpc_Merchant = $vpc_Merchant;
		return $this;
	}

	private function getVpcMerchant() {
		return $this->vpc_Merchant;
	}

	public function setVpcAccessCode($vpc_AccessCode) {
		$this->vpc_AccessCode = $vpc_AccessCode;
		return $this;
	}

	private function getVpcAccessCode() {
		return $this->vpc_AccessCode;
	}

	public function setVpcMerchTxnRef($vpc_MerchTxnRef) {
		$this->vpc_MerchTxnRef = $vpc_MerchTxnRef;
		return $this;
	}

	private function getVpcMerchTxnRef() {
		return $this->vpc_MerchTxnRef;
	}

	public function setVpcOrderInfo($vpc_OrderInfo) {
		$this->vpc_OrderInfo = $vpc_OrderInfo;
		return $this;
	}

	private function getVpcOrderInfo() {
		return $this->vpc_OrderInfo;
	}

	public function setVpcReturnURL($vpc_ReturnURL) {
		$this->vpc_ReturnURL = $vpc_ReturnURL;
		return $this;
	}

	private function getVpcReturnURL() {
		return $this->vpc_ReturnURL;
	}

	public function setTitle($Title) {
		$this->Title = $Title;
		return $this;
	}

	private function getTitle() {
		return $this->Title;
	}

	public function setAgainLink($AgainLink) {
		$this->AgainLink = $AgainLink;
		return $this;
	}

	private function getAgainLink() {
		return $this->AgainLink;
	}

	public function setVpcCommand($vpc_Command) {
		$this->vpc_Command = $vpc_Command;
		return $this;
	}

	private function getVpcCommand() {
		return $this->vpc_Command;
	}

	public function setVpcCreateDate($vpc_CreateDate) {
		$this->vpc_CreateDate = $vpc_CreateDate;
		return $this;
	}

	private function getVpcCreateDate() {
		return $this->vpc_CreateDate;
	}

	public function setVpcLocale($vpc_Locale) {
		$this->vpc_Locale = $vpc_Locale;
		return $this;
	}

	private function getVpcLocale() {
		return $this->vpc_Locale;
	}

	public function setVpcTicketNo($vpc_TicketNo) {
		$this->vpc_TicketNo = $vpc_TicketNo;
		return $this;
	}

	private function getVpcTicketNo() {
		return $this->vpc_TicketNo;
	}

	public function setVpcUserAgent($vpc_UserAgent) {
		$this->vpc_UserAgent = $vpc_UserAgent;
		return $this;
	}

	private function getVpcUserAgent() {
		return $this->vpc_UserAgent;
	}

	public function setVpcSHIPStreet01($vpc_SHIP_Street01) {
		$this->vpc_SHIP_Street01 = $vpc_SHIP_Street01;
		return $this;
	}

	private function getVpcSHIPStreet01() {
		return $this->vpc_SHIP_Street01;
	}

	public function setVpcSHIPProvice($vpc_SHIP_Provice) {
		$this->vpc_SHIP_Provice = $vpc_SHIP_Provice;
		return $this;
	}

	private function getVpcSHIPProvice() {
		return $this->vpc_SHIP_Provice;
	}

	public function setVpcSHIPCity($vpc_SHIP_City) {
		$this->vpc_SHIP_City = $vpc_SHIP_City;
		return $this;
	}

	private function getVpcSHIPCity() {
		return $this->vpc_SHIP_City;
	}

	public function setVpcSHIPCountry($vpc_SHIP_Country) {
		$this->vpc_SHIP_Country = $vpc_SHIP_Country;
		return $this;
	}

	private function getVpcSHIPCountry() {
		return $this->vpc_SHIP_Country;
	}

	public function setVpcCustomerPhone($vpc_Customer_Phone) {
		$this->vpc_Customer_Phone = $vpc_Customer_Phone;
		return $this;
	}

	private function getVpcCustomerPhone() {
		return $this->vpc_Customer_Phone;
	}

	public function setVpcCustomerEmail($vpc_Customer_Email) {
		$this->vpc_Customer_Email = $vpc_Customer_Email;
		return $this;
	}

	private function getVpcCustomerEmail() {
		return $this->vpc_Customer_Email;
	}

	public function setVpcCustomerId($vpc_Customer_Id) {
		$this->vpc_Customer_Id = $vpc_Customer_Id;
		return $this;
	}

	private function getVpcCustomerId() {
		return $this->vpc_Customer_Id;
	}

	public function setVpcAmount($vpc_Amount) {
		$this->vpc_Amount = $vpc_Amount;
		return $this;
	}

	private function getVpcAmount() {
		return $this->vpc_Amount;
	}

	public function setVpcCurrency($vpc_Currency) {
		$this->vpc_Currency = $vpc_Currency;
		return $this;
	}

	private function getVpcCurrency() {
		return $this->vpc_Currency;
	}

	public function setVpcHashSecret($vpc_HashSecret) {
		$this->vpc_HashSecret = $vpc_HashSecret;
		return $this;
	}

	private function getVpcHashSecret() {
		return $this->vpc_HashSecret;
	}

	public function setVpcSecureHashType($vpc_SecureHashType) {
		$this->vpc_SecureHashType = $vpc_SecureHashType;
		return $this;
	}

	private function getVpcSecureHashType() {
		return $this->vpc_SecureHashType;
	}

	private function bcryptHashSecret($str) {
		return strtoupper(hash_hmac('SHA256', $str, pack('H*',$this->getVpcHashSecret())));
		//return strtoupper(md5($this->getVpcHashSecret().$str));
	}

	public function setAVSStreet01($AVS_Street01) {
		$this->AVS_Street01 = $AVS_Street01;
		return $this;
	}

	private function getAVSStreet01() {
		return $this->AVS_Street01;
	}

	public function setAVSCity($AVS_City) {
		$this->AVS_City = $AVS_City;
		return $this;
	}

	private function getAVSCity() {
		return $this->AVS_City;
	}

	public function setAVSStateProv($AVS_StateProv) {
		$this->AVS_StateProv = $AVS_StateProv;
		return $this;
	}

	private function getAVSStateProv() {
		return $this->AVS_StateProv;
	}

	public function setAVSPostCode($AVS_PostCode) {
		$this->AVS_PostCode = $AVS_PostCode;
		return $this;
	}

	private function getAVSPostCode() {
		return $this->AVS_PostCode;
	}

	public function setAVSCountry($AVS_Country) {
		$this->AVS_Country = $AVS_Country;
		return $this;
	}

	private function getAVSCountry() {
		return $this->AVS_Country;
	}

	public function setIsLocal($is_local) {
		$this->is_local = $is_local;
		return $this;
	}

	private function getIsLocal() {
		return $this->is_local;
	}

	public function setDisplay($display) {
		$this->display = $display;
		return $this;
	}

	private function getDisplay() {
		return $this->display;
	}
    
    /*$onepay = new Onepay( );
    $onepay->setIsSandbox( TRUE );
    $onepay->setVpcMerchant( 'ONEPAY' );
    $onepay->setVpcAccessCode( 'D67342C2' );
    $onepay->setVpcHashSecret( 'A3EFDFABA8653DF2342E8DAC29B51AF0' );

    $onepay->setVpcMerchTxnRef( date ( 'YmdHis' ) );
    $onepay->setVpcOrderInfo( 'JSECURETEST01' );
    $onepay->setVpcReturnURL( $this->baseUrl .'/cart02/auth' );
    $onepay->setVpcCommand( 'pay' );
    $onepay->setVpcLocale( 'vn' );
    //$onepay->setVpcTicketNo( '' );
    $onepay->setVpcSHIPStreet01( '39A Ngo Quyen' );
    $onepay->setVpcSHIPProvice( 'Hoan Kiem' );
    $onepay->setVpcSHIPCity( 'Ha Noi' );
    $onepay->setVpcSHIPCountry( 'Viet Nam' );
    $onepay->setVpcCustomerPhone( '840904280949' );
    $onepay->setVpcCustomerEmail( 'support@onepay.vn' );
    $onepay->setVpcCustomerId( 'thanhvt' );
    $onepay->setVpcAmount( 30000 );
    $onepay->setVpcCurrency( 'VND' );
    $onepay->setTitle( 'VPC 3-Party' );
    $onepay->setAgainLink( 'onepay.vn' );
    $url = $onepay->getUrlPay();
    */
	public function getUrlPay() {

		$row = array(
			"Title" => $this->getTitle(),
		    "vpc_Merchant" => $this->getVpcMerchant(),
		    "vpc_AccessCode" => $this->getVpcAccessCode(),
		    "vpc_MerchTxnRef" => $this->getVpcMerchTxnRef(),
		    "vpc_OrderInfo" => $this->getVpcOrderInfo(),
		    "vpc_Amount" => $this->getVpcAmount(),
		    "vpc_ReturnURL" => $this->getVpcReturnURL(),
		    "vpc_Version" => $this->getVersion(),
		    "vpc_Command" => $this->getVpcCommand(),
		    "vpc_Locale" => $this->getVpcLocale(),

		    "vpc_TicketNo" => $this->getVpcTicketNo(),
		    "vpc_SHIP_Street01" => $this->getVpcSHIPStreet01(),
		    "vpc_SHIP_Provice" => $this->getVpcSHIPProvice(),
		    "vpc_SHIP_City" => $this->getVpcSHIPCity(),
		    "vpc_SHIP_Country" => $this->getVpcSHIPCountry(),
		    "vpc_Customer_Phone" => $this->getVpcCustomerPhone(),
		    "vpc_Customer_Email" => $this->getVpcCustomerEmail(),
		    "vpc_Customer_Id" => $this->getVpcCustomerId(),
		);
		if( $this->getIsLocal() ){
			$row['vpc_Currency'] = $this->getVpcCurrency();
		}else{
			$row['AVS_Street01'] = $this->getAVSStreet01();
			$row['AVS_City'] = $this->getAVSCity();
			$row['AVS_StateProv'] = $this->getAVSStateProv();
			$row['AVS_PostCode'] = $this->getAVSPostCode();
			$row['AVS_Country'] = $this->getAVSCountry();
			$row['display'] = $this->getDisplay();
			$row['AgainLink'] = $this->getAgainLink();
		}
		ksort($row);
		$query = '';
		$hashdata = '';
		foreach ($row as $key => $value) {
			if (strlen($value) > 0) {
				if ( 	(substr($key, 0,4)=="vpc_") 
						|| (substr($key,0,5) =="user_") ) {
				    $hashdata 	.= (!empty($hashdata) ? '&' : '' ) . $key . "=" . $value;
				}
				//$hashdata 	.= $value;
			    $query 		.= (!empty($query) ? '&' : '' ) . urlencode($key) . "=" . urlencode($value);
			}
		}
		$url = '';
		if( !empty($query) ){
			$url = $this->getVnpUrl() . "?" . $query;
			if ( !empty($this->getVpcHashSecret()) ) {
			    $vpcSecureHash = $this->bcryptHashSecret( $hashdata );
			    //$url .= '&vpc_SecureHashType='.$this->getVpcSecureHashType().'&vpc_SecureHash=' . $vpcSecureHash;
			    $url .= '&vpc_SecureHash=' . $vpcSecureHash;
			}
		}
		return $url;
	}

}
