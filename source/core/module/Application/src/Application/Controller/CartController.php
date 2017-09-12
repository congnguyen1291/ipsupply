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

class CartController extends FrontEndController
{
    private $version = '';

    public function indexAction()
    {
        if ( $this->getVersionCart() != $this->version ) {
            return $this->redirect()->toRoute( $this->getUrlRouterLang().$this->getRouteCart() , array(
                'action' => 'index'
            ));
        }
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle('Cart');
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $calculate = $helper->sumSubTotalPriceInCart();
        $price_total = $calculate['price_total'];
        $price_total_old = $calculate['price_total_old'];
        $this->data_view['price_total'] = $price_total;
        $this->data_view['price_total_old'] = $price_total_old;
        return $this->data_view;
    }

    public function popBuyFastProductAction()
    {
        $ext_list = array();
        $extensions_require = NULL;
        $error = array();
        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $id = 0;
        $quantity = 1;
        $product = array();
        if($request->isPost()){
            $exts = $request->getPost('extention', 0);
            $product_type = $request->getPost('product_type', 0);
            $id = $request->getPost('products_id', 0);
            $quantity = $request->getPost('quantity', 1);
        }else{
            $exts = $this->params()->fromQuery('extention', 0);
            $product_type = $this->params()->fromQuery('product_type', 0);
            $id = $this->params()->fromRoute('id', 0);
            $quantity = $this->params()->fromQuery('quantity', 1);
        }

        $ajax = $this->params()->fromQuery('ajax', 0);

        if (!empty($id)) {
            try {
                if(empty($product_type)){
                    $product = $this->getModelTable('ProductsTable')->getRow($id);
                }else{
                    $product = $this->getModelTable('ProductsTable')->getProductsWithType($id, $product_type);
                }
                $extensions = array();
                if(!empty($exts)){
                    $extensions = $this->getModelTable('ProductsTable')->getExtensions($exts);
                }
                $extensions_require = $this->getModelTable('ProductsTable')->getExtensionsAlwaysProduct($id);
                $extensions_transportations = $this->getModelTable('ProductsTable')->getExtensionsTransportationProduct($id);
                if(!(($product->is_available == 0 && $product->quantity == 0) || $product->is_delete == 1 || $product->is_published == 0)){
                    if($extensions && count($extensions)){
                        foreach($extensions as $key => $ext){
                            if($ext['is_always'] == 0){
                                $ext_list[$ext['id']] = $ext;
                            }
                        }
                    }
                }else{
                    $error[] = $translator->translate('txt_product_not_available');
                }
            } catch (\Exception $ex) {
                $error[] = $ex->getMessage();
            }
        }else{
            $error[] = $translator->translate('txt_product_not_exit');
        }

        if(empty($error) && !empty($product)){
            if(!empty($product['products_type_id'])){
                $product_type = $product['products_type_id'];
            }
            if(!isset($_SESSION['products_deals'])){
                $products_deals = $this->getModelTable('CategoriesTable')->getAllGoldTimerCurrent(array(),0, 100);
                $deals_data = array();
                foreach($products_deals as $p){
                    $deals_data[$p['products_id']] = $p;
                }
                $_SESSION['products_deals'] = $deals_data;
                $products_deals = $deals_data;
            }else{
                $products_deals = $_SESSION['products_deals'];
            }
            if(isset($products_deals[$product['products_id']])){
                $p = $products_deals[$product['products_id']];
                $product['price_sale'] = $p['price_sale'];
            }
            if (!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$id])) {
                $promotion_list = array();
                if (trim($product['promotion_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion'] ? $product['promotion'] : 0,
                        'text' => $product['promotion_description'],
                    );
                }
                if (trim($product['promotion1_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion1'] ? $product['promotion1'] : 0,
                        'text' => $product['promotion1_description'],
                    );
                }
                if (trim($product['promotion2_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion2'] ? $product['promotion2'] : 0,
                        'text' => $product['promotion2_description'],
                    );
                }
                if (trim($product['promotion3_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion3'] ? $product['promotion3'] : 0,
                        'text' => $product['promotion3_description'],
                    );
                }
                if(!isset($quantity) || $quantity==0){
                    $quantity=1;
                }
                $_SESSION['cart'][$id] = array(
                    'id' => $id,
                    'thumb' => $product['thumb_image'],
                    'code' => $product['products_code'],
                    'title' => $product['products_title'],
                    'alias' => $product['products_alias'],
                    'total_price_extention' => $product['total_price_extention'],
                    'quantity' => $quantity,
                    'price' => $product['price'],//gia chua thue
                    'price_sale' => $product['price_sale'],//gia chua thue
                    'vat' => (int)$product['vat'],
                    'promotion' => 0,
                    'promotion_list' => $promotion_list,
                    'extensions' => $ext_list,
                    'extensions_require' => $extensions_require,
                    'extensions_transportations' => $extensions_transportations,
                    'product_type' => array($product_type => array(
                            'products_type_id' => $product_type,
                            'type_name' => $productsHelper->getNameType($product),
                            'price' => $productsHelper->getPriceSimple($product),
                            'price_sale' => $productsHelper->getPriceSaleSimple($product),
                            'quantity' => $quantity,
                            'is_available' => $productsHelper->getIsAvailable($product),
                            'extensions' => $ext_list,
                            'extensions_require' => $extensions_require,
                            'transportation_id' => $product['transportation_id'],
                        )),
                );

            }elseif(isset($_SESSION['cart']) && isset($_SESSION['cart'][$id]) && count($ext_list) > 0){
                if(isset($_SESSION['cart'][$id]['product_type'][$product_type])){
                    unset($_SESSION['cart'][$id]['product_type'][$product_type]['extensions']);
                    $_SESSION['cart'][$id]['product_type'][$product_type]['extensions'] = $ext_list;
                    $_SESSION['cart'][$id]['product_type'][$product_type]['quantity'] += $quantity;
                }else{
                    $_SESSION['cart'][$id]['product_type'][$product_type] = array(
                        'products_type_id' => $product_type,
                        'type_name' => $productsHelper->getNameType($product),
                        'price' => $productsHelper->getPriceSimple($product),
                        'price_sale' => $productsHelper->getPriceSaleSimple($product),
                        'quantity' => $quantity,
                        'is_available' => $productsHelper->getIsAvailable($product),
                        'extensions' => $ext_list,
                        'extensions_require' => $extensions_require,
                        'transportation_id' => $product['transportation_id'],
                    );
                }
            }else if(isset($_SESSION['cart'][$id])){
                if(isset($_SESSION['cart'][$id]['product_type'][$product_type])){
                    //unset($_SESSION['cart'][$id]['product_type'][$product_type]['extensions']);
                    //$_SESSION['cart'][$id]['product_type'][$product_type]['extensions'] = array();
                    $_SESSION['cart'][$id]['product_type'][$product_type]['quantity'] += $quantity;
                }else{
                    $_SESSION['cart'][$id]['product_type'][$product_type] = array(
                        'products_type_id' => $product_type,
                        'type_name' => $productsHelper->getNameType($product),
                        'price' => $productsHelper->getPriceSimple($product),
                        'price_sale' => $productsHelper->getPriceSaleSimple($product),
                        'quantity' => $quantity,
                        'is_available' => $productsHelper->getIsAvailable($product),
                        'extensions' => $ext_list,
                        'extensions_require' => $extensions_require,
                        'transportation_id' => $product['transportation_id'],
                    );
                }
            }
            
            
            $price_this = $productsHelper->getPriceSale($product)*$quantity;
            $price_tax =  $price_this + ($price_this * $product['vat'] / 100);
            $price_this_old = $productsHelper->getPrice($product)*$quantity;
            $price_old_tax =  $price_this_old + ($price_this_old * $product['vat'] / 100);

