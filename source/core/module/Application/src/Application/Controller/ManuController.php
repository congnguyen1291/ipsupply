<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

//use Application\lib\Paging;
class ManuController extends FrontEndController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $script->appendFile(FOLDERWEB.'/styles/js/manufacturers.js');

		$id = $this->params()->fromRoute('id', null);
        $page_size = $this->params()->fromQuery('page_size', 20);

        $page = $this->params()->fromQuery('page', 0);
        $filter = $this->params()->fromQuery('filter', 'new');
        $filtermore = $this->params()->fromQuery('fillmore', 'hot');
		try{
			$manu = $this->getModelTable('ManufacturersTable')->getRow($id);
            $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($manu['manufacturers_name']);
            if (isset($manu['manufacturers_name'])) {
                $renderer->headMeta()->appendName('keyword', $manu['manufacturers_name']);
            }
            if (isset($manu['manufacturers_name'])) {
                $renderer->headMeta()->appendName('description', $manu['manufacturers_name']);
            }
            $topMenu = $this->getModelTable('CategoriesTable')->getAllCategoriesMenuTop();
            $params['page'] = $page;
            $params['page_size'] = $page_size;
            $params['filter'] = $filter;
            $fillarray = array('hot', 'most', 'deal');
            if (in_array($filtermore, $fillarray)) {
                $params['fillmore'] = $filtermore;
            } else {
                $filtermore = 'hot';
            }
            $manus = $this->params()->fromQuery('manus', NULL);
            if ($manus) {
                $params['manus'] = $manus;
            } else {
                $manus = array();
            }
            $feature = $this->params()->fromQuery('feature', NULL);
            if ($feature) {
                $params['feature'] = $feature;
            } else {
                $feature = array();
            }
            $area = $this->params()->fromQuery('area', NULL);
            if ($area) {
                $price = explode(';', $area);
                if (count($price) == 2 && is_numeric($price[0]) && is_numeric($price[1])) {
                    $params['price'] = array(
                        'min' => $price[0],
                        'max' => $price[1],
                    );
                    $area = $params['price'];
                }
            } else {
                $area = array();
            }
            $rating = $this->params()->fromQuery('rating', NULL);
            if ($rating && is_numeric($rating) && $rating >= 0 && $rating <= 5) {
                $params['rating'] = $rating;
            }
            $parentCategories = $this->getModelTable('CategoriesTable')->getParentCategories();
            //$list = $this->getMyTable('CategoriesTable')->getAllChildOfCate($id);
            //$list[] = $id;
			$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
            $breadCrumbMap = $helper->getBreadCrumbManu($manu);
            $breadCrumb = $helper->getBreadCrumbHome();
            //$menusLeft = $this->getMyTable('CategoriesTable')->getLeftMenuPageCategory($id);

            //$all_cate = $menusLeft['all_category'];
            //if(count($all_cate) == 0){
            //    $all_cate[] = $id;
            //}
            //$menusLeft = $menusLeft['html'];
            //$manufacturers = $this->getMyTable('ManufacturersTable')->getRows($list);
            $rows = $this->getModelTable('ProductsTable')->getProductsByManu($id, $params);
            $total = $this->getModelTable('ProductsTable')->countProductsByManu($id, $params);
            //$total_categories = $this->getMyTable('CategoriesTable')->countTotalProduct($all_cate);
            $link = '&page_size=' . $page_size . '&filter=' . $filter . '&fillmore=' . $filtermore;
            $link .= isset($params['price']) ? "&area={$params['price']['min']};{$params['price']['max']}" : '';
            $feature = array();
            if (isset($params['feature'])) {
                foreach ($params['feature'] as $key => $f) {
                    foreach ($f as $value) {
                        $link .= "&feature[{$key}]=" . $value;
                        $feature[] = $value;
                    }
                }
            }
            if (isset($params['manus'])) {
                foreach ($params['manus'] as $m) {
                    $link .= "&manus[]=" . $m;
                }
            }
            $objPage = new Paging($total, $page, $page_size, $link);
            $paging = $objPage->getListFooter($link);
            //$leftFilter = $this->getMyTable('CategoriesTable')->getHtmlLeftFilterFeature($list, $feature);
            //$concernProduct = $this->getMyTable('ProductsTable')->getConcernProduct($list);
            //$bannerSlideshows = $this->getMyTable('CategoriesTable')->getBannersSlideshow($list);

            $news = $this->getModelTable('ArticlesTable')->getRows(array(
                'is_published' => 1,
                'is_delete' => 0,
                'is_faq' => 0,
            ), 5);
            $productsBanchay = $this->getModelTable('ProductsTable')->getProductBanchay();
            $productsHots = $this->getModelTable('ProductsTable')->getHotProduct();

            $this->data_view['products'] = $rows;
            $this->data_view['manu'] = $manu;
            $this->data_view['paging'] = $paging;
            $this->data_view['filter'] = $filter;
            $this->data_view['page_size'] = $page_size;
            $this->data_view['id'] = $id;
            $this->data_view['total'] = $total;
            $this->data_view['breadCrumb'] = $breadCrumb;
            $this->data_view['breadCrumbMap'] = $breadCrumbMap;
            $this->data_view['topMenu'] = $topMenu;
            $this->data_view['news'] = $news;
            $this->data_view['productsBanchay'] = $productsBanchay;
            $this->data_view['productsHots'] = $productsHots;

            return $this->data_view;
//            return array(
//                //'bannerSlideshows' => $bannerSlideshows,
//                'products' => $rows,
//                'manu' => $manu,
//                //'area' => $area,
//                'paging' => $paging,
//                //'leftFilter' => $leftFilter,
//                //'manufacturers' => $manufacturers,
//                'filter' => $filter,
//                'page_size' => $page_size,
//                'id' => $id,
//                //'concernProduct' => $concernProduct,
//                //'menusLeft' => $menusLeft,
//                //'path_root' => $this->base,
//                'total' => $total,
//                //'categories' => $categories,
//                'breadCrumb' => $breadCrumb,
//                'breadCrumbMap' => $breadCrumbMap,
//                'parentCategories' => $parentCategories,
//                'topMenu' => $topMenu,
//                'languages' => $this->languages,
//                'current_language' => $this->current_language,
//                //'total_categories' => $total_categories,
//            );
        }catch(\Exception $ex){
		}
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
	/*
        $id = $this->params()->fromRoute('id');
        if(!$id){
            return $this->redirect()->toRoute('home');
        }
        try{
            $manu = $this->getModelTable('ManufacturersTable')->getRow($id);
        }catch (\Exception $ex){
            return $this->redirect()->toRoute('home');
        }
        $filter = $this->params()->fromQuery('filter', 'new');
        try{
            $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
            $breadCrumb = $helper->getBreadCrumbManu($manu);
            $parentCategories = $this->getModelTable('CategoriesTable')->getParentCategories();
            $topMenu = $this->getModelTable('CategoriesTable')->getAllCategoriesMenuTop();
            //$breadCrumb = $this->getModelTable('CategoriesTable')->getBreadCrumbHome();
            $intPageSize = 20;
            $intPage = $this->params()->fromRoute('page', 1);
            $paging = '';
            $products_data = $this->getModelTable('ProductsTable')->getProductsByManu($id, $intPage, $intPageSize);
            $total = $this->getModelTable('ProductsTable')->countProductsByManu($id);
            return array(
                'manu' => $manu,
                'products' => $products_data,
                'topMenu' => $topMenu,
                'paging' => $paging,
                'languages' => $this->languages,
                'current_language' => $this->current_language,
                'parentCategories' => $parentCategories,
                'breadCrumb' => $breadCrumb,
                'page_size' => $intPageSize,
                'filter' => $filter,
                'total' => $total,
            );
        }catch (\Exception $ex){
            return $this->redirect()->toRoute('home');
        }
		*/
    }

    public function showallAction(){

        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $script->appendFile(FOLDERWEB.'/styles/js/all_manufacturers.js');

        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
        $breadCrumbMap = $helper->getBreadCrumbManu();
        $breadCrumb = $helper->getBreadCrumbHome();
        $parentCategories = $this->getModelTable('CategoriesTable')->getParentCategories();
        $topMenu = $this->getModelTable('CategoriesTable')->getAllCategoriesMenuTop();
        //$breadCrumb = $this->getModelTable('CategoriesTable')->getBreadCrumbHome();
        $intPageSize = 5;
        $total = $this->getModelTable('ManufacturersTable')->countAllManu();
        $intPage = $this->params()->fromQuery('page', 1);
        $link = "";
        $objPage = new Paging( $total, $intPage, $intPageSize, $link );
        $paging = $objPage->getListFooter ( $link );
        $manus = $this->getModelTable('ManufacturersTable')->getAllManus($intPage,$intPageSize);
        $manuids = array_map(function($m){return $m['manufacturers_id'];}, $manus);
        $products = $this->getModelTable('ProductsTable')->getProductByManus($manuids);
        $products_data = array();
        foreach($products as $p){
            $products_data[$p['manufacturers_id']][] = $p;
        }
        $news = $this->getModelTable('ArticlesTable')->getRows(array(
            'is_published' => 1,
            'is_delete' => 0,
            'is_faq' => 0,
        ), 5);
        $productsBanchay = $this->getModelTable('ProductsTable')->getProductBanchay();
        $productsHots = $this->getModelTable('ProductsTable')->getHotProduct();

        $this->data_view['breadCrumbMap'] = $breadCrumbMap;
        $this->data_view['products'] = $products_data;
        $this->data_view['topMenu'] = $topMenu;
        $this->data_view['manus'] = $manus;
        $this->data_view['paging'] = $paging;
        $this->data_view['parentCategories'] = $parentCategories;
        $this->data_view['breadCrumb'] = $breadCrumb;
        $this->data_view['news'] = $news;
        $this->data_view['productsBanchay'] = $productsBanchay;
        $this->data_view['productsHots'] = $productsHots;

        return $this->data_view;
//        return array(
//            'breadCrumbMap' => $breadCrumbMap,
//            'products' => $products_data,
//            'topMenu' => $topMenu,
//            'manus' => $manus,
//            'paging' => $paging,
//            'languages' => $this->languages,
//            'current_language' => $this->current_language,
//            'parentCategories' => $parentCategories,
//            'breadCrumb' => $breadCrumb,
//        );
    }
	
	public function listingAction(){
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $script->appendFile(FOLDERWEB.'/styles/js/listing_manu.js');


		$id = $this->params()->fromRoute('id', null);
		$manu = array();
		if(!$id){
			$id = array();
		}else{
			try{
				$manu = $this->getModelTable('ManufacturersTable')->getRow($id);
			}catch(\Exception $ex){
				return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
			}
			$id = array($id);
		}
		try{
			$helperCom = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
			$translator = $this->getServiceLocator()->get('translator');
			$page_size = $this->params()->fromQuery('page_size', 18);
			$page = $this->params()->fromQuery('page', 0);
            $ft = $this->params()->fromQuery('filter', NULL);
			$filter = $ft ? $ft : 'new';
            $fm = $this->params()->fromQuery('fillmore', NULL);
			$filtermore = $fm ? $fm : 'hot';
			$renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
			/*$renderer->headTitle($categories->categories_title);
			if (isset($categories->seo_keywords)) {
				$renderer->headMeta()->appendName('keyword', $categories->seo_keywords);
			}
			if (isset($categories->seo_description)) {
				$renderer->headMeta()->appendName('description', $categories->seo_description);
			}*/
			$this->topMenu = $this->getModelTable('CategoriesTable')->getAllCategoriesMenuTop();
			$params['page'] = $page;
			$params['page_size'] = $page_size;
			$params['filter'] = $filter;
			$idcat = $this->params()->fromRoute('idcat', NULL);
			$catinfo = FALSE;
            $childCat = array();
			if($idcat){
				try{
					$catinfo = $this->getModelTable('CategoriesTable')->getRow($idcat);
					//print_r($catinfo);die();
					$list = $this->getModelTable('CategoriesTable')->getAllChildOfCate($idcat);
                    $childCat = $this->getModelTable('CategoriesTable')->getChildFirstRows($idcat);
					/*if(!count($childCat)){
						$childCat = $this->getModelTable('CategoriesTable')->getChildFirstRows($catinfo['parent_id']);
					}*/
                    $list[] = $idcat;
					$manus_all = $this->getModelTable('CategoriesTable')->getAllManu($list);
					$params['catid'] = $list;
				}catch(\Exception $ex){

                    if(isset($_GET['dev'])) {
                        die($ex->getMessage());
                    }
					return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
				}
			}else{
				$manus_all = $this->getModelTable('ManufacturersTable')->getAllManus(0,100);
			}
			$fillarray = array('hot', 'most', 'deal');
			if (in_array($filtermore, $fillarray)) {
				$params['fillmore'] = $filtermore;
			} else {
				$filtermore = 'hot';
			}
			/*
			$manus = $this->params()->fromQuery('manus', NULL);
			if ($manus) {
				$params['manus'] = $manus;
			} else {
				$manus = array();
			}*/
			$feature = $this->params()->fromQuery('feature', NULL);
			if ($feature) {
				$params['feature'] = $feature;
			} else {
				$feature = array();
			}
			$area = $this->params()->fromQuery('area', NULL);
			if ($area) {
				$price = explode(';', $area);
				if (count($price) == 2 && is_numeric($price[0]) && is_numeric($price[1])) {
					$params['price'] = array(
						'min' => $price[0],
						'max' => $price[1],
					);
					$area = $params['price'];
				}
			} else {
				$area = array();
			}
			$rating = $this->params()->fromQuery('rating', NULL);
			if ($rating && is_numeric($rating) && $rating >= 0 && $rating <= 5) {
				$params['rating'] = $rating;
			}
			$parentCategories = $this->getModelTable('CategoriesTable')->getParentCategories();
			//$list = $this->getMyTable('CategoriesTable')->getAllChildOfCate($id);
			//$list[] = $id;			
			//$menusLeft = $this->getMyTable('CategoriesTable')->getLeftMenuPageCategory($id);

			//$all_cate = $menusLeft['all_category'];
			//if(count($all_cate) == 0){
			//	$all_cate[] = $id;
			//}
			//$menusLeft = $menusLeft['html'];
			//$manufacturers = $this->getMyTable('ManufacturersTable')->getRows($list);
			$rows = $this->getModelTable('ManufacturersTable')->getProductsByManus($id, $params);
			$total = $this->getModelTable('ManufacturersTable')->countProductsByManus($id, $params);
			//$total_categories = $this->getMyTable('CategoriesTable')->countTotalProduct($all_cate);
            $link = '';
            if($ft){
                $link .= '&filter='.$ft;
            }
            if($fm){
                $link .= '&fillmore='.$fm;
            }
			//$link = '&page_size=' . $page_size . '&filter=' . $filter . '&fillmore=' . $filtermore;
			$link .= isset($params['price']) ? "&area={$params['price']['min']};{$params['price']['max']}" : '';
			$feature = array();
			if (isset($params['feature'])) {
				foreach ($params['feature'] as $key => $f) {
					foreach ($f as $value) {
						$link .= "&feature[{$key}][]=" . $value;
						$feature[] = $value;
					}
				}
			}
			/*
			if (isset($params['manus'])) {
				foreach ($params['manus'] as $m) {
					$link .= "&manus[]=" . $m;
				}
			}*/
			$objPage = new Paging($total, $page, $page_size, $link);
			$paging = $objPage->getListFooter($link);
			//$leftFilter = $this->getMyTable('CategoriesTable')->getHtmlLeftFilterFeature($list, $feature);
			//$concernProduct = $this->getMyTable('ProductsTable')->getConcernProduct($list);
			//$bannerSlideshows = $this->getMyTable('CategoriesTable')->getBannersSlideshow($list);
            if($idcat){
                $featureDataFilter = $this->getModelTable('ManufacturersTable')->loadAllFeatureByManu($id, $list);
            }else{
			    $featureDataFilter = $this->getModelTable('ManufacturersTable')->loadAllFeatureByManu($id);
            }
			$renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
			$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');

			if(isset($manu) && COUNT($manu) >0 ){
                if(isset($manu['manufacturers_name'])){
				$renderer->headTitle($manu['manufacturers_name']);
				$renderer->headMeta()->appendName('keyword', $manu['manufacturers_name']);
				$renderer->headMeta()->appendName('description', $manu['manufacturers_name']);
				/*$breadCrumb = '
					<div class="breakcum-top cl-box">
						<a href="'. FOLDERWEB . '/listing/all" class="item-breakcum" title="' .$translator->translate('all_manu'). '">
							<span class="txt">' .$translator->translate('all_manu'). '</span>
							<span class="corer">&gt;&gt;</span>
						</a>';
						if($catinfo){
							$breadCrumb .= '<a href="'. $helperCom->getCategoriesUrl($catinfo). '" class="item-breakcum " title="' .$catinfo['categories_title']. '">
							<span class="txt">' .$catinfo['categories_title']. '</span>
							<span class="corer">&gt;&gt;</span>
						</a>';
						}
						$breadCrumb .= '<a href="'. $helperCom->getManuUrl($manu). '" class="item-breakcum active " title="' .$manu['manufacturers_name']. '">
							<span class="txt">' .$manu['manufacturers_name']. '</span>
						</a></div>';*/
				$breadCrumbMap = $helper->getBreadCrumbManuListing($catinfo, $manu);
                }else{
                    if(isset($catinfo['categories_id'])){
                        $breadCrumbMap = $this->getModelTable('CategoriesTable')->getBreadCrumb($catinfo['categories_id']);
                    }else{
                        $breadCrumbMap = '
					<div class="breakcum-top cl-box">
						<a href="'. FOLDERWEB . '/listing/all" class="item-breakcum active" title="' .$translator->translate('all_manu'). '">
							<span class="txt">' .$translator->translate('all_manu'). '</span>
						</a></div>';
                    }
                }
			}else{
				$renderer->headTitle($translator->translate('all_manu'));
				$renderer->headMeta()->appendName('keyword', $translator->translate('all_manu_keyword'));
				$renderer->headMeta()->appendName('description', $translator->translate('all_manu_description'));
				/*$breadCrumb = '
					<div class="breakcum-top cl-box">
						<a href="'. FOLDERWEB . '/listing/all" class="item-breakcum active" title="' .$translator->translate('all_manu'). '">
							<span class="txt">' .$translator->translate('all_manu'). '</span>
						</a></div>';*/
				//$breadCrumb = $helper->getBreadCrumbManuListing($catinfo, $manu);;
                if(isset($catinfo['categories_id'])){
                    $breadCrumbMap = $this->getModelTable('CategoriesTable')->getBreadCrumb($catinfo['categories_id']);
                }else{
                    $breadCrumbMap = '
					<div class="breakcum-top cl-box">
						<a href="'. FOLDERWEB . '/listing/all" class="item-breakcum active" title="' .$translator->translate('all_manu'). '">
							<span class="txt">' .$translator->translate('all_manu'). '</span>
						</a></div>';
                }
			}
            $news = $this->getModelTable('ArticlesTable')->getRows(array(
                'is_published' => 1,
                'is_delete' => 0,
                'is_faq' => 0,
            ), 5);
            $productsHots = $this->getModelTable('ProductsTable')->getHotProduct();
            $productsBanchay = $this->getModelTable('ProductsTable')->getProductBanchay();

            $breadCrumb = $helper->getBreadCrumbHome();
            $this->data_view['breadCrumbMap'] = $breadCrumbMap;
            $this->data_view['childCat'] = $childCat;
            $this->data_view['catinfo'] = $catinfo;
            $this->data_view['manu'] = $manu;
            $this->data_view['current_feature'] = $feature;
            $this->data_view['featureDataFilter'] = $featureDataFilter;
            $this->data_view['manu_all'] = $manus_all;
            $this->data_view['products'] = $rows;
            $this->data_view['area'] = $area;
            $this->data_view['paging'] = $paging;
            $this->data_view['filter'] = $filter;
            $this->data_view['page_size'] = $page_size;
            $this->data_view['id'] = $id;
            $this->data_view['total'] = $total;
            $this->data_view['breadCrumb'] = $breadCrumb;
            $this->data_view['topMenu'] = $this->topMenu;
            $this->data_view['parentCategories'] = $parentCategories;
            $this->data_view['news'] = $news;
            $this->data_view['productsHots'] = $productsHots;
            $this->data_view['productsBanchay'] = $productsBanchay;
            return $this->data_view;
//			return array(
//                'breadCrumbMap' => $breadCrumbMap,
//                'childCat' => $childCat,
//				'catinfo' => $catinfo,
//				'manu' => $manu,
//				'current_feature' => $feature,
//				'featureDataFilter' => $featureDataFilter,
//				'manu_all' => $manus_all,
//				//'bannerSlideshows' => $bannerSlideshows,
//				'products' => $rows,
//				//'manus' => $manus,
//				'area' => $area,
//				'paging' => $paging,
//				//'leftFilter' => $leftFilter,
//				//'manufacturers' => $manufacturers,
//				'filter' => $filter,
//				'page_size' => $page_size,
//				'id' => $id,
//				//'concernProduct' => $concernProduct,
//				//'menusLeft' => $menusLeft,
//				//'path_root' => $this->base,
//				'total' => $total,
//				//'categories' => $categories,
//				'breadCrumb' => $breadCrumb,
//				//'parentCategories' => $parentCategories,
//				'topMenu' => $this->topMenu,
//				'languages' => $this->languages,
//				'current_language' => $this->current_language,
//				//'total_categories' => $total_categories,
//			);
		}catch(\Exception $ex){
			return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
		}
	}

    public function showWarrantyAction(){
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $script->appendFile(FOLDERWEB.'/styles/js/tin_tuc.js');

        $id = $this->params()->fromRoute('id', null);
        if(!$id){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }
        try{
            $manu = $this->getModelTable('ManufacturersTable')->getRow($id);
        }catch(\Exception $ex){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($manu['manufacturers_name']);
        $renderer->headMeta()->appendName('keyword', $manu['manufacturers_name']);
        $renderer->headMeta()->appendName('description', $manu['manufacturers_name']);
        $topMenu = $this->getModelTable('CategoriesTable')->getAllCategoriesMenuTop();
		$manus = $this->getModelTable('ManufacturersTable')->getAllManus(0,100);
        $translator = $this->getServiceLocator()->get('translator');
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
        $breadCrumbMap = '
					<div class="breakcum-top cl-box">
						<a href="'. FOLDERWEB . '" class="item-breakcum " title="' .$translator->translate('dieu_kien_bao_hanh'). '">
							<span class="txt">' .$translator->translate('home'). '</span>
							<span class="corer">&gt;&gt;</span>
						</a>
						<a href="javascript:;" class="item-breakcum " title="' .$translator->translate('all_manu'). '">
							<span class="txt">' .$translator->translate('dieu_kien_bao_hanh'). '</span>
							<span class="corer">&gt;&gt;</span>
						</a>
						<a href="'. FOLDERWEB. '/dieu-kien-bao-hanh/'. $helper->toAlias($manu['manufacturers_name']). '-' . $manu['manufacturers_id'] . '" class="item-breakcum active" title="' .$manu['manufacturers_name']. '">
							<span class="txt">' .$manu['manufacturers_name']. '</span>
						</a>
						</div>';

        $breadCrumb = $helper->getBreadCrumbHome();
		try{
			//$tech_cat = $this->getModelTable('CategoriesTable')->getCategoryArticleTech();
			//$articlesTopView = $this->getModelTable('ArticlesTable')->getArticlesByCatTopView($tech_cat['categories_articles_id'], 0, 5);
		}catch(\Exception $ex){
			//$tech_cat = FALSE;
			//$articlesTopView = FALSE;
		}
        $news = $this->getModelTable('ArticlesTable')->getRows(array(
            'is_published' => 1,
            'is_delete' => 0,
            'is_faq' => 0,
        ), 5);
        $productsBanchay = $this->getModelTable('ProductsTable')->getProductBanchay();
        $productsHots = $this->getModelTable('ProductsTable')->getHotProduct();
        $this->data_view['manus'] = $manus;
        $this->data_view['manu'] = $manu;
        $this->data_view['breadCrumb'] = $breadCrumb;
        $this->data_view['breadCrumbMap'] = $breadCrumbMap;
        $this->data_view['topMenu'] = $topMenu;
        $this->data_view['news'] = $news;
        $this->data_view['productsBanchay'] = $productsBanchay;
        $this->data_view['productsHots'] = $productsHots;

        return $this->data_view;
//        return array(
//			'manus' => $manus,
//			//'tech_cat' => $tech_cat,
//			//'articlesTopView' => $articlesTopView,
//            'manu' => $manu,
//            'breadCrumb' => $breadCrumb,
//            'breadCrumbMap' => $breadCrumbMap,
//            'languages' => $this->languages,
//            'current_language' => $this->current_language,
//            'topMenu' => $topMenu,
//        );
    }
	
}


