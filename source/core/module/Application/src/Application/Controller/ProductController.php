<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model;
use Zend\Filter\File\LowerCase;
use Zend\View\Model\JsonModel;
use Application\Model\Comments;
use Application\Model\Fqa;
use Application\Model\TraGop;
use Application\Model\RegisterMailProduct;
use Application\Model\EmailNewLetter;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use ApacheSolrphp\Service;
use ApacheSolrphp\Document;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class ProductController extends FrontEndController
{
    public function indexAction()
    {
        return $this->data_view;
    }

    public function detailAction()
    {
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
		
        $id = $this->params()->fromRoute('id', '');
		$alias = $this->params()->fromRoute('alias', '');
        if ( empty($id) 
            && empty($alias) ) {
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }

        $nAlias = $alias;
        if( !empty($id) ){
            $nAlias .= '-'.$id;
        }

        try {
			$product = $this->getModelTable('ProductsTable')->getRowAlias($nAlias);

			if( empty($product)
                && !empty($id) ){
				$product = $this->getModelTable('ProductsTable')->getRowAlias($id);
			}
            
            if ( empty($product) ) {
                return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }
        if ($product) {
            $reviews = $this->getModelTable('ProductsTable')->getReview($product->products_id);
            $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
			if(!empty($product->seo_title) && $product->seo_title!=""){
			$renderer->headTitle($product->seo_title);
			}else{
            $renderer->headTitle($product->products_title);
			}
            $helperProduct = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
            $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
            $images = $this->getServiceLocator()->get('viewhelpermanager')->get('Images');
            $currency = $this->getServiceLocator()->get('viewhelpermanager')->get('Currency');

            $linkProduct = $helperProduct->getProductsUrl($product);
            if (isset($product->seo_keywords)) {
                $renderer->headMeta()->appendName('keywords', $product->seo_keywords);
            }
            $description_top="";
            if (isset($product->seo_description)) {
                $description_top=$helper->replace_string(strip_tags($product->seo_description));
            }

            $decimals = $_SESSION['website']['website_currency_decimals'];
            $decimalpoint = $_SESSION['website']['website_currency_decimalpoint'];
            $separator = $_SESSION['website']['website_currency_separator'];
            $price_sale = number_format($product->price_sale,$decimals,$decimalpoint,$separator);
            //Twitter Card data
            $renderer->headMeta()->appendName('description',$description_top);
            $renderer->headMeta()->appendName('twitter:card',  'product');
            $renderer->headMeta()->appendName('twitter:site',  $this->domain);
            $renderer->headMeta()->appendName('twitter:title',  $product->products_title);
            $renderer->headMeta()->appendName('twitter:description',$description_top);
            $renderer->headMeta()->appendName('twitter:creator',  $this->domain);
            $renderer->headMeta()->appendName('twitter:image', $images->getUrlImage($product->thumb_image));
            $renderer->headMeta()->appendName('twitter:data1',  $price_sale);
            $renderer->headMeta()->appendName('twitter:label1',  'Price');
            $renderer->headMeta()->appendName('twitter:data2',  $_SESSION['website']['website_currency']);
            $renderer->headMeta()->appendName('twitter:label2',  'currency');
            //Open Graph data og:description product: og:site_name
            $renderer->headMeta()->appendProperty('og:title',  $product->products_title);
            $renderer->headMeta()->appendProperty('og:site_name',  $this->domain);
            $renderer->headMeta()->appendProperty('product:price:amount',  $price_sale);
            $renderer->headMeta()->appendProperty('product:price:currency', $_SESSION['website']['website_currency']);
            $renderer->headMeta()->appendProperty('og:description',  $description_top);
			// Bỏ domain gốc ra
            $renderer->headMeta()->appendProperty('og:image', $images->getUrlImage($product->thumb_image));
            $renderer->headMeta()->appendProperty('og:type', "product");
            $renderer->headMeta()->appendProperty('og:url', $linkProduct);
            if (isset($product->seo_keywords)) {
                $listkeyword=explode(",", $product->seo_keywords);
                if(count($listkeyword)>0){
                    for($i=0;$i<count($listkeyword);$i++){
                        $renderer->headMeta()->appendName('article:tag', trim($listkeyword[$i]));
                    }
                }
            }

            $extensions = $this->getModelTable('ProductsTable')->getExtensionsProduct($product->products_id);
            $products_type = $this->getModelTable('ProductsTable')->getTypeProduct($product->products_id);
            $tags = $this->getModelTable('ProductsTable')->getTagsProduct($product->products_id);
            
            $this->addLinkPageInfo( $linkProduct );

            $this->data_view['linkProduct'] = $linkProduct;
            $this->data_view['product'] = $product;
            $this->data_view['extensions'] = $extensions;
            $this->data_view['products_type'] = $products_type;
            $this->data_view['types'] = $products_type;
            $this->data_view['tags'] = $tags;

            $this->addLinkPageInfo( $linkProduct );
            $is_pjax = $this->params()->fromHeader('X-PJAX', '');
            if( !empty($is_pjax) ){
                $_pjax = $this->params()->fromQuery('_pjax', '');
                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                $viewModel->setTemplate("application/product/detail");
                $viewModel->setVariables($this->data_view);
                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);
                $html = "<html>
                            <head>
                                <title>{$product->products_title}</title>
                                <meta name=\"description\" content=\"{$description_top}\" />
                                <meta name=\"keywords\" content=\"$product->seo_keywords\" />
                            </head>
                            <body>
                               {$html}
                            </body>";
                echo $html;
                die();
            }

            return $this->data_view;
        }
        return array();
    }

    public function quickviewAction()
    {
        $html = '';
        $id = $this->params()->fromRoute('id', null);
        $ajax = $this->params()->fromQuery('ajax', 0);
        try {
            if ($id) {
                $product = $this->getModelTable('ProductsTable')->getRow($id);
                if ($product) {
                    $linkProduct = FOLDERWEB . "/{$product['products_alias']}-{$id}";
                    $this->data_view['linkProduct'] = $linkProduct;
                    $this->data_view['product'] = $product;
                    $this->data_view['path_root'] = $this->baseUrl.$this->getUrlPrefixLang();
                    $viewModel = new ViewModel();
                    $viewModel->setTerminal(true);
                    $viewModel->setVariables($this->data_view);
                    $viewModel->setTemplate("application/product/quickview");
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($viewModel);
                    if(!empty($ajax)){
                        echo json_encode(array(
                            'type' => 'quickviewProduct',
                            'flag' => TRUE,
                            'html' => $html,
                            'data' => $product,
                        ));
                        die;
                    }
                }
            }
        } catch (\Exception $ex) {}
        echo $html;
        die();
    }

    public function buyByEmailAction()
    {
        $html = '';
        $products_id = $this->params()->fromRoute('id', null);
        $exts = $this->params()->fromQuery('extention', 0);
        $product_type = $this->params()->fromQuery('product_type', 0);
        $quantity = $this->params()->fromQuery('quantity', 1);

        $ajax = $this->params()->fromQuery('ajax', 0);
        try {
            if ($products_id) {
                if( empty($product_type) ){
                    $product = $this->getModelTable('ProductsTable')->getRow($products_id);
                }else{
                    $product = $this->getModelTable('ProductsTable')->getProductsWithType($products_id, $product_type);
                }
                $transportations = $this->getModelTable('UserTable')->loadTransportations();
                if ($product) {
                    $linkProduct = FOLDERWEB . "/{$product['products_alias']}-{$products_id}";
                    $this->data_view['linkProduct'] = $linkProduct;
                    $this->data_view['product'] = $product;
                    $this->data_view['path_root'] = $this->baseUrl.$this->getUrlPrefixLang();
                    $this->data_view['transportations'] = $transportations;
                    $this->data_view['quantity'] = $quantity;
                    $this->data_view['product_type'] = $product_type;
                    $this->data_view['exts'] = $exts;
                    $viewModel = new ViewModel();
                    $viewModel->setTerminal(true);
                    $viewModel->setVariables($this->data_view);
                    $viewModel->setTemplate("application/product/buy-by-email");
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($viewModel);
                    if(!empty($ajax)){
                        echo json_encode(array(
                            'type' => 'buyByEmail',
                            'flag' => TRUE,
                            'html' => $html,
                            'data' => $product
                        ));
                        die;
                    }
                }
            }
        } catch (\Exception $ex) {}
        echo $html;
        die;
    }

    public function popWholesaleAction()
    {
        $html = '';
        $products_id = $this->params()->fromRoute('id', null);
        $exts = $this->params()->fromQuery('extention', 0);
        $product_type = $this->params()->fromQuery('product_type', 0);
        $quantity = $this->params()->fromQuery('quantity', 1);

        $ajax = $this->params()->fromQuery('ajax', 0);
        try {
            if ($products_id) {
                if( empty($product_type) ){
                    $product = $this->getModelTable('ProductsTable')->getRow($products_id);
                }else{
                    $product = $this->getModelTable('ProductsTable')->getProductsWithType($products_id, $product_type);
                }
                $transportations = $this->getModelTable('UserTable')->loadTransportations();
                if ($product) {
                    $linkProduct = FOLDERWEB . "/{$product['products_alias']}-{$products_id}";
                    $this->data_view['linkProduct'] = $linkProduct;
                    $this->data_view['product'] = $product;
                    $this->data_view['path_root'] = $this->baseUrl.$this->getUrlPrefixLang();
                    $this->data_view['transportations'] = $transportations;
                    $this->data_view['quantity'] = $quantity;
                    $this->data_view['product_type'] = $product_type;
                    $this->data_view['exts'] = $exts;
                    $viewModel = new ViewModel();
                    $viewModel->setTerminal(true);
                    $viewModel->setVariables($this->data_view);
                    $viewModel->setTemplate("application/product/pop-wholesale");
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($viewModel);
                    if(!empty($ajax)){
                        echo json_encode(array(
                            'type' => 'popWholesale',
                            'flag' => TRUE,
                            'html' => $html,
                            'data' => $product
                        ));
                        die;
                    }
                }
            }
        } catch (\Exception $ex) {}
        echo $html;
        die;
    }

    public function heartAction()
    {
        $id = $this->params()->fromRoute('id', null);
        try {
            if ($id) {
                $product = $this->getModelTable('ProductsTable')->getRow($id);
                if ($product) {
                    $linkProduct = FOLDERWEB . "/{$product['products_alias']}-{$id}";
                    $this->data_view['linkProduct'] = $linkProduct;
                    $this->data_view['product'] = $product;
                    $this->data_view['path_root'] = $this->baseUrl.$this->getUrlPrefixLang();
                    return $this->redirect()->toRoute($this->getUrlRouterLang().'product_front-detail', array('alias'=>$product['products_alias'], 'id' => $id));
                }
            }
        } catch (\Exception $ex) {}
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

    public function likeProductAction()
    {
        $request = $this->getRequest();
        if ($request->isPost() && isset($_SESSION['MEMBER']) && !empty($_SESSION['MEMBER']['users_id'])) {
            $products_id = $request->getPost('pid');
            $users_id = $_SESSION['MEMBER']['users_id'];
            $type = $request->getPost('type');
            if ($type == 1) { //dưới chưa like giờ like
                $this->getModelTable('ProductsTable')->likeProduct($users_id, $products_id);
            } else {
                $this->getModelTable('ProductsTable')->unlikeProduct($users_id, $products_id);
            }
            echo json_encode(array(
                'success' => TRUE
            ));
            die();
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

    public function popupRegisterEmailAction()
    {
        $request = $this->getRequest();
        $translator = $this->getServiceLocator()->get('translator');
        $result = new ViewModel();
        $result->setTerminal(true);
        $messager = '';
        if($request->isPost()){
            $products_id = $request->getPost('products_id');
            if (!$products_id) {
                $messager = $translator->translate('txt_phuong_thuc_khong_ho_tro');
                $result->setVariables(array(
                    'messager' => $messager,
                ));
            }else{
                try {
                    $product = $this->getModelTable('ProductsTable')->getRow($products_id);
                    if (!empty($product)) {
                        $transportations = $this->getModelTable('UserTable')->loadTransportations();
                        $cities = $this->getModelTable('UserTable')->loadCities();
                        $result->setVariables(array(
                            'transportations' => $transportations,
                            'cities' => $cities,
                            'product' => $product,
                            'messager' => $messager,
                            'products_id' => $products_id,
                        ));
                        
                    }else{
                        $messager = $translator->translate('txt_product_khong_tim_thay');
                        $result->setVariables(array(
                            'messager' => $messager,
                        ));
                    }
                } catch (\Exception $ex) {
                    $messager = $ex->getMessage();
                    $result->setVariables(array(
                        'messager' => $messager,
                    ));
                }
            }
        }
        return $result;

    }

    public function remarketingAction()
    {
        //$this->layout('app/ajax');
        //$viewModel = new ViewModel();
        //$viewModel->setTemplate('app/ajax');
        $newList = array();
        $catIds = $this->params()->fromQuery('catCookie', null);
        if ($catIds) {
            if (!is_array($catIds)) {
                $catIds = explode(',', $catIds);
            }
            $rows = $this->getModelTable('ProductsTable')->getProductCateRemarketing($catIds, array('page_size' => 20, 'page' => 0));
            $newList = array();
            if ($rows) {
                foreach ($rows as $row) {
                    $newList[$row['categories_id']][] = $row;
                }
            }
            //return array('rows'=>$newList);
        }

        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'rows' => $newList
        ));

        return $result;
    }

    public function dealHotAction()
    {
        $newList = array();
        $catIds = $this->params()->fromQuery('catCookie', null);
        if ($catIds) {
            /*if(!is_array($catIds)){
                $catIds = explode(',', $catIds);
            }*/
            $where[] = "categories_id IN ({$catIds})";
            $rows = $this->getModelTable('ProductsTable')->getHotDeal($where);
            echo json_encode(array(
                'success' => TRUE,
                'results' => $rows->toArray(),
            ));
        }
        die;
    }
	
	public function traGopAction()
    {
		
		$id = $this->params()->fromRoute('id', null);
        if (!$id) {
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }
        try {
            $product = $this->getModelTable('ProductsTable')->getRow($id);
            if (!$product) {
                return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
            }
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
                'action' => 'index'
            ));
        }

        if ($product) {			
            $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
            $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($product->products_title);
            if (isset($product->seo_keywords)) {
                $renderer->headMeta()->appendName('keyword', $product->seo_keywords);
            }
            if (isset($product->seo_description)) {
                $renderer->headMeta()->appendName('description', $product->seo_description);
            }
            $linkProduct = FOLDERWEB . "/san-pham/{$product['products_alias']}-{$id}";
            $cateId = $product['categories_id'];
			$banks = $this->getModelTable('BanksTable')->getAllBanks();
			$banks = $banks->toArray();
			$banksSort = $this->getModelTable('BanksTable')->getAllRateSort();
			$listCity = $this->getModelTable('CitiesTable')->getAll();

            $this->data_view['linkProduct'] = $linkProduct;
            $this->data_view['product'] = $product;
            $this->data_view['banks'] = $banks;
            $this->data_view['banksSort'] = $banksSort;
            $this->data_view['listCity'] = $listCity;
            return $this->data_view;
        }else{
			return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array(
                'action' => 'index'
            ));
		}
    }
	
	public function dealsAction()
    {
		$id = $this->params()->fromRoute('id', NULL);
		if(!$id){
			$id = array();
		}
		try{
			$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
			$renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
			$translator = $this->getServiceLocator()->get('translator');
			$renderer->headTitle($translator->translate('deals_title'));
			$renderer->headMeta()->appendName('keyword', $translator->translate('deals_keyword'));
			$renderer->headMeta()->appendName('description', $translator->translate('deals_description'));
			
			if(!is_array($id)){
				$cat = $this->getModelTable('CategoriesTable')->getMyCategories($id);
				$current = $id;
				$id = array($id);
			}else{
				$current = 'all';
			}
			$products = $this->getModelTable('CategoriesTable')->getAllGoldTimerCurrent($id,1, 4);
			$cats = $this->getModelTable('CategoriesTable')->getAllCategoriesHasGoldTimer();
            $deals_data = array();
            foreach($products as $p){
                $deals_data[$p['products_id']] = $p;
            }

            $_SESSION['products_deals'] = $deals_data;
            $this->data_view['products'] = $products;
            $this->data_view['cats'] = $cats;
            $this->data_view['current'] = $current;
            return $this->data_view;
		}catch(\Exception $ex){
			return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
		}
    }
	
	public function dealsMoreAction(){
		$request = $this->getRequest();
		if($request->isPost()){
			$intPageSize = 4;
			$intPage = $request->getPost('page');
			$cats = $request->getPost('cat');
			try{
				$products = $this->getModelTable('CategoriesTable')->getAllGoldTimerCurrent($cats,$intPage, $intPageSize);
				if(!count($products)){
					echo json_encode(array(
						'success' => FALSE,
					));
					die;
				}
				$viewModel = new ViewModel();
				$viewModel->setTerminal(true);
				$viewModel->setTemplate("application/product/product-list-deal");
				$viewModel->setVariables(array(
					'products' => $products
				));

				$viewRender = $this->getServiceLocator()->get('ViewRenderer');
				$html = $viewRender->render($viewModel);
				echo json_encode(array(
					'success' => TRUE,
					'html' => $html,
				));
				die;
			}catch(\Exception $ex){
				echo json_encode(array(
					'success' => FALSE,
					'msg' => $ex->getMessage(),
				));
				die;
			}
			
		}
		echo json_encode(array(
			'success' => FALSE,
			'msg' => 'Restrict access',
		));
		die;
	}

    public function compareAction()
    {
        $newList = array();
        $productIds = $this->params()->fromPost('id', null);
        if ($productIds) {
            if (!is_array($productIds)) {
                $productIds = explode(',', $productIds);
            }
            $newList = $this->getModelTable('ProductsTable')->getRow($productIds);
        }
        if (COUNT($newList) >= 2) {
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            $viewModel->setTemplate("application/product/compare");
            $viewModel->setVariables(array(
                'rows' => $newList
            ));

            $viewRender = $this->getServiceLocator()->get('ViewRenderer');
            $html = $viewRender->render($viewModel);

            $result = new JsonModel(array(
                'html' => $html,
                'flag' => true,
            ));
        } else {
            $result = new JsonModel(array(
                'html' => $productIds,
                'flag' => false,
            ));
        }
        return $result;
    }

    public function popupCommentsAction()
    {
        $id = $this->params()->fromPost('id', null);
        if (isset($_SESSION['MEMBER']) && !empty($_SESSION['MEMBER']['users_id']) && !empty($id)) {
            $product = $this->getModelTable('ProductsTable')->getRow($id);
            if (COUNT($product) >= 2) {
                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                $viewModel->setTemplate("application/product/popup-comments");
                $viewModel->setVariables(array(
                    'rows' => $product
                ));

                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);

                $result = new JsonModel(array(
                    'html' => $html,
                    'flag' => true,
                ));
            } else {
                $result = new JsonModel(array(
                    'html' => 'Có lỗi xảy ra ,bạn vui lòng thử lại',
                    'flag' => false,
                ));
            }
        } else {
            $result = new JsonModel(array(
                'html' => 'Bạn chưa đăng nhập',
                'flag' => false,
            ));
        }
        return $result;
    }

    public function postFqaAction()
    {
        $translator = $this->getServiceLocator()->get('translator');
        $id = $this->params()->fromPost('id', null);
        $noidung = $this->params()->fromPost('noidung', null);
        $email = $this->params()->fromPost('email', null);
        $title = $this->params()->fromPost('title', null);
        $id_parent = $this->params()->fromPost('idparent', 0);
        if (!empty($id) && !empty($noidung) && !empty($title)) {
            $date = date("y/m/d G.i:s", time());
            $itemFqa = new Fqa();
            $itemFqa->id_parent = $id_parent;
            $itemFqa->products_id = $id;
			if(isset($_SESSION['MEMBER']) && !empty($_SESSION['MEMBER']['users_id'])){
				$itemFqa->email = '';
				$itemFqa->users_id = $_SESSION['MEMBER']['users_id'];
			}else{
				$itemFqa->email = $email;
				$itemFqa->users_id = 0;
			}
            $itemFqa->tieu_de = $title;
            $itemFqa->noi_dung = $noidung;
            $itemFqa->date_crerate = $date;
            $itemFqa->is_published = 0;
			
            $lastID = $this->getModelTable('FqaTable')->insertFqa($itemFqa);
			
            if (!empty($lastID)) {
                $result = new JsonModel(array(
                    'msg' => $translator->translate('txt_cam_on_da_dat_cau_hoi'),
                    'flag' => true,
                ));
            } else {
                $result = new JsonModel(array(
                    'msg' => $translator->translate('txt_co_loi_xay_ra'),
                    'flag' => false,
                ));
            }
        } else {
            $result = new JsonModel(array(
                'msg' => $translator->translate('txt_co_loi_xay_ra'),
                'flag' => false,
            ));
        }
        return $result;
    }	
	
	public function postCommentsAction()
    {
        $id = $this->params()->fromPost('id', null);
        $noidung = $this->params()->fromPost('noidung', null);
        $numberrating = $this->params()->fromPost('numberrating', null);
        if (isset($_SESSION['MEMBER']) && !empty($_SESSION['MEMBER']['users_id'])
            && !empty($id) && !empty($noidung) && !empty($numberrating)
        ) {
            $date = date("y/m/d G.i:s", time());
            $itemCom = new Comments();
            $itemCom->content = $noidung;
            $itemCom->member = $_SESSION['MEMBER']['users_id'];
            $itemCom->product = $id;
            $itemCom->rating = $numberrating;
            $itemCom->parent = 0;
            $itemCom->type = 0;
            $itemCom->number = 0;
            $itemCom->date_create = $date;
            $itemCom->status = 0;
            $lastID = $this->getModelTable('CommentsTable')->insertComments($itemCom);
            if (!empty($lastID)) {
                $result = new JsonModel(array(
                    'html' => 'Cám ơn bạn đã bình luận nội dung này',
                    'flag' => true,
                ));
            } else {
                $result = new JsonModel(array(
                    'html' => 'Có lỗi xảy ra ,bạn vui lòng thử lại',
                    'flag' => false,
                ));
            }
        } else {
            $result = new JsonModel(array(
                'html' => 'Bạn chưa đăng nhập',
                'flag' => false,
            ));
        }
        return $result;
    }

    public function importdataAction()
    {
        $solr = new Service (hostsearch, portsearch, foldersearch, core);
        if (!$solr->ping()) {
            echo 'Solr service not responding.';
            exit ();
        }
        $docs1 = array();
        $listproduct = $this->getModelTable('ProductsTable')->getProductAddSearch(array('is_published' => 1, 'is_delete' => 0, 'convert_search' => 0));

        $i = 0;
        if (count($listproduct) > 0) {
            foreach ($listproduct as $rowlisting) {
                $docs1[] = array('id' => $rowlisting ["products_id"], 'keywords' => $rowlisting ["products_title"], 'keywords1' => $rowlisting ["products_title"], 'title' => $rowlisting ["products_title"], 'category' => $rowlisting ["products_title"]);
                //$this->getModelTable('ProductsTable')->updateAddsearch($rowlisting ["products_id"]);
                $i++;
            }
        }
        $docs = $docs1;
        $documents = array();
        foreach ($docs as $item => $fields) {
            $part = new Document();
            foreach ($fields as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $data) {
                        $part->setMultiValue($key, $data);
                    }
                } else {
                    $part->$key = $value;
                }
            }
            $documents[] = $part;
        }
        try {
            $solr->addDocuments($documents);
            $solr->commit();
            $solr->optimize();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        var_dump($solr);
        echo "thanh cong " . $i;
        die();
    }

    public function loadProductManuAction()
    {
        $limit = 15;
        //$manuid = $this->params()->fromQuery('manuid');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $manuid = $request->getPost('manuid');
            $products = $this->getModelTable('ProductsTable')->getByManus($manuid, $limit);
			$htmlSub = '';
			if(COUNT($products)>0){
				$viewModel = new ViewModel();
				$viewModel->setTerminal(true);
				$viewModel->setTemplate("application/product/list-products");
				$viewModel->setVariables(array(
					'products' => $products,
				));
				$viewRender = $this->getServiceLocator()->get('ViewRenderer');
				$htmlSub_ = $viewRender->render($viewModel);
				$htmlSub = '';
				if(COUNT($products)>5){
					$htmlSub = '<div class="btn-next-sl" >
											<a href="javascript:;" class="btn-next" ></a>
										</div>
										<div class="btn-prev-sl" >
											<a href="javascript:;" class="btn-prev" ></a>
										</div>';
				}
				$htmlSub = $htmlSub.'<div class="ct-product-hot owl-carousel owl-product" > '.$htmlSub_.' </div>';
			}
            echo $htmlSub;
        }
        die;
    }

    public function loadSearchPreviewAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $keyword = $request->getPost('keyword');
            $params['keyword'] = $keyword;
            $products = $this->getModelTable('ProductsTable')->getProductByCustom($params);
            $view = new ViewModel();
            $view->setTerminal(TRUE);
            $view->setVariables(array(
                'products' => $products,
            ));
            return $view;
        }
        die;
    }
	
	public function postEmailAction()
    {
        $name = $this->params()->fromPost('name', null);
        $phone = $this->params()->fromPost('phone', null);
        $id = $this->params()->fromPost('id', null);
        $email = $this->params()->fromPost('email', null);
        if (!empty($name) && !empty($phone) && !empty($id) && !empty($email)) {
            $date = date("y/m/d G.i:s", time());
            $em = new RegisterMailProduct();
            $em->products_id = $id;
            $em->name = $name;
            $em->phone = $phone;
            $em->email = $email;
            $em->date_create = $date;

            $lastID = $this->getModelTable('RegisterMailProductTable')->insertEmail($em);
			
            if (!empty($lastID)) {
                $result = new JsonModel(array(
                    'msg' => 'Cám ơn bạn đã đăng kí với chúng tôi',
                    'flag' => true,
                ));
            } else {
                $result = new JsonModel(array(
                    'msg' => 'Có lỗi xảy ra ,bạn vui lòng thử lại',
                    'flag' => false,
                ));
            }
        } else {
            $result = new JsonModel(array(
                'msg' => 'Có lỗi xảy ra ,bạn vui lòng thử lại',
                'flag' => false,
            ));
        }
        return $result;
    }

    public function validateInputContryPayment($data, $country)
    {
        $err = array();
        $translator = $this->getServiceLocator()->get('translator');
        if($country->country_type == 0){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 1 || $country->country_type == 2){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['state']) ) {
                $err['state'] = $translator->translate('txt_state_khong_duoc_bo_trong');
            }
            if ( empty($data['zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 3){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
            if ( empty($data['suburb']) ) {
                $err['suburb'] = $translator->translate('txt_suburb_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 4){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['state']) ) {
                $err['state'] = $translator->translate('txt_state_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 5){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['region']) ) {
                $err['region'] = $translator->translate('txt_region_khong_duoc_bo_trong');
            }
            if ( empty($data['zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 6){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['province']) ) {
                $err['province'] = $translator->translate('txt_province_khong_duoc_bo_trong');
            }
            if ( empty($data['zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 7){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['cities_id']) ) {
                $err['cities_id'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['districts_id']) ) {
                $err['districts_id'] = $translator->translate('txt_districts_khong_duoc_bo_trong');
            }
            if ( empty($data['wards_id']) ) {
                $err['wards_id'] = $translator->translate('txt_wards_khong_duoc_bo_trong');
            }
        }
        return $err;
    }

    public function wholesaleAction()
    {
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $request = $this->getRequest();
        $items = array('flag' => false, 'msg' => 'Not found');
        if($request->isPost()){
            $product = $request->getPost('product', array());
            $trans = $request->getPost('trans', '');

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

            if ( empty($error) ) {

                $products_id = $product['products_id'];
                $quality = $product['quality'];

                $product = $this->getModelTable('ProductsTable')->getRow($products_id);
                if ( !empty($product) ) {

                    $total = ($productsHelper->getPriceSaleSimple($product)+$product['total_price_extention'])*$quality + (($productsHelper->getPriceSaleSimple($product)+$product['total_price_extention'])*$quality * $product['vat'] / 100);
                    $total_old = ($productsHelper->getPriceSimple($product)+$product['total_price_extention'])*$quality + (($productsHelper->getPriceSimple($product)+$product['total_price_extention'])*$quality* $product['vat'] / 100);
                    
                    $product['price'] = $productsHelper->getPriceSimple($product);
                    $product['price_sale'] = $productsHelper->getPriceSaleSimple($product);
                    $product['price_total'] = $total;
                    $product['price_total_old'] = $total_old;
                    $product['quality'] = $quality;

                    $cart = array('quality' => $quality, 'total' => $total, 'total_old' => $total_old, 'products' => array($product));

                    $member = isset($_SESSION['MEMBER']) ? $_SESSION['MEMBER'] : array();
                    

                    $row = array();
                    $row['website_id']=$_SESSION['website_id'];
                    $row['users_id'] = (!empty($member['users_id'])) ? $member['users_id'] : NULL;
                    $row['first_name'] = $trans['first_name'];
                    $row['last_name'] = $trans['last_name'];
                    $row['full_name'] = $trans['full_name'];
                    $row['phone'] = $trans['phone'];
                    $row['email'] = $trans['email'];
                    $row['country_id'] = $trans['country_id'];
                    $row['address'] = $trans['address'];
                    $row['address01'] = empty($trans['address01']) ? '' : $trans['address01'];
                    $row['city'] = empty($trans['city']) ? '' : $trans['city'];
                    $row['state'] = empty($trans['state']) ? '' : $trans['state'];
                    $row['suburb'] = empty($trans['suburb']) ? '' : $trans['suburb'];
                    $row['region'] = empty($trans['region']) ? '' : $trans['region'];
                    $row['province'] = empty($trans['province']) ? '' : $trans['province'];
                    $row['zipcode'] = empty($trans['zipcode']) ? '' : $trans['zipcode'];
                    $row['cities_id'] = empty($trans['cities_id']) ? 0 : $trans['cities_id'];
                    $row['districts_id'] = empty($trans['districts_id']) ? 0 : $trans['districts_id'];
                    $row['wards_id'] = empty($trans['wards_id']) ? 0 : $trans['wards_id'];
                    $row['transportation_id'] = 0;
                    $row['wholesale_description'] = strip_tags($trans['invoice_description']);
                    $row['is_published'] = 1;
                    $row['is_delete'] = 0;

                    $row['date_create'] = date('Y-m-d H:i:s');
                    $row['date_update'] = date('Y-m-d H:i:s');
                    $row['payment'] = 'unpaid';
                    $row['delivery'] = 'no_delivery';
                    $row['wholesale_title'] = $this->website['website_order_code_prefix'] . strtotime(date("Y-m-d H:i:s")) . $this->website['website_order_code_suffix'];
                    $row['total'] = $total;
                    if($total == 0){
                        $row['payment'] = 'paid';
                    }

                    $viewModel = new ViewModel();
                    $viewModel->setTerminal(true);
                    $viewModel->setTemplate("application/product/content_wholesale");
                    $viewModel->setVariables(array(
                        'cart' => $cart,
                        'member' => $member,
                        'total' => $total,
                        'total_old' => $total_old,
                        'datapayment' => $row,
                    ));
                    $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                    $html = $viewRender->render($viewModel);
                    $row['content'] = htmlentities($html, ENT_QUOTES, 'UTF-8');

                    $wholesale_id = $this->getModelTable('WholesaleTable')->insertWholesale($row, $cart);
                    $wholesale = $this->getModelTable('WholesaleTable')->getOneWholesale($wholesale_id);
                    if(!empty($wholesale)){
                        $viewModel = new ViewModel();
                        $viewModel->setTerminal(true);
                        $viewModel->setTemplate("application/product/email_wholesale_success");
                        $viewModel->setVariables(array(
                            'wholesale' => $wholesale,
                            'cart' => $cart,
                            'member' => $member,
                            'total' => $total,
                            'total_old' => $total_old
                        ));
						$hoadoninvoice="HD".time();
                        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                        $html = $viewRender->render($viewModel);
                        $html = new MimePart($html);
                        $html->type = "text/html";
                        $body = new MimeMessage();
                        $body->setParts(array($html));
                        $message = new Message();
                        $message->addTo($wholesale->email)
							->addBcc($this->website['website_email_customer'], 'Admin Shop - Đặt hàng từ website')
							->addReplyTo($this->website['website_email_customer'], $this->website['website_name'])
                            ->addFrom(EMAIL_ADMIN_SEND, $this->website['website_name'])
                            ->setSubject($hoadoninvoice.' - Xác nhận đơn hàng từ '.$this->website['website_name'])
                            ->setBody($body)
                            ->setEncoding("UTF-8");

                        // Setup SMTP transport using LOGIN authentication
                        $transport = new SmtpTransport();
                        $options = new SmtpOptions(array(
                            'name' => HOST_MAIL,
                            'host' => HOST_MAIL,
                            'port' => 25,
                            'connection_class' => 'login',
                            'connection_config' => array(
                                'username' => USERNAME_HOST_MAIL,
                                'password' => PASSWORD_HOST_MAIL,
                            ),
                        ));

                        $transport->setOptions($options);
                        try {
                            $transport->send($message);
                            $items = array('flag' => true, 'msg' => 'Ban đã mua hàng thành công !');
                        } catch(\Zend\Mail\Exception $e) {
                            //die('aaa :'. $e->getMessage());
                            $items = array('flag' => false, 'msg' => 'oOo ! error. please check again ', 'data'=>$e->getMessage());
                        }catch(\Exception $e) {
                            //die('bbb :'. $ex->getMessage());
                            $items = array('flag' => false, 'msg' => 'oOo ! error. please check again ', 'data'=>$e->getMessage());
                        }
                    }
                }
            }else{
                $items = array('flag' => false, 'msg' => 'oOo ! error. please check again ');
            }
        }
        $result = new JsonModel($items);
        return $result;
    }
	
	public function emailNewLetterAction()
    {
        $email = $this->params()->fromPost('email', null);
        if (!empty($email)) {
            $date = date("y/m/d G.i:s", time());
            $em = new EmailNewLetter();
            $em->email = $email;
            $em->date_create = $date;

            $lastID = $this->getModelTable('EmailNewLetterTable')->insertEmail($em);
			
            if (!empty($lastID)) {
                $result = new JsonModel(array(
                    'html' => 'Cám ơn bạn đã đăng kí với chúng tôi',
                    'flag' => true,
                ));
            } else {
                $result = new JsonModel(array(
                    'html' => 'Có lỗi xảy ra ,bạn vui lòng thử lại',
                    'flag' => false,
                ));
            }
        } else {
            $result = new JsonModel(array(
                'html' => 'Có lỗi xảy ra ,bạn vui lòng thử lại',
                'flag' => false,
            ));
        }
        return $result;
    }
	
	public function dangKiTraGopAction()
    {		
        $id = $this->params()->fromPost('id', null);
        $kt3 = $this->params()->fromPost('slprovince', null);       
        $phone = $this->params()->fromPost('phone', null);
        $total_month = $this->params()->fromPost('month', null);
        $banks_id = $this->params()->fromPost('bank', null);
        $total_pay = $this->params()->fromPost('total_pay', null);
        $first_pay = $this->params()->fromPost('first_pay', null);
        $month_pay = $this->params()->fromPost('month_pay', null);
        if (!empty($id) && !empty($kt3) && !empty($phone) && !empty($total_month) && !empty($banks_id) ) {
            $date = date("y/m/d G.i:s", time());
            $tg = new TraGop();
            $tg->products_id = $id;
            $tg->cities_id = $kt3;
            $tg->total_month = $total_month;
            $tg->banks_id = $banks_id;
            $tg->total_pay = $total_pay;
            $tg->first_pay = $first_pay;
            $tg->month_pay = $month_pay;
			$name = $this->params()->fromPost('name', null);
			if( (!empty($name) && !(isset($_SESSION['MEMBER']) && !empty($_SESSION['MEMBER']['users_id'])))
				|| (empty($name) && isset($_SESSION['MEMBER']) && !empty($_SESSION['MEMBER']['users_id'])) ){
			
				if(isset($_SESSION['MEMBER']) && !empty($_SESSION['MEMBER']['users_id'])){
					$tg->full_name = '';
					$tg->phone = $phone;
					$tg->users_id = $_SESSION['MEMBER']['users_id'];
				}else{
					$tg->full_name = $name;
					$tg->phone = $phone;
					$tg->users_id = NULL;
				}    	
				$tg->date_create = $date;
				$lastID = $this->getModelTable('TraGopTable')->insertTraGop($tg);
				
				if (!empty($lastID)) {
					$result = new JsonModel(array(
						'html' => 'Cám ơn bạn đã đăng kí với chúng tôi',
						'flag' => true,
					));
				} else {
					$result = new JsonModel(array(
						'html' => 'Có lỗi xảy ra ,bạn vui lòng thử lại 2',
						'flag' => false,
					));
				}
			}else {
                $result = new JsonModel(array(
                    'html' => 'Có lỗi xảy ra ,bạn vui lòng thử lại 1',
                    'flag' => false,
                ));
            }
        } else {
            $result = new JsonModel(array(
                'html' => 'Có lỗi xảy ra ,bạn vui lòng thử lại',
                'flag' => false,
            ));
        }
        return $result;
    }

    public function findAction()
    {
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $imagesHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Images');
        $keyword = $this->params()->fromQuery('keyword', '');
        $params = array();
        if( !empty($keyword) ){
            $params['keyword'] = $keyword;
        }
        $rows = $this->getModelTable('ProductsTable')->getProductAll($params);
        $result = array();
        foreach ($rows as $key => $row) {
            $img = $imagesHelper->getUrlImage($productsHelper->getImage($row), 100, 100);
            $name = $productsHelper->getName($row);
            $link = $productsHelper->getLink($row);
            $row['sm_image'] =  $img;
            $row['link'] =  $link;
            $result[] = $row;
        }
        echo json_encode($result);
        die();
    }

	
}