            if( !empty($ext_list) ){
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

        //$transportations = $this->getModelTable('UserTable')->loadTransportations();
        //$cities = $this->getModelTable('UserTable')->loadCities();

        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $price_total = 0;
        $price_total_old = 0;
        if(isset($_SESSION['cart']) && count($_SESSION['cart'])){
            $calculate = $helper->sumSubTotalPriceInCart();
            $price_total = $calculate['price_total'];
            $price_total_old = $calculate['price_total_old'];
        }

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate("application/cart/pop-buy-fast-product");
        $viewModel->setVariables(array(
            'price_total' => $price_total,
            'price_total_old' => $price_total_old,
            'products_id' => $id,
            'product' => $product,
            'error' => $error,
            'website' => $this->website,
        ));
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRender->render($viewModel);

        if(!empty($ajax)){
            echo json_encode(array(
                'type' => 'popBuyFastProduct',
                'flag' => TRUE,
                'html' => $html,
                'data' => $_SESSION['cart']
            ));
            die;
        }

        return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
            'action' => 'payment'
        ));

    }

    private function caculatorShippingFlatRateAndSpeacialFlatRate($shipping, $product, $fee = 0, $fee_speacial = 0){
        $total_fee = 0;
        if(!empty($shipping) && !empty($product)){
            if($shipping['price_type'] == 0 && !empty($shipping['price'])){
                $total_fee = ($fee>=$fee_speacial) ? $fee : $fee_speacial;
            }else if($shipping['price_type'] == 1){
                $qty = $product['quantity'];
                $fee_p = $shipping['price']*$qty;
                $total_fee = $fee - $fee_p + $fee_speacial;
            }else if($shipping['price_type'] == 2){
                $total = $product['price_total'];
                $fee_p = $shipping['fee_percent']*$total;
                $total_fee = $fee - $fee_p + $fee_speacial;
            }else if($shipping['price_type'] == 3){
                $qty = $product['quantity'];
                $fee = $shipping['fee_percent']*$qty;
                $total_fee = $fee - $fee_p + $fee_speacial;
            }
        }
        return $total_fee;
    }

    private function caculatorShippingFlatRate($shipping){
        $fee = 0;
        if(!empty($shipping)){
            if($shipping['price_type'] == 0 && !empty($shipping['price'])){
                $fee = $shipping['price'];
            }else if($shipping['price_type'] == 1){
                $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
                $qty = $helper->getNumberProductInCart();
                $fee = $shipping['price']*$qty;
            }else if($shipping['price_type'] == 2){
                $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
                $total = $helper->sumSubTotalPriceInCart();
                $fee = $shipping['min_fee'] + $shipping['fee_percent']*$total;
            }else if($shipping['price_type'] == 3){
                $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
                $qty = $helper->getNumberProductInCart();
                $fee = $shipping['min_fee'] + $shipping['fee_percent']*$qty;
            }
        }
        return $fee;
    }

    private function caculatorShippingSpeacialFlatRate($shipping, $product){
        $fee = 0;
        if(!empty($shipping) && !empty($product)){
            if($shipping['price_type'] == 0 && !empty($shipping['price'])){
                $fee = $shipping['price'];
            }else if($shipping['price_type'] == 1){
                $qty = $product['quantity'];
                $fee = $shipping['price']*$qty;
            }else if($shipping['price_type'] == 2){
                $total = $product['price_total'];
                $fee = $shipping['min_fee'] + $shipping['fee_percent']*$total;
            }else if($shipping['price_type'] == 3){
                $qty = $product['quantity'];
                $fee = $shipping['min_fee'] + $shipping['fee_percent']*$qty;
            }
        }
        return $fee;
    }

    private function checkOrderFree($free){
        if(!empty($free['conditions']) && $free['transportation_type'] == 1){
            $condition = json_decode($free['conditions']);
            if(!empty($condition['column'])
                && !empty($condition['equal'])
                && !empty($condition['value']) ){
                $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
                $value_column = 0;
                if($condition['column'] == 'quality'){
                    $calculate = $helper->getNumberProductInCart();
                    $value_column = $calculate;
                }else if($condition['column'] == 'subtotal'){
                    $calculate = $helper->sumSubTotalPriceInCart();
                    $value_column = $calculate['price_total'];
                }

                if( ($condition['equal'] == '<' && $value_column < $condition['value'])
                    || ($condition['equal'] == '<=' && $value_column <= $condition['value'])
                    || ($condition['equal'] == '>' && $value_column > $condition['value'])
                    || ($condition['equal'] == '>=' && $value_column >= $condition['value'])
                    || ($condition['equal'] == '=' && $value_column == $condition['value']) ){
                    return true;
                }
            }
        }
        return false;
    }

    public function getShippingAction()
    {
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
        $country_id = $this->params()->fromQuery('country_id', 0);
        $cities_id = $this->params()->fromQuery('cities_id', 0);
        $districts_id = $this->params()->fromQuery('districts_id', 0);
        $transport_type = $this->params()->fromQuery('transport_type', 0);
        $shipping_id = $this->params()->fromQuery('shipping_id', 0);
        $ajax = $this->params()->fromQuery('ajax', 0);
        $translator = $this->getServiceLocator()->get('translator');
        $row = array(
            'type' => 'getShipping',
            'flag' => false,
            'html' => $translator->translate('txt_shipping_available'),
            'msg' => $translator->translate('txt_shipping_available'),
        );
        $msg = '';
        if( !empty($country_id) ){
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

            $no_shipping = FALSE;
            $free_shipping = array();
            $is_free = FALSE;
            $choose = 0;
            $shippings = array();
            $transports = array();
            if( !empty($lsShippings) ){
                $transports[0] = $translator->translate('txt_shipping_normal');
                foreach ($lsShippings as $key => $shipping) {
                    $fee = $this->getFeeShip($shipping, $transport_type);
                    if( !(empty($fee) && $transport_type == 1) ){
                        if( !($shipping['no_shipping'] == 1 
                            && !empty($districts_id) && $shipping['districts_id'] == $districts_id)
                            && $this->isAvaiableShip($shipping) ){
                            if( empty($shippings) ){
                                $choose = $shipping['shipping_id'];
                            }
                            $shippings[$shipping['shipping_id']] = $shipping;
                            if( empty($fee) ){
                                $free_shipping = $shipping;
                                $is_free = TRUE;
                                $choose = $shipping['shipping_id'];
                                break;
                            }else if( $shipping['shipping_id'] ==  $shipping_id) {
                                $choose = $shipping['shipping_id'];
                            }
                        }
                    }

                    if( !($shipping['no_shipping'] == 1 
                        && !empty($districts_id) && $shipping['districts_id'] == $districts_id) 
                        && !empty($this->getFeeShipFast($shipping)) ){
                        $transports[1] = $translator->translate('txt_shipping_fast');
                    }
                }
                if( empty($shippings) ){
                    $no_shipping = TRUE;
                    $msg = $translator->translate('txt_don_hang_khong_van_chuyen_den_dia_diem_nay_hoac_hoa_don_chua_phu_hop');
                }
            }else{
                $ships = $this->getModelTable('ShippingTable')->getShippings();
                if( empty($ships) ){
                    $no_shipping = FALSE;
                    $is_free = TRUE;
                }else{
                    $no_shipping = TRUE;
                    $msg = $translator->translate('txt_don_hang_khong_van_chuyen_den_dia_diem_nay');
                    $is_free = FALSE;
                }
            }

            $result = new ViewModel();
            $result->setTerminal(true);
            $result->setTemplate("application/cart/get-shipping");
            $this->data_view['shipping'] = $shippings;
            $this->data_view['free_shipping'] = $free_shipping;
            $this->data_view['cities_id'] = $cities_id;
            $this->data_view['districts_id'] = $districts_id;
            $this->data_view['is_free'] = $is_free;
            $this->data_view['no_shipping'] = $no_shipping;
            $this->data_view['transports'] = $transports;
            $this->data_view['transport_type'] = $transport_type;
            $this->data_view['country_id'] = $country_id;
            $this->data_view['cities_id'] = $cities_id;
            $this->data_view['districts_id'] = $districts_id;
            $this->data_view['shipping_id'] = $shipping_id;
            $this->data_view['choose'] = $choose;
            $result->setVariables($this->data_view);
            $viewRender = $this->getServiceLocator()->get('ViewRenderer');
            $html = $viewRender->render($result);
            $row = array(
                'type' => 'getShipping',
                'flag' => true,
                'msg' => $msg,
                'html' => $html,
                'cart' => (!empty($_SESSION['cart']) ? $_SESSION['cart'] : array()),
                'shipping' => $shippings,
                'free_shipping' => $free_shipping,
                'is_free' => $is_free,
                'no_shipping' => $no_shipping,
                'transport_type' => $transport_type,
                'country_id' => $country_id,
                'cities_id' => $cities_id,
                'districts_id' => $districts_id,
                'shipping_id' => $shipping_id,
                'choose' => $choose,
            );
        }

        if(!empty($ajax)){
            echo json_encode($row);
            die;
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
            'action' => 'index'
        ));
    }

    public function hasShippingAction()
    {
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
        $country_id = $this->params()->fromQuery('country_id', 0);
        $cities_id = $this->params()->fromQuery('cities_id', 0);
        $districts_id = $this->params()->fromQuery('districts_id', 0);
        $transport_type = $this->params()->fromQuery('transport_type', 0);
        $shipping_id = $this->params()->fromQuery('shipping_id', 0);
        $ajax = $this->params()->fromQuery('ajax', 0);
        $translator = $this->getServiceLocator()->get('translator');
        $row = array(
            'type' => 'hasShipping',
            'flag' => false,
            'msg' => $translator->translate('txt_shipping_available')
        );
        if( !empty($country_id) ){
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

            $no_shipping = FALSE;
            $msg = '';
            $shippings = array();
            if( !empty($lsShippings) ){
                foreach ($lsShippings as $key => $shipping) {
                    $fee = $this->getFeeShip($shipping, $transport_type);
                    if( !(empty($fee) && $transport_type == 1) ){
                        if( !($shipping['no_shipping'] == 1 
                            && !empty($districts_id) && $shipping['districts_id'] == $districts_id)
                            && $this->isAvaiableShip($shipping) ){
                            $shippings[$shipping['shipping_id']] = $shipping;
                        }
                    }
                }
                if( empty($shippings) ){
                    $no_shipping = TRUE;
                    $msg = $translator->translate('txt_shipping_available');
                }
            }else{
                $ships = $this->getModelTable('ShippingTable')->getShippings();
                if( !empty($ships) ){
                    $no_shipping = TRUE;
                    $msg = $translator->translate('txt_shipping_available');
                }
            }
            $row = array(
                'type' => 'hasShipping',
                'flag' => true,
                'no_shipping' => $no_shipping,
                'transport_type' => $transport_type,
                'country_id' => $country_id,
                'cities_id' => $cities_id,
                'districts_id' => $districts_id,
                'msg' => $msg
            );
        }

        if(!empty($ajax)){
            echo json_encode($row);
            die;
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
            'action' => 'index'
        ));
    }

    public function getFeeTransitionsAction()
    {
        $translator = $this->getServiceLocator()->get('translator');
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $currencyHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Currency');
        $country_id = $this->params()->fromQuery('country_id', 0);
        $cities_id = $this->params()->fromQuery('cities_id', 0);
        $districts_id = $this->params()->fromQuery('districts_id', 0);
        $transport_type = $this->params()->fromQuery('transport_type', 0);
        $shipping_id = $this->params()->fromQuery('shipping_id', '');
        $ajax = $this->params()->fromQuery('ajax', 0);

        $is_free = false;
        $no_shipping = TRUE;
        $fee = 0;
        $shipping = array();
        $subtotal = 0;
        $subtotal_orig = 0;
        $subtotal_tax = 0;
        if(!empty($_SESSION['cart'])){
            $calculate = $helper->sumSubTotalPriceInCart();
            $subtotal = $calculate['price_total'];
            $subtotal_orig = $calculate['price_total_orig'];
            $subtotal_tax = $calculate['price_total_tax'];
        }

        if( !empty($shipping_id) && !empty($country_id) ){
            $shipping = array();
            $cities = $this->getModelTable('CitiesTable')->getCitiesOfCountry($country_id);
            if( empty($cities) ){
                $shipping = $this->getModelTable('ShippingTable')->getShippingWithCountry($shipping_id, $country_id);
            }else{
                $shipping = $this->getModelTable('ShippingTable')->getShippingWithCityAndDistricts($shipping_id, $cities_id, $districts_id);
            }
            if( !empty($shipping) ){
                if( empty($cities) ){
                    $fee = $this->getFeeShip($shipping, $transport_type);
                    $no_shipping = FALSE;
                    if( empty($fee) ){
                        $is_free = TRUE;
                    }
                }else{
                    if( !($shipping['no_shipping'] == 1 
                        && !empty($districts_id) && $shipping['districts_id'] == $districts_id)
                        && $this->isAvaiableShip($shipping) ){
                        $fee = $this->getFeeShip($shipping, $transport_type);
                        $no_shipping = FALSE;
                        if( empty($fee) ){
                            $is_free = TRUE;
                        }
                    }
                }
            }
        }else{
            $ships = $this->getModelTable('ShippingTable')->getShippings();
            if( empty($ships) ){
                $no_shipping = FALSE;
                $is_free = TRUE;
            }else{
                $no_shipping = TRUE;
                $is_free = FALSE;
            }
        }
        $_SESSION['cart']['shipping'] = array(  
                                                'no_shipping' => $no_shipping, 
                                                'is_free' => $is_free,
                                                'fee' => $fee,
                                                'shipping' => $shipping,
                                            );
        $total = $subtotal+$fee;
        $total_tax = $subtotal_tax+$fee;
        echo json_encode(array(
            'type' => 'getFeeTransitions',
            'flag' => TRUE,
            'is_free' => $is_free,
            'no_shipping' => $no_shipping,
            'fee' => $fee,
            'fee_currency' => $currencyHelper->fomatCurrency($fee, ($no_shipping ? 'txt_no_shipping_ship' : 'txt_mien_phi_ship') ),
            'subtotal_orig' => $subtotal_orig,
            'subtotal_orig_currency' => $currencyHelper->fomatCurrency($subtotal_orig, ''),
            'subtotal' => $subtotal,
            'subtotal_currency' => $currencyHelper->fomatCurrency($subtotal, ''),
            'subtotal_tax' => $subtotal_tax,
            'subtotal_tax_currency' => $currencyHelper->fomatCurrency($subtotal_tax, ''),
            'total' => $total,
            'total_currency' => $currencyHelper->fomatCurrency($total, ''),
            'total_tax' => $total_tax,
            'total_tax_currency' => $currencyHelper->fomatCurrency($total_tax, '')
        ));
        die;
    }

    public function addProductToCart($product, $quantity, $exts)
    {
			//product_type
        if( !empty($product) ){
            $translator = $this->getServiceLocator()->get('translator');
            $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
            $ext_list = array();
            $extensions_require = NULL;
            $id = $product['products_id'];
            $product_type = 0;
            $extensions = array();
            if(!empty($exts)){
                $extensions = $this->getModelTable('ProductsTable')->getExtensions($exts);
            }
            $extensions_require = $this->getModelTable('ProductsTable')->getExtensionsAlwaysProduct($id);

            if( $productsHelper->getToBuy($product) && !empty($extensions) ){
                foreach($extensions as $key => $ext){
                    if($ext['is_always'] == 0){
                        $ext_list[$ext['id']] = $ext;
                    }
                }
            }
            if(!empty($product['products_type_id'])){
                $product_type = $product['products_type_id'];
            }
            if(!isset($_SESSION['products_deals'])){
                $products_deals = $this->getModelTable('CategoriesTable')->getAllGoldTimerCurrent(array(),0, 100);
                $deals_data = array();
                foreach($products_deals as $p){
                    $deals_data[$p['products_id']] = $p;
                }
                $_SESSION['products_deals'] = $deals_data;
                $products_deals = $deals_data;
            }else{
                $products_deals = $_SESSION['products_deals'];
            }
            if(isset($products_deals[$product['products_id']])){
                $p = $products_deals[$product['products_id']];
                $product['price_sale'] = $p['price_sale'];
            }
            if (!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$id])) {
                $promotion_list = array();
                if (trim($product['promotion_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion'] ? $product['promotion'] : 0,
                        'text' => $product['promotion_description'],
                    );
                }
                if (trim($product['promotion1_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion1'] ? $product['promotion1'] : 0,
                        'text' => $product['promotion1_description'],
                    );
                }
                if (trim($product['promotion2_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion2'] ? $product['promotion2'] : 0,
                        'text' => $product['promotion2_description'],
                    );
                }
                if (trim($product['promotion3_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion3'] ? $product['promotion3'] : 0,
                        'text' => $product['promotion3_description'],
                    );
                }
                if(!isset($quantity) || $quantity==0){
                    $quantity=1;
                }
                $_SESSION['cart'][$id] = array(
                    'id' => $id,
                    'thumb' => $product['thumb_image'],
                    'code' => $product['products_code'],
                    'title' => $product['products_title'],
                    'alias' => $product['products_alias'],
                    'total_price_extention' => $product['total_price_extention'],
                    'quantity' => $quantity,
                    'price' => $product['price'],//gia chua thue
                    'price_sale' => $product['price_sale'],//gia chua thue
                    'vat' => (int)$product['vat'],
                    'promotion' => 0,
                    'promotion_list' => $promotion_list,
                    'extensions' => $ext_list,
                    'extensions_require' => $extensions_require,
                    'product_type' => array($product_type => array(
                            'products_id' => $product['products_id'],
                            'products_code' => $product['products_code'],
                            'categories_id' => $product['categories_id'],
                            'manufacturers_id' => $product['manufacturers_id'],
                            'users_id' => $product['users_id'],
                            'users_fullname' => $product['users_fullname'],
                            'products_title' => $product['products_title'],
                            'products_alias' => $product['products_alias'],
                            'products_description' => $product['products_description'],
                            'products_longdescription' => $product['products_longdescription'],
                            'bao_hanh' => (empty($product['bao_hanh']) ? '' : $product['bao_hanh']),
                            'promotion' => (empty($product['promotion']) ? '' : $product['promotion']),
                            'promotion_description' => (empty($product['promotion_description']) ? '' : $product['promotion_description']),
                            'promotion_ordering' => (empty($product['promotion_ordering']) ? '' : $product['promotion_ordering']),
                            'promotion1' => (empty($product['promotion1']) ? '' : $product['promotion1']),
                            'promotion1_description' => (empty($product['promotion1_description']) ? '' : $product['promotion1_description']),
                            'promotion1_ordering' => (empty($product['promotion1_ordering']) ? '' : $product['promotion1_ordering']),
                            'promotion2' => (empty($product['promotion2']) ? '' : $product['promotion2']),
                            'promotion2_description' => (empty($product['promotion2_description']) ? '' : $product['promotion2_description']),
                            'promotion2_ordering' => (empty($product['promotion2_ordering']) ? '' : $product['promotion2_ordering']),
                            'promotion3' => (empty($product['promotion3']) ? '' : $product['promotion3']),
                            'promotion3_description' => (empty($product['promotion3_description']) ? '' : $product['promotion3_description']),
                            'promotion3_ordering' => (empty($product['promotion3_ordering']) ? '' : $product['promotion3_ordering']),
                            'seo_keywords' => $product['seo_keywords'],
                            'seo_description' => $product['seo_description'],
                            'seo_title' => $product['seo_title'],
                            'products_more' => $product['products_more'],
                            'is_published' => $product['is_published'],
                            'is_new' => $product['is_new'],
                            'is_available' => $product['is_available'],
                            'is_hot' => $product['is_hot'],
                            'is_goingon' => $product['is_goingon'],
                            'is_sellonline' => $product['is_sellonline'],
                            'is_viewed' => $product['is_viewed'],
                            'position_view' => $product['position_view'],
                            'tra_gop' => $product['tra_gop'],
                            'date_create' => $product['date_create'],
                            'date_update' => $product['date_update'],
                            'hide_price' => $product['hide_price'],
                            'wholesale' => $product['wholesale'],
                            'ordering' => $product['ordering'],
                            'thumb_image' => $product['thumb_image'],
                            't_thumb_image' => (empty($product['t_thumb_image']) ? '' : $product['t_thumb_image']),
                            'number_views' => $product['number_views'],
                            'vat' => $product['vat'],
                            'youtube_video' => $product['youtube_video'],
                            'tags' => $product['tags'],
                            'type_view' => $product['type_view'],
                            'title_extention_always' => $product['title_extention_always'],
                            'total_price_extention' => $product['total_price_extention'],
                            't_price' => $product['t_price'],
                            't_price_sale' => $product['t_price_sale'],
                            't_quantity' => $product['t_quantity'],
                            't_is_available' => $product['t_is_available'],
                            'products_type_id' => $product_type,
                            'type_name' => $product['type_name'],
                            'price' => $product['price'],
                            'price_sale' => $product['price_sale'],
                            'quantity' => $quantity,
                            'is_available' => $productsHelper->getIsAvailable($product),
                            'extensions' => $ext_list,
                            'extensions_require' => $extensions_require,
                            'transportation_id' => $product['transportation_id'],
                        )),
                );

            }elseif(isset($_SESSION['cart']) && isset($_SESSION['cart'][$id]) && count($ext_list) > 0){
                if(isset($_SESSION['cart'][$id]['product_type'][$product_type])){
                    unset($_SESSION['cart'][$id]['product_type'][$product_type]['extensions']);
                    $_SESSION['cart'][$id]['product_type'][$product_type]['extensions'] = $ext_list;
                    $_SESSION['cart'][$id]['product_type'][$product_type]['quantity'] += $quantity;
                }else{
                    $_SESSION['cart'][$id]['product_type'][$product_type] = array(
                        'products_id' => $product['products_id'],
                            'products_code' => $product['products_code'],
                            'categories_id' => $product['categories_id'],
                            'manufacturers_id' => $product['manufacturers_id'],
                            'users_id' => $product['users_id'],
                            'users_fullname' => $product['users_fullname'],
                            'products_title' => $product['products_title'],
                            'products_alias' => $product['products_alias'],
                            'products_description' => $product['products_description'],
                            'products_longdescription' => $product['products_longdescription'],
                            'bao_hanh' => (empty($product['bao_hanh']) ? '' : $product['bao_hanh']),
                            'promotion' => (empty($product['promotion']) ? '' : $product['promotion']),
                            'promotion_description' => (empty($product['promotion_description']) ? '' : $product['promotion_description']),
                            'promotion_ordering' => (empty($product['promotion_ordering']) ? '' : $product['promotion_ordering']),
                            'promotion1' => (empty($product['promotion1']) ? '' : $product['promotion1']),
                            'promotion1_description' => (empty($product['promotion1_description']) ? '' : $product['promotion1_description']),
                            'promotion1_ordering' => (empty($product['promotion1_ordering']) ? '' : $product['promotion1_ordering']),
                            'promotion2' => (empty($product['promotion2']) ? '' : $product['promotion2']),
                            'promotion2_description' => (empty($product['promotion2_description']) ? '' : $product['promotion2_description']),
                            'promotion2_ordering' => (empty($product['promotion2_ordering']) ? '' : $product['promotion2_ordering']),
                            'promotion3' => (empty($product['promotion3']) ? '' : $product['promotion3']),
                            'promotion3_description' => (empty($product['promotion3_description']) ? '' : $product['promotion3_description']),
                            'promotion3_ordering' => (empty($product['promotion3_ordering']) ? '' : $product['promotion3_ordering']),
                            'seo_keywords' => $product['seo_keywords'],
                            'seo_description' => $product['seo_description'],
                            'seo_title' => $product['seo_title'],
                            'products_more' => $product['products_more'],
                            'is_published' => $product['is_published'],
                            'is_new' => $product['is_new'],
                            'is_available' => $product['is_available'],
                            'is_hot' => $product['is_hot'],
                            'is_goingon' => $product['is_goingon'],
                            'is_sellonline' => $product['is_sellonline'],
                            'is_viewed' => $product['is_viewed'],
                            'position_view' => $product['position_view'],
                            'tra_gop' => $product['tra_gop'],
                            'date_create' => $product['date_create'],
                            'date_update' => $product['date_update'],
                            'hide_price' => $product['hide_price'],
                            'wholesale' => $product['wholesale'],
                            'ordering' => $product['ordering'],
                            'thumb_image' => $product['thumb_image'],
                            't_thumb_image' => (empty($product['t_thumb_image']) ? '' : $product['t_thumb_image']),
                            'number_views' => $product['number_views'],
                            'vat' => $product['vat'],
                            'youtube_video' => $product['youtube_video'],
                            'tags' => $product['tags'],
                            'type_view' => $product['type_view'],
                            'title_extention_always' => $product['title_extention_always'],
                            'total_price_extention' => $product['total_price_extention'],
                            't_price' => $product['t_price'],
                            't_price_sale' => $product['t_price_sale'],
                            't_quantity' => $product['t_quantity'],
                            't_is_available' => $product['t_is_available'],
                            'products_type_id' => $product_type,
                            'type_name' => $product['type_name'],
                            'price' => $product['price'],
                            'price_sale' => $product['price_sale'],
                            'quantity' => $quantity,
                            'is_available' => $productsHelper->getIsAvailable($product),
                            'extensions' => $ext_list,
                            'extensions_require' => $extensions_require,
                            'transportation_id' => $product['transportation_id'],
                    );
                }
            }else if(isset($_SESSION['cart'][$id])){
                if(isset($_SESSION['cart'][$id]['product_type'][$product_type])){
                    $_SESSION['cart'][$id]['product_type'][$product_type]['quantity'] += $quantity;
                }else{
                    $_SESSION['cart'][$id]['product_type'][$product_type] = array(
                        'products_id' => $product['products_id'],
                        'products_code' => $product['products_code'],
                        'categories_id' => $product['categories_id'],
                        'manufacturers_id' => $product['manufacturers_id'],
                        'users_id' => $product['users_id'],
                        'users_fullname' => $product['users_fullname'],
                        'products_title' => $product['products_title'],
                        'products_alias' => $product['products_alias'],
                        'products_description' => $product['products_description'],
                        'products_longdescription' => $product['products_longdescription'],
                        'bao_hanh' => (empty($product['bao_hanh']) ? '' : $product['bao_hanh']),
                        'promotion' => (empty($product['promotion']) ? '' : $product['promotion']),
                        'promotion_description' => (empty($product['promotion_description']) ? '' : $product['promotion_description']),
                        'promotion_ordering' => (empty($product['promotion_ordering']) ? '' : $product['promotion_ordering']),
                        'promotion1' => (empty($product['promotion1']) ? '' : $product['promotion1']),
                        'promotion1_description' => (empty($product['promotion1_description']) ? '' : $product['promotion1_description']),
                        'promotion1_ordering' => (empty($product['promotion1_ordering']) ? '' : $product['promotion1_ordering']),
                        'promotion2' => (empty($product['promotion2']) ? '' : $product['promotion2']),
                        'promotion2_description' => (empty($product['promotion2_description']) ? '' : $product['promotion2_description']),
                        'promotion2_ordering' => (empty($product['promotion2_ordering']) ? '' : $product['promotion2_ordering']),
                        'promotion3' => (empty($product['promotion3']) ? '' : $product['promotion3']),
                        'promotion3_description' => (empty($product['promotion3_description']) ? '' : $product['promotion3_description']),
                        'promotion3_ordering' => (empty($product['promotion3_ordering']) ? '' : $product['promotion3_ordering']),
                        'seo_keywords' => $product['seo_keywords'],
                        'seo_description' => $product['seo_description'],
                        'seo_title' => $product['seo_title'],
                        'products_more' => $product['products_more'],
                        'is_published' => $product['is_published'],
                        'is_new' => $product['is_new'],
                        'is_available' => $product['is_available'],
                        'is_hot' => $product['is_hot'],
                        'is_goingon' => $product['is_goingon'],
                        'is_sellonline' => $product['is_sellonline'],
                        'is_viewed' => $product['is_viewed'],
                        'position_view' => $product['position_view'],
                        'tra_gop' => $product['tra_gop'],
                        'date_create' => $product['date_create'],
                        'date_update' => $product['date_update'],
                        'hide_price' => $product['hide_price'],
                        'wholesale' => $product['wholesale'],
                        'ordering' => $product['ordering'],
                        'thumb_image' => $product['thumb_image'],
                        't_thumb_image' => (empty($product['t_thumb_image']) ? '' : $product['t_thumb_image']),
                        'number_views' => $product['number_views'],
                        'vat' => $product['vat'],
                        'youtube_video' => $product['youtube_video'],
                        'tags' => $product['tags'],
                        'type_view' => $product['type_view'],
                        'title_extention_always' => $product['title_extention_always'],
                        'total_price_extention' => $product['total_price_extention'],
                        't_price' => $product['t_price'],
                        't_price_sale' => $product['t_price_sale'],
                        't_quantity' => $product['t_quantity'],
                        't_is_available' => $product['t_is_available'],
                        'products_type_id' => $product_type,
                        'type_name' => $product['type_name'],
                        'price' => $product['price'],
                        'price_sale' => $product['price_sale'],
                        'quantity' => $quantity,
                        'is_available' => $productsHelper->getIsAvailable($product),
                        'extensions' => $ext_list,
                        'extensions_require' => $extensions_require,
                        'transportation_id' => $product['transportation_id'],
                    );
                }
            }
            
            
            //$price_this = ($productsHelper->getPriceSaleSimple($product)+$product['total_price_extention'])*$_SESSION['cart'][$id]['product_type'][$product_type]['quantity'] + (($productsHelper->getPriceSaleSimple($product)+$product['total_price_extention'])*$_SESSION['cart'][$id]['product_type'][$product_type]['quantity'] * $product['vat'] / 100);            
            //$price_this_old = ($productsHelper->getPriceSimple($product)+$product['total_price_extention'])*$_SESSION['cart'][$id]['product_type'][$product_type]['quantity'] + (($productsHelper->getPriceSimple($product)+$product['total_price_extention'])*$_SESSION['cart'][$id]['product_type'][$product_type]['quantity']* $product['vat'] / 100);
            $quantity = $_SESSION['cart'][$id]['product_type'][$product_type]['quantity'];
            $price_this = $productsHelper->getPriceSale($product)*$quantity;            
            $price_tax =  $price_this + ($price_this * $product['vat'] / 100);			
            $price_this_old = $productsHelper->getPrice($product)*$quantity;
            $price_old_tax =  $price_this_old + ($price_this_old * $product['vat'] / 100);

            if( !empty($ext_list) ){
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
            return TRUE;
        }
        return false;
    }

    public function popAddToCartAction()
    {
        $ext_list = array();
        $extensions_require = NULL;
        $error = array();
        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $id = 0;
        $quantity = 1;
        $product = array();
        if($request->isPost()){
            $exts = $request->getPost('extention', 0);
            $product_type = $request->getPost('product_type', 0);
            $id = $request->getPost('products_id', 0);
            $quantity = $request->getPost('quantity', 1);
        }else{
            $exts = $this->params()->fromQuery('extention', 0);
            $product_type = $this->params()->fromQuery('product_type', 0);
            $id = $this->params()->fromRoute('id', 0);
            $quantity = $this->params()->fromQuery('quantity', 1);
        }

        $ajax = $this->params()->fromQuery('ajax', 0);

        if (!empty($id)) {
            try {
                if(empty($product_type)){
                    $product = $this->getModelTable('ProductsTable')->getRow($id);
                }else{
                    $product = $this->getModelTable('ProductsTable')->getProductsWithType($id, $product_type);
                }

                if( !$productsHelper->getToBuy($product) ){
                    $error[] = $translator->translate('txt_product_not_available');
                }

            } catch (\Exception $ex) {
                $error[] = $ex->getMessage();
            }
        }else{
            $error[] = $translator->translate('txt_product_not_exit');
        }

        if(empty($error) && !empty($product)){
            $this->addProductToCart($product, $quantity, $exts);
        }

        $html = '';
        try{
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            $viewModel->setTemplate("application/cart/pop-add-to-cart");
            $viewModel->setVariables(array(
                'product_type_id' => $product_type,
                'products_id' => $id,
                'product' => $product,
                'error' => $error,
            ));
            $viewRender = $this->getServiceLocator()->get('ViewRenderer');
            $html = $viewRender->render($viewModel);
        } catch (\Exception $e) {};

        if(!empty($ajax)){
            echo json_encode(array(
                'type' => 'popAddToCart',
                'flag' => TRUE,
                'product' => $product,
                'html' => $html,
                'data' => $_SESSION['cart']
            ));
            die;
        }

        return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
            'action' => 'index'
        ));

    }


    public function updateQuantityProductInCartAjackAction()
    {
        $error = array();
        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        if($request->isPost()){
            $quantity = $request->getPost('quantity', 0);
            $id = $request->getPost('products_id', 0);
            $type_id = $request->getPost('products_type', 0);
        }else{
            $quantity = $this->params()->fromQuery('quantity', 0);
            $id = $this->params()->fromQuery('products_id', 0);
            $type_id = $this->params()->fromQuery('products_type', 0);
        }
            
        if ( !empty($id) && !empty($quantity) ) {
            if ( isset($_SESSION['cart']) && isset($_SESSION['cart'][$id])  && isset($_SESSION['cart'][$id]['product_type'][$type_id]) ) {
                try {
                    if(empty($type_id)){
                        $product = $this->getModelTable('ProductsTable')->getRow($id);
                    }else{
                        $product = $this->getModelTable('ProductsTable')->getProductsWithType($id, $type_id);
                    }
                    if( ($product->is_available == 0 && $product->quantity == 0) || $product->is_delete == 1 || $product->is_published == 0 ){
                        unset($_SESSION['cart'][$id]['product_type'][$type_id]);
                    }else{
                        $_SESSION['cart'][$id]['product_type'][$type_id]['quantity'] = $quantity;
                        $price_this = $productsHelper->getPriceSale($product)*$quantity;
                        $price_tax =  $price_this + ($price_this * $product['vat'] / 100);
                        $price_this_old = $productsHelper->getPrice($product)*$quantity;
                        $price_old_tax =  $price_this_old + ($price_this_old * $product['vat'] / 100);
                        
                        if( !empty($_SESSION['cart'][$id]['product_type'][$type_id]['extensions']) ){
                            foreach($_SESSION['cart'][$id]['product_type'][$type_id]['extensions'] as $ext){
                                $priceQl = $ext['price']*$ext['quantity'];
                                $price_this += $priceQl;
                                $price_tax += $priceQl;
                                $price_this_old += $priceQl;
                                $price_old_tax += $priceQl;
                            }
                        }

                        $_SESSION['cart'][$id]['product_type'][$type_id]['price_total'] = $price_this;
                        $_SESSION['cart'][$id]['product_type'][$type_id]['price_total_old'] = $price_this_old;
                        $_SESSION['cart'][$id]['product_type'][$type_id]['price_total_tax'] = $price_tax;
                        $_SESSION['cart'][$id]['product_type'][$type_id]['price_total_old_tax'] = $price_old_tax;
                    }
                } catch (\Exception $e) {
                    unset($_SESSION['cart'][$id]);
                    echo json_encode(array(
                        'flag' => FALSE,
                        'type' => 'updateQuantityProductInCartAjack',
                        'msg' => $translator->translate('txt_san_pham_khong_co_trong_cart')
                    ));
                    die;
                }
                echo json_encode(array(
                    'flag' => TRUE,
                    'type' => 'updateQuantityProductInCartAjack',
                    'data' => $_SESSION['cart']
                ));
                die;
            }else{
                echo json_encode(array(
                    'flag' => FALSE,
                    'type' => 'updateQuantityProductInCartAjack',
                    'msg' => $translator->translate('txt_san_pham_khong_co_trong_cart')
                ));
                die;
            }
        }else{
            echo json_encode(array(
                'flag' => FALSE,
                'type' => 'updateQuantityProductInCartAjack',
                'msg' => $translator->translate('txt_phuong_thuc_khong_ho_tro')
            ));
            die;
        }

    }

    public function addtoCartAjackAction()
    {
        $ext_list = array();
        $extensions_require = NULL;
        $error = array();
        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $id = 0;
        $quantity = 1;
        $product = array();
        if($request->isPost()){
            $exts = $request->getPost('extention', 0);
            $product_type = $request->getPost('product_type', 0);
            $id = $request->getPost('products_id', 0);
            $quantity = $request->getPost('quantity', 1);
        }else{
            $exts = $this->params()->fromQuery('extention', 0);
            $product_type = $this->params()->fromQuery('product_type', 0);
            $id = $this->params()->fromRoute('id', 0);
            $quantity = $this->params()->fromQuery('quantity', 1);
        }

        if (!empty($id)) {
            try {
                if(empty($product_type)){
                    $product = $this->getModelTable('ProductsTable')->getRow($id);
                }else{
                    $product = $this->getModelTable('ProductsTable')->getProductsWithType($id, $product_type);
                }
                
                if( !$productsHelper->getToBuy($product) ){
                    $error[] = $translator->translate('txt_product_not_available');
                }

            } catch (\Exception $ex) {
                $error[] = $ex->getMessage();
            }
        }else{
            $error[] = $translator->translate('txt_product_not_exit');
        }

        if(empty($error) && !empty($product)){
            $this->addProductToCart($product, $quantity, $exts);
        }

        echo json_encode(array(
            'flag' => TRUE,
            'type' => 'addToCartAjack',
            'product' => $product,
            'data' => $_SESSION['cart'],
            'msg' => $translator->translate('txt_them_san_pham_trong_cart_thanh_cong')
        ));
        die;
        

    }

    public function removeAction()
    {
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');

        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');
        if($request->isPost()){
            $id = $request->getPost('products_id', 0);
            $type_id = $request->getPost('product_type', 0);
        }else{
            $id = $this->params()->fromQuery('products_id', 0);
            $type_id = $this->params()->fromQuery('product_type', 0);
        }
        
        $ajax = $this->params()->fromQuery('ajax', 0);
        $product = array();
        if ( !empty($id) ) {
            if ( isset($_SESSION['cart']) && isset($_SESSION['cart'][$id]) && isset($_SESSION['cart'][$id]['product_type'][$type_id])) {
                $product = $_SESSION['cart'][$id];
                unset($_SESSION['cart'][$id]['product_type'][$type_id]);
                if(empty($_SESSION['cart'][$id]['product_type'])){
                    unset($_SESSION['cart'][$id]);
                }
            }
            $carts = $helper->getCart();
            if( empty($carts) ){
                unset($_SESSION['cart']);
                $_SESSION['cart'] = array();
                unset($_SESSION['PAYMENT_BUYER']);
                unset($_SESSION['PAYMENT_SHIPPER']);
            }
            if(!empty($ajax)){
                echo json_encode(array(
                    'type' => 'removeProductInCart',
                    'flag' => TRUE,
                    'product' => $product,
                    'data' => $_SESSION['cart'],
                    'msg' => $translator->translate('txt_xoa_san_pham_trong_cart_thanh_cong')
                ));
                die;
            }
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
            'action' => 'index'
        ));
    }

    public function popEditProductAction()
    {
        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');
        $hpProducts = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        if($request->isPost()){
            $id = $request->getPost('products_id', 0);
            $type_id = $request->getPost('product_type', 0);
        }else{
            $id = $this->params()->fromQuery('products_id', 0);
            $type_id = $this->params()->fromQuery('product_type', 0);
        }
        
        $ajax = $this->params()->fromQuery('ajax', 0);
        $product = array();
        if ( !empty($id) ) {
            if (    isset($_SESSION['cart']) 
                    && isset($_SESSION['cart'][$id]) 
                    && isset($_SESSION['cart'][$id]['product_type'][$type_id])) {
                $product = $_SESSION['cart'][$id]['product_type'][$type_id];
                $exts01 = $hpProducts->getExtension($product);
                $exts02 = $hpProducts->getExtensionRequire($product);
                $exts = array_merge($exts01, $exts02);
                $extIds = array();
                foreach ($exts as $kex => $ext) {
                    $extIds[] = $ext['id'];
                }
                $p = $this->getModelTable('ProductsTable')->getRow($id);
                $extensions = $this->getModelTable('ProductsTable')->getExtensionsProduct($id);
                $typeProducts = $this->getModelTable('ProductsTable')->getTypeProduct($id);

                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                $viewModel->setTemplate("application/cart/pop-edit-product");
                $viewModel->setVariables(array(
                    'product_type_id' => $type_id,
                    'products_id' => $id,
                    'p' => $p,
                    'product' => $product,
                    'extensions' => $extensions,
                    'typeProducts' => $typeProducts,
                    'exts' => $exts,
                    'extIds' => $extIds,
                ));
                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);

                if(!empty($ajax)){
                    echo json_encode(array(
                        'type' => 'popEditProduct',
                        'flag' => TRUE,
                        'html' => $html,
                        'product' => $product,
                        'data' => $_SESSION['cart'],
                        'msg' => $translator->translate('txt_xoa_san_pham_trong_cart_thanh_cong')
                    ));
                    die;
                }
            }
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
            'action' => 'index'
        ));
    }

    public function deleteProductInCartAjackAction()
    {
        $error = array();
        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');

        if($request->isPost()){
            $id = $request->getPost('products_id', 0);
            $type_id = $request->getPost('type_id', 0);
        }else{
            $id = $this->params()->fromQuery('products_id', 0);
            $type_id = $this->params()->fromQuery('type_id', 0);
        }

        $product = array();
        if ( !empty($id) ) {
            if ( isset($_SESSION['cart']) && isset($_SESSION['cart'][$id]) && isset($_SESSION['cart'][$id]['product_type'][$type_id])) {
                $product = $_SESSION['cart'][$id];
                unset($_SESSION['cart'][$id]['product_type'][$type_id]);
                if(empty($_SESSION['cart'][$id]['product_type'])){
                    unset($_SESSION['cart'][$id]);
                }
                echo json_encode(array(
                    'flag' => TRUE,
                    'type' => 'deleteProductInCartAjack',
                    'product' => $product,
                    'data' => $_SESSION['cart'],
                    'msg' => $translator->translate('txt_xoa_san_pham_trong_cart_thanh_cong')
                ));
                die;
            }else{
                echo json_encode(array(
                    'flag' => FALSE,
                    'type' => 'deleteProductInCartAjack',
                    'msg' => $translator->translate('txt_san_pham_khong_co_trong_cart')
                ));
                die;
            }
        }else{
            echo json_encode(array(
                'flag' => FALSE,
                'type' => 'deleteProductInCartAjack',
                'msg' => $translator->translate('txt_phuong_thuc_khong_ho_tro')
            ));
            die;
        }

    }


    public function paymentCartFastAction()
    {
        $translator = $this->getServiceLocator()->get('translator');
        $error = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            if (!($this->isLogin() || (isset($_POST['type']) && $_POST['type'] == 'fast-buy'))) {
                $error[] = $translator->translate('txt_not_auth');
            }
            if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
                $error[] = $translator->translate('txt_cart_error');
            }
            $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
            $price_total = 0;
            $price_total_old = 0;
            if(isset($_SESSION['cart']) && count($_SESSION['cart'])){
                $calculate = $helper->sumSubTotalPriceInCart();
                $price_total = $calculate['price_total'];
                $price_total_old = $calculate['price_total_old'];
            }
            $data = $request->getPost("trans");
            $order = $request->getPost("order");
            if (trim($data['full_name']) == '') {
                $error['full_name'] = "Bạn phải nhập tên người liên hệ";
            }
            if (trim($data['phone']) == '' || !is_numeric($data['phone'])) {
                $error['phone'] = "Số điện thoại không hợp lệ";
            }
            if (trim($data['email']) == '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $error['email'] = "Email không hợp lệ";
            }
            if (empty($data['country_id'])  || !is_numeric($data['country_id'])) {
                $error['country_id'] = "Bạn phải chọn country";
            }
            if (empty($data['type_address_delivery']) || !in_array($data['type_address_delivery'], array('global', 'city', 'districts', 'local'))){
                $error['type_address_delivery'] = "Kiểu địa chỉ không được bỏ trống";
            }
            if ($data['type_address_delivery'] == 'global' ){
                if (empty($data['city'])) {
                    $error['city'] = "Huyện/thành phố không được bỏ trống";
                }
                if (empty($data['zipcode'])) {
                    $error['zipcode'] = "zipcode không được bỏ trống";
                }
            }else if($data['type_address_delivery'] == 'city'){
                if (empty($data['cities_id'])) {
                    $error['cities_id'] = "Tỉnh/thành phố không được bỏ trống";
                }
            }else if($data['type_address_delivery'] == 'districts'){
                if (empty($data['cities_id'])) {
                    $error['cities_id'] = "Tỉnh/thành phố không được bỏ trống";
                }
                if (empty($data['districts_id'])) {
                    $error['districts_id'] = "Quận/Huyện không được bỏ trống";
                }
            }else if($data['type_address_delivery'] == 'local'){
                if (empty($data['cities_id'])) {
                    $error['cities_id'] = "Tỉnh/thành phố không được bỏ trống";
                }
                if (empty($data['districts_id'])) {
                    $error['districts_id'] = "Quận/Huyện không được bỏ trống";
                }
                if (empty($data['wards_id'])) {
                    $error['wards_id'] = "Xã/Phường không được bỏ trống";
                }
            }
            if (empty($data['address'])) {
                $error['address'] = "Địa chỉ không được bỏ trống";
            }
            if (empty($error)) {
                $dataPayment = $data;
                $dataMember = isset($_SESSION['MEMBER']) ? $_SESSION['MEMBER'] : array();
                $dataCart = $_SESSION['cart'];
                unset($dataCart['shipping']);
                $transportation = $this->getModelTable('TransportationTable')->getTransportationById($dataPayment['transportation_id']);

                $dataExtensions = array();
                $dataExtensionsTransportations = array();
                foreach ($dataCart as $products) {
                    foreach ($products['product_type'] as $type_id => $row) {
                        if(isset($row['extensions']) && count($row['extensions']) > 0){
                            if(!isset($dataExtensions[$products['id']][$type_id])){
                                $dataExtensions[$products['id']][$type_id] = array();
                            }
                            foreach($row['extensions'] as $ext){
                                if(!isset($dataExtensions[$products['id']][$type_id][$ext['id']])){
                                    $dataExtensions[$products['id']][$type_id][$ext['id']] = array();
                                }
                                $dataExtensions[$products['id']][$type_id][$ext['id']] = array(
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

                        if(isset($row['extensions_require']) && count($row['extensions_require']) > 0){
                            if(!isset($dataExtensions[$products['id']][$type_id])){
                                $dataExtensions[$products['id']][$type_id] = array();
                            }
                            foreach($row['extensions_require'] as $ext){
                                if(!isset($dataExtensions[$products['id']][$type_id][$ext['id']])){
                                    $dataExtensions[$products['id']][$type_id][$ext['id']] = array();
                                }
                                $dataExtensions[$products['id']][$type_id][$ext['id']] = array(
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

                $extensions_transportation = array_map(function($a){
                    if(isset($a['extensions_transportations'])){
                        return $a['extensions_transportations'];
                    }
                    return FALSE;
                }, $dataCart);

                $total = 0;
                $calculate = $helper->sumSubTotalPriceInCart();
                $total = $calculate['price_total'];
                $total_orig = $calculate['price_total_orig'];

                $dataOrder = $order;
                /**
                 * Lấy template Hóa đơn
                 */
                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                $viewModel->setTemplate("application/cart/content_invoice");
                $viewModel->setVariables(array(
                    'datapayment' => $dataPayment,
                    'datamember' => $dataMember,
                    'datacart' => $dataCart,
                    'dataorder' => $dataOrder,
                    'dataExtensions' => $dataExtensions,
                    'transportation' => $transportation,
                    'extensions_transportation' => $extensions_transportation,
                    'total' => $total,
                    'ship' => $this->website['ship'],
                ));
                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);

                $dataSave = array();
                $dataSave['users_id'] = (!empty($dataMember['users_id'])) ? $dataMember['users_id'] : NULL;
                $dataSave['transportation_id'] = $dataPayment['transportation_id'];
                $dataSave['invoice_description'] = strip_tags($dataPayment['invoice_description']);
                $dataSave['is_published'] = 1;
                $dataSave['is_delete'] = 0;
                $dataSave['full_name'] = strip_tags($dataPayment['full_name']);
                $dataSave['phone'] = strip_tags($dataPayment['phone']);
                $dataSave['email'] = strip_tags($dataPayment['email']);
                $dataSave['type_address_delivery'] = strip_tags($dataPayment['type_address_delivery']);
                $dataSave['country_id'] = $dataPayment['country_id'];
                $dataSave['city'] = $dataPayment['city'];
                $dataSave['zipcode'] = $dataPayment['zipcode'];
                $dataSave['address'] = strip_tags($dataPayment['address']);
                $dataSave['cities_id'] = empty($dataPayment['cities_id']) ? 0 : $dataPayment['cities_id'];
                $dataSave['districts_id'] = empty($dataPayment['districts_id']) ? 0 : $dataPayment['districts_id'];
                $dataSave['wards_id'] = empty($dataPayment['wards_id']) ? 0 : $dataPayment['wards_id'];
                $dataSave['content'] = htmlentities(strip_tags($html), ENT_QUOTES, 'UTF-8');
                $dataSave['date_create'] = date('Y-m-d H:i:s');
                $dataSave['date_update'] = date('Y-m-d H:i:s');
                $dataSave['payment'] = 'unpaid';
                $dataSave['delivery'] = 'no_delivery';
                $dataSave['invoice_title'] = "HD" . strtotime(date("Y-m-d H:i:s"));
                $dataSave['email_subscription'] = (!empty($dataOrder['subscription'])) ? $dataOrder['email'] : NULL;
                $dataSave['company_name'] = (!empty($dataOrder['xuathoadon'])) ? $dataOrder['company_name'] : NULL;
                $dataSave['company_tax_code'] = (!empty($dataOrder['xuathoadon'])) ? $dataOrder['company_tax_code'] : NULL;
                $dataSave['company_address'] = (!empty($dataOrder['xuathoadon'])) ? $dataOrder['company_address'] : NULL;

                if(isset($dataCart['coupon'])){
                    $dataCart['coupon']['price_used'] = $total_orig;
                }
                $dataSave['total'] = $total;
                $dataSave['value_ship'] = $this->website['ship'];
				// Sửa lại vì chua thanh toán mà trong admin báo đã thanh toán
               // if($total == 0){
                   // $dataSave['payment'] = 'paid';
               // }
                // Kết thúc
                try{
                    $id = $this->getModelTable('InvoiceTable')->saveInvoice($dataSave, $dataCart, $extensions_transportation, $dataExtensions);
                }catch(\Exception $ex){
                    $error['invoice'] = $ex->getMessage();
                }
                
                if(empty($error)){
                    try{
                        $invoice = $this->getModelTable('InvoiceTable')->getInvoiceByTitle($id);
                    }catch(\Exception $ex){
                        echo json_encode(array(
                            'flag' => FALSE,
                            'type' => 'paymentCartFast',
                            'detail' => $ex->getMessage()
                        ));
                        die;
                    }
                    unset($_SESSION['cart']);
                    if($dataSave['payment'] == 'paid'){
                        $_SESSION['invoice_id'] = $id;
                    }
                    if($this->sendMail($invoice)){
                        echo json_encode(array(
                            'flag' => true,
                            'invoice' => $invoice,
                            'type' => 'paymentCartFast',
                            'msg' => $translator->translate('txt_ban_da_dat_hang_thanh_cong')
                        ));
                        die;
                    }else{
                        echo json_encode(array(
                            'flag' => FALSE,
                            'type' => 'paymentCartFast',
                            'msg' => $translator->translate('txt_khong_gui_duoc_mail')
                        ));
                        die;
                    }
                }else{
                    echo json_encode(array(
                        'flag' => FALSE,
                        'type' => 'paymentCartFast',
                        'msg' => $translator->translate('txt_co_mot_loi_xu_ly'),
                        'detail' => $error
                    ));
                    die;
                }
            } else {
                echo json_encode(array(
                    'flag' => FALSE,
                    'type' => 'paymentCartFast',
                    'msg' => $translator->translate('txt_co_mot_loi_nhap_lieu'),
                    'detail' => $error
                ));
                die;
            }
        }
        echo json_encode(array(
            'flag' => FALSE,
            'type' => 'paymentCartFast',
            'msg' => $translator->translate('txt_khong_tim_thay')
        ));
        die;
    }

    public function addToCartAction()
    {
        $ext_list = array();
        $extensions_require = NULL;
        $error = array();
        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $id = 0;
        $quantity = 1;
        $product = array();
        $ajax = $this->params()->fromQuery('ajax', 0);
        if($request->isPost()){
            $exts = $request->getPost('extention', 0);
            $product_type = $request->getPost('product_type', 0);
            $id = $request->getPost('products_id', 0);
            $quantity = $request->getPost('quantity', 1);
        }else{
            $exts = $this->params()->fromQuery('extention', 0);
            $product_type = $this->params()->fromQuery('product_type', 0);
            $id = $this->params()->fromRoute('id', 0);
            $quantity = $this->params()->fromQuery('quantity', 1);
        }

        if (!empty($id)) {
            try {
                if(empty($product_type)){
                    $product = $this->getModelTable('ProductsTable')->getRow($id);
                }else{
                    $product = $this->getModelTable('ProductsTable')->getProductsWithType($id, $product_type);
                }
                if( !$productsHelper->getToBuy($product) ){
                    $error[] = $translator->translate('txt_product_not_available');
                }

            } catch (\Exception $ex) {
                $error[] = $ex->getMessage();
            }
        }else{
            $error[] = $translator->translate('txt_product_not_exit');
        }
        if(empty($error) && !empty($product)){
            $this->addProductToCart($product, $quantity, $exts);
        }

        return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
            'action' => 'index'
        ));
    }

    public function updateProductAction()
    {
        $ext_list = array();
        $extensions_require = NULL;
        $error = array();
        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $id = 0;
        $quantity = 1;
        $product = array();
        $ajax = $this->params()->fromQuery('ajax', 0);
        if($request->isPost()){
            $id = $request->getPost('products_id', 0);
            $product_type_id = $request->getPost('product_type_id', 0);
            $product_type = $request->getPost('product_type', 0);
            $quantity = $request->getPost('quantity', 1);
            $exts = $request->getPost('extention', 0);

            if( !empty($_SESSION['cart'])
                && !empty($_SESSION['cart'][$id])
                && !empty($_SESSION['cart'][$id]['product_type'])
                && !empty($_SESSION['cart'][$id]['product_type'][$product_type_id]) ){
                unset($_SESSION['cart'][$id]['product_type'][$product_type_id]);
                if( empty($_SESSION['cart'][$id]['product_type']) ){
                    unset($_SESSION['cart'][$id]);
                }
                try {
                    if(empty($product_type)){
                        $product = $this->getModelTable('ProductsTable')->getRow($id);
                    }else{
                        $product = $this->getModelTable('ProductsTable')->getProductsWithType($id, $product_type);
                    }
                    if( !$productsHelper->getToBuy($product) ){
                        $error[] = $translator->translate('txt_product_not_available');
                    }

                } catch (\Exception $ex) {
                    $error[] = $ex->getMessage();
                }
                if(empty($error) && !empty($product)){
                    $this->addProductToCart($product, $quantity, $exts);
                    if( !empty($_SESSION['PAYMENT_BUYER'])
                        && isset($_SESSION['PAYMENT_BUYER']['shipping_id']) ){
                        unset($_SESSION['PAYMENT_BUYER']['shipping_id']);
                    }
                    echo json_encode(array(
                        'flag' => TRUE,
                        'type' => 'updateProduct',
                        'cart' => $_SESSION['cart'],
                        'id' => $id,
                        'product_type_id' => $product_type_id,
                        'product_type' => $product_type,
                        'quantity' => $quantity,
                        'extention' => $exts,
                        'product' => $_SESSION['cart'][$id]['product_type'][$product_type],
                        'msg' => $translator->translate('txt_cap_nhat_thanh_cong')
                    ));
                    die;
                }
                echo json_encode(array(
                    'flag' => FALSE,
                    'type' => 'updateProduct',
                    'msg' => $translator->translate('txt_co_loi_xay_ra')
                ));
                die;
            }
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
            'action' => 'index'
        ));
    }

    public function getExtentionAction()
    {
        $ext_list = array();
        $extensions_require = NULL;
        $error = array();
        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $id = (int)$this->params()->fromQuery('products_id', 0);
        $product_type = (int)$this->params()->fromQuery('product_type', 0);
        $ajax = $this->params()->fromQuery('ajax', 0);
        $products = array();
        if (isset($_SESSION['cart'][$id]) && isset($_SESSION['cart'][$id]['product_type'][$product_type]) ) {
            $products = $_SESSION['cart'][$id];
        }
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate("application/cart/get-extention");
        $viewModel->setVariables(array(
            'products_id' => $id,
            'product_type_id' => $product_type,
            'products' => $products
        ));

        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRender->render($viewModel);
        if(!empty($ajax)){
            echo json_encode(array(
                'type' => 'getExtention',
                'flag' => TRUE,
                'html' => $html,
                'products_id' => $id,
                'product_type_id' => $product_type,
                'products' => $products
            ));
            die;
        }

        return $viewModel;
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
        return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
            'action' => 'index'
        ));
    }

    public function paymentAction()
    {
        if ( $this->getVersionCart() != $this->version ) {
            return $this->redirect()->toRoute( $this->getUrlRouterLang().$this->getRouteCart() , array(
                'action' => 'process'
            ));
        }

        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator');

        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($translator->translate('txt_title_site_payment'));
        
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $couponsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Coupons');
        $currencyHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Currency');
        $invoiceHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Invoice');
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        if (!($this->isLogin() || (isset($_GET['type']) && $_GET['type'] == 'fast-buy'))) {
            return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
                'action' => 'auth'
            ));
        }
        $cart = $helper->getCart();
        if( empty($cart) ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }
        
        $price_total = 0;
        $price_total_old = 0;
        $price_total_orig = 0;
        $price_total_tax = 0;
        $price_total_old_tax = 0;
        $price_total_orig_tax = 0;
        if( isset($_SESSION['cart']) 
            && count($_SESSION['cart'])){
            $calculate = $helper->sumSubTotalPriceInCart();
            $price_total = $calculate['price_total'];
            $price_total_old = $calculate['price_total_old'];
            $price_total_orig = $calculate['price_total_orig'];
            $price_total_tax = $calculate['price_total_tax'];
            $price_total_old_tax = $calculate['price_total_old_tax'];
            $price_total_orig_tax = $calculate['price_total_orig_tax'];
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $trans = $request->getPost("trans");
            $ships = $request->getPost("ships");
            $order = $request->getPost("order");

            $ship_to_different_address = $request->getPost("ship_to_different_address", 0);
            $error = array();
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

            if ( !empty($ship_to_different_address) && $ship_to_different_address == 1){
                if ( empty($ships['first_name']) ) {
                    $error['first_name'] = $translator->translate('txt_ban_phai_nhap_ten');
                }
                if ( empty($ships['last_name']) ) {
                    $error['last_name'] = $translator->translate('txt_ban_phai_nhap_ten');
                }
                if ( empty($ships['email']) || !filter_var($trans['email'], FILTER_VALIDATE_EMAIL)) {
                    $error['email'] = $translator->translate('txt_email_khong_hop_le');
                }
                if ( empty($ships['phone']) || !is_numeric($trans['phone'])) {
                    $error['phone'] = $translator->translate('txt_so_dien_thoai_khong_hop_le');
                }
                if ( empty($ships['country_id']) ) {
                    $error['country_id'] = $translator->translate('txt_chua_chon_contry');
                }else{
                    $country_ships = $this->getModelTable('CountryTable')->getOne($ships['country_id']);
                    $err = $this->validateInputContryPayment($ships, $country_ships);
                    $error = array_merge($error, $err);
                }
                $ships['full_name'] = $ships['first_name'].' '.$ships['last_name'];
            }

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

            $country_id = $trans['country_id'];
            if ( !empty($ship_to_different_address) && $ship_to_different_address == 1){
                $country_id = $ships['country_id'];
            }
            $cities_id = 0;
            $districts_id = 0;
            $transport_type = $trans['transport_type'];
            $shipping_id = $trans['shipping_id'];

            $country = $this->getModelTable('CountryTable')->getOne($country_id);
            if( empty($country) ){
                $error['country_id'] = $translator->translate('txt_chua_chon_contry');
            }else{

                if( $country->country_type == 2 ){
                    $cities_id = $trans['state'];
                }else if( $country->country_type == 5 ){
                    $cities_id = $trans['region'];
                }else if( $country->country_type == 6 ){
                    $cities_id = $trans['province'];
                }else if( $country->country_type == 7 ){
                    $cities_id = $trans['cities_id'];
                    $districts_id = $trans['districts_id'];
                }

                if ( !empty($ship_to_different_address) && $ship_to_different_address == 1){
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
            }

            $shipping = array();
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

            if ( empty($error) ) {
                $dataPayment = $trans;
                $dataMember = $hPUser->getMember();
                $dataCart = $_SESSION['cart'];
                unset($dataCart['shipping']);

                $dataExtensions = array();
                $dataExtensionsTransportations = array();
                foreach ($dataCart as $key => $products) {
                    if($key == 'coupon' || $key == 'shipping' ) continue;
                    foreach ($products['product_type'] as $type_id => $row) {
                        if( !empty($row['extensions']) ){
                            if(!isset($dataExtensions[$products['id']][$type_id])){
                                $dataExtensions[$products['id']][$type_id] = array();
                            }
                            foreach($row['extensions'] as $ext){
                                if(!isset($dataExtensions[$products['id']][$type_id][$ext['id']])){
                                    $dataExtensions[$products['id']][$type_id][$ext['id']] = array();
                                }
                                $dataExtensions[$products['id']][$type_id][$ext['id']] = array(
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
                            if(!isset($dataExtensions[$products['id']][$type_id])){
                                $dataExtensions[$products['id']][$type_id] = array();
                            }
                            foreach($row['extensions_require'] as $ext){
                                if(!isset($dataExtensions[$products['id']][$type_id][$ext['id']])){
                                    $dataExtensions[$products['id']][$type_id][$ext['id']] = array();
                                }
                                $dataExtensions[$products['id']][$type_id][$ext['id']] = array(
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

                $total = 0;
                $calculate = $helper->sumSubTotalPriceInCart();
                $total = $calculate['price_total'];
                $total_old = $calculate['price_total_old'];
                $total_orig = $calculate['price_total_orig'];
                $total_tax = $calculate['price_total_tax'];
                $total_old_tax = $calculate['price_total_old_tax'];
                $total_orig_tax = $calculate['price_total_orig_tax'];

                $dataOrder = $order;
                $dataSave = array();

                $dataSave['first_name'] = $dataPayment['first_name'];
                $dataSave['last_name'] = $dataPayment['last_name'];
                $dataSave['full_name'] = $dataPayment['full_name'];
                $dataSave['phone'] = $dataPayment['phone'];
                $dataSave['email'] = $dataPayment['email'];
                $dataSave['type_address_delivery'] = $dataPayment['type_address_delivery'];
                $dataSave['country_id'] = $dataPayment['country_id'];
                $dataSave['address'] = $dataPayment['address'];
                $dataSave['address01'] = empty($dataPayment['address01']) ? '' : $dataPayment['address01'];
                $dataSave['city'] = empty($dataPayment['city']) ? '' : $dataPayment['city'];
                $dataSave['state'] = empty($dataPayment['state']) ? '' : $dataPayment['state'];
                $dataSave['suburb'] = empty($dataPayment['suburb']) ? '' : $dataPayment['suburb'];
                $dataSave['region'] = empty($dataPayment['region']) ? '' : $dataPayment['region'];
                $dataSave['province'] = empty($dataPayment['province']) ? '' : $dataPayment['province'];
                $dataSave['zipcode'] = empty($dataPayment['zipcode']) ? '' : $dataPayment['zipcode'];
                $dataSave['cities_id'] = empty($dataPayment['cities_id']) ? 0 : $dataPayment['cities_id'];
                $dataSave['districts_id'] = empty($dataPayment['districts_id']) ? 0 : $dataPayment['districts_id'];
                $dataSave['wards_id'] = empty($dataPayment['wards_id']) ? 0 : $dataPayment['wards_id'];
                $dataSave['users_id'] = (!empty($dataMember['users_id'])) ? $dataMember['users_id'] : NULL;
                $dataSave['shipping_id'] = $dataPayment['shipping_id'];
                $dataSave['transport_type'] = $dataPayment['transport_type'];
                $dataSave['is_free_shipping'] = ( $is_free ? 1 : 0);
                $dataSave['ships_fee'] = $fee;
                
                $dataSave['ship_to_different_address'] = $ship_to_different_address;
                if ( empty($ship_to_different_address) || $ship_to_different_address == 0){
                    $dataSave['ships_first_name'] = $dataPayment['first_name'];
                    $dataSave['ships_last_name'] = $dataPayment['last_name'];
                    $dataSave['ships_full_name'] = $dataPayment['full_name'];
                    $dataSave['ships_email'] = $dataPayment['email'];
                    $dataSave['ships_phone'] = $dataPayment['phone'];
                    $dataSave['ships_country_id'] = $dataPayment['country_id'];
                    $dataSave['ships_address'] = $dataPayment['address'];
                    $dataSave['ships_address01'] = empty($dataPayment['address01']) ? '' : $dataPayment['address01'];
                    $dataSave['ships_city'] = empty($dataPayment['city']) ? '' : $dataPayment['city'];
                    $dataSave['ships_state'] = empty($dataPayment['state']) ? '' : $dataPayment['state'];
                    $dataSave['ships_suburb'] = empty($dataPayment['suburb']) ? '' : $dataPayment['suburb'];
                    $dataSave['ships_region'] = empty($dataPayment['region']) ? '' : $dataPayment['region'];
                    $dataSave['ships_province'] = empty($dataPayment['province']) ? '' : $dataPayment['province'];
                    $dataSave['ships_zipcode'] = empty($dataPayment['zipcode']) ? '' : $dataPayment['zipcode'];
                    $dataSave['ships_cities_id'] = empty($dataPayment['cities_id']) ? 0 : $dataPayment['cities_id'];
                    $dataSave['ships_districts_id'] = empty($dataPayment['districts_id']) ? 0 : $dataPayment['districts_id'];
                    $dataSave['ships_wards_id'] = empty($dataPayment['wards_id']) ? 0 : $dataPayment['wards_id'];
                }else{
                    $dataSave['ships_first_name'] = $ships['first_name'];
                    $dataSave['ships_last_name'] = $ships['last_name'];
                    $dataSave['ships_full_name'] = $ships['full_name'];
                    $dataSave['ships_email'] = $ships['email'];
                    $dataSave['ships_phone'] = $ships['phone'];
                    $dataSave['ships_country_id'] = $ships['country_id'];
                    $dataSave['ships_address'] = $ships['address'];
                    $dataSave['ships_address01'] = empty($ships['address01']) ? '' : $ships['address01'];
                    $dataSave['ships_city'] = empty($ships['city']) ? '' : $ships['city'];
                    $dataSave['ships_state'] = empty($ships['state']) ? '' : $ships['state'];
                    $dataSave['ships_suburb'] = empty($ships['suburb']) ? '' : $ships['suburb'];
                    $dataSave['ships_region'] = empty($ships['region']) ? '' : $ships['region'];
                    $dataSave['ships_province'] = empty($ships['province']) ? '' : $ships['province'];
                    $dataSave['ships_zipcode'] = empty($ships['zipcode']) ? '' : $ships['zipcode'];
                    $dataSave['ships_cities_id'] = empty($ships['cities_id']) ? 0 : $ships['cities_id'];
                    $dataSave['ships_districts_id'] = empty($ships['districts_id']) ? 0 : $ships['districts_id'];
                    $dataSave['ships_wards_id'] = empty($ships['wards_id']) ? 0 : $ships['wards_id'];
                }

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
                    'data' => $dataSave,
                    'datapayment' => $dataPayment,
                    'datamember' => $dataMember,
                    'datacart' => $dataCart,
                    'dataorder' => $dataOrder,
                    'dataExtensions' => $dataExtensions,
                    'shipping' => $shipping,
                    'is_free' => $is_free,
                    'transport_type' => $transport_type,
                    'fee' => $fee,
                    //'extensions_transportation' => $extensions_transportation,
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
                
                $dataSave['invoice_title'] = $this->website['website_order_code_prefix'] . strtotime(date("Y-m-d H:i:s")) . $this->website['website_order_code_suffix'];
                $dataSave['invoice_description'] = empty($dataPayment['invoice_description']) ? '' : strip_tags($dataPayment['invoice_description']);
                $dataSave['is_published'] = 1;
                $dataSave['is_delete'] = 0;
                $dataSave['payment_id'] = $payment_id;
                $dataSave['payment_code'] = $payment->code;
                $dataSave['payment'] = 'unpaid';
                $dataSave['delivery'] = 'pending';
                $dataSave['date_create'] = date('Y-m-d H:i:s');
                $dataSave['date_update'] = date('Y-m-d H:i:s');
                
                
                if(isset($dataCart['coupon'])){
                    $dataCart['coupon']['price_used'] = $total_orig_tax;
                }
                $dataSave['total'] = $total;//chua co ship
                $dataSave['total_old'] = $total_old;//chua co ship
                $dataSave['total_orig'] = $total_orig;//chua co ship
                $dataSave['total_tax'] = $total_tax;//chua co ship
                $dataSave['total_old_tax'] = $total_old_tax;//chua co ship
                $dataSave['total_orig_tax'] = $total_orig_tax;//chua co ship
                $dataSave['value_ship'] = $this->website['ship'];
                $dataSave['content'] = htmlentities($html, ENT_QUOTES, 'UTF-8');
                if( empty($total_tax) ){
                    $dataSave['payment'] = 'paid';
                }

                $dataSave['company_name'] = (!empty($dataOrder['xuathoadon'])) ? $dataOrder['company_name'] : '';
                $dataSave['company_tax_code'] = (!empty($dataOrder['xuathoadon'])) ? $dataOrder['company_tax_code'] : '';
                $dataSave['company_address'] = (!empty($dataOrder['xuathoadon'])) ? $dataOrder['company_address'] : '';
                $dataSave['email_subscription'] = (!empty($dataOrder['subscription'])) ? $dataOrder['email'] : '';
                $dataSave['ip_addr'] = $_SERVER['REMOTE_ADDR'];
                $dataSave['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                $dataSave['commission'] = $commission;
                $dataSave['from_currency'] = $_SESSION['website']['website_currency'];
                $dataSave['to_currency'] = $_SESSION['website']['website_currency'];
                $dataSave['rate_exchange'] = 1;
                $dataSave['is_incognito'] = empty($trans['is_incognito']) ? 0 : 1;
                $dataSave['date_delivery'] = empty($trans['date_delivery']) ? '' : $trans['date_delivery'];
                $dataSave['session_id'] = session_id();
                
                //ko la hoa don ban si
                $dataSave['is_wholesale'] = 0;
                if( !empty($shipping) ){
                    $shipping['shipping_fee'] = $fee;
                }

                try{
                    $id = $this->getModelTable('InvoiceTable')->saveInvoice($dataSave, $dataCart, $shipping, $dataExtensions);
                }catch(\Exception $e ){
                    //die($e->getMessage());
                    return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array('action' => 'index'));
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
                            $rate_exchange = 1;
                            if( strtolower($currency_code) != 'usd' ){
                                $rate_exchange = $currencyHelper->exchangerates('USD', $currency_code, 1);
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
                                        'to_currency' => 'USD',
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
                            $paypal->setCurrencyCode( 'USD' );
                            $paypal->setReturn( $cb_return );
                            $paypal->setCancelReturn( $cb_cancel );
                            $paypal->setNotifyUrl( $cb_notifi );
                            $paypal->setRm( 2 );
                            $paypal->setLc( 2 );
                            $paypal->setCbt( $translator->translate('txt_paypal_tiep_tuc_thanh_toan') );
                            $paypal->setProducts( $products );
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
                                $onepay->setAVSStreet01( $trans['avs_street01'] );
                                $onepay->setAVSCity( $trans['avs_city'] );
                                $onepay->setAVSStateProv( $trans['avs_stateprov'] );
                                $onepay->setAVSPostCode( $trans['avs_postCode'] );
                                $AVS_Country = $this->getModelTable('CountryTable')->getOne($trans['avs_country']);
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
                            $vnpay->setHashSecret( $payment->vnp_hashsecret );
                            $url = $vnpay->getUrlPay();
                            return $this->redirect()->toUrl($url);
                            break;
                        }
                    }
                }
                $_SESSION['invoice_id'] = $id;
                return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array('action' => 'success'));
            } else {
                $_SESSION['error'] = $error;
            }
        }
        $this->data_view['price_total'] = $price_total;
        $this->data_view['price_total_old'] = $price_total_old;
        $this->data_view['price_total_orig'] = $price_total_orig;
        $this->data_view['price_total_tax'] = $price_total_tax;
        $this->data_view['price_total_old_tax'] = $price_total_old_tax;
        $this->data_view['price_total_orig_tax'] = $price_total_orig_tax;
        //$this->data_view['transportations'] = $transportations;
        //$this->data_view['payments'] = $payments;
        return $this->data_view;
    }


    public function wholesaleAction()
    {
        $items = array('flag' => false, 'msg' => 'Not found');
        $translator = $this->getServiceLocator()->get('translator');
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $price_total = 0;
        $price_total_old = 0;
        $price_total_orig = 0;
        $price_total_tax = 0;
        $price_total_old_tax = 0;
        $price_total_orig_tax = 0;

        //$transportations = $this->getModelTable('UserTable')->loadTransportations();
        $payments = $this->getModelTable('UserTable')->getPaymentMethod();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $product = $request->getPost('product', array());
            $trans = $request->getPost("trans");
            $ships = $request->getPost("ships");
            $order = $request->getPost("order");

            $ship_to_different_address = $request->getPost("ship_to_different_address", 0);
            $error = array();
            if ( empty($product['products_id']) ) {
                $error['products_id'] = $translator->translate('txt_san_pham_khong_ton_tai');
            }
            if ( empty($product['quality']) ) {
                $error['quality'] = $translator->translate('txt_ban_chua_nhap_so_luong_san_pham');
            }
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

            if ( !empty($ship_to_different_address) && $ship_to_different_address == 1){
                if ( empty($ships['first_name']) ) {
                    $error['first_name'] = $translator->translate('txt_ban_phai_nhap_ten');
                }
                if ( empty($ships['last_name']) ) {
                    $error['last_name'] = $translator->translate('txt_ban_phai_nhap_ten');
                }
                if ( empty($ships['email']) || !filter_var($trans['email'], FILTER_VALIDATE_EMAIL)) {
                    $error['email'] = $translator->translate('txt_email_khong_hop_le');
                }
                if ( empty($ships['phone']) || !is_numeric($trans['phone'])) {
                    $error['phone'] = $translator->translate('txt_so_dien_thoai_khong_hop_le');
                }
                if ( empty($ships['country_id']) ) {
                    $error['country_id'] = $translator->translate('txt_chua_chon_contry');
                }else{
                    $country_ships = $this->getModelTable('CountryTable')->getOne($ships['country_id']);
                    $err = $this->validateInputContryPayment($ships, $country_ships);
                    $error = array_merge($error, $err);
                }
                $ships['full_name'] = $ships['first_name'].' '.$ships['last_name'];
            }
            if ( empty($trans['shipping_id']) && $trans['shipping_id'] != 0 ) {
                $error['shipping_id'] = $translator->translate('txt_chua_chon_transportation');
            }

            $products_id = $product['products_id'];
            $quantity = $product['quality'];
            $product_type = 0;
            $ext_list = array();
            if( !empty($product['products_type_id'])){
                $product_type = $product['products_type_id'];
            }
            if( !empty($product['extention'])){
                $exts = $product['extention'];
            }

            if( empty($product_type) ){
                $product = $this->getModelTable('ProductsTable')->getRow($products_id);
            }else{
                $product = $this->getModelTable('ProductsTable')->getProductsWithType($products_id, $product_type);
            }

            if ( !empty($product) ) {
                $extensions = array();
                if(!empty($exts)){
                    $extensions = $this->getModelTable('ProductsTable')->getExtensions($exts);
                }
                $extensions_require = $this->getModelTable('ProductsTable')->getExtensionsAlwaysProduct($products_id);

                if( $productsHelper->getToBuy($product) ){
                    $error['wholesale'] = $translator->translate('txt_san_pham_khong_ban_si');
                }else{
                    if($extensions && count($extensions)){
                        foreach($extensions as $key => $ext){
                            if($ext['is_always'] == 0){
                                $ext_list[$ext['id']] = $ext;
                            }
                        }
                    }
                }
                
                if(!empty($product['products_type_id'])){
                    $product_type = $product['products_type_id'];
                }

                if(!isset($_SESSION['products_deals'])){
                    $products_deals = $this->getModelTable('CategoriesTable')->getAllGoldTimerCurrent(array(),0, 100);
                    $deals_data = array();
                    foreach($products_deals as $p){
                        $deals_data[$p['products_id']] = $p;
                    }
                    $_SESSION['products_deals'] = $deals_data;
                    $products_deals = $deals_data;
                }else{
                    $products_deals = $_SESSION['products_deals'];
                }
                if(isset($products_deals[$product['products_id']])){
                    $p = $products_deals[$product['products_id']];
                    $product['price_sale'] = $p['price_sale'];
                }
                $promotion_list = array();
                if (trim($product['promotion_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion'] ? $product['promotion'] : 0,
                        'text' => $product['promotion_description'],
                    );
                }
                if (trim($product['promotion1_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion1'] ? $product['promotion1'] : 0,
                        'text' => $product['promotion1_description'],
                    );
                }
                if (trim($product['promotion2_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion2'] ? $product['promotion2'] : 0,
                        'text' => $product['promotion2_description'],
                    );
                }
                if (trim($product['promotion3_description']) != '') {
                    $promotion_list[] = array(
                        'value' => $product['promotion3'] ? $product['promotion3'] : 0,
                        'text' => $product['promotion3_description'],
                    );
                }

                $cart = array();
                $cart[$products_id] = array(
                    'id' => $products_id,
                    'thumb' => $product['thumb_image'],
                    'code' => $product['products_code'],
                    'title' => $product['products_title'],
                    'alias' => $product['products_alias'],
                    'total_price_extention' => $product['total_price_extention'],
                    'quantity' => $quantity,
                    'price' => $product['price'],//gia chua thue
                    'price_sale' => $product['price_sale'],//gia chua thue
                    'vat' => (int)$product['vat'],
                    'promotion' => 0,
                    'promotion_list' => $promotion_list,
                    'extensions' => $ext_list,
                    'extensions_require' => $extensions_require,
                    'product_type' => array($product_type => array(
                            'products_type_id' => $product_type,
                            'type_name' => $productsHelper->getNameType($product),
                            'price' => $productsHelper->getPriceSimple($product),
                            'price_sale' => $productsHelper->getPriceSaleSimple($product),
                            'quantity' => $quantity,
                            'is_available' => $productsHelper->getIsAvailable($product),
                            'extensions' => $ext_list,
                            'extensions_require' => $extensions_require,
                            'transportation_id' => $product['transportation_id'],
                        )),
                );
                $price_this = $productsHelper->getPriceSale($product)*$quantity;
                $price_this_old = $productsHelper->getPrice($product)*$quantity;

                if( !empty($ext_list) ){
                    foreach($ext_list as $ext){
                        $priceQl = $ext['price']*$ext['quantity'];
                        $price_this += $priceQl;
                        $price_this_old += $priceQl;
                    }
                }

                $cart[$products_id]['product_type'][$product_type]['price_total'] = 0;
                $cart[$products_id]['product_type'][$product_type]['price_total_old'] = 0;

                $no_shipping = FALSE;
                $shipping = array();
                $is_free = FALSE;
                $fee = 0;

                $country_id = $trans['country_id'];
                if ( !empty($ship_to_different_address) && $ship_to_different_address == 1){
                    $country_id = $ships['country_id'];
                }
                $cities_id = 0;
                $districts_id = 0;
                $transport_type = $trans['transport_type'];
                $shipping_id = $trans['shipping_id'];

                $country = $this->getModelTable('CountryTable')->getOne($country_id);
                if( empty($country) ){
                    $error['country_id'] = $translator->translate('txt_chua_chon_contry');
                }else{
                    if( $country->country_type == 2 ){
                        $cities_id = $trans['state'];
                    }else if( $country->country_type == 5 ){
                        $cities_id = $trans['region'];
                    }else if( $country->country_type == 6 ){
                        $cities_id = $trans['province'];
                    }else if( $country->country_type == 7 ){
                        $cities_id = $trans['cities_id'];
                        $districts_id = $trans['districts_id'];
                    }

                    if ( !empty($ship_to_different_address) && $ship_to_different_address == 1){
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
                }

                $shipping = array();
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

                $couponsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Coupons');
                $coupon = $couponsHelper->getCoupon();
                if( !empty($coupon) ){
                    $check_coupon = $this->getModelTable('InvoiceTable')->getCoupon($coupon['coupons_code']);
                    if( empty($check_coupon) || !$couponsHelper->isAvaliable() ){
                        $error['coupon'] = $translator->translate('txt_coupon_khong_hop_le');
                    }
                }

                if ( empty($error) ) {
                    $dataPayment = $trans;
                    $dataMember = isset($_SESSION['MEMBER']) ? $_SESSION['MEMBER'] : array();
                    $dataCart = $cart;
                    unset($dataCart['shipping']);

                    /*sản phẩm luôn theo cùng*/
                    $dataExtensions = array();
                    $dataExtensionsTransportations = array();
                    foreach ($dataCart as $products) {
                        foreach ($products['product_type'] as $type_id => $row) {
                            if(isset($row['extensions']) && count($row['extensions']) > 0){
                                if(!isset($dataExtensions[$products['id']][$type_id])){
                                    $dataExtensions[$products['id']][$type_id] = array();
                                }
                                foreach($row['extensions'] as $ext){
                                    if(!isset($dataExtensions[$products['id']][$type_id][$ext['id']])){
                                        $dataExtensions[$products['id']][$type_id][$ext['id']] = array();
                                    }
                                    $dataExtensions[$products['id']][$type_id][$ext['id']] = array(
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

                            if(isset($row['extensions_require']) && count($row['extensions_require']) > 0){
                                if(!isset($dataExtensions[$products['id']][$type_id])){
                                    $dataExtensions[$products['id']][$type_id] = array();
                                }
                                foreach($row['extensions_require'] as $ext){
                                    if(!isset($dataExtensions[$products['id']][$type_id][$ext['id']])){
                                        $dataExtensions[$products['id']][$type_id][$ext['id']] = array();
                                    }
                                    $dataExtensions[$products['id']][$type_id][$ext['id']] = array(
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

                    $total = 0;
                    $calculate = $helper->sumSubTotalPriceInCart();
                    $total = $calculate['price_total'];
                    $total_old = $calculate['price_total_old'];
                    $total_orig = $calculate['price_total_orig'];
                    $total_tax = $calculate['price_total_tax'];
                    $total_old_tax = $calculate['price_total_old_tax'];
                    $total_orig_tax = $calculate['price_total_orig_tax'];

                    $dataOrder = $order;
                    $dataSave = array();

                    $dataSave['first_name'] = $dataPayment['first_name'];
                    $dataSave['last_name'] = $dataPayment['last_name'];
                    $dataSave['full_name'] = $dataPayment['full_name'];
                    $dataSave['phone'] = $dataPayment['phone'];
                    $dataSave['email'] = $dataPayment['email'];
                    $dataSave['type_address_delivery'] = $dataPayment['type_address_delivery'];
                    $dataSave['country_id'] = $dataPayment['country_id'];
                    $dataSave['address'] = $dataPayment['address'];
                    $dataSave['address01'] = empty($dataPayment['address01']) ? '' : $dataPayment['address01'];
                    $dataSave['city'] = empty($dataPayment['city']) ? '' : $dataPayment['city'];
                    $dataSave['state'] = empty($dataPayment['state']) ? '' : $dataPayment['state'];
                    $dataSave['suburb'] = empty($dataPayment['suburb']) ? '' : $dataPayment['suburb'];
                    $dataSave['region'] = empty($dataPayment['region']) ? '' : $dataPayment['region'];
                    $dataSave['province'] = empty($dataPayment['province']) ? '' : $dataPayment['province'];
                    $dataSave['zipcode'] = empty($dataPayment['zipcode']) ? '' : $dataPayment['zipcode'];
                    $dataSave['cities_id'] = empty($dataPayment['cities_id']) ? 0 : $dataPayment['cities_id'];
                    $dataSave['districts_id'] = empty($dataPayment['districts_id']) ? 0 : $dataPayment['districts_id'];
                    $dataSave['wards_id'] = empty($dataPayment['wards_id']) ? 0 : $dataPayment['wards_id'];
                    $dataSave['users_id'] = (!empty($dataMember['users_id'])) ? $dataMember['users_id'] : NULL;
                    $dataSave['shipping_id'] = $dataPayment['shipping_id'];
                    $dataSave['transport_type'] = $dataPayment['transport_type'];
                    $dataSave['is_free_shipping'] = ( $is_free ? 1 : 0);
                    $dataSave['ships_fee'] = $fee;
                    
                    $dataSave['ship_to_different_address'] = $ship_to_different_address;
                    if ( empty($ship_to_different_address) || $ship_to_different_address == 0){
                        $dataSave['ships_first_name'] = $dataPayment['first_name'];
                        $dataSave['ships_last_name'] = $dataPayment['last_name'];
                        $dataSave['ships_full_name'] = $dataPayment['full_name'];
                        $dataSave['ships_email'] = $dataPayment['email'];
                        $dataSave['ships_phone'] = $dataPayment['phone'];
                        $dataSave['ships_country_id'] = $dataPayment['country_id'];
                        $dataSave['ships_address'] = $dataPayment['address'];
                        $dataSave['ships_address01'] = empty($dataPayment['address01']) ? '' : $dataPayment['address01'];
                        $dataSave['ships_city'] = empty($dataPayment['city']) ? '' : $dataPayment['city'];
                        $dataSave['ships_state'] = empty($dataPayment['state']) ? '' : $dataPayment['state'];
                        $dataSave['ships_suburb'] = empty($dataPayment['suburb']) ? '' : $dataPayment['suburb'];
                        $dataSave['ships_region'] = empty($dataPayment['region']) ? '' : $dataPayment['region'];
                        $dataSave['ships_province'] = empty($dataPayment['province']) ? '' : $dataPayment['province'];
                        $dataSave['ships_zipcode'] = empty($dataPayment['zipcode']) ? '' : $dataPayment['zipcode'];
                        $dataSave['ships_cities_id'] = empty($dataPayment['cities_id']) ? 0 : $dataPayment['cities_id'];
                        $dataSave['ships_districts_id'] = empty($dataPayment['districts_id']) ? 0 : $dataPayment['districts_id'];
                        $dataSave['ships_wards_id'] = empty($dataPayment['wards_id']) ? 0 : $dataPayment['wards_id'];
                    }else{
                        $dataSave['ships_first_name'] = $ships['first_name'];
                        $dataSave['ships_last_name'] = $ships['last_name'];
                        $dataSave['ships_full_name'] = $ships['full_name'];
                        $dataSave['ships_email'] = $ships['email'];
                        $dataSave['ships_phone'] = $ships['phone'];
                        $dataSave['ships_country_id'] = $ships['country_id'];
                        $dataSave['ships_address'] = $ships['address'];
                        $dataSave['ships_address01'] = empty($ships['address01']) ? '' : $ships['address01'];
                        $dataSave['ships_city'] = empty($ships['city']) ? '' : $ships['city'];
                        $dataSave['ships_state'] = empty($ships['state']) ? '' : $ships['state'];
                        $dataSave['ships_suburb'] = empty($ships['suburb']) ? '' : $ships['suburb'];
                        $dataSave['ships_region'] = empty($ships['region']) ? '' : $ships['region'];
                        $dataSave['ships_province'] = empty($ships['province']) ? '' : $ships['province'];
                        $dataSave['ships_zipcode'] = empty($ships['zipcode']) ? '' : $ships['zipcode'];
                        $dataSave['ships_cities_id'] = empty($ships['cities_id']) ? 0 : $ships['cities_id'];
                        $dataSave['ships_districts_id'] = empty($ships['districts_id']) ? 0 : $ships['districts_id'];
                        $dataSave['ships_wards_id'] = empty($ships['wards_id']) ? 0 : $ships['wards_id'];
                    }

                    $viewModel = new ViewModel();
                    $viewModel->setTerminal(true);
                    $viewModel->setTemplate("application/cart/content_invoice");
                    $viewModel->setVariables(array(
                        'data' => $dataSave,
                        'datapayment' => $dataPayment,
                        'datamember' => $dataMember,
                        'datacart' => $dataCart,
                        'dataorder' => $dataOrder,
                        'dataExtensions' => $dataExtensions,
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
                    ));
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($viewModel);
                    
                    $dataSave['invoice_title'] = $this->website['website_order_code_prefix'] . strtotime(date("Y-m-d H:i:s")) . $this->website['website_order_code_suffix'];
                    $dataSave['invoice_description'] = empty($dataPayment['invoice_description']) ? '' : strip_tags($dataPayment['invoice_description']);
                    $dataSave['is_published'] = 1;
                    $dataSave['is_delete'] = 0;
                    $dataSave['payment'] = 'unpaid';
                    $dataSave['delivery'] = 'no_delivery';
                    $dataSave['date_create'] = date('Y-m-d H:i:s');
                    $dataSave['date_update'] = date('Y-m-d H:i:s');
                    
                    
                    if(isset($dataCart['coupon'])){
                        $dataCart['coupon']['price_used'] = $total_orig;
                    }
                    $dataSave['total'] = $total;//chua co ship
                    $dataSave['total_old'] = $total_old;//chua co ship
                    $dataSave['total_orig'] = $total_orig;//chua co ship
                    $dataSave['total_tax'] = $total_tax;//chua co ship
                    $dataSave['total_old_tax'] = $total_old_tax;//chua co ship
                    $dataSave['total_orig_tax'] = $total_orig_tax;//chua co ship
                    $dataSave['value_ship'] = $this->website['ship'];
                    $dataSave['content'] = htmlentities($html, ENT_QUOTES, 'UTF-8');
                    if( empty($total_tax) ){
                        $dataSave['payment'] = 'unpaid';
                    }

                    $dataSave['company_name'] = (!empty($dataOrder['xuathoadon'])) ? $dataOrder['company_name'] : '';
                    $dataSave['company_tax_code'] = (!empty($dataOrder['xuathoadon'])) ? $dataOrder['company_tax_code'] : '';
                    $dataSave['company_address'] = (!empty($dataOrder['xuathoadon'])) ? $dataOrder['company_address'] : '';
                    $dataSave['email_subscription'] = (!empty($dataOrder['subscription'])) ? $dataOrder['email'] : '';
                    $dataSave['ip_addr'] = $_SERVER['REMOTE_ADDR'];
                    $dataSave['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                    //la hoa don ban si
                    $dataSave['is_wholesale'] = 1;
                    if( !empty($shipping) ){
                        $shipping['shipping_fee'] = $fee;
                    }

                    try{
                        $invoice_title = $this->getModelTable('InvoiceTable')->saveInvoice($dataSave, $dataCart, $shipping, $dataExtensions);
                        $invoice = $this->getModelTable('InvoiceTable')->getInvoiceByTitle($invoice_title);
                        $this->sendMail($invoice);
                        $items = array('flag' => true, 'msg' => $translator->translate('txt_ban_da_dat_hang_thanh_cong'));
                    }catch(\Exception $ex){
                        $items = array('flag' => false, 'msg' => $translator->translate('txt_co_loi_xay_ra_xin_vui_long_check_lai'));
                    }
                } else {
                    $items = array('flag' => false, 'msg' => $translator->translate('txt_co_loi_xay_ra_xin_vui_long_check_lai'));
                }
            }
        }

        $result = new JsonModel($items);
        return $result;
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

    public function processCreditCardPaypal($api, $nvp_string){
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

    public function getQueryStringCreditCardPaypal($payment, $carts, $amount, $currency_code, $desc){
        $query = array();
        $query['METHOD'] = 'DoDirectPayment';
        $query['USER'] = $payment['username'];
        $query['PWD'] = $payment['password'];
        $query['SIGNATURE'] = $payment['signature'];
        $query['VERSION'] = '85.0';
        $query['PAYMENTACTION'] = 'Sale';
        $query['IPADDRESS'] = $_SERVER['REMOTE_ADDR'];

        $query['CREDITCARDTYPE'] = $carts['creditcardtype'];
        $query['ACCT'] = $carts['acct'];
        $query['EXPDATE'] = $carts['expdate'];
        $query['CVV2'] = $carts['cw2'];

        $query['FIRSTNAME'] = $carts['first_name'];
        $query['LASTNAME'] = $carts['last_name'];
        $query['EMAIL'] = $carts['email'];

        $query['STREET'] = $carts['address'];
        $query['CITY'] = $carts['city'];
        $query['STATE'] = $carts['districts'];
        $query['COUNTRYCODE'] = $carts['country_code'];
        $query['ZIP'] = $carts['zipcode'];

        $query['AMT'] = $amount;
        $query['CURRENCYCODE'] = $currency_code;
        $query['DESC'] = $desc;

        $query_string = http_build_query($query);
        return $query_string;
    }

    public function getQueryStringPaypal($invoice_id,$payment, $rate_exchange, $carts,$dataPayment,
                                        $currency_code ='USD', $return = '', $cancel_return = '', $notify_url = ''){
        $query = array();
        $query['cmd'] = '_cart';
        $query['charset'] = 'utf-8';
        $query['upload'] = '1';
        $query['invoice'] = $invoice_id;
        $query['business'] = $payment['sale_account'];

        /*$query['address_override'] = '1';
        $query['first_name'] = $first_name;
        $query['last_name'] = $last_name;
        $query['email'] = $email;
        $query['address1'] = $ship_to_address;
        $query['city'] = $ship_to_city;
        $query['state'] = $ship_to_state;
        $query['zip'] = $ship_to_zip;*/

        $i =1;
        foreach ($carts as $id => $product) {
            if($id == 'coupon'){continue;}
            foreach ($product['product_type'] as $product_type_id => $p) {
                $query['item_name_'.$i] = $product['title'].'('.$p['type_name'].')';
                $query['quantity_'.$i] = $p['quantity'];
                $price_usd = $p['price_total']/$rate_exchange;
                $query['amount_'.$i] = number_format((float)$price_usd, 2, '.', '');
                $i++;
            }
        }

        //$query['shipping'] = 9.95;
        //$query['shipping2'] = 3.00;
        //$query['handling_cart'] = 1.05;
        $query['currency_code'] = $currency_code;
        $query['return'] = $return;
        $query['cancel_return'] = $cancel_return;
        $query['notify_url'] = $notify_url;
        $query['rm'] = '2';
        $query['lc'] = '2';
        $query['cbt'] = 'Return to The Store';
        $query_string = http_build_query($query);
        return $query_string;
    }

    public function currencyConvert($from,$to,$amount =1){
        $url = "http://www.google.com/finance/converter?a=$amount&from=$from&to=$to";
        $request = curl_init();
        $timeOut = 0;
        curl_setopt ($request, CURLOPT_URL, $url);
        curl_setopt ($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($request, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt ($request, CURLOPT_CONNECTTIMEOUT, $timeOut);
        $response = curl_exec($request);
        curl_close($request);
        $regex  = '#\<span class=bld\>(.+?)\<\/span\>#s';
        preg_match($regex, $response, $converted);
        $result = $converted[0];
        $rate_exchange= 0;
        if(!empty($result)){
            $list = explode ( ' ', $result );
            $dot = count ( $list );
            unset($list [$dot - 1]);
            $string = implode(' ', $list);
            $string = trim($string);
            $string = str_replace(' ', '-', $string);
            $string = preg_replace('/[^0-9\.]/', '', $string);
            $rate_exchange  = (float)$string;
        }
        if($rate_exchange ==0){
            try{
                $url_exchange = "http://rate-exchange.herokuapp.com/fetchRate?from=$from&to=$to";
                $rate_exchange = file_get_contents($url_exchange);
                $rate_exchange = json_decode($rate_exchange, true);
                if(!empty($rate_exchange) && !empty($rate_exchange['Rate'])){
                    $string = $rate_exchange['Rate'];
                    $string = trim($string);
                    $string = str_replace(' ', '-', $string);
                    $string = preg_replace('/[^0-9\.]/', '', $string);
                    $rate_exchange  = (float)$string;
                }
            }catch(\Exception $e){
                $rate_exchange = 0;
            }
        }
        return $rate_exchange;
    }

    public function removeExtensionAction(){
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $request = $this->getRequest();
        if($request->getPost()){
            $pid = $this->params()->fromQuery('products_id', 0);
            $product_type = $this->params()->fromQuery('product_type', 0);
            $eid = $this->params()->fromQuery('extension_id', 0);

            if(isset($_SESSION['cart'][$pid])){
                if(isset($_SESSION['cart'][$pid])
                    && isset($_SESSION['cart'][$pid]['product_type'][$product_type])
                    && isset($_SESSION['cart'][$pid]['product_type'][$product_type]['extensions'][$eid]) 
                    && $_SESSION['cart'][$pid]['product_type'][$product_type]['extensions'][$eid]['is_always'] == 0){
                    $product = $_SESSION['cart'][$pid]['product_type'][$product_type];
                    $quantity = $_SESSION['cart'][$pid]['product_type'][$product_type]['quantity'];
                    unset($_SESSION['cart'][$pid]['product_type'][$product_type]['extensions'][$eid]);

                    $price_this = $productsHelper->getPriceSale($product)*$quantity;
                    $price_tax =  $price_this + ($price_this * $product['vat'] / 100);
                    $price_this_old = $productsHelper->getPrice($product)*$quantity;
                    $price_old_tax =  $price_this_old + ($price_this_old * $product['vat'] / 100);
                    if( !empty($_SESSION['cart'][$pid]['product_type'][$product_type]['extensions'])){
                        foreach($_SESSION['cart'][$pid]['product_type'][$product_type]['extensions'] as $ext){
                            $priceQl = $ext['price']*$ext['quantity'];
                            $price_this += $priceQl;
                            $price_tax += $priceQl;
                            $price_this_old += $priceQl;
                            $price_old_tax += $priceQl;
                        }
                    }

                    $_SESSION['cart'][$pid]['product_type'][$product_type]['price_total'] = $price_this;
                    $_SESSION['cart'][$pid]['product_type'][$product_type]['price_total_old'] = $price_this_old;
                    $_SESSION['cart'][$pid]['product_type'][$product_type]['price_total_tax'] = $price_tax;
                    $_SESSION['cart'][$pid]['product_type'][$product_type]['price_total_old_tax'] = $price_old_tax;   
                }
            }
        }
        echo json_encode(array(
            'success' => TRUE,
            'data' => $_SESSION['cart'],
            'type' => 'removeExtension',
        ));
        die;
    }

    public function successAction()
    {
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($translator->translate('txt_title_site_sucess_payment'));
        $request = $this->getRequest();
        if( empty($_SESSION['invoice_id']) ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }else{
            $invoice_id = $_SESSION['invoice_id'];
            $invoice = $this->getModelTable('InvoiceTable')->getInvoiceByTitle($invoice_id);
            $products = $this->getModelTable('InvoiceTable')->getProducts($invoice->invoice_id);
            $this->sendMail($invoice);
            $this->data_view['invoice'] = $invoice;
            $this->data_view['invoice_code'] = $invoice->invoice_title;
            $this->data_view['products'] = $products;
            unset($_SESSION['invoice_id']);
            unset($_SESSION['PAYMENT_ERROR']);
            return $this->data_view;
        }
    }

    public function sendMail($invoice){
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $websitesHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Websites');
        $cart = $this->getModelTable('InvoiceTable')->getProductsCart($invoice->invoice_id);
        $dataExtension = $this->getModelTable('InvoiceTable')->getInvoiceExtensions($invoice->invoice_id);
        $shipping = $this->getModelTable('InvoiceTable')->getShipping($invoice->invoice_id);
        $coupon = $this->getModelTable('InvoiceTable')->getCouponUsed($invoice->invoice_id);
        $dataCart = array();
        foreach($cart as $key => $product){
            $products_type_id = (!empty($product['products_type_id']) ? $product['products_type_id'] : 0);
            if( empty($dataCart[$product['products_id']]) ){
                $dataCart[$product['products_id']] = $product;
                $dataCart[$product['products_id']]['title'] = $product['products_title'];
                $dataCart[$product['products_id']]['code'] = $product['products_code'];
                $dataCart[$product['products_id']]['alias'] = $product['products_alias'];
                $dataCart[$product['products_id']]['total_price_extention'] = 0;
                $dataCart[$product['products_id']]['extensions'] = array();
                $dataCart[$product['products_id']]['extensions_require'] = array();
                $dataCart[$product['products_id']]['product_type'] = array(
                        $products_type_id => $product
                    );
            }else{
                $dataCart[$product['products_id']]['product_type'][$products_type_id] = $product;
            }
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['products_type_id'] = $products_type_id;
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['title'] = $product['products_title'];
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['code'] = $product['products_code'];
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['alias'] = $product['products_alias'];
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['promotion'] = (!empty($product['promotion']) ? $product['promotion'] : array());
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['type_name'] = $productsHelper->getNameType($product);
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['price'] = (!empty($product['price']) ? $product['price'] : $product['t_price']);
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['price_sale'] = (!empty($product['price_sale']) ? $product['price_sale'] : $product['t_price_sale']);
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['quantity'] = (!empty($product['quantity']) ? $product['quantity'] : $product['t_quantity']);
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['extensions'] = array();
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['extensions_require'] = array();
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['total_price_extention'] = 0;
        }

        foreach($dataExtension as $ext){
            if($ext['is_always'] == 0){/*san pham ko bat buoc dinh kem*/
                if(isset($dataCart[$ext['products_id']]) 
                    && isset($dataCart[$ext['products_id']]['product_type'][$ext['products_type_id']])){
                    $dataCart[$ext['products_id']]['extensions'][$ext['id']] = $ext;
                    $dataCart[$ext['products_id']]['product_type'][$ext['products_type_id']]['extensions'][$ext['id']] = $ext;
                }
            }else{
                if(isset($dataCart[$ext['products_id']]) 
                    && isset($dataCart[$ext['products_id']]['product_type'][$ext['products_type_id']])){
                    $dataCart[$ext['products_id']]['extensions_require'][$ext['id']] = $ext;
                    $dataCart[$ext['products_id']]['product_type'][$ext['products_type_id']]['extensions_require'][$ext['id']] = $ext;
                    $dataCart[$ext['products_id']]['total_price_extention'] += $ext['price']*$ext['quantity'];
                    $dataCart[$ext['products_id']]['product_type'][$ext['products_type_id']]['total_price_extention'] += $ext['price']*$ext['quantity'];
                }
            }
        }

        foreach($dataCart as $item => $products){
            foreach($products['product_type'] as $product_type_id => $product){
                $quantity = $product['quantity'];
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

                
                $dataCart[$item]['product_type'][$product_type_id]['price_total'] = $price_this;
                $dataCart[$item]['product_type'][$product_type_id]['price_total_old'] = $price_this_old;
                $dataCart[$item]['product_type'][$product_type_id]['price_total_tax'] = $price_tax;
                $dataCart[$item]['product_type'][$product_type_id]['price_total_old_tax'] = $price_old_tax;
            }
        }

        if( !empty($coupon) ){
            $dataCart['coupon'] = $coupon;
        }
        
        $calculate = $helper->sumSubTotalPriceFromArray($dataCart);
        $total = $calculate['price_total'];
        $total_old = $calculate['price_total_old'];
        $total_orig = $calculate['price_total_orig'];
        $total_tax = $calculate['price_total_tax'];
        $total_old_tax = $calculate['price_total_old_tax'];
        $total_orig_tax = $calculate['price_total_orig_tax'];

        //tinh phi
        $is_free = FALSE;
        if( !empty($invoice->is_free_shipping) ){
            $is_free = TRUE;
        }
        $fee = 0;
        if( !empty($shipping->shipping_fee) ){
            $fee = $shipping->shipping_fee;
        }

        $this->data_view['invoice'] = $invoice;
        $this->data_view['cart'] = $dataCart;
        $this->data_view['extension'] = $dataExtension;
        $this->data_view['is_free'] = $is_free;
        $this->data_view['coupon'] = $coupon;
        $this->data_view['total'] = $total;
        $this->data_view['total_old'] = $total_old;
        $this->data_view['total_orig'] = $total_orig;
        $this->data_view['total_tax'] = $total_tax;
        $this->data_view['total_old_tax'] = $total_old_tax;
        $this->data_view['total_orig_tax'] = $total_orig_tax;
        $this->data_view['fee'] = $fee;
        $this->data_view['shipping'] = $shipping;
        $this->data_view['transport_type'] = $invoice->transport_type;

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate("app/email/email_template_success");
        $viewModel->setVariables(array(
            'invoice' => $invoice,
            'cart' => $dataCart,
            'extension' => $dataExtension,
            'is_free' => $is_free,
            'coupon' => $coupon,
            'total' => $total,
            'total_old' => $total_old,
            'total_orig' => $total_orig,
            'total_tax' => $total_tax,
            'total_old_tax' => $total_old_tax,
            'total_orig_tax' => $total_orig_tax,
            'ship' => $this->website['ship'],
            'fee' => $fee,
            'shipping' => $shipping,
            'transport_type' => $invoice->transport_type,
        ));

        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRender->render($viewModel);
        $html = new MimePart($html);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($html));
		$listemailcc=explode(",",$this->website['website_email_customer']);
		if(count($listemailcc)<=0){
			$listemailcc=$this->website['website_email_customer'];
		}
        $message = new Message();
        $message->addTo($invoice->email)
            ->addFrom($websitesHelper->getEmailSend(), $this->website['website_name'])
			->addCc($listemailcc)
			->addReplyTo($listemailcc, "Admin website")
            ->setSubject($this->website['website_name'].' đã nhận được đơn hàng '.$invoice->invoice_id)
            ->setBody($body)
            ->setEncoding("UTF-8");

        // Setup SMTP transport using LOGIN authentication
        $transport = new SmtpTransport();
        $options = new SmtpOptions(array(
            'name' => $websitesHelper->getHostMail(),
            'host' => $websitesHelper->getHostMail(),
            'port' => $websitesHelper->getPortMail(),
            'connection_class' => 'login',
            'connection_config' => array(
                'username' => $websitesHelper->getUserNameHostMail(),
                'password' => $websitesHelper->getPasswordHostMail(),
            ),
        ));

        $transport->setOptions($options);
        try {
            $result=$transport->send($message);
            return true;
        } catch(\Zend\Mail\Exception $e) {
            return false;
        }catch(\Exception $ex) {
            return false;
        }
    }

    public function cancelAction()
    {
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

    public function paypalConfirmAction()
    {
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

    public function authAction()
    {
        if ( $this->getVersionCart() != $this->version ) {
            return $this->redirect()->toRoute( $this->getUrlRouterLang().$this->getRouteCart() , array(
                'action' => 'process'
            ));
        }

        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        if ($this->isLogin()) {
            return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
                'action' => 'payment'
            ));
        }
        if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }

        $translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($translator->translate('title_site'));
        $renderer->headMeta()->appendName('description', $translator->translate('description_site'));
        $renderer->headMeta()->appendName('keyword', $translator->translate('keyword_site'));
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');

        $redirect = urlencode(FOLDERWEB . $this->getUrlPrefixLang()."/cart/payment");
        $price_total = 0;
        $price_total_old = 0;
        if(isset($_SESSION['cart']) && count($_SESSION['cart'])){
            $calculate = $helper->sumSubTotalPriceInCart();
            $price_total = $calculate['price_total'];
            $price_total_old = $calculate['price_total_old'];
        }

        $this->data_view['price_total'] = $price_total;
        $this->data_view['price_total_old'] = $price_total_old;
        $this->data_view['redirect'] = $redirect;
        return $this->data_view;
    }

    public function useCouponAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $coupon_code = $request->getPost('coupon');
            $translator = $this->getServiceLocator()->get('translator');
            try{
                $coupon = $this->getModelTable('InvoiceTable')->getCoupon($coupon_code);
                if( !empty($coupon) ){
                    $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
                    $couponsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Coupons');
                    $currencyHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Currency');
                    $calculate = $helper->sumSubTotalPriceInCart();
                    $price_total_orig = $calculate['price_total_orig'];
                    if( !($price_total_orig >= $coupon['min_price_use'] 
                        && $price_total_orig <= $coupon['max_price_use']) ){
                        $error_msg = "Hóa đơn của bạn phải có giá trị cao hơn ".$currencyHelper->fomatCurrency($coupon['min_price_use'])." VNĐ và thấp hơn ".$currencyHelper->fomatCurrency($coupon['max_price_use'])." VNĐ mới có thể sử dụng mã này";
                        echo json_encode(array(
                            'flag' => FALSE,
                            'type' => 'useCoupon',
                            'msg' => $error_msg,
                            'cp' => $couponsHelper->isAvaliable()
                        ));
                        die;
                    }
                    $_SESSION['cart']['coupon'] = $coupon;
                    $subtotal = 0;
                    $subtotal_orig = 0;
                    $subtotal_tax = 0;
                    if(!empty($_SESSION['cart'])){
                        $calculate = $helper->sumSubTotalPriceInCart();
                        $subtotal = $calculate['price_total'];
                        $subtotal_orig = $calculate['price_total_orig'];
                        $subtotal_tax = $calculate['price_total_tax'];
                    }
                    $is_free = $helper->getIsFreeShipping();
                    $no_shipping = $helper->getNoShipping();
                    $fee = $helper->getFeeShipping();
                    $total = $subtotal+$fee;
                    $total_tax = $subtotal_tax+$fee;
                    echo json_encode(array(
                        'flag' => TRUE,
                        'type' => 'useCoupon',
                        'coupon' => $coupon,
                        'coupon_price' => $couponsHelper->getPrice(),
                        'coupon_price_currency' => $couponsHelper->getPriceWithCurrency(),
                        'cart' => $helper->getCart(),
                        'is_free' => $is_free,
                        'no_shipping' => $no_shipping,
                        'fee' => $fee,
                        'fee_currency' => $currencyHelper->fomatCurrency($fee, ($no_shipping ? 'txt_no_shipping_ship' : 'txt_mien_phi_ship') ),
                        'subtotal_orig' => $subtotal_orig,
                        'subtotal_orig_currency' => $currencyHelper->fomatCurrency($subtotal_orig, ''),
                        'subtotal' => $subtotal,
                        'subtotal_currency' => $currencyHelper->fomatCurrency($subtotal, ''),
                        'subtotal_tax' => $subtotal_tax,
                        'subtotal_tax_currency' => $currencyHelper->fomatCurrency($subtotal_tax, ''),
                        'total' => $total,
                        'total_currency' => $currencyHelper->fomatCurrency($total, ''),
                        'total_tax' => $total_tax,
                        'total_tax_currency' => $currencyHelper->fomatCurrency($total_tax, ''),
                        'msg' => $translator->translate('txt_coupon_da_su_dung'),
                    ));
                    die;
                }else{
                    echo json_encode(array(
                        'flag' => FALSE,
                        'type' => 'useCoupon',
                        'msg' => $translator->translate('txt_coupon_khong_hop_le'),
                    ));
                    die;
                }
            }catch(\Exception $ex) {
                echo json_encode(array(
                    'flag' => FALSE,
                    'type' => 'useCoupon',
                    'msg' => $translator->translate('txt_coupon_khong_hop_le'),
                ));
                die;
            }
        }
        die();
    }

    public function removeCouponAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $couponsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Coupons');
            $translator = $this->getServiceLocator()->get('translator');
            try{
                $coupon = $couponsHelper->getCoupon();
                if( !empty($coupon) ){
                    $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
                    $currencyHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Currency');
                    unset($_SESSION['cart']['coupon']);
                    $subtotal = 0;
                    $subtotal_orig = 0;
                    $subtotal_tax = 0;
                    if(!empty($_SESSION['cart'])){
                        $calculate = $helper->sumSubTotalPriceInCart();
                        $subtotal = $calculate['price_total'];
                        $subtotal_orig = $calculate['price_total_orig'];
                        $subtotal_tax = $calculate['price_total_tax'];
                    }
                    $is_free = $helper->getIsFreeShipping();
                    $no_shipping = $helper->getNoShipping();
                    $fee = $helper->getFeeShipping();
                    $total = $subtotal+$fee;
                    $total_tax = $subtotal_tax+$fee;
                    echo json_encode(array(
                        'flag' => TRUE,
                        'type' => 'removeCoupon',
                        'cart' => $helper->getCart(),
                        'is_free' => $is_free,
                        'no_shipping' => $no_shipping,
                        'fee' => $fee,
                        'fee_currency' => $currencyHelper->fomatCurrency($fee, ($no_shipping ? 'txt_no_shipping_ship' : 'txt_mien_phi_ship') ),
                        'subtotal_orig' => $subtotal_orig,
                        'subtotal_orig_currency' => $currencyHelper->fomatCurrency($subtotal_orig, ''),
                        'subtotal' => $subtotal,
                        'subtotal_currency' => $currencyHelper->fomatCurrency($subtotal, ''),
                        'subtotal_tax' => $subtotal_tax,
                        'subtotal_tax_currency' => $currencyHelper->fomatCurrency($subtotal_tax, ''),
                        'total' => $total,
                        'total_currency' => $currencyHelper->fomatCurrency($total, ''),
                        'total_tax' => $total_tax,
                        'total_tax_currency' => $currencyHelper->fomatCurrency($total_tax, ''),
                        'msg' => $translator->translate('txt_coupon_da_duoc_xoa'),
                    ));
                    die;
                }else{
                    echo json_encode(array(
                        'flag' => FALSE,
                        'type' => 'removeCoupon',
                        'msg' => $translator->translate('txt_coupon_khong_hop_le'),
                    ));
                    die;
                }
            }catch(\Exception $ex) {
                echo json_encode(array(
                    'flag' => FALSE,
                    'type' => 'removeCoupon',
                    'msg' => $translator->translate('txt_coupon_khong_hop_le'),
                ));
                die;
            }
        }
        die();
    }

    public function showAction(){
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }
    
    public function isLogin()
    {
        if (isset($_SESSION['MEMBER'])) {
            return TRUE;
        }
        return FALSE;
    }

    public function emptyCartAction()
    {
        unset($_SESSION['cart']);
        return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
            'action' => 'index'
        ));
    }

    public function removeFromCartAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $type_id = $this->params()->fromRoute('type_id', 0);
        if (isset($_SESSION['cart'][$id]) && isset($_SESSION['cart'][$id]['product_type'][$type_id]) ) {
            unset($_SESSION['cart'][$id]['product_type'][$type_id]);
            if(empty($_SESSION['cart'][$id]['product_type'])){
                unset($_SESSION['cart'][$id]);
            }
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
            'action' => 'index'
        ));
    }

    public function errorAction()
    {
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator');
        $cart = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($translator->translate('txt_title_site_error_payment'));
        $request = $this->getRequest();
        $vnp_ResponseCode = $this->params()->fromQuery('vnp_ResponseCode', '');
        $error = array('vnp_ResponseCode' => $vnp_ResponseCode);

        unset($_SESSION['invoice_id']);
        unset($_SESSION['PAYMENT_ERROR']);
        $msg = $cart->getMessageError($error);
        $this->data_view['vnp_ResponseCode'] = $vnp_ResponseCode;
        $this->data_view['msg'] = $msg;
        return $this->data_view;
    }

    public function testSitAction()
    {
        return $this->redirect()->toRoute(
            $this->getUrlRouterLang().'cart', 
            array(
                'action' => 'error'
            ),
            array( 'query' => array(
                'foo' => 'bar'
            ))
        );
        die();
        $id = '973';
        $invoice = $this->getModelTable('InvoiceTable')->getInvoice($id);
        if( !empty($invoice) ){
            $payment = $this->getModelTable('PaymentTable')->getPayment($invoice->payment_id);
            if( !empty($payment) ){
                $cb_return = $this->baseUrl.$this->getUrlPrefixLang().'/vnpay/return/'.$invoice->invoice_id;
                $vnpay = new Vnpay();
                $is_sandbox = TRUE;
                if( $payment->is_sandbox == 0 ){
                    $is_sandbox = FALSE;
                }
                $vnpay->setIsSandbox( $is_sandbox );
                $vnpay->setVnpMerchant( $payment->vnp_merchant );
                $vnpay->setVnpTmnCode( $payment->vnp_tmncode );
                $vnpay->setVnpAmount( $invoice->total_converter );
                $vnpay->setVnpCommand( 'pay' );
                $vnpay->setVnpCurrCode( $invoice->to_currency );
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
                $vnpay->setHashSecret( 'BOFPOCIYWQWDGVWDPKLRZHJEAYYTKHJCVUT' );
                $url = $vnpay->getUrlPay();
                return $this->redirect()->toUrl($url);
            }
        }
        die('(-_-) test');
    }

    public function getModelTable($name)
    {
        if (!isset($this->{$name})) {
            $this->{$name} = NULL;
        }
        if (!$this->{$name}) {
            $sm = $this->getServiceLocator();
            $this->{$name} = $sm->get('Application\Model\\' . $name);
        }
        return $this->{$name};
    }

    public function loadCitiesAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $country_id = $request->getPost('country_id', '');
            if(!empty($country_id)){
                $data = $this->getModelTable('UserTable')->loadCities($country_id);
                echo json_encode(array(
                    'success' => TRUE,
                    'results' => $data,
                    'type' => 'loadCities',
                ));
                die();
            }
        }
        echo json_encode(array(
            'success' => FALSE,
            'type' => 'loadCities',
        ));
        die();
    }

    public function loadDistrictAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $cities_id = $request->getPost('cities_id', '');
            if(!empty($cities_id)){
                $data = $this->getModelTable('UserTable')->loadDistrict($cities_id);
                echo json_encode(array(
                    'success' => TRUE,
                    'results' => $data,
                    'type' => 'loadDistrict',
                ));
                die();
            }
        }
        echo json_encode(array(
            'success' => FALSE,
            'type' => 'loadDistrict',
        ));
        die();
    }

    public function loadWardAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $districts_id = $request->getPost('districts_id');
            $data = $this->getModelTable('UserTable')->loadWard($districts_id);
            echo json_encode(array(
                'success' => TRUE,
                'results' => $data,
                'type' => 'loadWard',
            ));
        }
        die();
    }

    public function loadCitiesByAreaAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $area_id = $request->getPost('area_id', '');
            if(!empty($area_id)){
                $data = $this->getModelTable('UserTable')->loadCitiesByArea($area_id);
                echo json_encode(array(
                    'success' => TRUE,
                    'results' => $data,
                    'type' => 'loadCitiesByArea',
                ));
                die();
            }
        }
        echo json_encode(array(
            'success' => FALSE,
            'type' => 'loadCitiesByArea',
        ));
        die();
    }

    public function getAddressAction()
    {
        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $country_id = (int)$this->params()->fromQuery('country_id', 0);
        $nsp = $this->params()->fromQuery('nsp', '');
        $country = $this->getModelTable('CountryTable')->getOne($country_id);
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate("application/cart/get-address");
        $viewModel->setVariables(array(
            'country_id' => $country_id,
            'country' => $country,
            'nsp' => $nsp
        ));

        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRender->render($viewModel);
        echo json_encode(array(
            'type' => 'getAddress',
            'country_id' => $country_id,
            'country' => $country,
            'flag' => TRUE,
            'html' => $html
        ));
        die;
    }

} 