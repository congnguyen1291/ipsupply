<?php
namespace Cms\Controller;


use Cms\Form\BanksForm;
use Cms\Lib\Paging;
use Cms\Model\Banks;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Location\Coordinate;
use Location\Distance\Vincenty;

use JasonGrimes\Paginator;

class AssignController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'assign';
    }

    public function indexAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id', 0);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;
        if( !empty($q) ){
            if( $type == 0 ){
                $params['assign_name'] = $q;
            }else if( $type == 1 ){
                $params['assign_code'] = $q;
            }else if( $type == 2 ){
                $params['invoice_title'] = $q;
            }
        }

        $total = $this->getModelTable('AssignTable')->getTotalAssigns( $params );
        $assigns = $this->getModelTable('AssignTable')->getAssigns( $params );

        $link = '/cms/assign?page=(:num)'. '?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['assigns'] = $assigns;
        $this->data_view['limit'] = $limit;
        $this->data_view['page'] = $page;
        $this->data_view['id'] = $id;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction()
    {
        $cartHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $id = (int)$this->params()->fromRoute('id', 0);//id invoice
        if ( empty($id) ) {
            return $this->redirect()->toRoute('cms/invoice', array(
                'action' => 'index'
            ));
        }
        $invoice = $this->getModelTable('InvoiceTable')->getDetailInvoice($id);
        if ( empty($invoice) ) {
            return $this->redirect()->toRoute('cms/invoice', array(
                'action' => 'index'
            ));
        }

        $assignI = $this->getModelTable('AssignTable')->getAssign( array('invoice_id' => $id) );
        if( !empty($assignI) ){
            return $this->redirect()->toRoute('cms/assign', array(
                'action' => 'edit',
                'id' => $assignI->assign_id
            ));
        }

        $longitude = 105.849998;
        $latitude = 21.033333;
        $IId = $id;
        $pId = array();

        $cart = $this->getModelTable('InvoiceTable')->getProductsCart($invoice->invoice_id);
        $dataExtension = $this->getModelTable('InvoiceTable')->getInvoiceExtensions($invoice->invoice_id);
        $shipping = $this->getModelTable('InvoiceTable')->getShipping($invoice->invoice_id);
        $coupon = $this->getModelTable('InvoiceTable')->getCouponByInvoice($invoice->invoice_id);
        $dataCart = array();
        foreach($cart as $key => $product){
            $products_id = $product['products_id'];
            $pId[] = $products_id;
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
                $price_this_old = $productsHelper->getPrice($product)*$quantity;

                if(isset($product['extensions']) && count($product['extensions'])){
                    $ext_list = $product['extensions'];
                    foreach($ext_list as $ext){
                        $priceQl = $ext['price']*$ext['quantity'];
                        $price_this += $priceQl;
                        $price_this_old += $priceQl;
                    }
                }

                
                $dataCart[$item]['product_type'][$product_type_id]['price_total'] = $price_this;
                $dataCart[$item]['product_type'][$product_type_id]['price_total_old'] = $price_this_old;
            }
        }

        if( !empty($coupon) ){
            $dataCart['coupon'] = $coupon;
        }

        //tinh phi
        $is_free = FALSE;
        if( !empty($invoice->is_free_shipping) ){
            $is_free = TRUE;
        }
        $fee = 0;
        if( !empty($shipping->shipping_fee) ){
            $fee = $shipping->shipping_fee;
        }

        $KMAssign = array();
        $merchants = array();
        $customer = array();
        if( !empty($IId) && !empty($pId) ){
            try{
                $customer = $this->getModelTable('InvoiceTable')->getLatLongInvoice( $IId );
                if( !empty($customer) ){
                    if( empty($customer['longitude'])
                        || empty($customer['latitude']) ){
                        $address = $customer['address'];
                        $prepAddr = str_replace(' ','+',$address);
                        $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
                        $output= json_decode($geocode);
                        $latitude = $output->results[0]->geometry->location->lat;
                        $longitude = $output->results[0]->geometry->location->lng;
                    }else{
                        $longitude = $customer['longitude'];
                        $latitude = $customer['latitude'];
                        $customer->longitude = $longitude;
                        $customer->latitude = $latitude;
                    }
                    if( !empty($longitude) && !empty($latitude) ){
                        $merchants = $this->getModelTable('ProductTable')->getPublisherOfProduct( $pId );
                        foreach ($merchants as $key => $pub) {
                            $coordinate1 = new Coordinate($latitude, $longitude);
                            $coordinate2 = new Coordinate($pub['latitude'], $pub['longitude']);
                            $calculator = new Vincenty();
                            $distance = $calculator->getDistance($coordinate1, $coordinate2);
                            $KMAssign[] = array(
                                    'latitude' => $latitude,
                                    'longitude' => $longitude,
                                    'from' => $customer,
                                    'to' => $pub,
                                    'distance' => $distance,
                                    'unit' => 'm',
                                );
                        }

                        //sort
                        $distances = array();
                        foreach ($KMAssign as $key => $kn)
                        {
                            $distances[$key] = $kn['distance'];
                        }
                        array_multisort($distances, SORT_ASC, $KMAssign);
                    }
                }
            }catch (\Exception $ex){
                echo $ex->getMessage();
                die();
            }
        }

        $calculate = $cartHelper->sumSubTotalPriceFromArray($dataCart);
        $total = $calculate['price_total'];
        $total_old = $calculate['price_total_old'];
        $total_orig = $calculate['price_total_orig'];
        $total_tax = $calculate['price_total_tax'];
        $total_old_tax = $calculate['price_total_old_tax'];
        $total_orig_tax = $calculate['price_total_orig_tax'];

        $merchantsOrther = array();
        if( !empty($merchants) ){
            $idmc = array();
            foreach ($merchants as $key => $mc) {
                $idmc[] = $mc['merchant_id'];
            }
            $params = array(
                'notIn' => array(
                    'merchant_id' => $idmc
                )
            );
            $merchantsOrther = $this->getModelTable('MerchantTable')->getMerchants( $params );
        }else{
            $merchantsOrther = $this->getModelTable('MerchantTable')->getMerchants();
        }

        $merchantsOfAssigns = array();
        if( !empty($invoice->assign_id) ){
            $meOfAss = $this->getModelTable('AssignTable')->getAssignMerchants(array('assign_id' => $invoice->assign_id));
            foreach ($meOfAss as $key => $meOfAs) {
                $merchantsOfAssigns[] = $meOfAs['merchant_id'];
            }
        }

        $this->data_view['KMAssign'] = $KMAssign;
        $this->data_view['longitude'] = $longitude;
        $this->data_view['latitude'] = $latitude;
        $this->data_view['invoice'] = $invoice;
        $this->data_view['invoice_id'] = $IId;
        $this->data_view['coupon'] = $coupon;
        $this->data_view['total'] = $total;
        $this->data_view['total_old'] = $total_old;
        $this->data_view['total_orig'] = $total_orig;
        $this->data_view['total_tax'] = $total_tax;
        $this->data_view['total_old_tax'] = $total_old_tax;
        $this->data_view['total_orig_tax'] = $total_orig_tax;
        $this->data_view['fee'] = $fee;
        $this->data_view['shipping'] = $shipping;
        $this->data_view['is_free'] = $is_free;
        $this->data_view['products'] = $dataCart;
        $this->data_view['merchants'] = $merchants;
        $this->data_view['merchantsOrther'] = $merchantsOrther;
        $this->data_view['customer'] = $customer;
        $this->data_view['cart'] = $cart;
        $this->data_view['merchantsOfAssigns'] = $merchantsOfAssigns;

        return $this->data_view;
    }

    public function editAction()
    {
        $cartHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $assign_id = (int)$this->params()->fromRoute('id', 0);//id assign
        if ( empty($assign_id) ) {
            return $this->redirect()->toRoute('cms/assign', array(
                'action' => 'index'
            ));
        }
        $assign = $this->getModelTable('AssignTable')->getAssign( array('assign_id' => $assign_id) );
        if ( empty($assign) ) {
            return $this->redirect()->toRoute('cms/assign', array(
                'action' => 'index'
            ));
        }

        $invoice = $this->getModelTable('InvoiceTable')->getDetailInvoice($assign->invoice_id);
        if ( empty($invoice) ) {
            return $this->redirect()->toRoute('cms/invoice', array(
                'action' => 'index'
            ));
        }

        $id = $invoice->invoice_id;

        $longitude = 105.849998;
        $latitude = 21.033333;
        $IId = $id;
        $pId = array();

        $cart = $this->getModelTable('InvoiceTable')->getProductsCart($invoice->invoice_id);
        $dataExtension = $this->getModelTable('InvoiceTable')->getInvoiceExtensions($invoice->invoice_id);
        $coupon = $this->getModelTable('InvoiceTable')->getCouponByInvoice($invoice->invoice_id);
        $assignPrice = $this->getModelTable('AssignTable')->getAssignProducts( array('assign_id' => $assign_id) );
        $prices = array();
        foreach ($assignPrice as $key => $prc) {
            $prices[$prc['products_invoice_id']] = $prc;
        }
        $dataCart = array();
        foreach($cart as $key => $product){
            $products_id = $product['products_id'];
            $pId[] = $products_id;
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
            //$dataCart[$product['products_id']]['product_type'][$products_type_id]['price_sale'] = (!empty($product['price_sale']) ? $product['price_sale'] : $product['t_price_sale']);
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['price_sale'] = (!empty($prices[$product['id']]) ? $prices[$product['id']]['price_sale'] : 0);
            $dataCart[$product['products_id']]['product_type'][$products_type_id]['t_price_sale'] = (!empty($prices[$product['id']]) ? $prices[$product['id']]['price_sale'] : 0);
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
                $price_this_old = $productsHelper->getPrice($product)*$quantity;

                if(isset($product['extensions']) && count($product['extensions'])){
                    $ext_list = $product['extensions'];
                    foreach($ext_list as $ext){
                        $priceQl = $ext['price']*$ext['quantity'];
                        $price_this += $priceQl;
                        $price_this_old += $priceQl;
                    }
                }

                
                $dataCart[$item]['product_type'][$product_type_id]['price_total'] = $price_this;
                $dataCart[$item]['product_type'][$product_type_id]['price_total_old'] = $price_this_old;
            }
        }

        if( !empty($coupon) ){
            $dataCart['coupon'] = $coupon;
        }

        //tinh phi
        $is_free = FALSE;
        $fee = $assign->assign_shipping;
        if( empty($fee) ){
            $is_free = TRUE;
        }

        $KMAssign = array();
        $merchants = array();
        $customer = array();
        if( !empty($IId) && !empty($pId) ){
            try{
                $customer = $this->getModelTable('InvoiceTable')->getLatLongInvoice( $IId );
                if( !empty($customer) ){
                    if( empty($customer['longitude'])
                        || empty($customer['latitude']) ){
                        $address = $customer['address'];
                        $prepAddr = str_replace(' ','+',$address);
                        $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
                        $output= json_decode($geocode);
                        $latitude = $output->results[0]->geometry->location->lat;
                        $longitude = $output->results[0]->geometry->location->lng;
                    }else{
                        $longitude = $customer['longitude'];
                        $latitude = $customer['latitude'];
                        $customer->longitude = $longitude;
                        $customer->latitude = $latitude;
                    }
                    if( !empty($longitude) && !empty($latitude) ){
                        $merchants = $this->getModelTable('ProductTable')->getPublisherOfProduct( $pId );
                        foreach ($merchants as $key => $pub) {
                            $coordinate1 = new Coordinate($latitude, $longitude);
                            $coordinate2 = new Coordinate($pub['latitude'], $pub['longitude']);
                            $calculator = new Vincenty();
                            $distance = $calculator->getDistance($coordinate1, $coordinate2);
                            $KMAssign[] = array(
                                    'latitude' => $latitude,
                                    'longitude' => $longitude,
                                    'from' => $customer,
                                    'to' => $pub,
                                    'distance' => $distance,
                                    'unit' => 'm',
                                );
                        }

                        //sort
                        $distances = array();
                        foreach ($KMAssign as $key => $kn)
                        {
                            $distances[$key] = $kn['distance'];
                        }
                        array_multisort($distances, SORT_ASC, $KMAssign);
                    }
                }
            }catch (\Exception $ex){
                echo $ex->getMessage();
                die();
            }
        }

        $calculate = $cartHelper->sumSubTotalPriceFromArray($dataCart);
        $total = $calculate['price_total'];
        $total_old = $calculate['price_total_old'];
        $total_orig = $calculate['price_total_orig'];
        $total_tax = $calculate['price_total_tax'];
        $total_old_tax = $calculate['price_total_old_tax'];
        $total_orig_tax = $calculate['price_total_orig_tax'];

        $merchantsOrther = array();
        if( !empty($merchants) ){
            $idmc = array();
            foreach ($merchants as $key => $mc) {
                $idmc[] = $mc['merchant_id'];
            }
            $params = array(
                'notIn' => array(
                    'merchant_id' => $idmc
                )
            );
            $merchantsOrther = $this->getModelTable('MerchantTable')->getMerchants( $params );
        }else{
            $merchantsOrther = $this->getModelTable('MerchantTable')->getMerchants();
        }

        $merchantsOfAssigns = array();
        $meOfAss = $this->getModelTable('AssignTable')->getAssignMerchants(array('assign_id' => $invoice->assign_id));
        foreach ($meOfAss as $key => $meOfAs) {
            $merchantsOfAssigns[] = $meOfAs['merchant_id'];
        }

        $this->data_view['KMAssign'] = $KMAssign;
        $this->data_view['longitude'] = $longitude;
        $this->data_view['latitude'] = $latitude;
        $this->data_view['invoice'] = $invoice;
        $this->data_view['invoice_id'] = $IId;
        $this->data_view['coupon'] = $coupon;
        $this->data_view['total'] = $total;
        $this->data_view['total_old'] = $total_old;
        $this->data_view['total_orig'] = $total_orig;
        $this->data_view['total_tax'] = $total_tax;
        $this->data_view['total_old_tax'] = $total_old_tax;
        $this->data_view['total_orig_tax'] = $total_orig_tax;
        $this->data_view['fee'] = $fee;
        $this->data_view['is_free'] = $is_free;
        $this->data_view['products'] = $dataCart;
        $this->data_view['merchants'] = $merchants;
        $this->data_view['merchantsOrther'] = $merchantsOrther;
        $this->data_view['customer'] = $customer;
        $this->data_view['cart'] = $cart;
        $this->data_view['merchantsOfAssigns'] = $merchantsOfAssigns;
        $this->data_view['assign'] = $assign;
        $this->data_view['assign_id'] = $assign_id;

        return $this->data_view;
    }

    public function addToAssignAction()
    {
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_co_loi_xay_ra'));
        $products_id = $this->params()->fromQuery('products_id', 0);
        $invoice_id = $this->params()->fromQuery('invoice_id', 0);
        $products_type_id = $this->params()->fromQuery('products_type_id', 0);
        //chi dc cho 1 invoice
        if( !empty($invoice_id) && !empty($products_id)
            && ( empty($_SESSION['assign']) 
                || empty($_SESSION['assign']['products']) 
                || (!empty($_SESSION['assign']) && !empty($_SESSION['assign']['products'][$invoice_id]))) ){
            $product = $this->getModelTable('InvoiceTable')->getProductOnInvoiceByInvoiceIdAndProductsId( $invoice_id, $products_id, $products_type_id );
            if( !empty($product) ){
                if ( empty($_SESSION['assign']) ) {
                    $_SESSION['assign'] = array('products' => array());
                }
                if (  empty($_SESSION['assign']['products'][$invoice_id]) ){
                    $_SESSION['assign']['products'][$invoice_id] = array();
                }
                if (  empty($_SESSION['assign']['products'][$invoice_id][$products_id]) ){
                    $_SESSION['assign']['products'][$invoice_id][$products_id] = array();
                }
                $_SESSION['assign']['products'][$invoice_id][$products_id][$products_type_id] = $product;
                $item = array(
                    'flag' => TRUE,
                    'invoice_id' => $invoice_id,
                    'products_id' => $products_id,
                    'products_type_id' => $products_type_id,
                    'product' => $product,
                    'data' => $_SESSION['assign'],
                    'msg' => $translator->translate('txt_them_san_pham_trong_assign_thanh_cong')
                );
            }
        }
        echo json_encode($item);
        die;
    }

    public function removeFromAssignAction()
    {
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_co_loi_xay_ra'));
        $products_id = $this->params()->fromQuery('products_id', 0);
        $invoice_id = $this->params()->fromQuery('invoice_id', 0);
        $products_type_id = $this->params()->fromQuery('products_type_id', 0);
        //chi dc cho 1 invoice
        if( !empty($invoice_id) 
            && !empty($products_id)
            && !empty($_SESSION['assign'])
            && !empty($_SESSION['assign']['products'][$invoice_id])
            && !empty($_SESSION['assign']['products'][$invoice_id][$products_id])
            && !empty($_SESSION['assign']['products'][$invoice_id][$products_id][$products_type_id]) ){

            unset($_SESSION['assign']['products'][$invoice_id][$products_id][$products_type_id]);

            if (  empty($_SESSION['assign']['products'][$invoice_id][$products_id]) ){
                unset($_SESSION['assign']['products'][$invoice_id][$products_id]);
            }

            if ( empty($_SESSION['assign']['products'][$invoice_id]) ) {
                unset($_SESSION['assign']['products'][$invoice_id]);
            }
            
            $item = array(
                'flag' => TRUE,
                'invoice_id' => $invoice_id,
                'products_id' => $products_id,
                'products_type_id' => $products_type_id,
                'data' => $_SESSION['assign'],
                'msg' => $translator->translate('txt_xoa_san_pham_trong_assign_thanh_cong')
            );
        }
        echo json_encode($item);
        die;
    }

    public function create_Action()
    {
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_co_loi_xay_ra'));
        $request = $this->getRequest();
        if ($request->isPost() && !empty($_SESSION['assign']) && !empty($_SESSION['assign']['products']) ) {
            $assign_id = $request->getPost('assign_id', 0);
            $id = $request->getPost('id', 0);
            $code = $request->getPost('code', '');
            $name = $request->getPost('name', '');
            $price = $request->getPost('price', '');
            $unit = $request->getPost('unit', '');
            if( !empty($id) && !empty($code) 
                && !empty($name) && !empty($price) && !empty($unit) ){
                $publisher = $this->getModelTable('UserTable')->getUserById( $id );
                if( !empty($publisher) && $publisher->type == 'publisher' ){
                    $products = $_SESSION['assign']['products'];
                    $invoices = array_keys($products);
                    if( !empty($invoices) ){
                        $invoice_id = $invoices[0];
                        $products = $products[$invoice_id];

                        $invoice = $this->getModelTable('InvoiceTable')->getInvoice($invoice_id);
                        if( !empty($invoice) 
                            && ( $invoice->delivery = 'pending' || $invoice->delivery = 'processing' ) ){
                            //user đã  da assign sản phẩm rồi thi thôi
                            $hasAssign = FALSE;
                            $pId = array();
                            foreach ($products as $products_id => $product) {
                                foreach ($product as $products_type_id => $type ) {
                                    $pId[$type['products_id']] = $type['products_type_id'];
                                    $hasAssign = $this->getModelTable('AssignTable')->checkProductAssignsPublisher( $id,  $type['invoice_id'], $type['products_id'], $type['products_type_id']);
                                    if( $hasAssign ){
                                        break;
                                    }
                                }
                            }
                            if( !$hasAssign && !empty($pId) ){
                                $ratio_usd = 1;
                                if( $unit != 'USD' ){
                                    $ratio_usd = $this->Currency()->exchangerates('USD', $unit, 1);
                                }
                                $row = array(
                                        'invoice_id' => $invoice_id,
                                        'assign_id' => $assign_id,
                                        'publisher_id' => $id,
                                        'assign_code' => $code,
                                        'assign_name' => $name,
                                        'total_money' => $price,
                                        'assign_unit' => $unit,
                                        'ratio_usd' => $ratio_usd,
                                    );
                                $assign_id = $this->getModelTable('AssignTable')->createAssign( $products, $row );
                                if( !empty($assign_id) ){
                                    $item = array(
                                        'flag' => TRUE,
                                        'data' => $_SESSION['assign'],
                                        'products' => $products,
                                        'id' => $id,
                                        'assign_id' => $assign_id,
                                        'msg' => $translator->translate('txt_assign_thanh_cong')
                                    );
                                }
                            }else{
                                $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_san_pham_da_assign'));
                            }
                        }
                    }
                }
            }
        }
        echo json_encode($item);
        die;
    }

    public function createAction()
    {
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_co_loi_xay_ra'));
        $request = $this->getRequest();
        if ( $request->isPost() ) {
            $assign_id = $request->getPost('assign_id', 0);
            $merchant_id = $request->getPost('merchant_id', array());
            $invoice_id = $request->getPost('invoice_id', 0);
            $assign_code = $request->getPost('assign_code', '');
            $assign_name = $request->getPost('assign_name', '');
            $price = $request->getPost('price', array());
            $shipping = $request->getPost('shipping', 0);
            if( !empty($invoice_id) && !empty($assign_code) && !empty($assign_name) ){
                $invoice = $this->getModelTable('InvoiceTable')->getInvoice($invoice_id);
                if( !empty($invoice) 
                    && ( $invoice->delivery = 'pending' || $invoice->delivery = 'processing' ) ){
                    $hasAssign = FALSE;
                    $assign = array();
                    $assignI = $this->getModelTable('AssignTable')->getAssign( array('invoice_id' => $invoice_id) );
                    if( !empty($assignI) ){
                        if( !empty($assign_id) && $assignI->assign_id != $assign_id ){
                            $hasAssign = TRUE;
                        }else if( empty($assign_id) ){
                            $assign_id = $assignI->assign_id;
                        }
                    }

                    if( !$hasAssign && !empty($assign_id) && !empty($merchant_id) ){
                        $assign = $this->getModelTable('AssignTable')->getAssign( array('assign_id' => $assign_id) );
                        if( !empty($assign) ){
                            $hasAssign = $this->getModelTable('AssignTable')->checkAssignsMerchant( $assign_id,  $merchant_id);
                        }else{
                            $hasAssign = TRUE;
                        }
                    }

                    if( !$hasAssign ){
                        $row = array(
                                'assign_id' => $assign_id,
                                'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                                'invoice_id' => $invoice_id,
                                'merchant_id' => $merchant_id,
                                'assign_code' => $assign_code,
                                'assign_name' => $assign_name,
                                'price' => $price,
                                'shipping' => $shipping
                            );
                        $assign_id = $this->getModelTable('AssignTable')->createAssign( $row );
                        if( !empty($assign_id) ){
                            return $this->redirect()->toRoute('cms/assign', array(
                                'action' => 'index'
                            ));
                        }
                    }else{
                        return $this->redirect()->toRoute('cms/assign', array(
                            'add' => 'index',
                            'id' => $invoice_id
                        ));
                    }
                }
            }
        }
        return $this->redirect()->toRoute('cms/assign', array(
            'action' => 'index'
        ));
    }

}