<?php

namespace PHPShipchung;

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

class Shipchung {

	const 	version = '1.0';
	const 	url_auth = 'http://services.shipchung.vn/api/merchant/rest/lading/auth';
	const 	url_calculate = 'http://services.shipchung.vn/api/rest/courier/calculate';
	const 	url_create = 'http://services.shipchung.vn/api/rest/courier/create';
	const 	url_accept = 'http://services.shipchung.vn/api/merchant/rest/lading/accept';
	const 	url_order_status = 'http://services.shipchung.vn/api/rest/lading/order-status';
	const 	url_city = 'http://services.shipchung.vn/api/merchant/rest/lading/city';
	const 	url_province = 'http://services.shipchung.vn/api/merchant/rest/lading/province/';
	const 	url_ward = 'http://services.shipchung.vn/api/merchant/rest/lading/ward/';
	const 	url_detail = 'http://services.shipchung.vn/api/merchant/rest/lading/detail';
	const 	url_status = 'http://services.shipchung.vn/api/merchant/rest/lading/status';
	const 	url_inventory = 'http://services.shipchung.vn/api/merchant/rest/lading/inventory';
	const 	url_create_inventory = 'http://services.shipchung.vn/api/merchant/rest/lading/create-inventory';
	const 	url_edit_inventory = 'http://services.shipchung.vn/api/merchant/rest/lading/edit-inventory';
	const 	url_cancel = 'http://services.shipchung.vn/api/rest/lading/cancel';
	private $is_sandbox = TRUE;

	private $MerchantKey = '';
	private $TrackingCode = '';
	private $CityID = '';
	private $ProvinceId = '';

	private $name = '';
	private $user_name = '';
	private $phone = '';
	private $ward_id = '';
	private $address = '';

	private $id = '';
	private $active = '';

	public function __construct( $data = array() ) {
	}

	private function getVersion() {
		return self::version;
	}

	private function getUrlAuth() {
		return self::url_auth;
	}

	private function getUrlCalculate() {
		return self::url_calculate;
	}

	private function getUrlCreate() {
		return self::url_create;
	}

	private function getUrlAccept() {
		return self::url_accept;
	}

	private function getUrlOrderStatus() {
		return self::url_order_status;
	}

	private function getUrlCity() {
		return self::url_city;
	}

	private function getUrlProvince() {
		return self::url_province;
	}

	private function getUrlWard() {
		return self::url_ward;
	}

	private function getUrlDetail() {
		return self::url_detail;
	}

	private function getUrlStatus() {
		return self::url_status;
	}

	private function getUrlInventory() {
		return self::url_inventory;
	}

	private function getUrlInventory() {
		return self::url_inventory;
	}

	private function getUrlCreateInventory() {
		return self::url_create_inventory;
	}

	private function getUrlEditInventory() {
		return self::url_edit_inventory;
	}

	private function getUrlCancel() {
		return self::url_cancel;
	}

	public function setIsSandbox($is_sandbox) {
		$this->is_sandbox = $is_sandbox;
		return $this;
	}

	private function getIsSandbox() {
		return $this->is_sandbox;
	}

	public function setMerchantKey($MerchantKey) {
		$this->MerchantKey = $MerchantKey;
		return $this;
	}

	private function getMerchantKey() {
		return $this->MerchantKey;
	}

	public function setTrackingCode($TrackingCode) {
		$this->TrackingCode = $TrackingCode;
		return $this;
	}

	private function getTrackingCode() {
		return $this->TrackingCode;
	}

	public function setCityID($CityID) {
		$this->CityID = $CityID;
		return $this;
	}

	private function getCityID() {
		return $this->CityID;
	}

	public function setProvinceId($ProvinceId) {
		$this->ProvinceId = $ProvinceId;
		return $this;
	}

	private function getProvinceId() {
		return $this->ProvinceId;
	}

	public function setWardId($ward_id) {
		$this->ward_id = $ward_id;
		return $this;
	}

