<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 10:45 AM
 */

namespace Cms\Controller;

use Cms\Form\ProductForm;
use Cms\Model\Product;
use Zend\Db\Sql\Predicate\In;
use Zend\View\Model\ViewModel;
use Cms\Form\CategoryForm;
use Cms\Model\Category;
use Cms\Lib\Paging;
use Zend\Dom\Query;
use Zend\Validator;

use JasonGrimes\Paginator;

class ProductController extends BackEndController
{

    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'product';
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
                $params['products_title'] = $q;
            }else if( $type == 1 ){
                $params['products_code'] = $q;
            }
            else if( $type == 2 ){
                $params['price'] = $q;
            }
            else if( $type == 3 ){
                $params['quantity'] = $q;
            }
        }

        $category = array();
        if( !empty($id) ){
            $params['categories_id'] = $id;
            $category = $this->getModelTable('CategoryTable')->getCategoryLanguage($id, $language);
        }
        $languages=  $this->getModelTable('LanguagesTable')->getLanguages();
        $total = $this->getModelTable('ProductTable')->countAll($params);
        $products = $this->getModelTable('ProductTable')->fetchAll($params);

        $link = '/cms/product' .( !empty($id) ? '/index/'.$id : ''). '?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['category'] = $category;
        $this->data_view['products'] = $products;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['languages'] = $languages;
        $this->data_view['q'] = $q;
		$this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $form = new ProductForm();
        $form->get('submit')->setValue('Lưu lại');
        $categories = $this->getModelTable('CategoryTable')->getAllCategoriesSort();
		$listlanguage=  $this->getModelTable('LanguagesTable')->getLanguages();
        $manufacturers = $this->getModelTable('ManufacturersTable')->fetchAll('', '', 0, 1000);
        $data_manus = array();
        foreach ($manufacturers as $manus) {
            $data_manus[$manus['manufacturers_id']] = $manus['manufacturers_name'];
        }
        
        $form->get('manufacturers_id')->setOptions(array(
            'options' => $data_manus
        ));

        $listPublisher = $this->getModelTable('MerchantTable')->getMerchants();
        $publisher = array();
        foreach($listPublisher as $pub){
            $publisher[$pub['merchant_id']] = $pub['merchant_name'];
        }
        $form->get('publisher_id')->setOptions(array(
            'options' => $publisher
        ));

        $extensions = $this->getModelTable('ExtensionTable')->fetchAll('is_delete=0 AND is_published=1 AND ext_require=0', '', 0, 100);
        $extension_require = $this->getModelTable('ExtensionTable')->fetchAll('is_delete=0 AND is_published=1 AND ext_require=1', '', 0, 100);
        $precommends = '';
        $request = $this->getRequest();
        if ($request->isPost()) {
            $precommends = $request->getPost('products_recommend');
            $p = new Product();
			$p->exchangeArray($request->getPost());
            $form->setInputFilter($p->getInputFilter());
            $form->setData($request->getPost());
			
            if ($form->isValid()) {
                $products_alias = $request->getPost('products_alias', '');

                $same = $this->getModelTable('ProductTable')->getProductsByAlias($products_alias);
                if( !empty($products_alias) && empty($same) ){
                    $p->products_description = htmlentities($p->products_description, ENT_QUOTES, 'UTF-8');
                    $p->products_longdescription = htmlentities($p->products_longdescription, ENT_QUOTES, 'UTF-8');
					$p->language=$request->getPost('language', '1');
                    $thumb_image = $request->getPost('thumb_image', '');
                    if( !empty($thumb_image) 
                        && is_array($thumb_image) ){
                        $p->thumb_image = $thumb_image[0];
                        $list_thumb_image = array();
                        foreach ($thumb_image as $key => $thumb ) {
                            $list_thumb_image[] = array(
                                                        'order' => 0,
                                                        'src' => $thumb 
                                                    );
                        }

                        $p->list_thumb_image = json_encode($list_thumb_image);
                    }

                    $publisher_id = $request->getPost('publisher_id', array());
                    $p->publisher_id = implode(',', $publisher_id);

                    $categories_name_list = $request->getPost('categories_id_list', array());
                    if( !empty($categories_name_list) ){
                        $p->categories_id = $categories_name_list[0];
                    }

                    $result = $this->getModelTable('ProductTable')->saveProduct($p, $request);
                    if( !empty($result['productid']) ){
                        $id = $result['productid'];
    					$this->getModelTable('ProductTable')->saveProductTranslate($p, $id);

                        if( !empty($categories_name_list) ){
                           $this->getModelTable('ProductTable')->addCategoryProduct($id, array_unique($categories_name_list));
                        }
    					
                        //Edit target city districts_id_list districts_id_list
                        $_country_id = $request->getPost('country_id', array());
                        $_cities_id = $request->getPost('cities_id', array());
                        
                        if( !empty($_country_id) || !empty($_cities_id) ){
                            $country_id = implode(',', $_country_id);
                            $cities_id = implode(',', $_cities_id);
                            $this->getModelTable('ProductTable')->addProductTarget($id,$country_id,$cities_id, '', '');
                        }

                        if ($result['success']) {
                            /*strigger change namespace cached*/
                            $this->updateNamespaceCached();
                            return $this->redirect()->toRoute('cms/product', array(
                                'action' => 'index',
                                'id' => $p->categories_id
                            ));
                        }
                        $_SESSION['error_message'] = $result['msg'];
                    }
                }
            }
        }
        $this->data_view['categories'] = $categories;
		$this->data_view['language_list'] = $listlanguage;
        $this->data_view['extensions'] = $extensions;
        $this->data_view['extension_requires'] = $extension_require;
        $this->data_view['form'] = $form;
        $this->data_view['precommends'] = $precommends;
        $this->data_view['langselected'] = $language;
        return $this->data_view;
    }

    public function ajaxProductAction(){
        $request = $this->getRequest();
        $products = array();
        if($request->isPost()){
            $ids = $request->getPost('id');
            $products = $this->getModelTable('ProductTable')->getProductsByIds($ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        echo json_encode($products);
        die();
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/product', array(
                'action' => 'add'
            ));
        }
		$language = $this->params()->fromQuery('language', 1);
        try {
			$p = $this->getModelTable('ProductTable')->getProductLanguage($id, $language);
            if( empty($p) ){
                return $this->redirect()->toRoute('cms/product', array(
                    'action' => 'add'
                ));
            }
            if( !empty($p->products_description) )
                $p->products_description = html_entity_decode($p->products_description, ENT_QUOTES, 'UTF-8');
            if( !empty($p->products_longdescription) )
                $p->products_longdescription = html_entity_decode($p->products_longdescription, ENT_QUOTES, 'UTF-8');
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/product', array(
                'action' => 'index'
            ));
        }
        $listlanguage=  $this->getModelTable('LanguagesTable')->getLanguages();
        /**
         * Lấy danh sách kiểu của sản phẩm categories_id
         */
        $products_type = $this->getModelTable('ProductTable')->getProductTypeLanguage($id, $language);
        /**
         * Lấy danh sách đặc tính đã chọn của sản phẩm
         */
        $checked_array = array();
        $checked = $this->getModelTable('ProductTable')->getFeature($id);
        foreach ($checked as $feat) {
            $checked_array[$feat['feature_id']] = $feat['value'];
        }
        /**
         * Lấy danh sách đặc tính theo danh mục của sản phẩm
         */
        $features = $this->getModelTable('FeatureTable')->getByCatId($p->categories_id);
        $features = $this->multiLevelData(FALSE, $features, 'feature_id', 'parent_id', 'feature_title');
        /**
         * Lấy danh sách hình ảnh sản phẩm
         */
        $list_image = $this->getModelTable('ProductTable')->getImageList($id);
        $form = new ProductForm();
        $categories = $this->getModelTable('CategoryTable')->getAllCategoriesSort( array('languages_id' => $language) );
        /**
         * Lấy danh sách nhà sản xuất
         */
        $manufacturers = $this->getModelTable('ManufacturersTable')->fetchAll('', '', 0, 1000);

        //Lấy thông product category
        $productcateogory = $this->getModelTable('ProductTable')->getCategoryProduct($id);
		
        $data_manus = array();
        foreach ($manufacturers as $manus) {
            $data_manus[$manus['manufacturers_id']] = $manus['manufacturers_name'];
        }
        
        $extensions = $this->getModelTable('ExtensionTable')->fetchAll('is_delete=0 AND is_published=1 AND ext_require=0', '', 0, 100);
        $extension_require = $this->getModelTable('ExtensionTable')->fetchAll('is_delete=0 AND is_published=1 AND ext_require=1', '', 0, 100);
        /**
         * Lấy danh sách extension
         */

        $extensions_current = $this->getModelTable('ProductTable')->getExtensionByProductId($id, $language);
        $extensions_current_require_tmp = $this->getModelTable('ProductTable')->getExtensionRequireByProductId($id);
        $extensions_current_require = array();
        foreach ($extensions_current_require_tmp as $key => $value) {
            if(!isset($extensions_current_require[$value['transportation_type']])){
                $extensions_current_require[$value['transportation_type']] = array('type'=>$value['transportation_type'], 'data'=> array($value));
            }else{
                $extensions_current_require[$value['transportation_type']]['data'][] = $value;
            }
        }
        $require_cities = $this->getModelTable('ProductTable')->getCitiesInExtensionRequireByProductId($id);
        $extensions_current_require_cities = array();
        $extensions_current_require_cities_choose = array();
        foreach ($require_cities as $key => $require_city) {
            $ciy_tmp = explode(',', $require_city['transportation_cities']);
            $extensions_current_require_cities_choose = array_merge($extensions_current_require_cities_choose, $ciy_tmp);
            if(!isset($extensions_current_require_cities[$require_city['area_id']])){
                $extensions_current_require_cities[$require_city['area_id']] = array($require_city['cities_id']=>$require_city);
            }else{
                $extensions_current_require_cities[$require_city['area_id']][$require_city['cities_id']] = $require_city;
            }
        }

        $form->get('manufacturers_id')->setOptions(array(
            'options' => $data_manus
        ));
        
        $htmlFeature = $this->getModelTable('FeatureTable')->getHtmlFeature($features, $checked_array, 0);
		
        $p->seo_keywords = @end(explode(',', $p->seo_keywords));
        $arr_tags = $this->getModelTable('TagsTable')->getTagsOfProduct($p->tags);
        $tags = array();
        foreach ($arr_tags as $key => $val) {
            $tags[] = $val['tags_name'];
        }
        $p->tags = implode(',', $tags);

        $recommends = $this->getModelTable('ProductTable')->getRecommendProducts($id);
        $precommends = array();
        foreach($recommends as $prod){
            $precommends[] = $prod['products_id'];
        }
        $precommends = implode(',', $precommends);

        $targetProduct = $this->getModelTable('ProductTable')->getProductTarget($id);
        $targetCountry = array();
        $targetCities = array();
        if( !empty($targetProduct) ){
            foreach ($targetProduct as $key => $tg) {
                if( !empty($tg['country_id']) ){
                    $cy = explode(',', $tg['country_id']);
                    $targetCountry = array_unique (array_merge ($targetCountry, $cy));
                }
                if( !empty($tg['cities_id']) ){
                    $cit = explode(',', $tg['cities_id']);
                    $targetCities = array_unique (array_merge ($targetCities, $cit));
                }
            }
            if( !empty($targetCountry) ){
                $cities = $this->getModelTable('CityTable')->getCities( array('country_id' => $targetCountry) );
                foreach ($cities as $city) {
                    $dataCities[$city["cities_id"]] = $city['cities_title'];
                }
            }
        }

        $listPublisher = $this->getModelTable('MerchantTable')->getMerchants();
        $publisher = array();
        foreach($listPublisher as $pub){
            $publisher[$pub['merchant_id']] = $pub['merchant_name'];
        }
        $form->get('publisher_id')->setOptions(array(
            'options' => $publisher
        ));
        
        $form->bind($p);
        $form->get('submit')->setAttribute('value', 'Edit');
        if( !empty($p->publisher_id) ){
            $publisher_id = explode(',', $p->publisher_id);
            $form->get('publisher_id')->setValue($publisher_id);
        }

        $catSelected = array();
        foreach ($productcateogory as $key => $pc) {
            $catSelected[] = $pc['categories_id'];
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            $precommends = $request->getPost('products_recommend');
            $p = new Product();
            $form->setInputFilter($p->getInputFilter());
            $form->setData($request->getPost());
			
            if ( $form->isValid()) {
                $products_alias = $request->getPost('products_alias', '');
                $same = $this->getModelTable('ProductTable')->getProductsByAlias($products_alias);
				
				$linkrefer="/cms/product";
				$linkredirect = urldecode($request->getPost('link_redirect', ''));
				if(isset($linkredirect) && $linkredirect!=""){
					$linkrefer=$linkredirect ;
				}				
                if( !empty($products_alias) 
                    && (empty($same) || (!empty($same) && $same->products_id == $id))  ){
                    $p->exchangeArray($request->getPost());
                    $p->products_description = htmlentities($p->products_description, ENT_QUOTES, 'UTF-8');
                    $p->products_longdescription = htmlentities($p->products_longdescription, ENT_QUOTES, 'UTF-8');
					$p->language=$request->getPost('language', '1');
                    $thumb_image = $request->getPost('thumb_image', '');
                    if( !empty($thumb_image) 
                        && is_array($thumb_image) ){
                        $p->thumb_image = $thumb_image[0];
                        $list_thumb_image = array();
                        foreach ($thumb_image as $key => $thumb ) {
                            $list_thumb_image[] = array(
                                                        'order' => 0,
                                                        'src' => $thumb 
                                                    );
                        }
                        $p->list_thumb_image = json_encode($list_thumb_image);
                    }

                    $publisher_id = $request->getPost('publisher_id', array());
                    $p->publisher_id = implode(',', $publisher_id);

                    $categories_name_list = $request->getPost('categories_id_list', array());
                    if( !empty($categories_name_list) ){
                        $p->categories_id = $categories_name_list[0];
                    }
                    $result = $this->getModelTable('ProductTable')->saveProduct($p, $request);
                    if( !empty($result['productid']) ){
    					$this->getModelTable('ProductTable')->saveProductTranslate($p, $id);

                        if( !empty($categories_name_list) ){
    					   $this->getModelTable('ProductTable')->addCategoryProduct($id, array_unique($categories_name_list));
    					}

    					//Edit target city districts_id_list districts_id_list
                        $_country_id = $request->getPost('country_id', array());
    					$_cities_id = $request->getPost('cities_id', array());
                        
                        if( !empty($_country_id) || !empty($_cities_id) ){
                            $country_id = implode(',', $_country_id);
                            $cities_id = implode(',', $_cities_id);
        					$this->getModelTable('ProductTable')->addProductTarget($id,$country_id,$cities_id, '', '');
                        }

                        if ( $result['success'] ) {
                            /*strigger change namespace cached*/
                            $this->updateNamespaceCached();
                            return $this->redirect()->toRoute('cms/product', array(
                                'action' => 'index',
                                'id' => $p->categories_id
                            ));
                        }
                        $_SESSION['error_message'] = $result['msg'];
                    }
                }
            }
        }
		
        $this->data_view['id'] = $id;
        $this->data_view['catSelected'] = $catSelected;
        $this->data_view['categories'] = $categories;
        $this->data_view['productcateogory'] = $productcateogory;
		$this->data_view['products_type'] = $products_type;
        $this->data_view['precommends'] = $precommends;
        $this->data_view['form'] = $form;
        $this->data_view['features'] = $features;
        $this->data_view['checked'] = $checked_array;
        $this->data_view['product'] = $p;
        $this->data_view['list_image'] = $list_image;
        $this->data_view['targetProduct'] = $targetProduct;
        $this->data_view['targetCities'] = $targetCities;
        $this->data_view['targetCountry'] = $targetCountry;
		$this->data_view['language_list'] = $listlanguage;
        $this->data_view['extensions'] = $extensions;
        $this->data_view['htmlFeature'] = $htmlFeature;
        $this->data_view['extensions_current'] = $extensions_current;
        $this->data_view['extension_requires'] = $extension_require;
        $this->data_view['extensions_current_requires'] = $extensions_current_require;
        $this->data_view['extensions_current_require_cities'] = $extensions_current_require_cities;
        $this->data_view['extensions_current_require_cities_choose'] = array_unique($extensions_current_require_cities_choose);
		$this->data_view['langselected'] = $language;

        return $this->data_view;
    }

    public function addarticleAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/product', array(
                'action' => 'index'
            ));
        }
        try {
            $p = $this->getModelTable('ProductTable')->getProduct($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/product', array(
                'action' => 'index'
            ));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('ProductTable')->addArticles($id, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

            return $this->redirect()->toRoute('cms/product');
        }

        $articles = $this->getModelTable('ProductTable')->loadArticlesProduct($id);
        $detail = $this->getModelTable('ProductTable')->countAllProductArticles($id);
        $this->data_view['id'] = $id;
        $this->data_view['product'] = $p;
        $this->data_view['articles'] = $articles;
        $this->data_view['total'] = $detail->total;
        return $this->data_view;
    }

    public function reviewManagementAction(){
        $id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/product');
        }
        try{
            $product = $this->getModelTable('ProductTable')->getProduct($id);
        }catch(\Exception $ex){
            return $this->redirect()->toRoute('cms/product');
        }
        $total = $this->getModelTable('ProductTable')->countAllReview("comments_product={$product->products_id}");
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        $link = "";
        $objPage = new Paging( $total, $page, $page_size, $link );
        $paging = $objPage->getListFooter ( $link );
        $reviews = $this->getModelTable('ProductTable')->getReviews("comments_product={$product->products_id}", '', $this->intPage, $this->intPageSize);
        $this->data_view['reviews'] = $reviews;
        $this->data_view['product'] = $product;
        $this->data_view['paging'] = $paging;
        $this->data_view['id'] = $id;
        return $this->data_view;
    }

    public function deleteReviewAction(){
        $id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/product');
        }
        try{
            $product = $this->getModelTable('ProductTable')->getProduct($id);
        }catch(\Exception $ex){
            return $this->redirect()->toRoute('cms/product');
        }
        $request = $this->getRequest();
        if($request->isPost()){
            $ids = $request->getPost('cid');
            try{
                $this->getModelTable('ProductTable')->deleteReview($ids);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }catch(\Exception $ex){

            }
        }
        return $this->redirect()->toRoute('cms/product', array('action' => 'review-management', 'id' => $id));
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid', array());
            if ( !empty($ids) ) {
                $this->getModelTable('ProductTable')->deleteProducts($ids);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {                
                $this->getModelTable('ProductTable')->deleteProducts($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }

        return $this->redirect()->toRoute('cms/product');
    }

    public function singlepublishAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('ProductTable')->updateProducts($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/product');
    }
	public function singlepublish11Action()
    {
     return $this->redirect()->toRoute('cms/product');
    }
    public function singleunpublishAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('ProductTable')->updateProducts($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/product');
    }
	
	public function singlehotAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_hot' => 1
            );
            $this->getModelTable('ProductTable')->updateProducts($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/product');
    }

    public function singlenothotAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_hot' => 0
            );
            $this->getModelTable('ProductTable')->updateProducts($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/product');
    } 
	
    public function singleavailableAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_available' => 1
            );
            $this->getModelTable('ProductTable')->updateProducts($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/product');
    }

    public function singlegoingonAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_goingon' => 1
            );
            $this->getModelTable('ProductTable')->updateProducts($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/product');
    } 
    
	public function singlenotgoingonAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_goingon' => 0
            );
            $this->getModelTable('ProductTable')->updateProducts($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/product');
    }

    public function singlenotavailableAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_available' => 0
            );
            $this->getModelTable('ProductTable')->updateProducts($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/product');
    }

    public function undeleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_delete' => 0
            );
            $this->getModelTable('ProductTable')->updateProducts($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_delete' => 0
                );
                $this->getModelTable('ProductTable')->updateProducts($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/product');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('ProductTable')->updateProducts($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();


        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('ProductTable')->updateProducts($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/product');
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            if (count($ids) > 0) {
                $this->getModelTable('ProductTable')->updateProducts($ids, $data);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('ProductTable')->updateProducts($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/product');
    }

    public function updateorderAction()
    {
        $request = $this->getRequest();
		$linkrefer="/cms/product";
        if ($request->isPost()) {
            $data = $request->getPost('ordering');
            $this->getModelTable('ProductTable')->updateOrder($data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
			$link=$request->getPost('link_redirect');
			if(isset($link) && $link!=""){
			$linkrefer=urldecode($request->getPost('link_redirect'));
			}
        }
        return $this->redirect()->toUrl($linkrefer);
    }

    public function deleteimageAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = intval($request->getPost('itemid'));
            $image = $request->getPost('filename');
            $folder = 'product' . $id;
            $filename = explode('/', $image);
            $filename = end($filename);
            $file = PATH_BASE_ROOT . DS . 'custom' . DS . 'domain_1' . DS . 'products' . DS . 'fullsize' . DS . $folder . DS . $filename;
            if (is_file($file)) {
                @unlink($file);
            }
            $this->getModelTable('ProductTable')->deleteImageProduct($id, $image);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

            echo json_encode(array(
                'success' => TRUE,
                'msg' => 'Xóa thành công'
            ));
            die();
        }
        echo json_encode(array(
            'success' => FALSE,
            'error_code' => '501'
        ));
        die();
    }

    public function filterAction()
    {
        $request = $this->getRequest();
        $products = array();
        if ($request->isPost()) {
            $data_filter = $request->getPost();
            $products = $this->getModelTable('ProductTable')->filter($data_filter);
        }
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'products' => $products
        ));
        return $result;
    }

    public function filterChoiceAction()
    {
        $request = $this->getRequest();
        $products = array();
        if ($request->isPost()) {
            $data_filter = $request->getPost('query');
            $products = $this->getModelTable('ProductTable')->findProducts($data_filter);

        }
        echo json_encode($products);
        die();
    }

    public function copyAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/product', array(
                'action' => 'add'
            ));
        }
        try {
            $p = $this->getModelTable('ProductTable')->getProduct($id);
            $p->products_description = html_entity_decode($p->products_description, ENT_QUOTES, 'UTF-8');
            $p->products_longdescription = html_entity_decode($p->products_longdescription, ENT_QUOTES, 'UTF-8');
            $pid = $this->getModelTable('ProductTable')->copyProduct($p);
            $this->redirect()->toRoute('cms/product', array('action' => 'edit','id' => $pid));
        } catch (\Exception $ex) {
		die($ex->getMessage());
            return $this->redirect()->toRoute('cms/product', array(
                'action' => 'index'
            ));
        }
    }

    public function findProductsAction()
    {
        //findProducts
        $str = $_GET['query'];
        $products = $this->getModelTable('ProductTable')->findProducts($str);
        echo json_encode(array(
            'suggestions' => $products
        ));
        die;
    }

    public function loadProductAjaxAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $id = $request->getPost('id');
            $product = $this->getModelTable('ProductTable')->getProduct($id);
            if ($product) {
                echo json_encode(array(
                    'success' => TRUE,
                    'result' => $product
                ));
            } else {
                echo json_encode(array(
                    'success' => FALSE
                ));
            }
        } else {
            echo json_encode(array(
                'success' => FALSE
            ));
        }
        die;
    }

    public function findProductsByAliasAction()
    {
        $str = $_GET['aias'];
        $products = $this->getModelTable('ProductTable')->getProductsByAlias($str);
        echo json_encode(array(
            'products' => $products
        ));
        die;
    }
	
	public function pushSearchAction(){
        try{
            $this->getModelTable('ProductTable')->pushSearch();
            echo json_encode(array(
                'success' => TRUE,
                'msg' => 'Push successed!'
            ));
            die();
        }catch(\Exception $ex){
            die($ex->getMessage());
        }
    }

    public function singleDropdownAction(){
        $page = $this->params()->fromQuery('page', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        $products_title = $this->params()->fromQuery('products_title', '');
        if( !empty($products_title) ){
            $params['products_title'] = $products_title;
        }

        $total = $this->getModelTable('ProductTable')->countAll($params);
        $products = $this->getModelTable('ProductTable')->fetchAll($params);

        $link = 'dropMenuLoadDataProduct(this, (:num));';
        $paginator = new Paginator($total, $limit, $page, $link);

        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'products_title' => $products_title,
            'total' => $total,
            'products' => $products,
            'paginator' => $paginator,
        ));

        return $result;
	}

    public function singleSuggestAction()
    {
        $page = $this->params()->fromQuery('page', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        $products_title = $this->params()->fromQuery('products_title', '');
        if( !empty($products_title) ){
            $params['products_title'] = $products_title;
        }

        $total = $this->getModelTable('ProductTable')->countAll($params);
        $products = $this->getModelTable('ProductTable')->fetchAll($params);

        $link = 'dropMenuLoadDataProduct(this, (:num));';
        $paginator = new Paginator($total, $limit, $page, $link);

        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'products_title' => $products_title,
            'total' => $total,
            'products' => $products,
            'paginator' => $paginator,
        ));

        return $result;
    }

    public function autoOrderAction()
    {
        $products = $this->getModelTable('ProductTable')->fetchAll();
        foreach ($products as $key => $product) {
            $row = array();
            $row['ordering'] = $key;
            $this->getModelTable('ProductTable')->updateProduct($row, array('products_id' => $product['products_id']) );
        }
        /*strigger change namespace cached*/
        $this->updateNamespaceCached();
        return $this->redirect()->toRoute('cms/product', array(
                'action' => 'index'
            ));
    }

    public function upOrderAction()
    {
        $result = array( 'flag' => FALSE );
        $request = $this->getRequest();
        if($request->isPost()){
            $id = $request->getPost('id', '');
            $idup = $request->getPost('idup', '');
            if( !empty($id) && !empty($idup) ){
                $p = $this->getModelTable('ProductTable')->getProduct($id);
                $pu = $this->getModelTable('ProductTable')->getProduct($idup);
                if( !empty($p) && !empty($pu) ){
                    $products = $this->getModelTable('ProductTable')->getProductOrderUpper($pu->ordering);
                    foreach ($products as $key => $product) {
                        if( $product['products_id'] != $pu->products_id ){
                            $row = array();
                            $row['ordering'] = $product['ordering']-1;
                            $this->getModelTable('ProductTable')->updateProduct($row, array('products_id' => $product['products_id']) );

                        }else{
                            break;
                        }
                    }
                    $rowOwn = array();
                    $rowOwn['ordering'] = ($pu->ordering-2);
                    $this->getModelTable('ProductTable')->updateProduct($rowOwn, array('products_id' => $p->products_id) );
                    $result = array( 'flag' => TRUE );

                    $products = $this->getModelTable('ProductTable')->fetchAll();
                    foreach ($products as $key => $product) {
                        $row = array();
                        $row['ordering'] = $key;
                        $this->getModelTable('ProductTable')->updateProduct($row, array('products_id' => $product['products_id']) );
                    }
                }
            }
        }
        /*strigger change namespace cached*/
        $this->updateNamespaceCached();
        echo json_encode($result);
        die();
    }

    public function downOrderAction()
    {
        $result = array( 'flag' => FALSE );
        $request = $this->getRequest();
        if($request->isPost()){
            $id = $request->getPost('id', '');
            $iddown = $request->getPost('iddown', '');
            if( !empty($id) && !empty($iddown) ){
                $p = $this->getModelTable('ProductTable')->getProduct($id);
                $pd = $this->getModelTable('ProductTable')->getProduct($iddown);
                if( !empty($p) && !empty($pd) ){
                    $products = $this->getModelTable('ProductTable')->getProductOrderUpper($pd->ordering);
                    $hasFind = FALSE;
                    foreach ($products as $key => $product) {
                        if( !$hasFind 
                            && $product['products_id'] == $pd->products_id ){
                            $hasFind = TRUE;
                            continue;
                        }else if( $hasFind ){
                            $row = array();
                            $row['ordering'] = $product['ordering']+2;
                            $this->getModelTable('ProductTable')->updateProduct($row, array('products_id' => $product['products_id']) );
                        }
                    }
                    $rowOwn = array();
                    $rowOwn['ordering'] = ($pd->ordering+1);
                    $this->getModelTable('ProductTable')->updateProduct($rowOwn, array('products_id' => $p->products_id) );
                    $result = array( 'flag' => TRUE );

                    $products = $this->getModelTable('ProductTable')->fetchAll();
                    foreach ($products as $key => $product) {
                        $row = array();
                        $row['ordering'] = $key;
                        $this->getModelTable('ProductTable')->updateProduct($row, array('products_id' => $product['products_id']) );
                    }
                    
                }
            }
        }
        /*strigger change namespace cached*/
        $this->updateNamespaceCached();
        echo json_encode($result);
        die();
    }
	

}