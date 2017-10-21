<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 9:27 AM
 */

namespace Application\Controller;


use Application\Model\User;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Filter\File\LowerCase;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

use PHPVnpay\Vnpay;
use PHPOnepay\Onepay;
use PHPPaypal\Paypal;

class CheckoutController extends FrontEndController
{
    private $version = '02';
    private $currencyPaypalAccept = array(
            'AUD','BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK', 'NZD', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'USD'
        );
    
    public function indexAction()
    {
        if ( $this->getVersionCart() != $this->version ) {
            return $this->redirect()->toRoute( $this->getUrlRouterLang().$this->getRouteCart() , array(
                'action' => 'index'
            ));
        }

        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle('Cart');
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $calculate = $helper->sumSubTotalPriceInCart();
        $price_total = $calculate['price_total'];
        $price_total_old = $calculate['price_total_old'];

        $this->has_header = FALSE;
        $this->has_footer = TRUE;
        $this->setDataView('has_header', $this->has_header);
        $this->setDataView('has_footer', $this->has_footer);
        $this->data_view['price_total'] = $price_total;
        $this->data_view['price_total_old'] = $price_total_old;
        return $this->data_view;
    }

    public function addressAction()
    {
        if ( $this->getVersionCart() != $this->version ) {
            return $this->redirect()->toRoute( $this->getUrlRouterLang().$this->getRouteCart() , array(
                'action' => 'index'
            ));
        }

        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle('Cart');
        $cartHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');

        $cart = $cartHelper->getCart();
        if( empty($cart) ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }

        $error = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $trans = $request->getPost("trans");
            $ships = $request->getPost("ships");
            if ( empty($trans['full_name']) ) {
                $error['full_name'] = $translator->translate('txt_ban_phai_nhap_ten');
            }
            if ( empty($trans['email']) || !filter_var($trans['email'], FILTER_VALIDATE_EMAIL)) {
                $error['email'] = $translator->translate('txt_email_khong_hop_le');
            }
            if ( empty($trans['phone']) || !is_numeric($trans['phone'])) {
                $error['phone'] = $translator->translate('txt_so_dien_thoai_khong_hop_le');
            }
            $has_ship = 0;
            if ( empty($trans['has_ship']) ) {
                $has_ship = 1;
                if ( empty($trans['country_id']) ) {
                    $error['country_id'] = $translator->translate('txt_chua_chon_contry');
                }else{
                    $country = $this->getModelTable('CountryTable')->getOne($trans['country_id']);
                    $err = $this->validateInputContryPayment($trans, $country);
                    $error = array_merge($error, $err);
                }
            }else{
                $has_ship = 1;
            }

            $ship_to_different_address = 0;
            if ( !empty($trans['ship_to_different_address']) ) {
                $ship_to_different_address = 1;
                if ( empty($ships['full_name']) ) {
                    $error['full_name'] = $translator->translate('txt_ban_phai_nhap_ten');
                }
                if ( empty($ships['country_id']) ) {
                    $error['ship_country_id'] = $translator->translate('txt_chua_chon_contry');
                }else{
                    $country = $this->getModelTable('CountryTable')->getOne($ships['country_id']);
                    $err = $this->validateInputContryPayment($ships, $country);
                    $error = array_merge($error, $err);
                }
            }

            if ( !empty($trans['has_ship']) && 
                (empty($trans['shipping_id']) && $trans['shipping_id'] != 0) ) {
                $error['shipping_id'] = $translator->translate('txt_chua_chon_transportation');
            }

            $buyer = array();
            $buyer['has_ship'] = $has_ship;
            $buyer['full_name'] = $trans['full_name'];
            $buyer['phone'] = $trans['phone'];
            $buyer['email'] = $trans['email'];
            $buyer['type_address_delivery'] = $trans['type_address_delivery'];
            if ( empty($has_ship) ) {
                $buyer['country_id'] = $trans['country_id'];
                $buyer['address'] = $trans['address'];
                $buyer['address01'] = empty($trans['address01']) ? '' : $trans['address01'];
                $buyer['city'] = empty($trans['city']) ? '' : $trans['city'];
                $buyer['state'] = empty($trans['state']) ? '' : $trans['state'];
                $buyer['suburb'] = empty($trans['suburb']) ? '' : $trans['suburb'];
                $buyer['region'] = empty($trans['region']) ? '' : $trans['region'];
                $buyer['province'] = empty($trans['province']) ? '' : $trans['province'];
                $buyer['zipcode'] = empty($trans['zipcode']) ? '' : $trans['zipcode'];
                $buyer['cities_id'] = empty($trans['cities_id']) ? 0 : $trans['cities_id'];
                $buyer['districts_id'] = empty($trans['districts_id']) ? 0 : $trans['districts_id'];
                $buyer['wards_id'] = empty($trans['wards_id']) ? 0 : $trans['wards_id'];
            }else{
                $buyer['country_id'] = $ships['country_id'];
                $buyer['address'] = $ships['address'];
                $buyer['address01'] = empty($ships['address01']) ? '' : $ships['address01'];
                $buyer['city'] = empty($ships['city']) ? '' : $ships['city'];
                $buyer['state'] = empty($ships['state']) ? '' : $ships['state'];
                $buyer['suburb'] = empty($ships['suburb']) ? '' : $ships['suburb'];
                $buyer['region'] = empty($ships['region']) ? '' : $ships['region'];
                $buyer['province'] = empty($ships['province']) ? '' : $ships['province'];
                $buyer['zipcode'] = empty($ships['zipcode']) ? '' : $ships['zipcode'];
                $buyer['cities_id'] = empty($ships['cities_id']) ? 0 : $ships['cities_id'];
                $buyer['districts_id'] = empty($ships['districts_id']) ? 0 : $ships['districts_id'];
                $buyer['wards_id'] = empty($ships['wards_id']) ? 0 : $ships['wards_id'];
            }
            $buyer['users_id'] = (!empty($_SESSION['MEMBER']['users_id'])) ? $_SESSION['MEMBER']['users_id'] : NULL;
            $buyer['invoice_description'] = empty($trans['invoice_description']) ? '' : strip_tags($trans['invoice_description']);

            $shipper = array();
            if ( !empty($ship_to_different_address) ) {
                $ship_to_different_address = 1;
                $shipper = array();
                $shipper['ships_full_name'] = $ships['full_name'];
                $shipper['ships_email'] = $trans['email'];
                $shipper['ships_phone'] = $trans['phone'];
                $shipper['ships_country_id'] = $ships['country_id'];
                $shipper['ships_address'] = $ships['address'];
                $shipper['ships_address01'] = empty($ships['address01']) ? '' : $ships['address01'];
                $shipper['ships_city'] = empty($ships['city']) ? '' : $ships['city'];
                $shipper['ships_state'] = empty($ships['state']) ? '' : $ships['state'];
                $shipper['ships_suburb'] = empty($ships['suburb']) ? '' : $ships['suburb'];
                $shipper['ships_region'] = empty($ships['region']) ? '' : $ships['region'];
                $shipper['ships_province'] = empty($ships['province']) ? '' : $ships['province'];
                $shipper['ships_zipcode'] = empty($ships['zipcode']) ? '' : $ships['zipcode'];
                $shipper['ships_cities_id'] = empty($ships['cities_id']) ? 0 : $ships['cities_id'];
                $shipper['ships_districts_id'] = empty($ships['districts_id']) ? 0 : $ships['districts_id'];
                $shipper['ships_wards_id'] = empty($ships['wards_id']) ? 0 : $ships['wards_id'];
            }else{
                $shipper = array();
                $shipper['ships_full_name'] = $buyer['full_name'];
                $shipper['ships_email'] = $buyer['email'];
                $shipper['ships_phone'] = $buyer['phone'];
                $shipper['ships_country_id'] = $buyer['country_id'];
                $shipper['ships_address'] = $buyer['address'];
                $shipper['ships_address01'] = empty($buyer['address01']) ? '' : $buyer['address01'];
                $shipper['ships_city'] = empty($buyer['city']) ? '' : $buyer['city'];
                $shipper['ships_state'] = empty($buyer['state']) ? '' : $buyer['state'];
                $shipper['ships_suburb'] = empty($buyer['suburb']) ? '' : $buyer['suburb'];
                $shipper['ships_region'] = empty($buyer['region']) ? '' : $buyer['region'];
                $shipper['ships_province'] = empty($buyer['province']) ? '' : $buyer['province'];
                $shipper['ships_zipcode'] = empty($buyer['zipcode']) ? '' : $buyer['zipcode'];
                $shipper['ships_cities_id'] = empty($buyer['cities_id']) ? 0 : $buyer['cities_id'];
                $shipper['ships_districts_id'] = empty($buyer['districts_id']) ? 0 : $buyer['districts_id'];
                $shipper['ships_wards_id'] = empty($buyer['wards_id']) ? 0 : $buyer['wards_id'];
            }

            $shipping_id = 0;
            $transport_type = 0;
            $fee = 0;
            if ( !empty($has_ship) ) {
                $country_id = 0;
                if( !empty($shipper['ships_country_id']) ){
                    $country_id = $shipper['ships_country_id'];
                }
                $cities_id = 0;
                $districts_id = 0;
                if( !empty($trans['transport_type']) ){
                    $transport_type = $trans['transport_type'];
                }
                if( !empty($trans['shipping_id']) ){
                    $shipping_id = $trans['shipping_id'];
                }

                $country = $this->getModelTable('CountryTable')->getOne($country_id);
                if( empty($country) ){
                    $error['country_id'] = $translator->translate('txt_chua_chon_contry');
                }else{
                    if( $country->country_type == 2 ){
                        $cities_id = $shipper['ships_state'];
                    }else if( $country->country_type == 5 ){
                        $cities_id = $shipper['ships_region'];
                    }else if( $country->country_type == 6 ){
                        $cities_id = $shipper['ships_province'];
                    }else if( $country->country_type == 7 ){
                        $cities_id = $shipper['ships_cities_id'];
                        $districts_id = $shipper['ships_districts_id'];
                    }
                }

                $cities = $this->getModelTable('CitiesTable')->getCitiesOfCountry($country_id);
                if( empty($cities) ){
                    $shipping = $this->getModelTable('ShippingTable')->getShippingWithCountry($shipping_id, $country_id);
                }else{
                    $city = $this->getModelTable('CitiesTable')->getCityOfCountry($cities_id, $country_id);
                    if( !empty($city) ){
                        $shipping = $this->getModelTable('ShippingTable')->getShippingWithCityAndDistricts($shipping_id, $cities_id, $districts_id);
                    }
                }

                if( !empty($shipping) ){
                    if( empty($cities) ){
                        $fee = $this->getFeeShip($shipping, $transport_type);
                        if( empty($fee) ){
                            $is_free = TRUE;
                        }
                    }else{
                        if( !($shipping['no_shipping'] == 1 
                            && !empty($districts_id) && $shipping['districts_id'] == $districts_id)
                            && $this->isAvaiableShip($shipping) ){
                            $isFreeShip = $this->isFreeShip($shipping);
                            $fee = $this->getFeeShip($shipping, $transport_type);
                            if( empty($fee) ){
                                $is_free = TRUE;
                            }
                        }else{
                            $no_shipping = TRUE;
                            $error['shipping_id'] = $translator->translate('txt_khong_van_chuyen');
                        }
                    }
                }else{
                    $lsShips = $this->getModelTable('ShippingTable')->getShippings();
                    if( empty($lsShips) ){
                        $is_free = TRUE;
                    }else{
                        $no_shipping = TRUE;
                        $error['shipping_id'] = $translator->translate('txt_khong_van_chuyen');
                    }
                }
            }

            $member = isset($_SESSION['MEMBER']) ? $_SESSION['MEMBER'] : array();
            
            if( empty($error) ){
                $buyer['shipping_id'] = $shipping_id;
                $buyer['transport_type'] = $transport_type;
                $buyer['ships_fee'] = $fee;
                $buyer['ship_to_different_address'] = $ship_to_different_address;
                $_SESSION['PAYMENT_BUYER'] = $buyer;
                $_SESSION['PAYMENT_SHIPPER'] = $shipper;
                return $this->redirect()->toRoute($this->getUrlRouterLang().'checkout', array(
                    'action' => 'payment'
                ));
            }
        }
        $shipper = (!empty($_SESSION['PAYMENT_SHIPPER']) ? $_SESSION['PAYMENT_SHIPPER'] : array());
        $buyer = (!empty($_SESSION['PAYMENT_BUYER']) ? $_SESSION['PAYMENT_BUYER'] : array());