class Paging
{
    /** @var int The record number to start dislpaying from */
    var $m_intLimitStart = null;
    /** @var int Number of rows to display per page */
    var $m_intLimit = null;
    /** @var int Total number of rows */
    var $m_intTotal = null;
    /** @var arr Limit record number to display */
    var $urlsearch = null;
    var $m_arrLimit = array(10 => 10, 15 => 15, 20 => 20, 30 => 30, 40 => 40, 50 => 50);

    /**
     * Ham khoi tao cho class
     * Type:      function<br>
     * Name:     PageNav <br>
     * @param:    int p_intTotal la tong so record
     * @param:    int p_intPage la trang hien tai
     * @param:    int p_intLimit la so record cho mot trang
     * @return:
     */
    function __construct($p_intTotal, $p_intPage, $p_intLimit, $urlsearch)
    {
        $this->m_intTotal = (int)$p_intTotal;
        $this->m_intLimit = (int)max($p_intLimit, 1);

        $this->m_intLimitStart = (int)max(($p_intPage - 1) * ($this->m_intLimit), 0);

        if ($this->m_intLimit > $this->m_intTotal) {
            $this->m_intLimitStart = 0;
        } elseif ($this->m_intLimitStart >= $this->m_intTotal) {
            $this->m_intLimitStart = $this->m_intTotal - ($this->m_intTotal % $this->m_intLimit) - $this->m_intLimit;
        }

    }