	private function getWardId() {
		return $this->ward_id;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	private function getName() {
		return $this->name;
	}

	public function setUserName($user_name) {
		$this->user_name = $user_name;
		return $this;
	}

	private function getUserName() {
		return $this->user_name;
	}

	public function setPhone($phone) {
		$this->phone = $phone;
		return $this;
	}

	private function getPhone() {
		return $this->phone;
	}

	public function setAddress($address) {
		$this->address = $address;
		return $this;
	}

	private function getAddress() {
		return $this->address;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	private function getId() {
		return $this->id;
	}

	public function setActive($active) {
		$this->active = $active;
		return $this;
	}

	private function getActive() {
		return $this->active;
	}

	public function auth() {
		$body = array();
		if( !empty($this->getMerchantKey()) ){
			$Params = array(
					'MerchantKey'=>$this->getMerchantKey()
				);
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlAuth());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($CurlStart, CURLOPT_POSTFIELDS, http_build_query($Params));
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

	public function calculate ( $Params ) {
		$body = array();
		if( !empty($Params) ){
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlCalculate());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($CurlStart, CURLOPT_POSTFIELDS, http_build_query($Params));
			curl_setopt($CurlStart, CURLOPT_POST, true);
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

	public function create ( $Params ) {
		$body = array();
		if( !empty($Params) ){
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlCreate());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($CurlStart, CURLOPT_POSTFIELDS, http_build_query($Params));
			curl_setopt($CurlStart, CURLOPT_POST, true);
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

	public function accept ( ) {
		$body = array();
		if( !empty($this->getMerchantKey()) 
			&& !empty($this->getTrackingCode()) ){
			$Params = array(
					'MerchantKey'=>$this->getMerchantKey(),
					'TrackingCode'=>$this->getTrackingCode(),
				);
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlAccept());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($CurlStart, CURLOPT_POSTFIELDS, http_build_query($Params));
			curl_setopt($CurlStart, CURLOPT_POST, true);
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

	public function orderStatus ( ) {
		$body = array();
		if( !empty($this->getTrackingCode()) ){
			$Params = array(
					'tracking_code'=>$this->getTrackingCode(),
				);
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlOrderStatus());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($CurlStart, CURLOPT_POSTFIELDS, http_build_query($Params));
			curl_setopt($CurlStart, CURLOPT_POST, true);
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

	public function city ( ) {
		$body = array();
		$CurlStart = curl_init();
		curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlCity());
		curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
		curl_setopt ($CurlStart, CURLOPT_HEADER, false);
		curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
		$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
		$source = curl_exec ($CurlStart);
		$header = substr($source, 0, $header_size);
		$body = substr($source, $header_size);
		curl_close ($CurlStart);
		return $body;
	}

	public function province ( ) {
		$body = array();
		if( !empty($this->getCityID()) ){
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlProvince().'/'.$this->getCityID());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

	public function ward ( ) {
		$body = array();
		if( !empty($this->getProvinceId()) ){
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlWard().'/'.$this->getProvinceId());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

	public function detail ( ) {
		$body = array();
		if( !empty($this->getMerchantKey()) 
			&& !empty($this->getTrackingCode()) ){
			$Params = array(
					'MerchantKey'=>$this->getMerchantKey(),
					'TrackingCode'=>$this->getTrackingCode(),
				);
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlDetail());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($CurlStart, CURLOPT_POSTFIELDS, http_build_query($Params));
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

	public function status ( ) {
		$body = array();
		$CurlStart = curl_init();
		curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlStatus());
		curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
		curl_setopt ($CurlStart, CURLOPT_HEADER, false);
		curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
		$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
		$source = curl_exec ($CurlStart);
		$header = substr($source, 0, $header_size);
		$body = substr($source, $header_size);
		curl_close ($CurlStart);
		return $body;
	}

	public function inventory ( ) {
		$body = array();
		if( !empty($this->getMerchantKey()) ){
			$Params = array(
					'MerchantKey'=>$this->getMerchantKey()
				);
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlInventory());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($CurlStart, CURLOPT_POSTFIELDS, http_build_query($Params));
			curl_setopt($CurlStart, CURLOPT_POST, true);
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

	public function createInventory ( ) {
		$body = array();
		if( !empty($this->getMerchantKey()) && !empty($this->getName())
			&& !empty($this->getUserName()) && !empty($this->getPhone())
			&& !empty($this->getCityID()) && !empty($this->getProvinceId())
			&& !empty($this->getWardId()) && !empty($this->getAddress()) ){
			$Params = array(
					'MerchantKey'=>$this->getMerchantKey(),
					'name'=>$this->getName(),
					'user_name'=>$this->getUserName(),
					'phone'=>$this->getPhone(),
					'city_id'=>$this->getCityID(),
					'province_id'=>$this->getProvinceId(),
					'ward_id'=>$this->getWardId(),
					'address'=>$this->getAddress(),
				);
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlCreateInventory());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($CurlStart, CURLOPT_POSTFIELDS, http_build_query($Params));
			curl_setopt($CurlStart, CURLOPT_POST, true);
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

	public function editInventory ( ) {
		$body = array();
		if( !empty($this->getMerchantKey()) && !empty($this->getName())
			&& !empty($this->getUserName()) && !empty($this->getPhone())
			&& !empty($this->getId()) ){
			$Params = array(
					'MerchantKey'=>$this->getMerchantKey(),
					'name'=>$this->getName(),
					'user_name'=>$this->getUserName(),
					'phone'=>$this->getPhone(),
					'id'=>$this->getId(),
					'active'=>$this->getActive(),
				);
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlEditInventory());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($CurlStart, CURLOPT_POSTFIELDS, http_build_query($Params));
			curl_setopt($CurlStart, CURLOPT_POST, true);
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

	public function cancel ( ) {
		$body = array();
		if( !empty($this->getMerchantKey()) && !empty($this->getTrackingCode()) ){
			$Params = array(
					'MerchantKey'=>$this->getMerchantKey(),
					'TrackingCode'=>$this->getTrackingCode(),
				);
			$CurlStart = curl_init();
			curl_setopt ($CurlStart, CURLOPT_URL, $this->getUrlCancel());
			curl_setopt ($CurlStart, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($CurlStart, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; nl; rv:1.9.1.11) Gecko/20100701 Firefox/3.5.11");
			curl_setopt ($CurlStart, CURLOPT_HEADER, false);
			curl_setopt ($CurlStart, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($CurlStart, CURLOPT_POSTFIELDS, http_build_query($Params));
			curl_setopt($CurlStart, CURLOPT_POST, true);
			$header_size = curl_getinfo($CurlStart, CURLINFO_HEADER_SIZE);
			$source = curl_exec ($CurlStart);
			$header = substr($source, 0, $header_size);
			$body = substr($source, $header_size);
			curl_close ($CurlStart);
		}
		return $body;
	}

}
