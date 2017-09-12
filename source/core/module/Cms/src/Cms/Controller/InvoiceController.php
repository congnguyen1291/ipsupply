<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 2:43 PM
 */

namespace Cms\Controller;

use Cms\Form\InvoiceForm;
use Cms\Model\Invoice;
use Zend\View\Model\ViewModel;
use Cms\Lib\Paging;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

use JasonGrimes\Paginator;

class InvoiceController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'invoice';
    }

    public function indexAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        if( !empty($q) ){
            if( $type == 0 ){
                $params['invoice_title'] = $q;
            }else if( $type == 1 ){
                $params['full_name'] = $q;
            }
        }

        $total = $this->getModelTable('InvoiceTable')->countAll($params);
        $invoices = $this->getModelTable('InvoiceTable')->fetchAll( $params );

        $link = '/cms/invoice?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['invoices'] = $invoices;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction(){
        return $this->redirect()->toRoute('cms/invoice', array(
            'action' => 'index'
        ));
        $form = new InvoiceForm();
        $trans = $this->getModelTable('TransportationTable')->fetchAll('', '', 0, 100);
        $users = $this->getModelTable('InvoiceTable')->getAllUsers();
        $cities = $this->getModelTable('UserTable')->loadCities();
        $trans_data = array();
        $users_data = array();
        foreach($trans as $tran){
            $trans_data[$tran->transportation_id] = $tran->transportation_title;
        }
        $users_data[0] = '-- Chọn User --';
        foreach($users as $user){
            $users_data[$user->users_id] = $user->user_name;
        }
        $form->get('transportation_id')->setOptions(array(
            'options' => $trans_data
        ));
        $form->get('users_id')->setOptions(array(
            'options' => $users_data
        ));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $m = new Invoice();
            $form->setInputFilter($m->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $data = $form->getData();
                    $data['cities_id'] = $request->getPost('cities_id');
                    $data['districts_id'] = $request->getPost('districts_id');
                    $data['wards_id'] = $request->getPost('wards_id');
                    $viewModel = new ViewModel();
                    $viewModel->setTerminal(true);
                    $viewModel->setTemplate("cms/invoice/content_invoice");
                    $dataPayment = array();
                    $dataPayment['full_name'] = $data['full_name'];
                    $dataPayment['phone'] = $data['phone'];
                    $city = $this->getModelTable('UserTable')->getCity($data['cities_id']);
                    $dist = $this->getModelTable('UserTable')->getDistrict($data['districts_id']);
                    $ward = $this->getModelTable('UserTable')->getWard($data['wards_id']);
                    $dataPayment['address'] = $data['address'].','.$ward['wards_title'].' - '.$dist['districts_title'].' - '. $city['cities_title'];
                    $dataOrder = array();
                    if($data['company_name']){
                        $dataOrder['xuathoadon'] = 1;
                        $dataOrder['company_name'] = $data['company_name'];
                        $dataOrder['company_tax_code'] = $data['company_tax_code'];
                        $dataOrder['company_address'] = $data['company_address'];
                    }
                    $dataMember = array();
                    $cart = $request->getPost('pdetail');
                    $dataCart = array();
                    foreach($cart as $key => $p){
                        $dataCart[$key] = array(
                            'price_sale' => $p['price'],
                            'quantity' => $p['quantity'],
                            'code' => $p['products_code'],
                            'title' => $p['products_title'],
                            'vat' => $p['vat'],
                        );
                    }
                    $viewModel->setVariables(array(
                        'datapayment' => $dataPayment,
                        'datamember' => $dataMember,
                        'datacart' => $dataCart,
                        'dataorder' => $dataOrder,
                    ));
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $data['content'] = $viewRender->render($viewModel);
                    $data['content'] = htmlentities($data['content'], ENT_QUOTES, 'UTF-8');
                    $m->exchangeArray($data);
                    if ($this->getModelTable('InvoiceTable')->saveInvoice($m, $request)) {
                        /*strigger change namespace cached*/
                        $this->updateNamespaceCached();

                        return $this->redirect()->toRoute('cms/invoice');
                    }
                }catch (\Exception $ex){
                    $_SESSION['error_message'] = $ex->getMessage();
                }
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['cities'] = $cities;
        return $this->data_view;
    }

    private function checkOrderFree($free){
        if(!empty($free['conditions']) && $free['transportation_type'] == 1){
            $condition = json_decode($free['conditions']);
            if(!empty($condition['column'])
                && !empty($condition['equal'])
                && !empty($condition['value']) ){
                $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
                $value_column = 0;
                $cart = $_SESSION['cart'];
                if($condition['column'] == 'quality'){
                    $calculate = $helper->getNumberProductInCart($cart);
                    $value_column = $calculate;
                }else if($condition['column'] == 'subtotal'){
                    $calculate = $helper->calTotalCart($cart);
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
                $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
                $cart = $_SESSION['cart'];
                $qty = $helper->getNumberProductInCart($cart);
                $fee = $shipping['price']*$qty;
            }else if($shipping['price_type'] == 2){
                $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
                $cart = $_SESSION['cart'];
                $total = $helper->calTotalCart($cart);
                $fee = $shipping['min_fee'] + $shipping['fee_percent']*$total;
            }else if($shipping['price_type'] == 3){
                $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
                $cart = $_SESSION['cart'];
                $qty = $helper->getNumberProductInCart($cart);
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

    public function editAction(){
        $cartHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/invoice', array(
                'action' => 'index'
            ));
        }
        $prices = array();
        $merchants = array();
        $assign = array();
        $logs = array();
        try {
            $invoice = $this->getModelTable('InvoiceTable')->getDetailInvoice($id);
            $logs = $this->getModelTable('InvoiceTable')->getLogs($id);
            if( !empty($invoice->assign_id) ){
                $merchants = $this->getModelTable('AssignTable')->getAssignMerchants(array('assign_id' => $invoice->assign_id));
                $assign = $this->getModelTable('AssignTable')->getAssign( array('assign_id' => $invoice->assign_id) );
                $assignPrice = $this->getModelTable('AssignTable')->getAssignProducts( array('assign_id' => $invoice->assign_id) );
                foreach ($assignPrice as $key => $prc) {
                    $prices[$prc['products_invoice_id']] = $prc;
                }
            }
            $invoice->content = html_entity_decode($invoice->content, ENT_QUOTES, 'UTF-8');
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/invoice', array(
                'action' => 'index'
            ));
        }

        $user = array();
        $merchant = array();
        try {
            if( !empty($assign) ){
                $user = $this->getModelTable('UserTable')->getUserById( $assign->users_id );
                if( !empty($user->merchant_id) ){
                    $merchant = $this->getModelTable('MerchantTable')->getMerchant( array('merchant_id' => $user->merchant_id) );
                }
            }
        } catch (\Exception $ex) {
            $user = array();
        }

        $form = new InvoiceForm();
        $cart = $this->getModelTable('InvoiceTable')->getProductsCart($invoice->invoice_id);
        $dataExtension = $this->getModelTable('InvoiceTable')->getInvoiceExtensions($invoice->invoice_id);
        $shipping = $this->getModelTable('InvoiceTable')->getShipping($invoice->invoice_id);
        $coupon = $this->getModelTable('InvoiceTable')->getCouponByInvoice($id);
        
        $dataCart = array();
        $assignCart = array();
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

            if( !empty($assign) ){
                $assignCart[$product['products_id']] = $dataCart[$product['products_id']];
                $assignCart[$product['products_id']]['product_type'][$products_type_id]['price_sale'] = (!empty($prices[$product['id']]) ? $prices[$product['id']]['price_sale'] : 0);
                $assignCart[$product['products_id']]['product_type'][$products_type_id]['t_price_sale'] = (!empty($prices[$product['id']]) ? $prices[$product['id']]['price_sale'] : 0);
            }
        }

        foreach($dataExtension as $ext){
            if($ext['is_always'] == 0){/*san pham ko bat buoc dinh kem*/
                if(isset($dataCart[$ext['products_id']]) 
                    && isset($dataCart[$ext['products_id']]['product_type'][$ext['products_type_id']])){
                    $dataCart[$ext['products_id']]['extensions'][$ext['id']] = $ext;
                    $dataCart[$ext['products_id']]['product_type'][$ext['products_type_id']]['extensions'][$ext['id']] = $ext;
                    if( !empty($assign) ){
                        $assignCart[$ext['products_id']]['extensions'][$ext['id']] = $ext;
                        $assignCart[$ext['products_id']]['product_type'][$ext['products_type_id']]['extensions'][$ext['id']] = $ext;
                    }
                }
            }else{
                if(isset($dataCart[$ext['products_id']]) 
                    && isset($dataCart[$ext['products_id']]['product_type'][$ext['products_type_id']])){
                    $dataCart[$ext['products_id']]['extensions_require'][$ext['id']] = $ext;
                    $dataCart[$ext['products_id']]['product_type'][$ext['products_type_id']]['extensions_require'][$ext['id']] = $ext;
                    $dataCart[$ext['products_id']]['total_price_extention'] += $ext['price']*$ext['quantity'];
                    $dataCart[$ext['products_id']]['product_type'][$ext['products_type_id']]['total_price_extention'] += $ext['price']*$ext['quantity'];
                    if( !empty($assign) ){
                        $assignCart[$ext['products_id']]['extensions_require'][$ext['id']] = $ext;
                        $assignCart[$ext['products_id']]['product_type'][$ext['products_type_id']]['extensions_require'][$ext['id']] = $ext;
                        $assignCart[$ext['products_id']]['total_price_extention'] += $ext['price']*$ext['quantity'];
                        $assignCart[$ext['products_id']]['product_type'][$ext['products_type_id']]['total_price_extention'] += $ext['price']*$ext['quantity'];
                    }
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

        if( !empty($assign) ){
            foreach($assignCart as $item => $products){
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

                    $assignCart[$item]['product_type'][$product_type_id]['price_total'] = $price_this;
                    $assignCart[$item]['product_type'][$product_type_id]['price_total_old'] = $price_this_old;
                }
            }
        }

        if( !empty($coupon) ){
            $dataCart['coupon'] = $coupon;
            if( !empty($assign) ){
                $assignCart['coupon'] = $coupon;
            }
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

        $form->bind($invoice);

        $calculate = $cartHelper->sumSubTotalPriceFromArray($dataCart);
        $total = $calculate['price_total'];
        $total_old = $calculate['price_total_old'];
        $total_orig = $calculate['price_total_orig'];
        $total_tax = $calculate['price_total_tax'];
        $total_old_tax = $calculate['price_total_old_tax'];
        $total_orig_tax = $calculate['price_total_orig_tax'];

        if( !empty($assign) ){
            $assignCalculate = $cartHelper->sumSubTotalPriceFromArray($assignCart);
            $assign_total = $assignCalculate['price_total'];
            $assign_total_old = $assignCalculate['price_total_old'];
            $assign_total_orig = $assignCalculate['price_total_orig'];
            $assign_total_tax = $assignCalculate['price_total_tax'];
            $assign_total_old_tax = $assignCalculate['price_total_old_tax'];
            $assign_total_orig_tax = $assignCalculate['price_total_orig_tax'];
        }

        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['products'] = $dataCart;
        $this->data_view['invoice'] = $invoice;
        $this->data_view['merchants'] = $merchants;
        $this->data_view['assign'] = $assign;
        $this->data_view['coupon'] = $coupon;

        $this->data_view['total'] = $total;
        $this->data_view['total_old'] = $total_old;
        $this->data_view['total_orig'] = $total_orig;
        $this->data_view['total_tax'] = $total_tax;
        $this->data_view['total_old_tax'] = $total_old_tax;
        $this->data_view['total_orig_tax'] = $total_orig_tax;

        if( !empty($assign) ){
            $this->data_view['assign_products'] = $assignCart;
            $this->data_view['assign_total'] = $assign_total;
            $this->data_view['assign_total_old'] = $assign_total_old;
            $this->data_view['assign_total_orig'] = $assign_total_orig;
            $this->data_view['assign_total_tax'] = $assign_total_tax;
            $this->data_view['assign_total_old_tax'] = $assign_total_old_tax;
            $this->data_view['assign_total_orig_tax'] = $assign_total_orig_tax;
        }

        $this->data_view['fee'] = $fee;
        $this->data_view['shipping'] = $shipping;
        $this->data_view['is_free'] = $is_free;
        $this->data_view['user'] = $user;
        $this->data_view['merchant'] = $merchant;
        $this->data_view['logs'] = $logs;
        return $this->data_view;
    }

    public function updateInvoiceAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            try {
                $id = $request->getPost('invoice_id', 0);
                $delivery = $request->getPost('delivery', '');
                $has_feedback = $request->getPost('has_feedback', 0);
                $has_comment = $request->getPost('has_comment', 0);
                $comment = $request->getPost('comment', '');
                if( !empty($id) && !empty($delivery) ){
                    $invoice = $this->getModelTable('InvoiceTable')->getInvoice($id);
    				if( !empty($invoice) ){
                        if($invoice->delivery !=$delivery){
                            $row = array(
                                'delivery' => $delivery
                            );
                            $this->getModelTable('InvoiceTable')->updateInvoices($id, $row);
                        }
                        $row = array(
                            'invoice_id' => $id,
                            'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                            'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                            'has_feedback' => $has_feedback,
                            'invoice_status' => $delivery,
                            'comment' => $comment,
                        );
                        $this->getModelTable('InvoiceTable')->updateLog($row);

                        if( !empty($has_feedback) ){
            				$viewModel = new ViewModel();
                            $viewModel->setTerminal(true);
                            $viewModel->setTemplate("cms/partial/email_template_feedback");
                            $viewModel->setVariables(array(
                                'invoice' => $invoice,
                                'id' => $id,
                                'delivery' => $delivery,
                                'comment' => $comment,
                                'has_feedback' => $has_feedback,
                                'has_comment' => $has_comment,
                            ));
            				
                            $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                            $html = $viewRender->render($viewModel);
                            $html = new MimePart($html);
                            $html->type = "text/html";
                            $body = new MimeMessage();
                            $body->setParts(array($html));
                            $message = new Message();
                            $message->addTo($invoice->email)
                                ->addFrom(EMAIL_ADMIN_RECEIVE)
                                ->setSubject('Thông báo đơn hàng trên điện hoa')
                                ->setBody($body)
                                ->setEncoding("UTF-8");

                            // Setup SMTP transport using LOGIN authentication
                            $transport = new SmtpTransport();
                            $options = new SmtpOptions(array(
                                'name' => HOST_MAIL,
                                'host' => HOST_MAIL,
                                'connection_class' => 'login',
                                'connection_config' => array(
                                    'username' => USERNAME_HOST_MAIL,
                                    'password' => PASSWORD_HOST_MAIL,
                                ),
                            ));

                            $transport->setOptions($options);
                            $transport->send($message);
                        }
                    }
                    return $this->redirect()->toRoute('cms/invoice', array(
                        'action' => 'edit',
                        'id' => $id
                    ));
                }
            }catch (\Exception $ex){}
        }
        return $this->redirect()->toRoute('cms/invoice', array(
            'action' => 'index'
        ));

    }

    public function filterAction(){
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        $invoices = array();
        if ($request->isPost()) {
            $data_filter = $request->getPost('filter');
            $invoices = $this->getModelTable('InvoiceTable')->filter($data_filter);
        }
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'invoices' => $invoices
        ));
        return $result;
    }

    public function useCouponAction(){
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $coupon = $request->getPost('coupon');
            try{
                $coupon = $this->getModelTable('InvoiceTable')->getCouponByCode($coupon);
                $html = "<ul style='margin: 0;padding:0;list-style-type: none'>
                            <li>ID: <strong>{$coupon['coupons_id']}</strong></li>
                            <li>Loại mã: <strong>".($coupon['coupons_type'] == 0 ? 'Dùng 1 lần' :'Dùng nhiều lần')."</strong></li>
                            <li>Ngày hết hạn: <strong>".(date('d/m/Y', strtotime($coupon['expire_date'])))."</strong></li>
                            <li>Giá trị: <strong>".(number_format($coupon['coupon_price'],0,',','.')).' '.PRICE_UNIT."</strong></li>
                            <li>Giá trị thấp nhất: <strong>".(number_format($coupon['min_price_use'],0,',','.')).' '.PRICE_UNIT."</strong></li>
                            <li>Giá trị cao nhất: <strong>".(number_format($coupon['max_price_use'],0,',','.')).' '.PRICE_UNIT."</strong></li>
                        </ul>";
                if($coupon){
                    echo json_encode(array(
                        'success' => TRUE,
                        'result' => $coupon,
                        'msg' => $html,
                    ));
                    die;
                }else{
                    echo json_encode(array(
                        'success' => FALSE,
                        'msg' => 'Mã khuyến mãi không hợp lệ',
                    ));
                    die;
                }
            }catch(\Exception $ex) {
                echo json_encode(array(
                    'success' => FALSE,
                    'msg' => $ex->getMessage(),
                ));
                die;
            }
        }
        die('Hack right?');
    }

    public function repotTodayAction(){
        $date_from = date("Y-m-d");
        $today_timestamp =  strtotime('tomorrow - 1 second');
        $date_to = date("Y-m-d H:i:s",$today_timestamp);
        $invoices = $this->getModelTable('InvoiceTable')->getSubTotalInvoiceByDay($date_from, $date_to);
        $products = $this->getModelTable('InvoiceTable')->getProductOnInvoiceByDay($date_from, $date_to);
        $in_data = array();
        foreach ($invoices as $key => $invoice) {
            $time = strtotime($invoice['date_create']);
            $date = date("Y-m-d", $time);
            if(isset($in_data[$date])){
                $in_data[$date]['subtotal'] +=  $invoice['total'];
            }else{
                $in_data[$date] =  array('subtotal' => $invoice['total'], 'day' => $date );
            }
        }
        echo json_encode(array(
            'invoices_by_day' => $in_data,
            'products' => $products,
        ));
        die();
    }

    public function repotYesterdayAction(){
        $yesterday_timestamp =  strtotime('today - 1 day');
        $date_from = date("Y-m-d",$yesterday_timestamp);
        $today_timestamp =  strtotime('today - 1 second');
        $date_to = date("Y-m-d H:i:s",$today_timestamp);
        $invoices = $this->getModelTable('InvoiceTable')->getSubTotalInvoiceByDay($date_from, $date_to);
        $products = $this->getModelTable('InvoiceTable')->getProductOnInvoiceByDay($date_from, $date_to);
        $in_data = array();
        foreach ($invoices as $key => $invoice) {
            $time = strtotime($invoice['date_create']);
            $date = date("Y-m-d", $time);
            if(isset($in_data[$date])){
                $in_data[$date]['subtotal'] +=  $invoice['total'];
            }else{
                $in_data[$date] =  array('subtotal' => $invoice['total'], 'day' => $date );
            }
        }
        echo json_encode(array(
            'invoices_by_day' => $in_data,
            'products' => $products,
        ));
        die();
    }

    public function repotLastSevenDaysAction(){
        $yesterday_timestamp =  strtotime('today - 7 day');
        $date_from = date("Y-m-d",$yesterday_timestamp);
        //$today_timestamp =  strtotime('today - 1 second');
        //$date_to = date("Y-m-d H:i:s",$today_timestamp);
        $date_to = date("Y-m-d H:i:s");
        $invoices = $this->getModelTable('InvoiceTable')->getSubTotalInvoiceByDay($date_from, $date_to);
        $products = $this->getModelTable('InvoiceTable')->getProductOnInvoiceByDay($date_from, $date_to);
        $in_data = array();
        foreach ($invoices as $key => $invoice) {
            $time = strtotime($invoice['date_create']);
            $date = date("Y-m-d", $time);
            if(isset($in_data[$date])){
                $in_data[$date]['subtotal'] +=  $invoice['total'];
            }else{
                $in_data[$date] =  array('subtotal' => $invoice['total'], 'day' => $date );
            }
        }
        echo json_encode(array(
            'invoices_by_day' => $in_data,
            'products' => $products,
        ));
        die();
    }

    public function repotLastThirtyDaysAction(){
        $yesterday_timestamp =  strtotime('today - 30 day');
        $date_from = date("Y-m-d",$yesterday_timestamp);
        //$today_timestamp =  strtotime('today - 1 second');
        //$date_to = date("Y-m-d H:i:s",$today_timestamp);
        $date_to = date("Y-m-d H:i:s");
        $invoices = $this->getModelTable('InvoiceTable')->getSubTotalInvoiceByDay($date_from, $date_to);
        $products = $this->getModelTable('InvoiceTable')->getProductOnInvoiceByDay($date_from, $date_to);
        $in_data = array();
        foreach ($invoices as $key => $invoice) {
            $time = strtotime($invoice['date_create']);
            $date = date("Y-m-d", $time);
            if(isset($in_data[$date])){
                $in_data[$date]['subtotal'] +=  $invoice['total'];
            }else{
                $in_data[$date] =  array('subtotal' => $invoice['total'], 'day' => $date );
            }
        }
        echo json_encode(array(
            'invoices_by_day' => $in_data,
            'products' => $products,
        ));
        die();
    }

    public function repotLastNinetyDaysAction(){
        $yesterday_timestamp =  strtotime('today - 90 day');
        $date_from = date("Y-m-d",$yesterday_timestamp);
        //$today_timestamp =  strtotime('today - 1 second');
        //$date_to = date("Y-m-d H:i:s",$today_timestamp);
        $date_to = date("Y-m-d H:i:s");
        $invoices = $this->getModelTable('InvoiceTable')->getSubTotalInvoiceByDay($date_from, $date_to);
        $products = $this->getModelTable('InvoiceTable')->getProductOnInvoiceByDay($date_from, $date_to);
        $in_data = array();
        foreach ($invoices as $key => $invoice) {
            $time = strtotime($invoice['date_create']);
            $date = date("Y-m-d", $time);
            if(isset($in_data[$date])){
                $in_data[$date]['subtotal'] +=  $invoice['total'];
            }else{
                $in_data[$date] =  array('subtotal' => $invoice['total'], 'day' => $date );
            }
        }
        echo json_encode(array(
            'invoices_by_day' => $in_data,
            'products' => $products,
        ));
        die();
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_delete' => 1
            );
            $this->getModelTable('InvoiceTable')->updateInvoices($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_delete' => 1
                );
                $this->getModelTable('InvoiceTable')->updateInvoices($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/invoice');
    }

    public function pendingAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'delivery' => 'pending'
            );
            $this->getModelTable('InvoiceTable')->updateInvoices($ids, $data);
            $row = array(
                'invoice_id' => $ids,
                'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                'has_feedback' => 0,
                'invoice_status' => 'pending',
                'comment' => '',
            );
            $this->getModelTable('InvoiceTable')->updateLog($row);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'delivery' => 'pending'
                );
                $this->getModelTable('InvoiceTable')->updateInvoices($id, $data);
                $row = array(
                    'invoice_id' => $id,
                    'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                    'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                    'has_feedback' => 0,
                    'invoice_status' => 'pending',
                    'comment' => '',
                );
                $this->getModelTable('InvoiceTable')->updateLog($row);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/invoice');
    }

    public function processingAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'delivery' => 'processing'
            );
            $this->getModelTable('InvoiceTable')->updateInvoices($ids, $data);
            $row = array(
                'invoice_id' => $ids,
                'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                'has_feedback' => 0,
                'invoice_status' => 'processing',
                'comment' => '',
            );
            $this->getModelTable('InvoiceTable')->updateLog($row);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'delivery' => 'processing'
                );
                $this->getModelTable('InvoiceTable')->updateInvoices($id, $data);
                $row = array(
                    'invoice_id' => $id,
                    'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                    'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                    'has_feedback' => 0,
                    'invoice_status' => 'processing',
                    'comment' => '',
                );
                $this->getModelTable('InvoiceTable')->updateLog($row);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/invoice');
    }

    public function deliveredAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'delivery' => 'delivered'
            );
            $this->getModelTable('InvoiceTable')->updateInvoices($ids, $data);
            $row = array(
                'invoice_id' => $ids,
                'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                'has_feedback' => 0,
                'invoice_status' => 'delivered',
                'comment' => '',
            );
            $this->getModelTable('InvoiceTable')->updateLog($row);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'delivery' => 'delivered'
                );
                $this->getModelTable('InvoiceTable')->updateInvoices($id, $data);
                $row = array(
                    'invoice_id' => $id,
                    'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                    'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                    'has_feedback' => 0,
                    'invoice_status' => 'delivered',
                    'comment' => '',
                );
                $this->getModelTable('InvoiceTable')->updateLog($row);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/invoice');
    }

    public function updateAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'delivery' => 'update'
            );
            $this->getModelTable('InvoiceTable')->updateInvoices($ids, $data);
            $row = array(
                'invoice_id' => $ids,
                'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                'has_feedback' => 0,
                'invoice_status' => 'delivered',
                'comment' => '',
            );
            $this->getModelTable('InvoiceTable')->updateLog($row);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'delivery' => 'update'
                );
                $this->getModelTable('InvoiceTable')->updateInvoices($id, $data);
                $row = array(
                    'invoice_id' => $id,
                    'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                    'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                    'has_feedback' => 0,
                    'invoice_status' => 'delivered',
                    'comment' => '',
                );
                $this->getModelTable('InvoiceTable')->updateLog($row);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/invoice');
    }

    public function cancelAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'delivery' => 'cancel'
            );
            $this->getModelTable('InvoiceTable')->updateInvoices($ids, $data);
            $row = array(
                'invoice_id' => $ids,
                'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                'has_feedback' => 0,
                'invoice_status' => 'cancel',
                'comment' => '',
            );
            $this->getModelTable('InvoiceTable')->updateLog($row);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'delivery' => 'cancel'
                );
                $this->getModelTable('InvoiceTable')->updateInvoices($id, $data);
                $row = array(
                    'invoice_id' => $id,
                    'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                    'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                    'has_feedback' => 0,
                    'invoice_status' => 'cancel',
                    'comment' => '',
                );
                $this->getModelTable('InvoiceTable')->updateLog($row);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/invoice');
    }

    public function acceptAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'delivery' => 'accept'
            );
            $this->getModelTable('InvoiceTable')->updateInvoices($ids, $data);
            $row = array(
                'invoice_id' => $ids,
                'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                'has_feedback' => 0,
                'invoice_status' => 'cancel',
                'comment' => '',
            );
            $this->getModelTable('InvoiceTable')->updateLog($row);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'delivery' => 'accept'
                );
                $this->getModelTable('InvoiceTable')->updateInvoices($id, $data);
                $row = array(
                    'invoice_id' => $id,
                    'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                    'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                    'has_feedback' => 0,
                    'invoice_status' => 'cancel',
                    'comment' => '',
                );
                $this->getModelTable('InvoiceTable')->updateLog($row);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/invoice');
    }

    public function finishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'delivery' => 'finish'
            );
            $this->getModelTable('InvoiceTable')->updateInvoices($ids, $data);
            $row = array(
                'invoice_id' => $ids,
                'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                'has_feedback' => 0,
                'invoice_status' => 'cancel',
                'comment' => '',
            );
            $this->getModelTable('InvoiceTable')->updateLog($row);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'delivery' => 'finish'
                );
                $this->getModelTable('InvoiceTable')->updateInvoices($id, $data);
                $row = array(
                    'invoice_id' => $id,
                    'users_id' => $_SESSION['CMSMEMBER']['users_id'],
                    'user_name' => $_SESSION['CMSMEMBER']['user_name'],
                    'has_feedback' => 0,
                    'invoice_status' => 'cancel',
                    'comment' => '',
                );
                $this->getModelTable('InvoiceTable')->updateLog($row);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/invoice');
    }

    public function reportAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id', 0);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);
        $date = $this->params()->fromQuery('date', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        if( !empty($q) ){
            if( $type == 0 ){
                $params['merchant_name'] = $q;
            }else if( $type == 1 ){
                $params['address'] = $q;
            }else if( $type == 2 ){
                $params['merchant_phone'] = $q;
            }else if( $type == 3 ){
                $params['merchant_fax'] = $q;
            }else if( $type == 4 ){
                $params['merchant_email'] = $q;
            }
        }

        $total = $this->getModelTable('InvoiceTable')->countReport($params);
        $invoices = $this->getModelTable('InvoiceTable')->getReport( $params );

        $total_revenue = $this->getModelTable('InvoiceTable')->getTotalRevenue($params);
        $total_cost = $this->getModelTable('InvoiceTable')->getTotalCost($params);
        $total_commission = $this->getModelTable('InvoiceTable')->getTotalCommission($params);

        $link = '/cms/invoice/report?page=(:num)';
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['id'] = $id;
        $this->data_view['total'] = $total;
        $this->data_view['invoices'] = $invoices;
        $this->data_view['q'] = $q;
        $this->data_view['total_revenue'] = $total_revenue;
        $this->data_view['total_cost'] = $total_cost;
        $this->data_view['total_commission'] = $total_commission;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

}