    /**
     * Ham tao combobox gioi han record trong mot trang
     * Type:     function<br>
     * Name:     getLimitBox <br>
     * @param:
     * @return:  string HTLM
     */
    function getLimitBox()
    {
        $limit = array();
        foreach ($this->m_arrLimit as $k => $v) {
            $limit[] = $this->makeOption($k);
        }
        // build the html select list
        $strHtml = $this->selectList($limit, 'display', 'size="1" onchange="document.adminForm.submit()"', 'value', 'text', $this->m_intLimit);
        return $strHtml;
    }

    /**
     * Writes the html limit # input box
     */
    function writeLimitBox()
    {
        echo Paging::getLimitBox();
    }

    /**
     * Writes total page
     */
    function writePagesCounter()
    {
        echo $this->getPagesCounter();
    }

    /**
     * @return string The html for the pages counter, eg, Results 1-10 of x
     */
    function getPagesCounter()
    {
        $strHtml = '';
        $intFromResult = $this->m_intLimitStart + 1;
        if ($this->m_intLimitStart + $this->m_intLimit < $this->m_intTotal) {
            $intToResult = $this->m_intLimitStart + $this->m_intLimit;
        } else {
            $intToResult = $this->m_intTotal;
        }
        if ($this->m_intTotal > 0) {
            $strHtml .= "Results " . $intFromResult . " - " . $intToResult . " of " . $this->m_intTotal;
        } else {
            $strHtml .= "NoResults";
        }
        return $strHtml;
    }

