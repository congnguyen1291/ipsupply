<?php
namespace Cms\Controller;

use Cms\Lib\Paging;
use Zend\Db\Sql\Predicate\In;
use Zend\View\Model\ViewModel;
use Cms\Form\CategoryForm;
use Cms\Model\Category;

use JasonGrimes\Paginator;

class CategoryController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'category';
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
        $params['parent_id'] = $id;

        if( !empty($q) ){
            $params['categories_title'] = $q;
        }

        $total = $this->getModelTable('CategoryTable')->countAll($params);
        $categories = $this->getModelTable('CategoryTable')->fetchAll($params);

        $link = '/cms/category' .( !empty($id) ? '/index/'.$id : ''). '?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $category = array();
        if( !empty($id) ){
            $category = $this->getModelTable('CategoryTable')->getCategoryLanguage($id, $language);
        }
        $languages=  $this->getModelTable('LanguagesTable')->getLanguages();
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['languages'] = $languages;
        $this->data_view['category'] = $category;
        $this->data_view['categories'] = $categories;
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['id'] = $id;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction()
    {
        $parent_id = $this->params()->fromRoute('id', 0);
        $form = new CategoryForm();
        $form->get('submit')->setValue('Lưu lại');
        $cats = $this->getModelTable('CategoryTable')->fetchAll();
        $form->get('parent_id')->setOptions(array(
            'options' => $this->multiLevelData(TRUE, $cats, 'categories_id', 'parent_id', 'categories_title')
        ));
        if( !empty($parent_id) ){
            $form->get('parent_id')->setValue( $parent_id );
        }
		$listlanguage=  $this->getModelTable('LanguagesTable')->getLanguages();
        $cats_recom = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $cat = new Category();
            $cats_recom = $request->getPost('cat');
            $form->setInputFilter($cat->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $cat->exchangeArray($form->getData());
				$language = $request->getPost('language', '1');
				$cat->language=$language;

                try{
                    if($this->isMasterPage()){
                        $template_id = $request->getPost('template_id', 0);
                        $cat->template_id = $template_id;
                    }
                    $picture_id = $request->getPost('picture_id', '');
                    $this->getModelTable('CategoryTable')->saveCategory($cat, $picture_id);
                    $id = $this->getModelTable('CategoryTable')->getLastestId();
                    $this->getModelTable('CategoryTable')->addRecommendCat($id, $cats_recom);
					$this->getModelTable('CategoryTable')->saveCategoryTranslate($cat, $id);
                    /*strigger change namespace cached */
                    $this->updateNamespaceCached();
                    
                    // Redirect to list of albums
                    return $this->redirect()->toRoute('cms/category', array('action' => 'index', 'id' => $parent_id));
                }catch(\Exception $ex){
                    die($ex->getMessage());
                }
            }
        }
        $cats = $this->getModelTable('CategoryTable')->fetchAll();
        $this->data_view['cats'] = $this->multiLevelData(FALSE, $cats, 'categories_id', 'parent_id', 'categories_title');
        
        if($this->isMasterPage()){
            $themes = $this->getModelTable('TemplatesTable')->getAll();
            $this->data_view['themes'] = $themes;
        }
        
        $this->data_view['form'] = $form;
		 $this->data_view['language_list'] = $listlanguage;
        $this->data_view['cats_recommend'] = $cats_recom;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
		$language = $this->params()->fromQuery('language', 1);
        if (!$id) {
            return $this->redirect()->toRoute('cms/category', array(
                'action' => 'add'
            ));
        }
		$catdata = "";
        try {
			if(!empty($language) && !empty($id)){
				$catdata = $this->getModelTable('CategoryTable')->getCategoryLanguage($id, $language);
			}
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/category', array(
                'action' => 'index'
            ));
        }
        $form = new CategoryForm();
        $cats = $this->getModelTable('CategoryTable')->fetchAll( array('languages_id' => $language) );
		
        $form->get('parent_id')->setOptions(array(
            'options' => $this->multiLevelData(TRUE, $cats, 'categories_id', 'parent_id', 'categories_title')
        ));
		$listlanguage=  $this->getModelTable('LanguagesTable')->getLanguages();
        $form->bind($catdata);
        $form->get('submit')->setAttribute('value', 'Edit');
        $cats_recommend = $this->getModelTable('CategoryTable')->getRecommendCat($id);
        $cats_recom = array();
        foreach($cats_recommend as $catv){
            $cats_recom[] = $catv['categories_id'];
        }
        $old_image = $catdata->icon;
        $request = $this->getRequest();
        if ($request->isPost()) {
			$cat = new Category();
            $cats_recom = $request->getPost('cat');
            $form->setInputFilter($cat->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
				$cat->exchangeArray($form->getData());
				$language = $request->getPost('language', '1');
				$cat->language=$language;
                try{
                    $cat->template_id = 1;
                    $picture_id = $request->getPost('picture_id', '');
                    $this->getModelTable('CategoryTable')->saveCategory($cat, $picture_id, $old_image);
                    $this->getModelTable('CategoryTable')->addRecommendCat($id, $cats_recom);
					$this->getModelTable('CategoryTable')->saveCategoryTranslate($cat, $id);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    // Redirect to list of albums
                    return $this->redirect()->toRoute('cms/category', array('action' => 'index', 'id' => $cat->parent_id));
                }catch(\Exception $ex){}
            }
        }
        $cats = $this->getModelTable('CategoryTable')->fetchAll($id);
        $this->data_view['cats'] = $this->multiLevelData(FALSE, $cats, 'categories_id', 'parent_id', 'categories_title');
        
        if($this->isMasterPage()){
            $themes = $this->getModelTable('TemplatesTable')->getAll();
            $this->data_view['themes'] = $themes;
        }

        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['category'] = $catdata;
        $this->data_view['cats_recommend'] = $cats_recom;
		$this->data_view['language_list'] = $listlanguage;
		$this->data_view['langselected'] = $language;
        return $this->data_view;
    }


    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('CategoryTable')->deleteCategories($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('CategoryTable')->deleteCategories($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/category');
    }

    public function updateFeatureAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/category', array(
                'action' => 'index'
            ));
        }
        try {
            $cat = $this->getModelTable('CategoryTable')->getCategory($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/category', array(
                'action' => 'index'
            ));
        }
        $current_features = $this->getModelTable('CategoryTable')->getAllFeatureChecked($id);
        if(count($current_features) < 1){
            $current_features = array();
        }

        $checked_array = array();
        foreach($current_features as $row){
            $checked_array[] = $row->feature_id;
        }
        $features = $this->getModelTable('FeatureTable')->fetchAllAndSort();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $newdata = $request->getPost('featureid');
            $this->getModelTable('CategoryTable')->updateFeatureData($id, $checked_array, $newdata);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

            return $this->redirect()->toRoute('cms/category');
        }
        $this->data_view['id'] = $id;
        $this->data_view['cat'] = $cat;
        $this->data_view['features'] = $features;
        $this->data_view['checked'] = $checked_array;
        return $this->data_view;
    }

    public function undeleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_delete' => 0
            );
            $this->getModelTable('CategoryTable')->updateCategories($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_delete' => 0
                );
                $this->getModelTable('CategoryTable')->updateCategories($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/category');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('CategoryTable')->updateCategories($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('CategoryTable')->updateCategories($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/category');
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
                $this->getModelTable('CategoryTable')->updateCategories($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('CategoryTable')->updateCategories($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/category');
    }

	
	public function staticAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_static' => 1
            );
            $this->getModelTable('CategoryTable')->updateCategories($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_static' => 1
                );
                $this->getModelTable('CategoryTable')->updateCategories($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/category');
    }

    public function unstaticAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_static' => 0
            );
            if (count($ids) > 0) {
                $this->getModelTable('CategoryTable')->updateCategories($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_static' => 0
                );
                $this->getModelTable('CategoryTable')->updateCategories($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/category');
    }
	
	
	
    public function updateorderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('ordering');
            $this->getModelTable('CategoryTable')->updateOrder($data);
            
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }
        return $this->redirect()->toRoute('cms/category');
    }

    public function stepUpdateOrderAction($categories, $index )
    {
        if( !empty($categories[$index]) ){
            foreach ($categories[$index] as $key => $category) {
                $row = array();
                $row['ordering'] = $key;
                $this->getModelTable('CategoryTable')->updateCategories($category['categories_id'], $row);
                if( !empty($categories[$category['categories_id']]) ){
                    $this->stepUpdateOrderAction($categories, $category['categories_id']);
                }
            }
        }
    }

    public function autoOrderAction()
    {
        $categories = $this->getModelTable('CategoryTable')->getAllCategoriesSort();
        if( !empty($categories) ){
            $this->stepUpdateOrderAction($categories, 0);
        }
        /*strigger change namespace cached*/
        $this->updateNamespaceCached();
        return $this->redirect()->toRoute('cms/category', array(
            'action' => 'index'
        ));
    }

    public function filterAction(){
        $request = $this->getRequest();
        $cats = array();
        if($request->isPost()){
            $data_filter = $request->getPost();
            $cats = $this->getModelTable('CategoryTable')->filter($data_filter);
            $cats = $this->multiLevelData(FALSE, $cats, 'categories_id', 'parent_id', 'categories_title');
        }
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'cats' => $cats
        ));
        return $result;
    }

    public function manageBannerAction(){
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        $total = $this->getModelTable('CategoryTable')->countAllBanners($params);
        $banners = $this->getModelTable('CategoryTable')->getBanners($params);
        $link = '/cms/category/manageBanner/?page=(:num)';
        $paginator = new Paginator($total, $limit, $page, $link);
        $this->data_view['banners'] = $banners;
        $this->data_view['paging'] = $paginator->toHtml();
        return $this->data_view;
    }

    public function addBannerAction(){
        $cats = $this->getModelTable('CategoryTable')->fetchAll();
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            //$banner_type = $request->getPost('banner_type');
            $data['banner_type'] = 1;
            $data['file'] = '';
            $picture_id = $request->getPost('picture_id', '');
            if(!empty($picture_id)){
                $picture = $this->getModelTable('PictureTable')->getPicture($picture_id);
                if(!empty($picture)){
                    $data['file'] = $picture->folder.'/'.$picture->name.'.'.$picture->type;
                }
                unset($data['picture_id']);
            }

            $data['code'] = "";
            try{
                $this->getModelTable('CategoryTable')->addBanner($data);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                return $this->redirect()->toRoute('cms/category', array('action' => 'manageBanner'));
            }catch(\Exception $ex){
                die($ex->getMessage());
            }
        }
        $this->data_view['cats'] = $this->multiLevelData(FALSE, $cats, 'categories_id', 'parent_id', 'categories_title');
        return $this->data_view;
    }

    public function editBannerAction(){
        $id = $this->params()->fromRoute('id');
        if(!$id){
            return $this->redirect()->toRoute('cms/category',array('action' => 'addBanner'));
        }
        try{
            $banner = $this->getModelTable('CategoryTable')->getBanner($id);
        }catch(\Exception $ex){
            return $this->redirect()->toRoute('cms/category',array('action' => 'manageBanner'));
        }
        $cats = $this->getModelTable('CategoryTable')->fetchAll();
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $picture_id = $request->getPost('picture_id', '');
            if(!empty($picture_id)){
                $picture = $this->getModelTable('PictureTable')->getPicture($picture_id);
                if(!empty($picture)){
                    $data['file'] = $picture->folder.'/'.$picture->name.'.'.$picture->type;
                }
                unset($data['picture_id']);
            }
            try{
                $this->getModelTable('CategoryTable')->editBanner($id,$data);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                return $this->redirect()->toRoute('cms/category', array('action' => 'manageBanner'));
            }catch(\Exception $ex){
                die($ex->getMessage());
            }
        }
        $this->data_view['banner'] = $banner;
        $this->data_view['cats'] = $this->multiLevelData(FALSE, $cats, 'categories_id', 'parent_id', 'categories_title');
        return $this->data_view;
    }

    public function deleteBannerAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $ids = $request->getPost('cid');
            try{
                $this->getModelTable('CategoryTable')->deleteBanner($ids);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }catch(\Exception $ex){}
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('CategoryTable')->deleteBanner($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/category',array('action' => 'manageBanner'));
    }

    public function updateorderBannerAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('order');
            $this->getModelTable('CategoryTable')->updateorderBannerData($data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }
        return $this->redirect()->toRoute('cms/category',array('action' => 'manageBanner'));
    }

    public function filterBannerAction(){
        $request = $this->getRequest();
        $banners = array();
        if($request->isPost()){
            $data_filter = $request->getPost();
            $banners = $this->getModelTable('CategoryTable')->filterBanners($data_filter);
        }
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'banners' => $banners,
        ));
        return $result;
    }

    public function singleDropdownAction(){
        $limit = $this->params()->fromQuery('limit', 5);
        $page = $this->params()->fromQuery('page', 1);
        $params = array();
        $categories = $this->getModelTable('CategoryTable')->getAllCategoriesSort($params);
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'categories' => $categories
        ));

        return $result;
    }

    public function singleSuggestAction()
    {
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $categories_title = $this->params()->fromQuery('categories_title', '');
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        if($categories_title){
            $params['categories_title'] = $categories_title;
        }

        $total = $this->getModelTable('CategoryTable')->countListCategory($params);
        $categories = $this->getModelTable('CategoryTable')->getListCategory($params);

        $link = 'javascript:dropMMNextPage(this, (:num));';
        $paginator = new Paginator($total, $limit, $page, $link);

        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'total' => $total,
            'page' => $page,
            'intPageSize' => $limit,
            'categories' => $categories,
            'categories_title' => $categories_title,
            'paginator' => $paginator,
        ));

        return $result;
    }

}