<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/2/14
 * Time: 4:55 PM
 */

namespace Cms\Controller;

use Cms\Lib\Paging;
use Zend\Db\Sql\Predicate\In;
use Zend\View\Model\ViewModel;
use Cms\Form\ManufacturersForm;
use Cms\Model\Manufacturers;

use JasonGrimes\Paginator;

class ManufacturersController extends BackEndController{
    public function __construct(){
        parent::__construct();
        $this->data_view['current'] = 'manufacturers';
    }

    public function indexAction(){
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        if( !empty($q) ){
            $params['manufacturers_name'] = $q;
        }

        $total = $this->getModelTable('ManufacturersTable')->countAll( $params );
        $manufacturers = $this->getModelTable('ManufacturersTable')->fetchAll( $params );

        $link = '/cms/manufacturers?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['manufacturers'] = $manufacturers;
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction(){
        $form = new ManufacturersForm();
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $m = new Manufacturers();
            $form->setInputFilter($m->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $m->exchangeArray($form->getData());
                $picture_id = $request->getPost('picture_id', '');
                $this->getModelTable('ManufacturersTable')->saveManufacture($m,$picture_id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
                // Redirect to list of albums
                return $this->redirect()->toRoute('cms/manufacturers');
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function edit1Action(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/manufacturers', array(
                'action' => 'add'
            ));
        }
        try {
            $m = $this->getModelTable('ManufacturersTable')->getManufacture($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/manufacturers', array(
                'action' => 'index'
            ));
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $m->manufacturers_name = $request->getPost('manufacturers_name');
            $picture_id = $request->getPost('picture_id', '');
            $this->getModelTable('ManufacturersTable')->saveManufacture($m, $picture_id);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

            // Redirect to list of albums
        }
        echo json_encode(array(
            'success' => TRUE,
            'msg'     => ' Cập nhật thành công',
        ));
        die();
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/manufacturers', array(
                'action' => 'add'
            ));
        }
        try {
            $m = $this->getModelTable('ManufacturersTable')->getManufacture($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/manufacturers', array(
                'action' => 'index'
            ));
        }
        $form = new ManufacturersForm();
        $form->bind($m);
        $old_image = $m->thumb_image;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($m->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $picture_id = $request->getPost('picture_id', '');
                $this->getModelTable('ManufacturersTable')->saveManufacture($m,$picture_id);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                // Redirect to list of albums
                return $this->redirect()->toRoute('cms/manufacturers');
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['manufacture'] = $m;
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('ManufacturersTable')->deleteManufacturers($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('ManufacturersTable')->deleteManufacturers($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/manufacturers');
    }

    public function undeleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_delete' => 0
            );
            $this->getModelTable('ManufacturersTable')->softUpdateData($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/manufacturers');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('ManufacturersTable')->updateManufacturers($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('ManufacturersTable')->updateManufacturers($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/manufacturers');
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('ManufacturersTable')->updateManufacturers($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('ManufacturersTable')->updateManufacturers($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/manufacturers');
    }

    public function updateorderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('order');
            $this->getModelTable('ManufacturersTable')->updateorderData($data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/manufacturers');
    }

    public function filterAction()
    {
        $request = $this->getRequest();
        $manufacturers = array();
        if ($request->isPost()) {
            $data_filter = $request->getPost();
            $manufacturers = $this->getModelTable('ManufacturersTable')->filter($data_filter);
        }
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'manufacturers' => $manufacturers,
        ));
        return $result;
    }

    public function managePromotionAction(){
        try{
            $promos = $this->getModelTable('ManufacturersTable')->getAllPromotions(array(), array(), 0, 100);
            $this->data_view['data'] = $promos;
            return $this->data_view;
        }catch (\Exception $ex){
            return $this->redirect()->toRoute('cms/manufacturers');
        }
    }

    public function addPromotionAction(){
        try{
            $manus = $this->getModelTable('ManufacturersTable')->fetchAll('','', 0, 100);
            $cats = $this->getModelTable('CategoryTable')->fetchAll();
            $cats = $this->multiLevelData(FALSE, $cats, 'categories_id', 'parent_id', 'categories_title');
            /**
             * @var $request \Zend\Http\Request
             */
            $request = $this->getRequest();
            if($request->isPost()){
                $data = $request->getPost('data');
                $categories = $request->getPost('cats');
                $this->data_view['data'] = $data;
                $this->data_view['data']['cats'] = $categories;
                try{
                    $this->getModelTable('ManufacturersTable')->savePromotion($data, $categories);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/manufacturers', array(
                        'action' => 'manager-promotion',
                    ));
                }catch (\Exception $ex){
                    $_SESSION['error_message'] = $ex->getMessage();
                }
            }
            $this->data_view['manus'] = $manus->toArray();
            $this->data_view['cats'] = $cats;
            return $this->data_view;
        }catch (\Exception $ex){
            return $this->redirect()->toRoute('cms/manufacturers', array(
                'action' => 'manage-promotion',
            ));
        }
    }

    public function editPromotionAction(){
        $id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/manufacturers', array(
                'action' => 'manage-promotion',
            ));
        }
        try{
            $promotion = $this->getModelTable('ManufacturersTable')->getPromotionById($id);
            $promotion['description'] = html_entity_decode($promotion['description'],ENT_QUOTES, 'UTF-8');
            $cat_current = $this->getModelTable('ManufacturersTable')->getCategoriesByPromotionId($id);
            $cat_current = array_map(function($r){
                    return $r['categories_id'];
                }, $cat_current);
            $this->data_view['data'] = $promotion;
            $this->data_view['data']['cats'] = $cat_current;
        }catch (\Exception $ex){
            return $this->redirect()->toRoute('cms/manufacturers', array(
                'action' => 'manage-promotion',
            ));
        }
        try{
            $manus = $this->getModelTable('ManufacturersTable')->fetchAll('','', 0, 100);
            $cats = $this->getModelTable('CategoryTable')->fetchAll();
            $cats = $this->multiLevelData(FALSE, $cats, 'categories_id', 'parent_id', 'categories_title');
            /**
             * @var $request \Zend\Http\Request
             */
            $request = $this->getRequest();
            if($request->isPost()){
                $data = $request->getPost('data');
                $categories = $request->getPost('cats');
                $this->data_view['data'] = $data;
                $this->data_view['data']['cats'] = $categories;
                try{
                    $this->getModelTable('ManufacturersTable')->savePromotion($data, $categories);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/manufacturers', array(
                        'action' => 'manage-promotion',
                    ));
                }catch (\Exception $ex){
                    $_SESSION['error_message'] = $ex->getMessage();
                }
            }
            $this->data_view['manus'] = $manus->toArray();
            $this->data_view['cats'] = $cats;
            return $this->data_view;
        }catch (\Exception $ex){
            return $this->redirect()->toRoute('cms/manufacturers', array(
                'action' => 'manage-promotion',
            ));
        }
    }

    public function unpublishPromotionAction(){
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if($request->isPost()){
            $ids = $request->getPost('cid');
        }else{
            $id = $this->params()->fromRoute('id', NULL);
            if(!$id){
                $ids = array();
            }else{
                $id = (int)$id;
                $ids = array($id);
            }
        }
        try{
            $data['is_published'] = 0;
            $this->getModelTable('ManufacturersTable')->updatePromotion($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }catch (\Exception $ex){
        }
        return $this->redirect()->toRoute('cms/manufacturers', array(
            'action' => 'manage-promotion',
        ));
    }


    public function publishPromotionAction(){
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if($request->isPost()){
            $ids = $request->getPost('cid');
        }else{
            $id = $this->params()->fromRoute('id', NULL);
            if(!$id){
                $ids = array();
            }else{
                $id = (int)$id;
                $ids = array($id);
            }
        }
        try{
            $data['is_published'] = 1;
            $this->getModelTable('ManufacturersTable')->updatePromotion($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }catch (\Exception $ex){
        }
        return $this->redirect()->toRoute('cms/manufacturers', array(
            'action' => 'manage-promotion',
        ));
    }

    public function deletePromotionAction(){
        /**
         * @var $request \Zend\Http\Request
         */
        $request = $this->getRequest();
        if($request->isPost()){
            $ids = $request->getPost('cid');
        }else{
            $id = $this->params()->fromRoute('id', NULL);
            if(!$id){
                $ids = array();
            }else{
                $id = (int)$id;
                $ids = array($id);
            }
        }
        try{
            $this->getModelTable('ManufacturersTable')->deletePromotion($ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }catch (\Exception $ex){
        }
        return $this->redirect()->toRoute('cms/manufacturers', array(
            'action' => 'manage-promotion',
        ));
    }

}