    /**
     * Writes the html for the pages counter, eg, Results 1-10 of x
     */
    function writePagesLinks()
    {
        echo $this->getPagesLinks();
    }

    /**
     * @return string The html links for pages, eg, previous, next, 1 2 3 ... x
     */
    function getPageFooter()
    {

    }

    function getPagesLinks($searchtitle = "")
    {
        $strHtml = '';
        $intDispPages = 5;
        // tong so trang co duoc
        $intTotalPages = ceil($this->m_intTotal / $this->m_intLimit);
        // trang hien tai dang xem
        $intPageCurrent = ceil(($this->m_intLimitStart + 1) / $this->m_intLimit);
        // trang duoc hien thi dau tien
        $intStartLoop = 1;
        if ($intPageCurrent > ceil($intDispPages / 2)) {
            $intStartLoop = $intPageCurrent - ceil($intDispPages / 2) + 1;
        }
        // trang duoc hien thi cuoi cung
        $intStopLoop = $intStartLoop + $intDispPages - 1;
        $strHtml .= "<input type='hidden' name='page' id='page' value='$intPageCurrent' />";
        if ($intStopLoop >= $intTotalPages) {
            if ($intTotalPages - $intStartLoop < $intDispPages)
                $intStartLoop = max(($intTotalPages - $intDispPages + 1), 1);
            $intStopLoop = $intTotalPages;
        }
        if ($intPageCurrent > 1) {
            $pre = $intPageCurrent - 1;
            $strHtml .= "<li class=\"last\"><a href=\"?page=1&" . $searchtitle . "\" title=\"First page\">&lt;&lt;&nbsp;</a></li>";
            $strHtml .= "<li class=\"next\"><a href=\"?page=$pre" . $searchtitle . "\" title=\"Pre page\">&lt;&nbsp;</a></li>";
        } else {
            $strHtml .= "<li class=\"last\">&lt;&lt;&nbsp;</li>";
            $strHtml .= "<li class=\"next\">&lt;&nbsp;</li>";
        }
        for ($i = $intStartLoop; $i <= $intStopLoop; $i++) {
            if ($i == $intPageCurrent) {
                $strHtml .= "<li class=\"active\"> $i </li>";
            } else {
                $strHtml .= "<li><a href=\"?page=$i" . $searchtitle . "\">$i</a></li>";
            }
        }
        if ($intPageCurrent < $intTotalPages) {
            $intRowEnd = ($intTotalPages);
            $pre = $intPageCurrent + 1;
            $strHtml .= "<li class=\"next\"><a href=\"?page=$pre" . $searchtitle . "\" title=\"Next page\">&nbsp;&gt;</a></li>";
            $strHtml .= "<li class=\"next\"><a href=\"?page=$intRowEnd" . $searchtitle . "\" title=\"Last page\"> &nbsp;&gt;&gt;</a></li>";
        } else {
            $strHtml .= "<li class=\"next\">&nbsp;&gt;</li>";
            $strHtml .= "<li class=\"next\">&nbsp;&gt;&gt;</li>";
        }

        return $strHtml;
    }

