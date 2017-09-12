<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\ShippingForm;
use Cms\Model\Shipping;
use Cms\Lib\Paging;
use Zend\View\Model\ViewModel;

use JasonGrimes\Paginator;

class ShippingController extends BackEndController
{
    public $shipping_type = array(0 => 'Theo hóa đơn',1 => 'Số lượng',2=>'Khối lượng' );

    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'shipping';
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
            $params['shipping_title'] = $q;
        }

        $languages=  $this->getModelTable('LanguagesTable')->getLanguages();
        $total = $this->getModelTable('ShippingTable')->getTotalShippings($params);
        $shippings = $this->getModelTable('ShippingTable')->getShippings($params);

        $link = '/cms/shipping?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['shippings'] = $shippings;
        $this->data_view['limit'] = $limit;
        $this->data_view['page'] = $page;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['languages'] = $languages;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function listAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id', 0);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/shipping', array(
                'action' => 'add'
            ));
        }
        $GroupsRegions = $this->getModelTable('GroupsRegionsTable')->one($id);
        if( !empty($GroupsRegions) ){
            $params = array();
            $params['page'] = $page;
            $params['limit'] = $limit;
            $params['group_regions_id'] = $id;

            if( !empty($q) ){
                $params['shipping_title'] = $q;
            }

            $languages=  $this->getModelTable('LanguagesTable')->getLanguages();
            $shippings = $this->getModelTable('ShippingTable')->getShippings($params);
            $total = $this->getModelTable('ShippingTable')->getTotalShippings($params);

            $link = '/cms/shipping' .( !empty($id) ? '/index/'.$id : ''). '?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
            $paginator = new Paginator($total, $limit, $page, $link);

            $this->data_view['GroupsRegions'] = $GroupsRegions;
            $this->data_view['shippings'] = $shippings;
            $this->data_view['limit'] = $limit;
            $this->data_view['page'] = $page;
            $this->data_view['paging'] = $paginator->toHtml();
            $this->data_view['languages'] = $languages;
            $this->data_view['id'] = $id;
            $this->data_view['q'] = $q;
            $this->data_view['type'] = $type;
            return $this->data_view;
        }else{
            return $this->redirect()->toRoute('cms/shipping', array(
                'action' => 'add'
            ));
        }
    }

    public function addAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        $form = new ShippingForm();
        $groups = $this->getModelTable('GroupsRegionsTable')->getGroupsRegions();
        $optionsGroup = array();
        foreach ($groups as $key => $group) {
            $optionsGroup[$group['group_regions_id']] = $group['group_regions_name'];
        }
        $form->get('group_regions_id')->setOptions(array(
            'options' => $optionsGroup
        ));
        $form->get('shipping_type')->setOptions(array(
            'options' => $this->shipping_type
        ));
        if( !empty($id) ){
            $form->get('group_regions_id')->setValue($id);
        }
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ship = new Shipping();
            $form->setInputFilter($ship->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $ship->exchangeArray($request->getPost());
                $districts = $request->getPost('datas', array());
                try {
                    $this->getModelTable('ShippingTable')->saveWithDistricts($ship, $districts);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/shipping');
                } catch (\Exception $ex) {
                    die($ex->getMessage());
                }

            }else{
                print_r($form->getMessages());die();
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/shipping', array(
                'action' => 'add'
            ));
        }
        try {
            $ship = $this->getModelTable('ShippingTable')->getShipping($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/shipping', array(
                'action' => 'index'
            ));
        }
        $form = new ShippingForm();
        $groups = $this->getModelTable('GroupsRegionsTable')->getGroupsRegions();
        $optionsGroup = array();
        foreach ($groups as $key => $group) {
            $optionsGroup[$group['group_regions_id']] = $group['group_regions_name'];
        }
        $form->get('group_regions_id')->setOptions(array(
            'options' => $optionsGroup
        ));
        $form->get('shipping_type')->setOptions(array(
            'options' => $this->shipping_type
        ));
        $form->bind($ship);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($ship->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $ship->exchangeArray($request->getPost());
                $districts = $request->getPost('datas', array());
                try {
                    $this->getModelTable('ShippingTable')->saveWithDistricts($ship, $districts);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/shipping');
                } catch (\Exception $ex) {
                    //die($ex->getMessage());
                }
            }
        }
        $districtsShippings = array();
        $disShips = $this->getModelTable('ShippingTable')->getDistrictsShippings($id);
        foreach ($disShips as $key => $disShip) {
            $districtsShippings[$disShip['districts_id']] = $disShip;
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['ship'] = $ship;
        $this->data_view['districtsShip'] = $districtsShippings;
        return $this->data_view;
    }

    public function deleteAction(){
        $request = $this->getRequest();
        $id = (int)$this->params()->fromRoute('id', 0);
        if($request->isPost()){
            $cid = $request->getPost('cid');
            try{
                $this->getModelTable('ShippingTable')->delete(array('shipping_id' => $cid, 'website_id' => $_SESSION['CMSMEMBER']['website_id']));
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }catch(\Exception $ex){
            }
        }
        if( empty($id) ){
            return $this->redirect()->toRoute('cms/shipping');
        }else{
            return $this->redirect()->toRoute('cms/shipping', array(
                    'action' => 'list',
                    'id' => $id,
                ));
        }
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('ShippingTable')->updateShipping($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('ShippingTable')->updateShipping($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/shipping');
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('ShippingTable')->updateShipping($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('ShippingTable')->updateShipping($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/shipping');
    }

    public function autoOrderAction()
    {
        $shippings = $this->getModelTable('ShippingTable')->getShippings();
        foreach ($shippings as $key => $shipping) {
            $row = array();
            $row['ordering'] = $key;
            $this->getModelTable('ShippingTable')->updateShipping($shipping['shipping_id'], $row);
        }
        /*strigger change namespace cached*/
        $this->updateNamespaceCached();
        return $this->redirect()->toRoute('cms/shipping', array(
                'action' => 'index'
            ));
    }

    public function updateorderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('ordering');
            $this->getModelTable('ShippingTable')->updateOrder($data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }
        return $this->redirect()->toRoute('cms/shipping');
    }

}