<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Invoice extends App
{
	public function getAddress( $invoice , $type = 0){
		$str = '';
		if( !empty($invoice) ){
			if( !is_array($invoice) ){
				$invoice = (array)$invoice;
			}
			if( empty($type) ){
				$str = $invoice['address'];
				$contry = $this->view->Contries()->getContry($invoice['country_id']);
				if( !empty($contry) ){
					switch ($contry->country_type) {
						case 0:
							$str .= ','.$invoice['city'].'('.$invoice['zipcode'].')'.','.$contry->title;
							break;

						case 1:
							$str .= ','.$invoice['city'].','.$invoice['state'].'('.$invoice['zipcode'].')'.','.$contry->title;
							break;

						case 2:
							$city = $this->view->Cities()->getCity($invoice['state']);
							if( !empty($city) ){
								$str .= ','.$invoice['city'].'('.$invoice['zipcode'].')'.','.$city->cities_title.','.$contry->title;
							}
							break;

						case 3:
							$str .= ','.$invoice['suburb'].'('.$invoice['zipcode'].')'.','.$contry->title;
							break;

						case 4:
							$str .= ','.$invoice['city'].','.$invoice['state'].','.$contry->title;
							break;

						case 5:
							$city = $this->view->Cities()->getCity($invoice['region']);
							if( !empty($city) ){
								$str .= ','.$invoice['city'].'('.$invoice['zipcode'].')'.','.$city->cities_title.','.$contry->title;
							}
							break;

						case 6:
							$city = $this->view->Cities()->getCity($invoice['province']);
							if( !empty($city) ){
								$str .= ','.$invoice['city'].'('.$invoice['zipcode'].')'.','.$city->cities_title.','.$contry->title;
							}
							break;

						case 7:
							$city = $this->view->Cities()->getCity($invoice['cities_id']);
							$district = $this->view->Districts()->getDistrict($invoice['districts_id']);
							$ward = $this->view->Wards()->getWard($invoice['wards_id']);
							if( !empty($ward) ){
								$str .= ','.$ward->wards_title;
							}
							if( !empty($district) ){
								$str .= ','.$district->districts_title;
							}
							if( !empty($city) ){
								$str .= ','.$city->cities_title;
							}
							break;
						
						default:
							$str = $invoice['address'];
							break;
					}
				}
			}else{
				$str = $invoice['ships_address'];
				$contry = $this->view->Contries()->getContry($invoice['ships_country_id']);
				if( !empty($contry) ){
					switch ($contry->country_type) {
						case 0:
							$str .= ','.$invoice['ships_city'].'('.$invoice['ships_zipcode'].')'.','.$contry->title;
							break;

						case 1:
							$str .= ','.$invoice['ships_city'].','.$invoice['ships_state'].'('.$invoice['ships_zipcode'].')'.','.$contry->title;
							break;

						case 2:
							$city = $this->view->Cities()->getCity($invoice['ships_state']);
							if( !empty($city) ){
								$str .= ','.$invoice['ships_city'].'('.$invoice['ships_zipcode'].')'.','.$city->cities_title.','.$contry->title;
							}
							break;

						case 3:
							$str .= ','.$invoice['ships_suburb'].'('.$invoice['ships_zipcode'].')'.','.$contry->title;
							break;

						case 4:
							$str .= ','.$invoice['ships_city'].','.$invoice['ships_state'].','.$contry->title;
							break;

						case 5:
							$city = $this->view->Cities()->getCity($invoice['ships_region']);
							if( !empty($city) ){
								$str .= ','.$invoice['ships_city'].'('.$invoice['ships_zipcode'].')'.','.$city->cities_title.','.$contry->title;
							}
							break;

						case 6:
							$city = $this->view->Cities()->getCity($invoice['ships_province']);
							if( !empty($city) ){
								$str .= ','.$invoice['ships_city'].'('.$invoice['ships_zipcode'].')'.','.$city->cities_title.','.$contry->title;
							}
							break;

						case 7:
							$city = $this->view->Cities()->getCity($invoice['ships_cities_id']);
							$district = $this->view->Districts()->getDistrict($invoice['ships_districts_id']);
							$ward = $this->view->Wards()->getWard($invoice['ships_wards_id']);
							if( !empty($ward) ){
								$str .= ','.$ward->wards_title;
							}
							if( !empty($district) ){
								$str .= ','.$district->districts_title;
							}
							if( !empty($city) ){
								$str .= ','.$city->cities_title;
							}
							break;
						
						default:
							$str = $invoice['address'];
							break;
					}
				}
			}
		}
		return $str;
	}

	public function getProvice( $invoice , $type = 0){
		$str = '';
		if( !empty($invoice) ){
			if( !is_array($invoice) ){
				$invoice = (array)$invoice;
			}
			if( empty($type) ){
				$contry = $this->view->Contries()->getContry($invoice['country_id']);
				if( !empty($contry) ){
					switch ($contry->country_type) {
						case 0:
							$str = $invoice['city'];
							break;

						case 1:
							$str = $invoice['city'];
							break;

						case 2:
							$str = $invoice['city'];
							break;

						case 3:
							$str = $invoice['suburb'];
							break;

						case 4:
							$str = $invoice['city'];
							break;

						case 5:
							$str = $invoice['city'];
							break;

						case 6:
							$str =  $invoice['city'];
							break;

						case 7:
							$district = $this->view->Districts()->getDistrict($invoice['districts_id']);
							$str = $district->districts_title;
							break;
						
						default:
							$str = '';
							break;
					}
				}
			}else{
				$contry = $this->view->Contries()->getContry($invoice['ships_country_id']);
				if( !empty($contry) ){
					switch ($contry->country_type) {
						case 0:
							$str = $invoice['ships_city'];
							break;

						case 1:
							$str = $invoice['ships_city'];
							break;

						case 2:
							$str = $invoice['ships_city'];
							break;

						case 3:
							$str = $invoice['ships_suburb'];
							break;

						case 4:
							$str = $invoice['ships_city'];
							break;

						case 5:
							$str = $invoice['ships_city'];
							break;

						case 6:
							$str =  $invoice['ships_city'];
							break;

						case 7:
							$district = $this->view->Districts()->getDistrict($invoice['ships_districts_id']);
							$str = $district->districts_title;
							break;
						
						default:
							$str = '';
							break;
					}
				}
			}
		}
		return $str;
	}

	public function getCity( $invoice , $type = 0){
		$str = '';
		if( !empty($invoice) ){
			if( !is_array($invoice) ){
				$invoice = (array)$invoice;
			}
			if( empty($type) ){
				$contry = $this->view->Contries()->getContry($invoice['country_id']);
				if( !empty($contry) ){
					switch ($contry->country_type) {
						case 0:
							$str = $invoice['city'];
							break;

						case 1:
							$str = $invoice['state'];
							break;

						case 2:
							$city = $this->view->Cities()->getCity($invoice['state']);
							$str = $city->cities_title;
							break;

						case 3:
							$str = $invoice['suburb'];
							break;

						case 4:
							$str = $invoice['state'];
							break;

						case 5:
							$city = $this->view->Cities()->getCity($invoice['region']);
							$str = $city->cities_title;
							break;

						case 6:
							$city = $this->view->Cities()->getCity($invoice['province']);
							$str = $city->cities_title;
							break;

						case 7:
							$city = $this->view->Cities()->getCity($invoice['cities_id']);
							$str = $city->cities_title;
							break;
						
						default:
							$str = '';
							break;
					}
				}
			}else{
				$contry = $this->view->Contries()->getContry($invoice['ships_country_id']);
				if( !empty($contry) ){
					switch ($contry->country_type) {
						case 0:
							$str = $invoice['ships_city'];
							break;

						case 1:
							$str = $invoice['ships_state'];
							break;

						case 2:
							$city = $this->view->Cities()->getCity($invoice['ships_state']);
							$str = $city->cities_title;
							break;

						case 3:
							$str = $invoice['ships_suburb'];
							break;

						case 4:
							$str = $invoice['ships_state'];
							break;

						case 5:
							$city = $this->view->Cities()->getCity($invoice['ships_region']);
							$str = $city->cities_title;
							break;

						case 6:
							$city = $this->view->Cities()->getCity($invoice['ships_province']);
							$str = $city->cities_title;
							break;

						case 7:
							$city = $this->view->Cities()->getCity($invoice['ships_cities_id']);
							$str = $city->cities_title;
							break;
						
						default:
							$str = '';
							break;
					}
				}
			}
		}
		return $str;
	}


}