    function getListFooter($searchtitle)
    {
        $strHtml = '';
        $intTotalPages = ceil($this->m_intTotal / $this->m_intLimit);
        if ($intTotalPages > 1) {
            $strHtml = $this->getPagesLinks($searchtitle);
        }
        return $strHtml;
    }

    //////////// Show pages2
    function getPagesLinksContent()
    {
        $strHtml = '';
        $intDispPages = 5;
        // tong so trang co duoc
        $intTotalPages = ceil($this->m_intTotal / $this->m_intLimit);
        // trang hien tai dang xem
        $intPageCurrent = ceil(($this->m_intLimitStart + 1) / $this->m_intLimit);
        // trang duoc hien thi dau tien
        $intStartLoop = 1;
        if ($intPageCurrent > ceil($intDispPages / 2)) {
            $intStartLoop = $intPageCurrent - ceil($intDispPages / 2) + 1;
        }
        // trang duoc hien thi cuoi cung
        $intStopLoop = $intStartLoop + $intDispPages - 1;
        $strHtml .= "<input type='hidden' name='page' id='page' value='$intPageCurrent' />";
        if ($intStopLoop >= $intTotalPages) {
            if ($intTotalPages - $intStartLoop < $intDispPages)
                $intStartLoop = max(($intTotalPages - $intDispPages + 1), 1);
            $intStopLoop = $intTotalPages;
        }
        if ($intPageCurrent > 1) {
            $pre = $intPageCurrent - 1;
            $strHtml .= "<a href=\"?page=1" . $urlsearch . "\" title=\"First page\">&lt;&lt;&nbsp;</a>";
            $strHtml .= "<a href=\"?page=$pre" . $urlsearch . "\" title=\"Pre page\">&lt;&nbsp;</a>";
        } else {
            $strHtml .= "<a>Trang &#272;&#7847;u&lt;&lt;&nbsp;</a>";
            $strHtml .= "<a>&lt;&nbsp;</a>";
        }
        for ($i = $intStartLoop; $i <= $intStopLoop; $i++) {
            if ($i == $intPageCurrent) {
                $strHtml .= "<a class=\"selected\"> $i </a>";
            } else {
                $strHtml .= "<a href=\"?page=$i" . $urlsearch . "\"><strong>$i</strong></a>";
            }
        }
        if ($intPageCurrent < $intTotalPages) {
            $intRowEnd = ($intTotalPages);
            $pre = $intPageCurrent + 1;
            $strHtml .= "<a href=\"?page=$pre" . $urlsearch . "\" title=\"Next page theo\">&nbsp;&gt;</a>";
            $strHtml .= "<a href=\"?page=$intRowEnd" . $urlsearch . "\" title=\"Last page\"> &nbsp;&gt;&gt;</a>";
        }

        return $strHtml;
    }