        $shipping = array();
        $billing = array();
        if( $hPUser->hasLogin() ){
            $member = $hPUser->getMember();
            $shipping = $this->getModelTable('UserTable')->getShippingAddress($member['users_id']);
            $billing = $this->getModelTable('UserTable')->getBillingAddress($member['users_id']);
        }

        $this->data_view['shipping'] = $shipping;
        $this->data_view['billing'] = $billing;

        $this->has_header = FALSE;
        $this->has_footer = TRUE;
        $this->setDataView('has_header', $this->has_header);
        $this->setDataView('has_footer', $this->has_footer);
        $this->data_view['error'] = $error;
        $this->data_view['buyer'] = $buyer;
        $this->data_view['shipper'] = $shipper;
        return $this->data_view;
    }

    public function processAction()
    {
        return $this->redirect()->toRoute( $this->getUrlRouterLang().$this->getRouteCart() , array(
            'action' => 'address'
        ));
        if ( $this->getVersionCart() != $this->version ) {
            return $this->redirect()->toRoute( $this->getUrlRouterLang().$this->getRouteCart() , array(
                'action' => 'auth'
            ));
        }

        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle('Cart');
        $cartHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');

        $cart = $cartHelper->getCart();
        if( empty($cart) ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }
        $shipper = (!empty($_SESSION['PAYMENT_SHIPPER']) ? $_SESSION['PAYMENT_SHIPPER'] : array());
        $buyer = (!empty($_SESSION['PAYMENT_BUYER']) ? $_SESSION['PAYMENT_BUYER'] : array());
        $this->data_view['buyer'] = $buyer;
        $this->data_view['shipper'] = $shipper;
        return $this->data_view;
    }


    public function buyerAction()
    {
        return $this->redirect()->toRoute( $this->getUrlRouterLang().$this->getRouteCart() , array(
            'action' => 'address'
        ));
        $translator = $this->getServiceLocator()->get('translator');
        $cartHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $cart = $cartHelper->getCart();
        if( !empty($cart) ){
            $error = array();
            $request = $this->getRequest();
            if ($request->isPost()) {
                $trans = $request->getPost("trans");
                if ( empty($trans['first_name']) ) {
                    $error['first_name'] = $translator->translate('txt_ban_phai_nhap_ten');
                }
                if ( empty($trans['last_name']) ) {
                    $error['last_name'] = $translator->translate('txt_ban_phai_nhap_ten');
                }
                if ( empty($trans['email']) || !filter_var($trans['email'], FILTER_VALIDATE_EMAIL)) {
                    $error['email'] = $translator->translate('txt_email_khong_hop_le');
                }
                if ( empty($trans['phone']) || !is_numeric($trans['phone'])) {
                    $error['phone'] = $translator->translate('txt_so_dien_thoai_khong_hop_le');
                }
                if ( empty($trans['country_id']) ) {
                    $error['country_id'] = $translator->translate('txt_chua_chon_contry');
                }else{
                    $country = $this->getModelTable('CountryTable')->getOne($trans['country_id']);
                    $err = $this->validateInputContryPayment($trans, $country);
                    $error = array_merge($error, $err);
                }
                $trans['full_name'] = $trans['first_name'].' '.$trans['last_name'];

                $member = isset($_SESSION['MEMBER']) ? $_SESSION['MEMBER'] : array();
                $step_content = array(
                        'datas' => $trans,
                        'error' => $error
                    );
                $rowLog = array(
                        'session_id' => session_id(),
                        'users_id' => (!empty($member['users_id']) ? $member['users_id'] : 0),
                        'email' => (!empty($trans['email']) ? $trans['email'] : ''),
                        'step_sign' => 1,
                        'step_name' => 'buyer',
                        'step_content' => json_encode($step_content)
                    );
                $this->getModelTable('InvoiceTable')->saveLog($rowLog);
                
                if( empty($error) ){
                    $buyer = array();
                    $buyer['first_name'] = $trans['first_name'];
                    $buyer['last_name'] = $trans['last_name'];
                    $buyer['full_name'] = $trans['full_name'];
                    $buyer['phone'] = $trans['phone'];
                    $buyer['email'] = $trans['email'];
                    $buyer['type_address_delivery'] = $trans['type_address_delivery'];
                    $buyer['country_id'] = $trans['country_id'];
                    $buyer['address'] = $trans['address'];
                    $buyer['address01'] = empty($trans['address01']) ? '' : $trans['address01'];
                    $buyer['city'] = empty($trans['city']) ? '' : $trans['city'];
                    $buyer['state'] = empty($trans['state']) ? '' : $trans['state'];
                    $buyer['suburb'] = empty($trans['suburb']) ? '' : $trans['suburb'];
                    $buyer['region'] = empty($trans['region']) ? '' : $trans['region'];
                    $buyer['province'] = empty($trans['province']) ? '' : $trans['province'];
                    $buyer['zipcode'] = empty($trans['zipcode']) ? '' : $trans['zipcode'];
                    $buyer['cities_id'] = empty($trans['cities_id']) ? 0 : $trans['cities_id'];
                    $buyer['districts_id'] = empty($trans['districts_id']) ? 0 : $trans['districts_id'];
                    $buyer['wards_id'] = empty($trans['wards_id']) ? 0 : $trans['wards_id'];
                    $buyer['users_id'] = (!empty($member['users_id'])) ? $member['users_id'] : NULL;

                    $_SESSION['PAYMENT_BUYER'] = $buyer;
                    $viewModel = new ViewModel();
                    $viewModel->setTerminal(true);
                    $viewModel->setTemplate("application/checkout/shipper");
                    $viewModel->setVariables(array('buyer' => $buyer));
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($viewModel);
                    $html_confirm = '';
                    if( !empty($_SESSION['PAYMENT_BUYER']) 
                        && !empty($_SESSION['PAYMENT_SHIPPER'])
                        && isset($_SESSION['PAYMENT_BUYER']['shipping_id'])
                        && !empty($_SESSION['PAYMENT_BUYER']['payment_id']) ){
                        $viewModel = new ViewModel();
                        $viewModel->setTerminal(true);
                        $viewModel->setTemplate("application/checkout/confirm");
                        $viewModel->setVariables(array('buyer' => $_SESSION['PAYMENT_BUYER'], 'shipper' => $_SESSION['PAYMENT_SHIPPER']));
                        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                        $html_confirm = $viewRender->render($viewModel);
                    }

                    echo json_encode(array(
                        'type' => 'cartBuyer',
                        'flag' => TRUE,
                        'msg' => $translator->translate('txt_buyer_thanh_cong'),
                        'buyer' => $_SESSION['PAYMENT_BUYER'],
                        'html' => $html,
                        'html_confirm' => $html_confirm,
                    ));
                    die;
                }
            }
        }
        echo json_encode(array(
            'type' => 'cartBuyer',
            'flag' => FALSE,
            'msg' => $translator->translate('txt_buyer_ko_thanh_cong'),
            'html' => $translator->translate('txt_buyer_ko_thanh_cong'),
            'html_confirm' => $translator->translate('txt_buyer_ko_thanh_cong'),
        ));
        die;
    }

    public function shipperAction()
    {
        return $this->redirect()->toRoute( $this->getUrlRouterLang().$this->getRouteCart() , array(
            'action' => 'address'
        ));
        $translator = $this->getServiceLocator()->get('translator');
        $couponsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Coupons');
        $cartHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $cart = $cartHelper->getCart();
        $error = array();
        if( !empty($cart)  && !empty($_SESSION['PAYMENT_BUYER']) ){
            $request = $this->getRequest();
            if ($request->isPost()) {
                $ships = $request->getPost("ships");
                if ( empty($ships['first_name']) ) {
                    $error['first_name'] = $translator->translate('txt_ban_phai_nhap_ten');
                }
                if ( empty($ships['last_name']) ) {
                    $error['last_name'] = $translator->translate('txt_ban_phai_nhap_ten');
                }
                if ( empty($ships['email']) ) {
                    $error['email'] = $translator->translate('txt_email_khong_hop_le');
                }
                if ( empty($ships['phone']) ) {
                    $error['phone'] = $translator->translate('txt_so_dien_thoai_khong_hop_le');
                }
                if ( empty($ships['country_id']) ) {
                    $error['country_id'] = $translator->translate('txt_chua_chon_contry');
                }else{
                    $country_ships = $this->getModelTable('CountryTable')->getOne($ships['country_id']);
                    $err = $this->validateInputContryPayment($ships, $country_ships);
                    $error = array_merge($error, $err);
                }

                if( empty($error) ){
                    $ships['full_name'] = $ships['first_name'].' '.$ships['last_name'];
                    $country_id = $ships['country_id'];
                    $cities_id = 0;
                    $districts_id = 0;

                    $country = $this->getModelTable('CountryTable')->getOne($country_id);
                    if( empty($country) ){
                        $error['country_id'] = $translator->translate('txt_chua_chon_contry');
                    }else{
                        if( $country->country_type == 2 ){
                            $cities_id = $ships['state'];
                        }else if( $country->country_type == 5 ){
                            $cities_id = $ships['region'];
                        }else if( $country->country_type == 6 ){
                            $cities_id = $ships['province'];
                        }else if( $country->country_type == 7 ){
                            $cities_id = $ships['cities_id'];
                            $districts_id = $ships['districts_id'];
                        }
                    }

                    if( !empty($country_id) && empty($error) ){
                        $lsShippings = array();
                        $cities = $this->getModelTable('CitiesTable')->getCitiesOfCountry($country_id);
                        if( empty($cities) ){
                            $lsShippings = $this->getModelTable('ShippingTable')->getShippingOfCountry($country_id);
                        }else{
                            $city = $this->getModelTable('CitiesTable')->getRow($cities_id);
                            if( !empty($city) ){
                                $lsShippings = $this->getModelTable('ShippingTable')->getShippingOfCityAndDistricts($cities_id, $districts_id);
                            }
                        }

                        if( !empty($lsShippings) ){
                            foreach ($lsShippings as $key => $shipping) {
                                if( !($shipping['no_shipping'] == 1 
                                    && !empty($districts_id) && $shipping['districts_id'] == $districts_id)
                                    && $this->isAvaiableShip($shipping) ){
                                    $shippings[$shipping['shipping_id']] = $shipping;
                                }
                            }
                            if( empty($shippings) ){
                                $error['shipping_id'] = $translator->translate('txt_khong_van_chuyen');
                            }
                        }else{
                            $lsShips = $this->getModelTable('ShippingTable')->getShippings();
                            if( !empty($lsShips) ){
                                $error['shipping_id'] = $translator->translate('txt_khong_van_chuyen');
                            }
                        }
                    }
                }

                $coupon = $couponsHelper->getCoupon();
                if( !empty($coupon) ){
                    $check_coupon = $this->getModelTable('InvoiceTable')->getCoupon($coupon['coupons_code']);
                    if( empty($check_coupon) || !$couponsHelper->isAvaliable() ){
                        $error['coupon'] = $translator->translate('txt_coupon_khong_hop_le');
                    }
                }

                $member = isset($_SESSION['MEMBER']) ? $_SESSION['MEMBER'] : array();
                $step_content = array(
                        'datas' => $ships,
                        'error' => $error
                    );
                $rowLog = array(
                        'session_id' => session_id(),
                        'users_id' => (!empty($member['users_id']) ? $member['users_id'] : 0),
                        'email' => (!empty($ships['email']) ? $ships['email'] : ''),
                        'step_sign' => 2,
                        'step_name' => 'shipper',
                        'step_content' => json_encode($step_content)
                    );
                $this->getModelTable('InvoiceTable')->saveLog($rowLog);
                
                if( empty($error) ){
                    $shipper = array();
                    $shipper['ships_first_name'] = $ships['first_name'];
                    $shipper['ships_last_name'] = $ships['last_name'];
                    $shipper['ships_full_name'] = $ships['full_name'];
                    $shipper['ships_email'] = $ships['email'];
                    $shipper['ships_phone'] = $ships['phone'];
                    $shipper['ships_country_id'] = $ships['country_id'];
                    $shipper['ships_address'] = $ships['address'];
                    $shipper['ships_address01'] = empty($ships['address01']) ? '' : $ships['address01'];
                    $shipper['ships_city'] = empty($ships['city']) ? '' : $ships['city'];
                    $shipper['ships_state'] = empty($ships['state']) ? '' : $ships['state'];
                    $shipper['ships_suburb'] = empty($ships['suburb']) ? '' : $ships['suburb'];
                    $shipper['ships_region'] = empty($ships['region']) ? '' : $ships['region'];
                    $shipper['ships_province'] = empty($ships['province']) ? '' : $ships['province'];
                    $shipper['ships_zipcode'] = empty($ships['zipcode']) ? '' : $ships['zipcode'];
                    $shipper['ships_cities_id'] = empty($ships['cities_id']) ? 0 : $ships['cities_id'];
                    $shipper['ships_districts_id'] = empty($ships['districts_id']) ? 0 : $ships['districts_id'];
                    $shipper['ships_wards_id'] = empty($ships['wards_id']) ? 0 : $ships['wards_id'];

                    $_SESSION['PAYMENT_SHIPPER'] = $shipper;

                    $viewModel = new ViewModel();
                    $viewModel->setTerminal(true);
                    $viewModel->setTemplate("application/checkout/shipping");
                    $viewModel->setVariables(array('buyer' => $_SESSION['PAYMENT_BUYER'], 'shipper' => $shipper));
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($viewModel);

                    $html_confirm = '';
                    if( !empty($_SESSION['PAYMENT_BUYER']) 
                        && !empty($_SESSION['PAYMENT_SHIPPER'])
                        && isset($_SESSION['PAYMENT_BUYER']['shipping_id'])
                        && !empty($_SESSION['PAYMENT_BUYER']['payment_id']) ){
                        $viewModel = new ViewModel();
                        $viewModel->setTerminal(true);
                        $viewModel->setTemplate("application/checkout/confirm");
                        $viewModel->setVariables(array('buyer' => $_SESSION['PAYMENT_BUYER'], 'shipper' => $_SESSION['PAYMENT_SHIPPER']));
                        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                        $html_confirm = $viewRender->render($viewModel);
                    }

                    echo json_encode(array(
                        'type' => 'cartShipper',
                        'flag' => TRUE,
                        'msg' => $translator->translate('txt_shipper_thanh_cong'),
                        'shipper' => $_SESSION['PAYMENT_SHIPPER'],
                        'html' => $html,
                        'html_confirm' => $html_confirm,
                    ));
                    die;
                }
            }
        }
        echo json_encode(array(
            'type' => 'cartBuyer',
            'flag' => FALSE,
            'msg' => $translator->translate('txt_shipper_ko_thanh_cong'),
            'error' => $error,
            'html' => $translator->translate('txt_shipper_ko_thanh_cong'),
            'html_confirm' => $translator->translate('txt_shipper_ko_thanh_cong'),
        ));
        die;
    }

    public function shippingAction()
    {
        return $this->redirect()->toRoute( $this->getUrlRouterLang().$this->getRouteCart() , array(
            'action' => 'address'
        ));
        $translator = $this->getServiceLocator()->get('translator');
        $couponsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Coupons');
        $cartHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $cart = $cartHelper->getCart();
        if( !empty($cart) && !empty($_SESSION['PAYMENT_BUYER']) && !empty($_SESSION['PAYMENT_SHIPPER']) ){
            $shipper = $_SESSION['PAYMENT_SHIPPER'];
            $buyer = $_SESSION['PAYMENT_BUYER'];
            $error = array();
            $request = $this->getRequest();
            if ($request->isPost()) {
                $trans = $request->getPost("trans");
                $error = array();
                if ( empty($trans['shipping_id']) && $trans['shipping_id'] != 0 ) {
                    $error['shipping_id'] = $translator->translate('txt_chua_chon_transportation');
                }
                if ( empty($trans['payment_id']) ) {
                    $error['payment_id'] = $translator->translate('txt_chua_chon_payment_method');
                }

                $payment = $this->getModelTable('PaymentTable')->getPayment($trans['payment_id']);
                if ( empty($payment) ) {
                    $error['payment_id'] = $translator->translate('txt_chua_chon_payment_method');
                }else{
                    if( $payment->code == 'ONEPAY' 
                        && empty($payment->is_local)
                        && (    empty($trans['avs_street01']) || empty($trans['avs_city']) 
                                || empty($trans['avs_stateprov']) || empty($trans['avs_postCode']) || empty($trans['avs_country'])) ){
                        $error['payment_id'] = $translator->translate('txt_chua_nhap_day_du_thong_tin_payment_method');
                    }
                }

                $payment_id = $payment->payment_id;
                $no_shipping = FALSE;
                $shipping = array();
                $is_free = FALSE;
                $fee = 0;

                $country_id = 0;
                if( !empty($shipper['ships_country_id']) ){
                    $country_id = $shipper['ships_country_id'];
                }
                $cities_id = 0;
                $districts_id = 0;
                $transport_type = 0;
                if( !empty($trans['transport_type']) ){
                    $transport_type = $trans['transport_type'];
                }
                $shipping_id = 0;
                if( !empty($trans['shipping_id']) ){
                    $shipping_id = $trans['shipping_id'];
                }

                $country = $this->getModelTable('CountryTable')->getOne($country_id);
                if( empty($country) ){
                    $error['country_id'] = $translator->translate('txt_chua_chon_contry');
                }else{
                    if( $country->country_type == 2 ){
                        $cities_id = $shipper['ships_state'];
                    }else if( $country->country_type == 5 ){
                        $cities_id = $shipper['ships_region'];
                    }else if( $country->country_type == 6 ){
                        $cities_id = $shipper['ships_province'];
                    }else if( $country->country_type == 7 ){
                        $cities_id = $shipper['ships_cities_id'];
                        $districts_id = $shipper['ships_districts_id'];
                    }
                }

                $cities = $this->getModelTable('CitiesTable')->getCitiesOfCountry($country_id);
                if( empty($cities) ){
                    $shipping = $this->getModelTable('ShippingTable')->getShippingWithCountry($shipping_id, $country_id);
                }else{
                    $city = $this->getModelTable('CitiesTable')->getCityOfCountry($cities_id, $country_id);
                    if( !empty($city) ){
                        $shipping = $this->getModelTable('ShippingTable')->getShippingWithCityAndDistricts($shipping_id, $cities_id, $districts_id);
                    }
                }

                if( !empty($shipping) ){
                    if( empty($cities) ){
                        $fee = $this->getFeeShip($shipping, $transport_type);
                        if( empty($fee) ){
                            $is_free = TRUE;
                        }
                    }else{
                        if( !($shipping['no_shipping'] == 1 
                            && !empty($districts_id) && $shipping['districts_id'] == $districts_id)
                            && $this->isAvaiableShip($shipping) ){
                            $isFreeShip = $this->isFreeShip($shipping);
                            $fee = $this->getFeeShip($shipping, $transport_type);
                            if( empty($fee) ){
                                $is_free = TRUE;
                            }
                        }else{
                            $no_shipping = TRUE;
                            $error['shipping_id'] = $translator->translate('txt_khong_van_chuyen');
                        }
                    }
                }else{
                    $lsShips = $this->getModelTable('ShippingTable')->getShippings();
                    if( empty($lsShips) ){
                        $is_free = TRUE;
                    }else{
                        $no_shipping = TRUE;
                        $error['shipping_id'] = $translator->translate('txt_khong_van_chuyen');
                    }
                }
                
                $coupon = $couponsHelper->getCoupon();
                if( !empty($coupon) ){
                    $check_coupon = $this->getModelTable('InvoiceTable')->getCoupon($coupon['coupons_code']);
                    if( empty($check_coupon) || !$couponsHelper->isAvaliable() ){
                        $error['coupon'] = $translator->translate('txt_coupon_khong_hop_le');
                    }
                }

                $member = isset($_SESSION['MEMBER']) ? $_SESSION['MEMBER'] : array();
                $step_content = array(
                        'datas' => $trans,
                        'error' => $error
                    );
                $rowLog = array(
                        'session_id' => session_id(),
                        'users_id' => (!empty($member['users_id']) ? $member['users_id'] : 0),
                        'email' => (!empty($buyer['email']) ? $buyer['email'] : ''),
                        'step_sign' => 3,
                        'step_name' => 'shipping',
                        'step_content' => json_encode($step_content)
                    );
                $this->getModelTable('InvoiceTable')->saveLog($rowLog);

                if( empty($error) ){
                    $buyer['transport_type'] = $transport_type;
                    $buyer['transport_type'] = $transport_type;
                    $buyer['shipping_id'] = $shipping_id;
                    $buyer['payment_id'] = $payment_id;
                    if( $payment->code == 'ONEPAY' 
                        && empty($payment->is_local) ){
                        $buyer['avs_street01'] = $trans['avs_street01'];
                        $buyer['avs_city'] = $trans['avs_city'];
                        $buyer['avs_stateprov'] = $trans['avs_stateprov'];
                        $buyer['avs_postCode'] = $trans['avs_postCode'];
                        $buyer['avs_country'] = $trans['avs_country'];
                    }

                    $_SESSION['PAYMENT_BUYER'] = $buyer;

                    $viewModel = new ViewModel();
                    $viewModel->setTerminal(true);
                    $viewModel->setTemplate("application/checkout/confirm");
                    $viewModel->setVariables(array('buyer' => $buyer, 'shipper' => $shipper));
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($viewModel);
                    
                    echo json_encode(array(
                        'type' => 'cartShipping',
                        'flag' => TRUE,
                        'msg' => $translator->translate('txt_shipping_thanh_cong'),
                        'buyer' => $buyer,
                        'shipper' => $shipper,
                        'html' => $html
                    ));
                    die;
                }
            }
        }
        echo json_encode(array(
            'type' => 'cartBuyer',
            'flag' => FALSE,
            'msg' => $translator->translate('txt_shipping_ko_thanh_cong'),
            'html' => $translator->translate('txt_shipping_ko_thanh_cong'),
        ));
        die;
    }

    public function paymentAction()
    {
        $translator = $this->getServiceLocator()->get('translator');
        $cartHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $couponsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Coupons');
        $currencyHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Currency');
        $invoiceHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Invoice');
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $cart = $cartHelper->getCart();
        if( empty($cart) ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }

        if (    empty($_SESSION['PAYMENT_BUYER'])
                || empty($_SESSION['PAYMENT_SHIPPER']) ) {
            unset($_SESSION['PAYMENT_BUYER']);
            unset($_SESSION['PAYMENT_SHIPPER']);
            return $this->redirect()->toRoute($this->getUrlRouterLang().'checkout', array(
                'action' => 'address'
            ));
        }
        $buyer = $_SESSION['PAYMENT_BUYER'];
        $shipper = $_SESSION['PAYMENT_SHIPPER'];
        $error = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $trans = $request->getPost("trans");
            $order = $request->getPost("order");
            $visa = $request->getPost("visa");
            if ( empty($buyer['full_name']) ) {
                $error['full_name'] = $translator->translate('txt_ban_phai_nhap_ten');
            }
            if ( empty($buyer['email']) || !filter_var($buyer['email'], FILTER_VALIDATE_EMAIL)) {
                $error['email'] = $translator->translate('txt_email_khong_hop_le');
            }
            if ( empty($buyer['phone']) || !is_numeric($buyer['phone'])) {
                $error['phone'] = $translator->translate('txt_so_dien_thoai_khong_hop_le');
            }
            if ( empty($buyer['country_id']) ) {
                $error['country_id'] = $translator->translate('txt_chua_chon_contry');
            }else{
                $country = $this->getModelTable('CountryTable')->getOne($buyer['country_id']);
                $err = $this->validateInputContryPayment($buyer, $country);
                $error = array_merge($error, $err);
            }

            if ( !empty($buyer['ship_to_different_address']) && $buyer['ship_to_different_address'] == 1){
                if ( empty($shipper['ships_full_name']) ) {
                    $error['full_name'] = $translator->translate('txt_ban_phai_nhap_ten');
                }
                if ( empty($shipper['ships_country_id']) ) {
                    $error['country_id'] = $translator->translate('txt_chua_chon_contry');
                }else{
                    $country_ships = $this->getModelTable('CountryTable')->getOne($shipper['ships_country_id']);
                    $err = $this->validateInputContryShipper($shipper, $country_ships);
                    $error = array_merge($error, $err);
                }
            }

            if ( !empty($buyer['has_ship']) && 
                (empty($buyer['shipping_id']) && $buyer['shipping_id'] != 0) ) {
                $error['shipping_id'] = $translator->translate('txt_chua_chon_transportation');
            }

            $payment_id = 0;
            if ( empty($trans['payment_id']) ) {
                $error['payment_id'] = $translator->translate('txt_chua_chon_payment_method');
            }else{
                $buyer['payment_id'] = $trans['payment_id'];
                $payment = $this->getModelTable('PaymentTable')->getPayment($buyer['payment_id']);
                if ( empty($payment) ) {
                    $error['payment_id'] = $translator->translate('txt_chua_chon_payment_method');
                }else{
                    if( $payment->code == 'ONEPAY' 
                        && empty($payment->is_local)
                        && (    empty($buyer['avs_street01']) || empty($buyer['avs_city']) 
                                || empty($buyer['avs_stateprov']) || empty($buyer['avs_postCode']) || empty($buyer['avs_country'])) ){
                        $error['payment_id'] = $translator->translate('txt_chua_nhap_day_du_thong_tin_payment_method');
                    }else if(   $payment->code == 'VISA' 
                                && empty($payment->is_local)
                                && ( empty($visa['name']) || empty($visa['number']) || empty($visa['month']) 
                            || empty($visa['year']) || empty($visa['ccv']) ) ){
                        $error['payment_id'] = $translator->translate('txt_chua_nhap_day_du_thong_tin_payment_method');
                    }
                    $payment_id = $payment->payment_id;
                }
            }

            $no_shipping = FALSE;
            $shipping = array();
            $is_free = FALSE;
            $fee = 0;
            $transport_type = 0;
            $shipping_id = 0;
            $country_id = 0;
            $cities_id = 0;
            $districts_id = 0;

            if( !empty($buyer['has_ship']) ){
                $country_id = $shipper['ships_country_id'];
                $cities_id = 0;
                $districts_id = 0;
                $transport_type = $buyer['transport_type'];
                $shipping_id = $buyer['shipping_id'];

                $country = $this->getModelTable('CountryTable')->getOne($country_id);
                if( empty($country) ){
                    $error['country_id'] = $translator->translate('txt_chua_chon_contry');
                }else{
                    if( $country->country_type == 2 ){
                        $cities_id = $shipper['ships_state'];
                    }else if( $country->country_type == 5 ){
                        $cities_id = $shipper['ships_region'];
                    }else if( $country->country_type == 6 ){
                        $cities_id = $shipper['ships_province'];
                    }else if( $country->country_type == 7 ){
                        $cities_id = $shipper['ships_cities_id'];
                        $districts_id = $shipper['ships_districts_id'];
                    }
                }

                $cities = $this->getModelTable('CitiesTable')->getCitiesOfCountry($country_id);
                if( empty($cities) ){
                    $shipping = $this->getModelTable('ShippingTable')->getShippingWithCountry($shipping_id, $country_id);
                }else{
                    $city = $this->getModelTable('CitiesTable')->getCityOfCountry($cities_id, $country_id);
                    if( !empty($city) ){
                        $shipping = $this->getModelTable('ShippingTable')->getShippingWithCityAndDistricts($shipping_id, $cities_id, $districts_id);
                    }
                }

                if( !empty($shipping) ){
                    if( empty($cities) ){
                        $fee = $this->getFeeShip($shipping, $transport_type);
                        if( empty($fee) ){
                            $is_free = TRUE;
                        }
                    }else{
                        if( !($shipping['no_shipping'] == 1 
                            && !empty($districts_id) && $shipping['districts_id'] == $districts_id)
                            && $this->isAvaiableShip($shipping) ){
                            $isFreeShip = $this->isFreeShip($shipping);
                            $fee = $this->getFeeShip($shipping, $transport_type);
                            if( empty($fee) ){
                                $is_free = TRUE;
                            }
                        }else{
                            $no_shipping = TRUE;
                            $error['shipping_id'] = $translator->translate('txt_khong_van_chuyen');
                        }
                    }
                }else{
                    $lsShips = $this->getModelTable('ShippingTable')->getShippings();
                    if( empty($lsShips) ){
                        $is_free = TRUE;
                    }else{
                        $no_shipping = TRUE;
                        $error['shipping_id'] = $translator->translate('txt_khong_van_chuyen');
                    }
                }
            }
            
            $coupon = $couponsHelper->getCoupon();
            if( !empty($coupon) ){
                $check_coupon = $this->getModelTable('InvoiceTable')->getCoupon($coupon['coupons_code']);
                if( empty($check_coupon) || !$couponsHelper->isAvaliable() ){
                    $error['coupon'] = $translator->translate('txt_coupon_khong_hop_le');
                }
            }

            $member = $hPUser->getMember();
            if( empty($error) ){
                $invoice = array_merge( $buyer, $shipper);
                $extensions = array();
                $dataCart = $_SESSION['cart'];
                unset($dataCart['shipping']);

                foreach ($cart as $key => $products) {
                    if($key == 'coupon' || $key == 'shipping' ) continue;
                    foreach ($products['product_type'] as $type_id => $row) {
                        if( !empty($row['extensions']) ){
                            if(!isset($extensions[$products['id']][$type_id])){
                                $extensions[$products['id']][$type_id] = array();
                            }
                            foreach($row['extensions'] as $ext){
                                if(!isset($extensions[$products['id']][$type_id][$ext['id']])){
                                    $extensions[$products['id']][$type_id][$ext['id']] = array();
                                }
                                $extensions[$products['id']][$type_id][$ext['id']] = array(
                                    'ext_name' => $ext['ext_name'],
                                    'price' => $ext['price'],
                                    'quantity' => $ext['quantity'],
                                    'is_available' => $ext['is_available'],
                                    'is_always' => $ext['is_always'],
                                    'type' => $ext['type'],
                                    'refer_product_id' => $ext['refer_product_id']
                                );
                            }
                        }

                        if( !empty($row['extensions_require']) ){
                            if(!isset($extensions[$products['id']][$type_id])){
                                $extensions[$products['id']][$type_id] = array();
                            }
                            foreach($row['extensions_require'] as $ext){
                                if(!isset($extensions[$products['id']][$type_id][$ext['id']])){
                                    $extensions[$products['id']][$type_id][$ext['id']] = array();
                                }
                                $extensions[$products['id']][$type_id][$ext['id']] = array(
                                    'ext_name' => $ext['ext_name'],
                                    'price' => $ext['price'],
                                    'quantity' => $ext['quantity'],
                                    'is_available' => $ext['is_available'],
                                    'is_always' => $ext['is_always'],
                                    'type' => $ext['type'],
                                    'refer_product_id' => $ext['refer_product_id']
                                );
                            }
                        }
                    }
                }

                $calculate = $cartHelper->sumSubTotalPriceInCart();
                $total = $calculate['price_total'];
                $total_old = $calculate['price_total_old'];
                $total_orig = $calculate['price_total_orig'];
                $total_tax = $calculate['price_total_tax'];
                $total_old_tax = $calculate['price_total_old_tax'];
                $total_orig_tax = $calculate['price_total_orig_tax'];

                $commission = 0;
                if( $hPUser->isMerchant() ){
                    $commissionRow = $this->getModelTable('CommissionTable')->getCommissionOfUser($member['users_id']);
                    if( !empty($commissionRow) ){
                        $commission = $commissionRow->rate;
                    }
                }

                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                $viewModel->setTemplate("application/cart/content_invoice");
                $viewModel->setVariables(array(
                    'data' => $invoice,
                    'datamember' => $member,
                    'datacart' => $dataCart,
                    'dataorder' => $order,
                    'dataExtensions' => $extensions,
                    'shipping' => $shipping,
                    'is_free' => $is_free,
                    'transport_type' => $transport_type,
                    'fee' => $fee,
                    'total' => $total,
                    'total_old' => $total_old,
                    'total_orig' => $total_orig,
                    'total_tax' => $total_tax,
                    'total_old_tax' => $total_old_tax,
                    'total_orig_tax' => $total_orig_tax,
                    'ship' => $this->website['ship'],
                    'commission' => $commission,
                ));
                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);
                
                $invoice['invoice_title'] = $this->website['website_order_code_prefix'] . strtotime(date("Y-m-d H:i:s")) . $this->website['website_order_code_suffix'];
                $invoice['is_published'] = 1;
                $invoice['is_delete'] = 0;
                $invoice['payment_id'] = $payment_id;
                $invoice['payment_code'] = $payment->code;
                $invoice['payment'] = 'unpaid';
                $invoice['delivery'] = 'pending';
                $invoice['date_create'] = date('Y-m-d H:i:s');
                $invoice['date_update'] = date('Y-m-d H:i:s');
                
                
                if( isset($dataCart['coupon']) ){
                    $dataCart['coupon']['price_used'] = $total_orig_tax;
                }
                $invoice['total'] = $total;//chua co ship
                $invoice['total_old'] = $total_old;//chua co ship
                $invoice['total_orig'] = $total_orig;//chua co ship
                $invoice['total_tax'] = $total_tax;//chua co ship
                $invoice['total_old_tax'] = $total_old_tax;//chua co ship
                $invoice['total_orig_tax'] = $total_orig_tax;//chua co ship
                $invoice['value_ship'] = $this->website['ship'];
                $invoice['content'] = htmlentities($html, ENT_QUOTES, 'UTF-8');
                if( empty($total_tax) ){
                    $invoice['payment'] = 'paid';
                }

                $invoice['company_name'] = (!empty($order['xuathoadon'])) ? $order['company_name'] : '';
                $invoice['company_tax_code'] = (!empty($order['xuathoadon'])) ? $order['company_tax_code'] : '';
                $invoice['company_address'] = (!empty($order['xuathoadon'])) ? $order['company_address'] : '';
                $invoice['email_subscription'] = (!empty($order['subscription'])) ? $order['email'] : '';
                $invoice['ip_addr'] = $_SERVER['REMOTE_ADDR'];
                $invoice['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                $invoice['commission'] = $commission;
                $invoice['from_currency'] = $_SESSION['website']['website_currency'];
                $invoice['to_currency'] = $_SESSION['website']['website_currency'];
                $invoice['rate_exchange'] = 1;
                $invoice['is_incognito'] = empty($trans['is_incognito']) ? 0 : 1;
                $invoice['date_delivery'] = empty($trans['date_delivery']) ? '' : $trans['date_delivery'];
                $invoice['session_id'] = session_id();
                
                //ko la hoa don ban si
                $invoice['is_wholesale'] = 0;
                if( !empty($shipping) ){
                    $shipping['shipping_fee'] = $fee;
                }
                $id = 0;
                try{
                    if( $payment->code == 'VISA' ){
                        $paypal = new Paypal( );
                        $is_sandbox = TRUE;
                        if( $payment->is_sandbox == 0 ){
                            $is_sandbox = FALSE;
                        }
                        $paypal->setIsSandbox( $is_sandbox );
                        $creditCard = array(
                                'username' => $payment->api_username,
                                'password' => $payment->api_password,
                                'signature' => $payment->api_signature,
                                'creditcardtype' => 'Visa',//Visa, MasterCard, và Discover, American Express
                                'acct' => $visa['number'],
                                'expdate' => $visa['month'].''.$visa['year'],
                                'cvv' => $visa['ccv'],
                                'amount' => $total_tax,
                                'currency' => $currencyHelper->getCurrencySymbol()
                            );
                        $query_string = $paypal->getQueryStringCreditCardPaypal($creditCard);
                        $resultCCD = $paypal->processCreditCardPaypal($query_string);
                        $resultCCD = $paypal->NVPToArray($resultCCD);
                        if( !empty($resultCCD) 
                            && !empty($resultCCD['ACK'])
                            && strtolower($resultCCD['ACK']) == 'success' ){
                            $invoice['payment'] = 'paid';
                            $id = $this->getModelTable('InvoiceTable')->saveInvoice($invoice, $dataCart, $shipping, $extensions);
                            session_regenerate_id();
                        }else{
                            return $this->redirect()->toRoute($this->getUrlRouterLang().'checkout', array('action' => 'payment'));
                        }
                    }else{
                        $id = $this->getModelTable('InvoiceTable')->saveInvoice($invoice, $dataCart, $shipping, $extensions);
                        session_regenerate_id();
                    }
                    if( empty($id) ){
                        return $this->redirect()->toRoute($this->getUrlRouterLang().'checkout', array('action' => 'payment'));
                    }
                }catch(\Exception $e ){
                    return $this->redirect()->toRoute($this->getUrlRouterLang().'checkout', array('action' => 'address'));
                }

                if( $hPUser->hasLogin() ){
                    $member = $hPUser->getMember();
                    $row = array(
                            'users_id' => $member['users_id'],
                            'website_id' => $this->website->website_id,
                            'email' => $invoice['ships_email'],
                            'full_name' => $invoice['ships_full_name'],
                            'phone' => $invoice['ships_phone'],
                            'address' => $invoice['ships_address'],
                            'address01' => $invoice['ships_address01'],
                            'zipcode' => $invoice['ships_zipcode'],
                            'country_id' => $invoice['ships_country_id'],
                            'city' => $invoice['ships_city'],
                            'state' => $invoice['ships_state'],
                            'suburb' => $invoice['ships_suburb'],
                            'region' => $invoice['ships_region'],
                            'province' => $invoice['ships_province'],
                            'cities_id' => $invoice['ships_cities_id'],
                            'districts_id' => $invoice['ships_districts_id'],
                            'wards_id' => $invoice['ships_wards_id'],
                        );
                    $this->getModelTable('UserTable')->updateShippingAddress($row);
                    $row = array(
                            'users_id' => $member['users_id'],
                            'website_id' => $this->website->website_id,
                            'email' => $invoice['email'],
                            'full_name' => $invoice['full_name'],
                            'phone' => $invoice['phone'],
                            'address' => $invoice['address'],
                            'address01' => $invoice['address01'],
                            'zipcode' => $invoice['zipcode'],
                            'country_id' => $invoice['country_id'],
                            'city' => $invoice['city'],
                            'state' => $invoice['state'],
                            'suburb' => $invoice['suburb'],
                            'region' => $invoice['region'],
                            'province' => $invoice['province'],
                            'cities_id' => $invoice['cities_id'],
                            'districts_id' => $invoice['districts_id'],
                            'wards_id' => $invoice['wards_id'],
                        );
                    $this->getModelTable('UserTable')->updateBillingAddress($row);
                }
                
                unset($_SESSION['cart']);
                unset($_SESSION['PAYMENT_BUYER']);
                unset($_SESSION['PAYMENT_SHIPPER']);
                if( empty($total_tax) ){
                    $_SESSION['invoice_id'] = $id;
                    return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array('action' => 'success'));
                }

                if(!empty($payment) 
                    && (    $payment->code == 'HOME'
                            || $payment->code == 'ATM'
                            || ($payment->code == 'PAYPAL' && !empty($payment->sale_account))
                            || ($payment->code == 'VISA' && !empty($payment->api_username) && !empty($payment->api_password) && !empty($payment->api_signature))
                            || ($payment->code == 'ONEPAY' && !empty($payment->vpc_merchant) 
                                && !empty($payment->vpc_accesscode) 
                                && !empty($payment->vpc_hashcode) )
                            || ($payment->code == 'VNPAY' && !empty($payment->vnp_merchant) && !empty($payment->vnp_tmncode) && !empty($payment->vnp_hashsecret))  ) ){
                    switch ( $payment->code ) {
                        case 'HOME':
                        {
                            $_SESSION['invoice_id'] = $id;
                            return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array('action' => 'success'));
                            break;
                        }

                        case 'VISA':
                        {
                            $_SESSION['invoice_id'] = $id;
                            return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array('action' => 'success'));
                            break;
                        }
                            
                        case 'ATM':
                        {
                            $_SESSION['invoice_id'] = $id;
                            return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array('action' => 'success'));
                            break;
                        }
                            
                        case 'PAYPAL':
                        {
                            $total_all = $fee + $total_tax;
                            $currency_code = $_SESSION['website']['website_currency'];
                            $to_currency = $_SESSION['website']['website_currency'];
                            $rate_exchange = 1;
                            if( !in_array(strtoupper($currency_code), $this->currencyPaypalAccept) ){
                                $rate_exchange = $currencyHelper->exchangerates('USD', $currency_code, 1);
                                $to_currency = 'USD';
                            }

                            $products = array();
                            foreach ($cart as $id_x => $product) {
                                foreach ($product['product_type'] as $product_type_id => $p) {
                                    $item = array();
                                    $item['title'] = $p['products_title'];
                                    $item['type_name'] = $p['type_name'];
                                    $item['quantity'] = $p['quantity'];
                                    $item['price_total'] = $p['price_total_tax'];
                                    $products[] = $item;
                                }
                            }

                            $amount = $total_tax/$rate_exchange;
                            $row = array(
                                        'from_currency' => $currency_code,
                                        'to_currency' => $to_currency,
                                        'rate_exchange' => $rate_exchange,
                                        'total_converter' => $amount
                                    );
                            try{
                                $invoice = $this->getModelTable('InvoiceTable')->getInvoiceByTitle($id);
                                $this->getModelTable('InvoiceTable')->updateData($row, $invoice->invoice_id);
                            }catch(\Exception $ex){}

                            $cb_return = $this->baseUrl.$this->getUrlPrefixLang().'/paypal/return/'.$invoice->invoice_id;
                            $cb_cancel = $this->baseUrl.$this->getUrlPrefixLang().'/paypal/cancel/'.$invoice->invoice_id;
                            $cb_notifi = $this->baseUrl.$this->getUrlPrefixLang().'/paypal/notifi/'.$invoice->invoice_id;

                            $paypal = new Paypal( );
                            $is_sandbox = TRUE;
                            if( $payment->is_sandbox == 0 ){
                                $is_sandbox = FALSE;
                            }
                            $paypal->setIsSandbox( $is_sandbox );
                            $paypal->setCmd( '_cart' );
                            $paypal->setCharset( 'utf-8' );
                            $paypal->setUpload( 1 );
                            $paypal->setInvoice( $invoice->invoice_id );
                            $paypal->setBusiness( $payment->sale_account );
                            $paypal->setCurrencyCode( $to_currency );
                            $paypal->setReturn( $cb_return );
                            $paypal->setCancelReturn( $cb_cancel );
                            $paypal->setNotifyUrl( $cb_notifi );
                            $paypal->setRm( 2 );
                            $paypal->setLc( 2 );
                            $paypal->setCbt( $translator->translate('txt_paypal_tiep_tuc_thanh_toan') );
                            $paypal->setProducts( $products );
                            $paypal->setShipping(max(0, $fee));
                            $paypal->setTax(max(0, $total_tax-$total));
                            //$paypal->setItemName( 'test mua hang paypal' );
                            //$paypal->setAmount( 30000 );
                            $paypal->setRateExchange( $rate_exchange );
                            $url = $paypal->getUrlPay();
                            return $this->redirect()->toUrl($url);
                            break;
                        }

                        case 'ONEPAY':
                        {
                            $total_all = $fee + $total_tax;
                            $currency_code = $_SESSION['website']['website_currency'];

                            $row = array(
                                        'from_currency' => $currency_code,
                                        'to_currency' => $currency_code,
                                        'rate_exchange' => 1,
                                        'total_converter' => $total_all
                                    );
                            try{
                                $invoice = $this->getModelTable('InvoiceTable')->getInvoiceByTitle($id);
                                $this->getModelTable('InvoiceTable')->updateData($row, $invoice->invoice_id);
                            }catch(\Exception $ex){}

                            $cb_return = $this->baseUrl.$this->getUrlPrefixLang().'/onepay/return/'.$invoice->invoice_id;
                            $onepay = new Onepay( );
                            $is_sandbox = TRUE;
                            if( $payment->is_sandbox == 0 ){
                                $is_sandbox = FALSE;
                            }
                            $is_local = FALSE;
                            if( !empty($payment->is_local) ){
                                $is_local = TRUE;
                            }
                            $onepay->setIsSandbox( $is_sandbox );
                            $onepay->setIsLocal( $is_local );

                            $onepay->setVpcMerchant( $payment->vpc_merchant );
                            $onepay->setVpcAccessCode( $payment->vpc_accesscode );
                            $onepay->setVpcHashSecret( $payment->vpc_hashcode );

                            $onepay->setTitle( $translator->translate('txt_onepay_thanh_toan_don_hang'). ' '. $id );
                            $onepay->setVpcMerchTxnRef( $invoice->invoice_id );
                            $onepay->setVpcOrderInfo( $id );
                            $onepay->setVpcAmount( $total_all );
                            $onepay->setVpcReturnURL( $cb_return );
                            $onepay->setVpcCommand( 'pay' );
                            $locale = 'vn';
                            $quocGia = $this->getModelTable('CountryTable')->getOne($this->website->website_contries);
                            if( !empty($quocGia) 
                                && strtolower($quocGia->code) != 'vn' ){
                                $locale = 'en';
                            }
                            $onepay->setVpcLocale( $locale );

                            $onepay->setVpcTicketNo( $_SERVER['REMOTE_ADDR'] );
                            $onepay->setVpcSHIPStreet01( $invoiceHelper->getAddress($invoice, 1) );
                            $onepay->setVpcSHIPProvice( $invoiceHelper->getProvice($invoice, 1) );
                            $onepay->setVpcSHIPCity( $invoiceHelper->getCity($invoice, 1) );
                            $onepay->setVpcSHIPCountry( $country->title );
                            $onepay->setVpcCustomerPhone( $invoice->ships_phone );
                            $onepay->setVpcCustomerEmail( $invoice->ships_email );
                            $onepay->setVpcCustomerId( $invoice->users_id );

                            if( $is_local ){
                                $onepay->setVpcCurrency( $currency_code );
                            }else{
                                $onepay->setAVSStreet01( $buyer['avs_street01'] );
                                $onepay->setAVSCity( $buyer['avs_city'] );
                                $onepay->setAVSStateProv( $buyer['avs_stateprov'] );
                                $onepay->setAVSPostCode( $buyer['avs_postCode'] );
                                $AVS_Country = $this->getModelTable('CountryTable')->getOne($buyer['avs_country']);
                                if( empty($AVS_Country) ){
                                    $onepay->setAVSCountry( $AVS_Country->title );
                                }
                                $onepay->setDisplay( '' );
                                $onepay->setAgainLink( $_SERVER['HTTP_REFERER'] );
                            }

                            $url = $onepay->getUrlPay();
                            return $this->redirect()->toUrl($url);
                            break;
                        }

                        case 'VNPAY':
                        {
                            $total_all = $fee + $total_tax;
                            $currency_code = $_SESSION['website']['website_currency'];

                            $row = array(
                                        'from_currency' => $currency_code,
                                        'to_currency' => $currency_code,
                                        'rate_exchange' => 1,
                                        'total_converter' => $total_all
                                    );
                            try{
                                $invoice = $this->getModelTable('InvoiceTable')->getInvoiceByTitle($id);
                                $this->getModelTable('InvoiceTable')->updateData($row, $invoice->invoice_id);
                            }catch(\Exception $ex){}

                            $cb_return = $this->baseUrl.$this->getUrlPrefixLang().'/vnpay/return/'.$invoice->invoice_id;
                            $vnpay = new Vnpay();
                            $is_sandbox = TRUE;
                            if( $payment->is_sandbox == 0 ){
                                $is_sandbox = FALSE;
                            }
                            $vnpay->setIsSandbox( $is_sandbox );
                            $vnpay->setVnpMerchant( $payment->vnp_merchant );
                            $vnpay->setVnpTmnCode( $payment->vnp_tmncode );
                            $vnpay->setVnpAmount( $total_all );
                            $vnpay->setVnpCommand( 'pay' );
                            $vnpay->setVnpCurrCode( $currency_code );
                            $locale = 'vn';
                            $quocGia = $this->getModelTable('CountryTable')->getOne($this->website->website_contries);
                            if( !empty($quocGia) 
                                && strtolower($quocGia->code) != 'vn' ){
                                $locale = 'en';
                            }
                            $vnpay->setVnpLocale( $locale );
                            $vnpay->setVnpOrderInfo( ' ' );
                            $vnpay->setVnpOrderType( 'billpayment' );
                            $vnpay->setVnpReturnUrl( $cb_return );
                            $vnpay->setVnpTxnRef( $invoice->invoice_id );
                            //$vnpay->setVnpBankCode( 'NCB' );
                            $vnpay->setHashSecret( $payment->vnp_hashsecret );
                            //$vnpay->setWebsiteId( $this->website->website_id );
                            $url = $vnpay->getUrlPay();
                            return $this->redirect()->toUrl($url);
                            break;
                        }
                    }
                }

                return $this->redirect()->toRoute('cart', array('action' => 'success'));
            }
        }
        $this->has_header = FALSE;
        $this->has_footer = TRUE;
        $this->setDataView('has_header', $this->has_header);
        $this->setDataView('has_footer', $this->has_footer);
        $this->data_view['buyer'] = $buyer;
        $this->data_view['shipper'] = $shipper;
        return $this->data_view;
    }

    public function updateCartAction()
    {
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $list_update = $request->getPost('quantity');

            foreach ($list_update as $id => $quantitys) {
                foreach ($quantitys as $product_type => $quantity) {
                    if( isset($_SESSION['cart'][$id]) 
                        && isset($_SESSION['cart'][$id]['product_type'][$product_type]) ){
                        
                        $product = $_SESSION['cart'][$id]['product_type'][$product_type];
                        $_SESSION['cart'][$id]['product_type'][$product_type]['quantity'] = $quantity;

                        $price_this = $productsHelper->getPriceSale($product)*$quantity;
                        $price_tax =  $price_this + ($price_this * $product['vat'] / 100);
                        $price_this_old = $productsHelper->getPrice($product)*$quantity;
                        $price_old_tax =  $price_this_old + ($price_this_old * $product['vat'] / 100);

                        if( !empty($product['extensions']) ){
                            $ext_list = $product['extensions'];
                            foreach($ext_list as $ext){
                                $priceQl = $ext['price']*$ext['quantity'];
                                $price_this += $priceQl;
                                $price_tax += $priceQl;
                                $price_this_old += $priceQl;
                                $price_old_tax += $priceQl;
                            }
                        }

                        
                        $_SESSION['cart'][$id]['product_type'][$product_type]['price_total'] = $price_this;
                        $_SESSION['cart'][$id]['product_type'][$product_type]['price_total_old'] = $price_this_old;
                        $_SESSION['cart'][$id]['product_type'][$product_type]['price_total_tax'] = $price_tax;
                        $_SESSION['cart'][$id]['product_type'][$product_type]['price_total_old_tax'] = $price_old_tax;
                    }
                }
            }

        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'checkout', array(
            'action' => 'index'
        ));
    }

} 