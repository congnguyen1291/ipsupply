<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\CouponsForm;
use Cms\Lib\Paging;
use Cms\Model\Coupons;
use Zend\View\Model\ViewModel;

use JasonGrimes\Paginator;

class CouponsController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'coupons';
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
                $params['coupons_code'] = $q;
            }else if( $type == 1 ){
                $params['coupon_price'] = $q;
            }
        }

        $total = $this->getModelTable('CouponsTable')->countAll( $params );
        $coupons = $this->getModelTable('CouponsTable')->fetchAll( $params );

        $link = '/cms/coupons?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['coupons'] = $coupons;
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new CouponsForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $g = new Coupons();
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $g->exchangeArray($request->getPost());
                try {
                    $this->getModelTable('CouponsTable')->saveCoupons($g);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/coupons');
                } catch (\Exception $ex) {}
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/coupons', array(
                'action' => 'add'
            ));
        }
        try {
            $coupons = $this->getModelTable('CouponsTable')->getCoupon($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/coupons', array(
                'action' => 'index'
            ));
        }
        $form = new CouponsForm();
        $form->bind($coupons);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($coupons->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $this->getModelTable('CouponsTable')->saveCoupons($coupons);
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/coupons');
                } catch (\Exception $ex) {}
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['coupons'] = $coupons;
        return $this->data_view;
    }

    public function autoAddAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            try {
                $this->getModelTable('CouponsTable')->autoAdd($data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            } catch (\Exception $ex) {}
        }
        return $this->redirect()->toRoute('cms/coupons');
    }

    public function showLogsAction(){
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id');

        if(!$id){
            return $this->redirect()->toRoute('cms/coupons');
        }
        try {
            $coupons = $this->getModelTable('CouponsTable')->getCoupon($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/coupons', array(
                'action' => 'index'
            ));
        }
        try{
            $params = array();
            $params['page'] = $page;
            $params['limit'] = $limit;
            $params['coupon_id'] = $id;

            $total = $this->getModelTable('CouponsTable')->getTotalLogsCoupon($params);
            $logs = $this->getModelTable('CouponsTable')->getLogsCoupon($params);
            
            $link = '/cms/coupons/show-logs?page=(:num)';
            $paginator = new Paginator($total, $limit, $page, $link);

            $this->data_view['paging'] = $paginator->toHtml();
            $this->data_view['logs'] = $logs;
            $this->data_view['page'] = $page;
            $this->data_view['limit'] = $limit;
            $this->data_view['coupons'] = $coupons;
            return $this->data_view;
        }catch (\Exception $ex){}
        return $this->redirect()->toRoute('cms/coupons');
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('CouponsTable')->deleteCoupons($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('CouponsTable')->deleteCoupons($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/coupons');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('CouponsTable')->updateCoupons($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('CouponsTable')->updateCoupons($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/coupons');
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('CouponsTable')->updateCoupons($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                try {
                    $this->getModelTable('CouponsTable')->updateCoupons($id, $data);
                } catch (\Exception $e) {}
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/coupons');
    }

    public function filterAction()
    {
        $request = $this->getRequest();
        $params = array();
        if ($request->isPost()) {
            $params = $request->getPost();
        }
        $data = $this->getModelTable('CouponsTable')->filter($params);
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'coupons' => $data,
        ));
        return $result;
    }
}