    function getListFooterContent()
    {

        $strHtml = '';
        $intTotalPages = ceil($this->m_intTotal / $this->m_intLimit);
        if ($intTotalPages > 1) {
            $strHtml .= "<div width='95%' align='right' style='padding-right:10px;'>" . $this->getPagesLinksContent() . "</div>";
        }
        return $strHtml;
    }

    /**
     * @param int The row index
     * @return int
     */
    function rowNumber($i)
    {

        return $i + 1 + $this->m_intLimitStart;
    }

    function setLimitRecord($p_arrLimit)
    {
        $this->m_arrLimit = $p_arrLimit;
    }

    function selectList(&$arr, $tag_name, $tag_attribs, $key, $text, $selected = NULL)
    {
        reset($arr);
        $html = "<select name=\"$tag_name\" $tag_attribs>";
        for ($i = 0, $n = count($arr); $i < $n; $i++) {
            $k = $arr[$i]->$key;
            $t = $arr[$i]->$text;
            $id = @$arr[$i]->id;

            $extra = '';
            $extra .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
            if (is_array($selected)) {
                foreach ($selected as $obj) {
                    $k2 = $obj->$key;
                    if ($k == $k2) {
                        $extra .= " selected=\"selected\"";
                        break;
                    }
                }
            } else {
                $extra .= ($k == $selected ? " selected=\"selected\"" : '');
            }
            $html .= "\t<option value=\"" . $k . "\"$extra>" . $t . "</option>";
        }
        $html .= "</select>";
        return $html;
    }

    function makeOption($value, $text = '')
    {
        $obj = new stdClass;
        $obj->value = $value;
        $obj->text = trim($text) ? $text : $value;
        return $obj;
    }
}
