<?php
namespace Cms\Controller;


use Cms\Form\BannersForm;
use Cms\Model\Banners;

use JasonGrimes\Paginator;

class BannersController extends BackEndController{
    public function __construct(){
        parent::__construct();
        $this->data_view['current'] = 'banners';
    }

    public function indexAction(){
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);
        $position_id = $this->params()->fromQuery('position_id', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        if( !empty($q) ){
            $params['banners_title'] = $q;
        }

        $position = array();
        if( !empty($position_id) ){
            $params['position_id'] = $position_id;
            $position = $this->getModelTable('BannerPositionTable')->getBannerPosition($position_id);
        }

        $total = $this->getModelTable('BannersTable')->countAll($params);
        $banners = $this->getModelTable('BannersTable')->fetchAll($params);

        $link = '/cms/banners?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '').( !empty($position_id) ? '&position_id='.$position_id : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['banners'] = $banners;
        $this->data_view['position'] = $position;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction(){
        $form = new BannersForm();

        $positions = $this->getModelTable('BannersTable')->getPositions();
        //$type_banners = $this->getModelTable('BannersTable')->getTypeBanners();
        //$size_banners = $this->getModelTable('BannersTable')->getSizeBanners();

        $positions_data = array();
        foreach($positions as $p){
            $positions_data[$p['position_id']] = $p['position_name'];
        }
        $form->get('position_id')->setOptions(array(
            'value_options' => $positions_data,
        ));

        $request = $this->getRequest();
        if($request->isPost()){
            $banner = new Banners();
            $form->setInputFilter($banner->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                $data = $form->getData();
                try{
                    $picture_id = $request->getPost('picture_id', '');
                    $banner->exchangeArray($data);
                    $this->getModelTable('BannersTable')->saveBanner($banner, $picture_id);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/banners');
                }catch(\Exception $e){
                    echo $e->getMessage();die();
                }
            }else{
                echo $form->getMessage();die('fom');
            }
        }

        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction(){
        $id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/banners',array('action' => 'add'));
        }
        try{
            $banner = $this->getModelTable('BannersTable')->getBanner($id);
        }catch (\Exception $ex){
            return $this->redirect()->toRoute('cms/banners');
        }
        $form = new BannersForm();

        $positions = $this->getModelTable('BannersTable')->getPositions();
        //$type_banners = $this->getModelTable('BannersTable')->getTypeBanners();
        //$size_banners = $this->getModelTable('BannersTable')->getSizeBanners();

        $positions_data = array();
        foreach($positions as $p){
            $positions_data[$p['position_id']] = $p['position_name'];
        }
        $form->get('position_id')->setOptions(array(
            'value_options' => $positions_data,
        ));
        
        $form->bind($banner);
        $request = $this->getRequest();
        if($request->isPost()){
            $form->setInputFilter($banner->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                try{
                    $picture_id = $request->getPost('picture_id', '');
                    $this->getModelTable('BannersTable')->saveBanner($banner, $picture_id);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/banners');
                }catch(\Exception $ex){
                    die($ex->getMessage());
                }
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['id'] = $id;
        $this->data_view['banner'] = $banner;

        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('BannersTable')->deleteBanners($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('BannersTable')->deleteBanners($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/banners');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('BannersTable')->updateBanners($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('BannersTable')->updateBanners($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/banners');
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
                $this->getModelTable('BannersTable')->updateBanners($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('BannersTable')->updateBanners($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/banners');
    }

}