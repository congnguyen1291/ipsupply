<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Model\User;
use Application\Form\EditProfileForm;
use Application\Form\EditPasswordForm;
use Application\Form\EditMailForm;

use JasonGrimes\Paginator;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;



class ProfileController extends FrontEndController
{

    public function indexAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);
        
		if( !$hPUser->hasLogin() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();
		
		$total_invoice = $this->getModelTable('InvoiceTable')->countInvoiceMember($member['users_id']);
        $total_comments = $this->getModelTable ( 'CommentsTable' )->countCommentOfUser($member['users_id']);
		$total_product = $this->getModelTable ( 'InvoiceTable' )->countProductsOfUser($member['users_id']);
		$point = $this->getModelTable( 'UserTable')->getTotalPoint($member['users_id']);

        $this->data_view['total_invoice'] = $total_invoice;
        $this->data_view['total_comments'] = $total_comments;
        $this->data_view['total_product'] = $total_product;
        $this->data_view['point'] = $point;

        return $this->data_view;
    }
    
    public function editPasswordAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);

        if( !$hPUser->hasLogin() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();
        $request = $this->getRequest ();
        if ($request->isPost ()) {
            $password_old = $request->getPost ('password_old', '');
            $password_new = $request->getPost ('password_new', '');
            $re_password_new = $request->getPost ('re_password_new', '');
            
            if( !empty($password_old) && (md5($password_old) != $_SESSION ['password'])
                && !empty($password_new) && !empty($re_password_new)
                && $password_old != $password_new
                && $password_new == $re_password_new ){
                $this->getModelTable ( 'UserTable' )->editPassword( $password_new, $_SESSION['MEMBER']['users_id'] );
                return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile' );
            }
        }
        return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile', array('action' => 'edit') );
    }
	
	public function editMailAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        if( !$hPUser->hasLogin() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();
    	return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile' );
    }
	
	public function pointAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        if( !$hPUser->hasLogin() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();
        return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile' );
    }
	
	public function historyAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);

        if( !$hPUser->hasLogin() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();

        $page_size = $this->params()->fromQuery('page_size', 24);
        $page = $this->params()->fromQuery('page', 0);
        $ofset = $page*$page_size;

        $total = $this->getModelTable('InvoiceTable')->countInvoiceMember($member['users_id']);
        $invoices = $this->getModelTable ( 'InvoiceTable' )->getInvoiceMember($member['users_id'], $ofset, $page_size);

        $link = '/profile/history?page=(:num)';
        $paginator = new Paginator($total, $page_size, $page, $link);

        $point = $this->getModelTable( 'UserTable')->getTotalPoint($member['users_id']);

        $this->data_view['invoices'] = $invoices;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['page_size'] = $page_size;
        $this->data_view['total'] = $total;
        $this->data_view['point'] = $point;

        return $this->data_view;
    }

    public function invoiceAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);

        if( !$hPUser->hasLogin() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();

        $id = $this->params()->fromRoute('id', 0);
        die('');

        return $this->data_view;
    }

    public function recoverAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        if( !$hPUser->hasLogin() ){
            $translator = $this->getServiceLocator()->get('translator');
            $request = $this->getRequest ();
            if ($request->isPost ()) {
                $username = $request->getPost ('email', '');
                $user = $this->getModelTable('UserTable')->getUserByUserame($username);
                if( !empty($user) ){
                    $password = $this->generateRandomString(8);
                    $helperWebsites = $this->getServiceLocator()->get('viewhelpermanager')->get('Websites');
                    $title = $translator->translate('txt_thay_doi_mat_khau');
                    $html = $translator->translate('template_change_pasword_success');
                    $html = str_replace('{EMAIL}',  $username,  $html);
                    $html = str_replace('{PASSWORD}',   $password,  $html);
                    $html = str_replace('{FULL_NAME}',  $user->full_name,   $html);
                    $viewModel = new ViewModel();
                    $viewModel->setTerminal(true);
                    $viewModel->setTemplate("app/email/email_template");
                    $viewModel->setVariables(array(
                        'title' => $title,
                        'html' => $html
                    ));

                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($viewModel);
                    $html = new MimePart($html);
                    $html->type = "text/html";

                    $body = new MimeMessage();
                    $body->setParts(array($html));
                    $emailCC = explode(',', $this->website->website_email_customer);
                    if( !empty($emailCC) ){
                        $emailCC = $this->website->website_email_customer;
                    }
                    $message = new Message();
                    $message->addTo($username)
                        ->addFrom($helperWebsites->getEmailSend(), $this->website->website_name)
                        ->addCc($emailCC)
                        ->setSubject($this->website->website_name.' '.$translator->translate('txt_thay_doi_mat_khau'))
                        ->setBody($body)
                        ->setEncoding("UTF-8");

                    // Setup SMTP transport using LOGIN authentication
                    $transport = new SmtpTransport();
                    $options = new SmtpOptions(array(
                        'name' => $helperWebsites->getHostMail(),
                        'host' => $helperWebsites->getHostMail(),
                        'port' => $helperWebsites->getPortMail(),
                        'connection_class' => 'login',
                        'connection_config' => array(
                            'username' => $helperWebsites->getUserNameHostMail(),
                            'password' => $helperWebsites->getPasswordHostMail(),
                        ),
                    ));

                    $transport->setOptions($options);
                    try {
                        $result=$transport->send($message);
                        $flag = $this->getModelTable('UserTable')->editPassword($password, $user->users_id);
                    } catch(\Zend\Mail\Exception $e) {
                    }catch(\Exception $ex) {
                    }
                }
            }
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
    }

    public function chartInvoiceAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $member = $hPUser->getMember();
        $from = $this->params()->fromQuery('from', '');
        if( !empty($member) && !empty($from) ){
            $date_from = $from;
            $date_to = date("Y-m-d H:i:s");
            $invoices = $this->getModelTable('InvoiceTable')->getSubTotalInvoiceByDayOfMember($member['users_id'], $date_from, $date_to);
            $products = $this->getModelTable('InvoiceTable')->getProductOnInvoiceByDayOfMember($member['users_id'], $date_from, $date_to);
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
        }
        die();
    }
	
	public function paymentAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);

        if( !$hPUser->hasLogin() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();

    	$page_size = $this->params()->fromQuery('page_size', 24);
        $page = $this->params()->fromQuery('page', 0);
        $ofset = $page*$page_size;

        $total = $this->getModelTable ( 'InvoiceTable' )->countProductsOfUser($member['users_id']);
    	$products = $this->getModelTable ( 'InvoiceTable' )->getProductsOfUser($member['users_id'], $ofset, $page_size);

        $link = '/profile/payment?page=(:num)';
        $paginator = new Paginator($total, $page_size, $page, $link);

        $point = $this->getModelTable( 'UserTable')->getTotalPoint($member['users_id']);

        $this->data_view['products'] = $products;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['page_size'] = $page_size;
        $this->data_view['total'] = $total;
        $this->data_view['point'] = $point;

        return $this->data_view;
    }
	
	public function ratingAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        if( !$hPUser->hasLogin() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();
        return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile' );
    }

    public function deassignAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $cartHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $id = $this->params()->fromQuery('id', '');

        if( !$hPUser->hasLogin() || empty($id) ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $assign = $this->getModelTable('AssignTable')->getAssign( array('assign_id' => $id) );
        if( empty($assign) ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'profile', array('action' => 'assign'));
        }
        $row = array(
                'assign_id' => $id,
                'invoice_id' => $assign->invoice_id,
                'users_id' => $hPUser->getUsersId(),
                'merchant_id' => $hPUser->getMerchantId(),
            );
        $this->getModelTable('AssignTable')->read( $row );
        $member = $hPUser->getMember();
        $prices = array();
        $assignPrice = $this->getModelTable('AssignTable')->getAssignProducts( array('assign_id' => $id) );
        foreach ($assignPrice as $key => $prc) {
            $prices[$prc['products_invoice_id']] = $prc;
        }

        $cart = $this->getModelTable('InvoiceTable')->getProductsCart($assign->invoice_id);
        $dataExtension = $this->getModelTable('InvoiceTable')->getInvoiceExtensions($assign->invoice_id);
        $shipping = $this->getModelTable('InvoiceTable')->getShipping($assign->invoice_id);
        $coupon = $this->getModelTable('InvoiceTable')->getCouponByInvoice($assign->invoice_id);

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

        $calculate = $cartHelper->sumSubTotalPriceFromArray($dataCart);
        $total = $calculate['price_total'];
        $total_old = $calculate['price_total_old'];
        $total_orig = $calculate['price_total_orig'];
        $total_tax = $calculate['price_total_tax'];
        $total_old_tax = $calculate['price_total_old_tax'];
        $total_orig_tax = $calculate['price_total_orig_tax'];

        //$products = $this->getModelTable( 'AssignTable' )->getProductsOfAssign( $assign->assign_id );
        $point = $this->getModelTable( 'UserTable')->getTotalPoint($member['users_id']);
        if( empty($assign->is_read) ){
            $row = array(
                    'assign_id' => $assign->assign_id,
                    'invoice_id' => $assign->invoice_id,
                    'users_id' => $assign->users_id
                );
            $this->getModelTable('AssignTable')->read( $row );
        }

        $this->data_view['assign'] = $assign;
        $this->data_view['products'] = $dataCart;
        $this->data_view['point'] = $point;
        $this->data_view['total'] = $total;
        $this->data_view['total_old'] = $total_old;
        $this->data_view['total_orig'] = $total_orig;
        $this->data_view['total_tax'] = $total_tax;
        $this->data_view['total_old_tax'] = $total_old_tax;
        $this->data_view['total_orig_tax'] = $total_orig_tax;
        $this->data_view['fee'] = $fee;
        $this->data_view['shipping'] = $shipping;
        $this->data_view['is_free'] = $is_free;
        return $this->data_view;
    }

    public function deinvoiceAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $id = $this->params()->fromQuery('id', '');

        if( !$hPUser->hasLogin() || empty($id) ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $invoice = $this->getModelTable('InvoiceTable')->getInvoice($id);
        if( empty($invoice) ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'profile', array('action' => 'history'));
        }
        $member = $hPUser->getMember();
        $point = $this->getModelTable( 'UserTable')->getTotalPoint($member['users_id']);
        $this->data_view['invoice'] = $invoice;
        $this->data_view['point'] = $point;
    	return $this->data_view;
    }
    
    public function editAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);
        
        if( !$hPUser->hasLogin() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();

        $point = $this->getModelTable( 'UserTable')->getTotalPoint($member['users_id']);

    	$request = $this->getRequest ();
    	if ($request->isPost ()) {
            $first_name = $request->getPost ('first_name', '');
            $last_name = $request->getPost ('last_name', '');
            $full_name = $request->getPost ('full_name', '');
            $phone = $request->getPost ('phone', '');
            $birthday = $request->getPost ('birthday', '');
            $address = $request->getPost ('address', '');
            $country_id = $request->getPost ('country_id', '');
            $address01 = $request->getPost ('address01', '');
            $address01 = $request->getPost ('address01', '');
            $city = $request->getPost ('city', '');
            $state = $request->getPost ('state', '');
            $suburb = $request->getPost ('suburb', '');
            $region = $request->getPost ('region', '');
            $province = $request->getPost ('province', '');
            $zipcode = $request->getPost ('zipcode', '');
            $cities_id = $request->getPost ('cities_id', '');
            $districts_id = $request->getPost ('districts_id', '');
            $wards_id = $request->getPost ('wards_id', '');

            $longitude = $request->getPost ('longitude', 0);
    		$latitude = $request->getPost ('latitude', 0);
            
            if( empty($full_name) 
                && !empty($first_name)
                && !empty($last_name) ){
                $full_name = $first_name.' '.$last_name;
            }

    		if( !empty($first_name) && !empty($last_name)
                && !empty($phone) && !empty($address) ){
                $row = array(   'first_name' => $first_name,
                                'last_name' => $last_name,
                                'full_name' => $full_name,
                                'phone' => $phone,
                                'birthday' => $birthday,
                                'country_id' => $country_id,
                                'address' => $address,
                                'address01' => $address01,
                                'city' => $city,
                                'state' => $state,
                                'suburb' => $suburb,
                                'region' => $region,
                                'province' => $province,
                                'zipcode' => $zipcode,
                                'cities_id' => $cities_id,
                                'districts_id' => $districts_id,
                                'wards_id' => $wards_id,
                                'longitude' => $longitude,
                                'latitude' => $latitude );
				$this->getModelTable ( 'UserTable' )->editUserByArray ( $row, $_SESSION['MEMBER']['users_id'] );
				return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile' );
    		}
    	}
        $this->data_view['point'] = $point;
        return $this->data_view;
    }
    
    public function commentsAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);
        
        if( !$hPUser->hasLogin() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();

        $page_size = $this->params()->fromQuery('page_size', 24);
        $page = $this->params()->fromQuery('page', 0);
        $ofset = $page*$page_size;

        $total = $this->getModelTable ( 'CommentsTable' )->countCommentOfUser($member['users_id']);
        $comments = $this->getModelTable ( 'CommentsTable' )->getCommentOfUser($member['users_id'], $ofset, $page_size);
        $link = '/profile/comments?page=(:num)';
        $paginator = new Paginator($total, $page_size, $page, $link);

        $point = $this->getModelTable( 'UserTable')->getTotalPoint($member['users_id']);
        
        $this->data_view['comments'] = $comments;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['page_size'] = $page_size;
        $this->data_view['total'] = $total;
        $this->data_view['point'] = $point;

        return $this->data_view;
    }

    public function assignAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);

        if( !$hPUser->isMerchant() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();
        $page_size = $this->params()->fromQuery('page_size', 24);
        $page = $this->params()->fromQuery('page', 0);
        $ofset = $page*$page_size;

        $total = $this->getModelTable('UserTable')->totalAssignMember($member['users_id']);
        $assigns = $this->getModelTable ( 'UserTable' )->getAssignMember($member['users_id'], $ofset, $page_size);
        $link = '/profile/assign?page=(:num)';
        $paginator = new Paginator($total, $page_size, $page, $link);
        $point = $this->getModelTable( 'UserTable')->getTotalPoint($member['users_id']);
        $this->data_view['assigns'] = $assigns;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['page_size'] = $page_size;
        $this->data_view['total'] = $total;
        $this->data_view['point'] = $point;
        return $this->data_view;
    }

    public function industryAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);

        if( !$hPUser->isMerchant() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();
        $from = $this->params()->fromQuery('from', date('2017-01-01'));
        $to = $this->params()->fromQuery('to', date('Y-m-d'));
        $datas = array();
        $invoices = $this->getModelTable('InvoiceTable')->getInvoiceByDayOfMember($member['users_id'], $from, $to);
        $assigns = $this->getModelTable('AssignTable')->getAssignByDayOfMember($member['users_id'], $from, $to);
        $datas = array_merge($invoices, $assigns);
        usort($datas, function($a, $b) {
            return ($a['date_create'] < $b['date_create']) ? -1 : 1;
        });
        $point = $this->getModelTable( 'UserTable')->getTotalPoint($member['users_id']);
        $this->data_view['from'] = $from;
        $this->data_view['to'] = $to;
        $this->data_view['datas'] = $datas;
        $this->data_view['point'] = $point;
        return $this->data_view;
    }

    public function statisticAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);

        if( !$hPUser->isAffiliate() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();
        $point = $this->getModelTable( 'UserTable')->getTotalPoint($member['users_id']);
        $this->data_view['point'] = $point;
        return $this->data_view;
    }

    public function trackingAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);

        if( !$hPUser->isAffiliate() ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'login');
        }
        $member = $hPUser->getMember();
        $point = $this->getModelTable( 'UserTable')->getTotalPoint($member['users_id']);
        $this->data_view['point'] = $point;
        return $this->data_view;
    }

    public function avatarAction(){
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $hPImages = $this->getServiceLocator()->get('viewhelpermanager')->get('Images');
        $translator = $this->getServiceLocator()->get('translator');
        $request = $this->getRequest();
        $items = array('flag' => FALSE,'msg' => $translator->translate('txt_not_found'));
        if ($request->isPost() && $hPUser->hasLogin() ) {
            $website_id = $this->website->website_id;
            $domain= md5($_SESSION['website']['website_domain'].':'.$website_id);
            $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
            if(!is_dir($websiteFolder)){
                @mkdir ( $websiteFolder, 0777 );
            }
            $websiteFolder .= '/user';
            if(!is_dir($websiteFolder)){
                @mkdir ( $websiteFolder, 0777 );
            }
            
            $size = $_FILES['user_file']['size'][0];
            if($size > 20971520)
            {
                @unlink($_FILES['user_file']['tmp_name'][0]);
                $items = array( 'flag' => FALSE ,'msg' => $translator->translate('txt_limit_picture_2m'));
            }else{

                $temp = preg_split ( '/[\/\\\\]+/', $_FILES ['user_file'] ["name"][0] );
                $filename = $temp [count ( $temp ) - 1];
                if (!empty($filename)) {
                    $name = $this->file_name ( $filename );
                    $extention = $this->file_extension ( $filename );
                    $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                    if(is_file($upload_url)){
                        $name = $name.'-'.date("YmdHis");
                        $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                    }
                    if (copy ( $_FILES ['user_file']["tmp_name"][0], $upload_url )) {
                        chmod ( $upload_url, 0777 );
                        $url = '/custom/domain_1/' . $domain . '/user/' . $name.'.'.$extention;
                        $row = array(
                                    'avatar' => $url
                                );
                        $this->getModelTable ( 'UserTable' )->editUserByArray ( $row, $_SESSION['MEMBER']['users_id'] );
                        $items = array('flag' => TRUE, 'url' => $url, 'avatar' => $hPImages->getUrlImage($url, 100, 100), 'msg' => $translator->translate('txt_edit_avatar_thanh_cong') );
                    }
                }else{
                    $items = array('flag' => FALSE,'msg' => $translator->translate('txt_file_not_exit'));
                }
            }
        }
        echo json_encode($items);
        die();
    }

    protected function file_name($file_name) {
        $list = explode ( '.', $file_name );
        $dot = count ( $list );
        unset($list [$dot - 1]);
        return implode('-', $list);
    }
    protected function file_extension($file_name) {
        $list = explode ( '.', $file_name );
        $file_ext = strtolower(end($list));
        return $file_ext;
    }


}

