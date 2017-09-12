<?php
namespace Cms\Controller;


use Cms\Form\MerchantForm;
use Cms\Form\CommissionForm;
use Cms\Model\Merchant;
use Cms\Model\Commission;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Location\Coordinate;
use Location\Distance\Vincenty;
use JasonGrimes\Paginator;

class MerchantController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'merchant';
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

        $total = $this->getModelTable('MerchantTable')->getTotalMerchants($params);
        $merchants = $this->getModelTable('MerchantTable')->getMerchants($params);

        $link = '/cms/merchant?page=(:num)';
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['id'] = $id;
        $this->data_view['total'] = $total;
        $this->data_view['merchants'] = $merchants;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function autoLongLatAction()
    {
        $merchants = $this->getModelTable('MerchantTable')->getMerchants();
        foreach ($merchants as $key => $merchant) {
            if( empty($merchant['longitude'])
                || empty($merchant['latitude']) ){
                $address = $merchant['address'];
                $prepAddr = str_replace(' ','+',$address);
                $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
                $longitude = 105.849998;
                $latitude = 21.033333;
                try{
                    $output= json_decode($geocode);
                    if( !empty($output) 
                        && !empty($output->results) 
                        && !empty($output->results[0]) ){
                        $latitude = $output->results[0]->geometry->location->lat;
                        $longitude = $output->results[0]->geometry->location->lng;
                    }
                }catch (\Exception $ex){}
                $row = array(
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                );
                $this->getModelTable('MerchantTable')->updateMerchant($merchant['merchant_id'], $row);
            }
        }
        return $this->redirect()->toRoute('cms/merchant');
    }

    public function usersAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'add'
            ));
        }
        try {
            $merchant = $this->getModelTable('MerchantTable')->getMerchant( array('merchant_id' => $id) );
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'index'
            ));
        }
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;
        $params['merchant_id'] = $id;

        $total = $this->getModelTable('MerchantTable')->getTotalUsers($params);
        $users = $this->getModelTable('MerchantTable')->getUsers($params);

        $link = '/cms/merchant/users?page=(:num)';
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['id'] = $id;
        $this->data_view['total'] = $total;
        $this->data_view['users'] = $users;
        $this->data_view['merchant'] = $merchant;
        return $this->data_view;
    }

    public function commissionAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'add'
            ));
        }
        try {
            $merchant = $this->getModelTable('MerchantTable')->getMerchant( array('merchant_id' => $id) );
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'index'
            ));
        }
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;
        $params['merchant_id'] = $id;

        $total = $this->getModelTable('MerchantTable')->getTotalCommissions($params);
        $commissions = $this->getModelTable('MerchantTable')->getCommissions($params);

        $link = '/cms/merchant/commission?page=(:num)';
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['id'] = $id;
        $this->data_view['total'] = $total;
        $this->data_view['commissions'] = $commissions;
        $this->data_view['merchant'] = $merchant;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new MerchantForm();
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $merchant = new Merchant();
            $form->setInputFilter($merchant->getInputFilter());
            $form->setData($data);
            if( $form->isValid() ){
                $merchant->exchangeArray($data);
                try {
                    $merchant_name = $request->getPost('merchant_name', '');
                    $alias = $this->toAlias($merchant_name, '.');
                    $sum = $this->getModelTable('MerchantTable')->getMerchants( array('merchant_alias' => $alias) );
                    if( !empty($sum) ){
                        $alias.= '.'.max( (COUNT($sum)+1), 1);
                    }
                    $merchant->merchant_alias = $alias;
                    $id = $this->getModelTable('MerchantTable')->createMerchant($merchant);
                    return $this->redirect()->toRoute('cms/merchant');
                } catch (\Exception $e ) {}
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'add'
            ));
        }
        try {
            $merchant = $this->getModelTable('MerchantTable')->getMerchant( array('merchant_id' => $id) );
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'index'
            ));
        }
        $form = new MerchantForm();
        $form->get('submit')->setValue('Cập nhật');
        $form->bind($merchant);
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $merchant = new Merchant();
            $form->setInputFilter($merchant->getInputFilter());
            $form->setData($data);
            if( $form->isValid() ){
                $merchant->exchangeArray($data);
                try {
                    $merchant_name = $request->getPost('merchant_name', '');
                    $alias = $this->toAlias($merchant_name, '.');
                    $sum = $this->getModelTable('MerchantTable')->getMerchants( array('merchant_alias' => $alias) );
                    if( !empty($sum)
                        && (count($sum) > 1 || $sum[0]['merchant_id'] != $id) ){
                        $alias.= '.'.max( (COUNT($sum)+1), 1);
                    }
                    $merchant->merchant_alias = $alias;
                    $id = $this->getModelTable('MerchantTable')->createMerchant($merchant);
                    return $this->redirect()->toRoute('cms/merchant');
                } catch (\Exception $e ) {}
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['merchant'] = $merchant;
        $this->data_view['id'] = $id;
        return $this->data_view;
    }

    public function addCommissionAction()
    {
        $form = new CommissionForm();
        $listPublisher = $this->getModelTable('MerchantTable')->getMerchants();
        $publisher = array();
        foreach($listPublisher as $pub){
            $publisher[$pub['merchant_id']] = $pub['merchant_name'];
        }
        $form->get('merchant_id')->setOptions(array(
            'options' => $publisher
        ));
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $commission = new Commission();
            $form->setInputFilter($commission->getInputFilter());
            $form->setData($data);
            if( $form->isValid() ){
                $commission->exchangeArray($data);
                try {
                    $id = $this->getModelTable('CommissionTable')->saveCommission($commission);
                    if( !empty($commission->merchant_id) ){
                        return $this->redirect()->toRoute('cms/merchant', array(
                            'action' => 'commission',
                            'id' => $commission->merchant_id
                        ));
                    }else{
                        return $this->redirect()->toRoute('cms/merchant');
                    }
                } catch (\Exception $e ) {}
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editCommissionAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'addCommission'
            ));
        }
        try {
            $commission = $this->getModelTable('CommissionTable')->getCommission( array('commission_id' => $id) );
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'commission'
            ));
        }
        $form = new CommissionForm();
        $listPublisher = $this->getModelTable('MerchantTable')->getMerchants();
        $publisher = array();
        foreach($listPublisher as $pub){
            $publisher[$pub['merchant_id']] = $pub['merchant_name'];
        }
        $form->get('merchant_id')->setOptions(array(
            'options' => $publisher
        ));
        $form->get('submit')->setValue('Cập nhật');
        $form->bind($commission);
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $commission = new Commission();
            $form->setInputFilter($commission->getInputFilter());
            $form->setData($data);
            if( $form->isValid() ){
                $commission->exchangeArray($data);
                try {
                    $id = $this->getModelTable('CommissionTable')->saveCommission($commission);
                    if( !empty($commission->merchant_id) ){
                        return $this->redirect()->toRoute('cms/merchant', array(
                            'action' => 'commission',
                            'id' => $commission->merchant_id
                        ));
                    }else{
                        return $this->redirect()->toRoute('cms/merchant');
                    }
                } catch (\Exception $e ) {}
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['commission'] = $commission;
        $this->data_view['id'] = $id;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('MerchantTable')->deleteMerchant($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('MerchantTable')->deleteMerchant($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/merchant');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );

            if ( !empty($ids) ) {
                $this->getModelTable('MerchantTable')->updateMerchant($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('MerchantTable')->updateMerchant($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/merchant');
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            if ( !empty($ids) ) {
                $this->getModelTable('MerchantTable')->updateMerchant($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }

        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('MerchantTable')->updateMerchant($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/merchant');
    }

    public function deleteUserAction()
    {
        $merchant = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $merchants = $this->getModelTable('MerchantTable')->getUsers( array('id' => $id) );
            if( !empty($merchants) ){
                $merchant = $merchants[0];
                $this->getModelTable('MerchantTable')->deleteUsers($ids);
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $merchant = $this->getModelTable('MerchantTable')->getUser( array('id' => $id) );
                if( !empty($merchant) ){
                    $this->getModelTable('MerchantTable')->deleteUsers($id);
                    $this->updateNamespaceCached();
                }
            }
        }
        if( empty($merchant) ){
            return $this->redirect()->toRoute('cms/merchant');
        }else{
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'users',
                'id' => $merchant['merchant_id']
            ));
        }
    }

    public function publishUserAction()
    {
        $merchant = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            if ( !empty($ids) ) {
                $merchants = $this->getModelTable('MerchantTable')->getUsers( array('id' => $id) );
                if( !empty($merchants) ){
                    $this->getModelTable('MerchantTable')->updateUsers($ids, $data);
                    $this->updateNamespaceCached();
                }
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            $data = array(
                'is_published' => 1
            );
            if ( !empty($id) ) {
                $merchant = $this->getModelTable('MerchantTable')->getUser( array('id' => $id) );
                if( !empty($merchant) ){
                    $this->getModelTable('MerchantTable')->updateUsers($id, $data);
                    $this->updateNamespaceCached();
                }
            }
        }
        if( empty($merchant) ){
            return $this->redirect()->toRoute('cms/merchant');
        }else{
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'users',
                'id' => $merchant['merchant_id']
            ));
        }
    }

    public function unpublishUserAction()
    {
        $merchant = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            if ( !empty($ids) ) {
                $merchants = $this->getModelTable('MerchantTable')->getUsers( array('id' => $id) );
                if( !empty($merchants) ){
                    $this->getModelTable('MerchantTable')->updateUsers($ids, $data);
                    $this->updateNamespaceCached();
                }
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            $data = array(
                'is_published' => 0
            );
            if ( !empty($id) ) {
                $merchant = $this->getModelTable('MerchantTable')->getUser( array('id' => $id) );
                if( !empty($merchant) ){
                    $this->getModelTable('MerchantTable')->updateUsers($id, $data);
                    $this->updateNamespaceCached();
                }
            }
        }
        if( empty($merchant) ){
            return $this->redirect()->toRoute('cms/merchant');
        }else{
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'users',
                'id' => $merchant['merchant_id']
            ));
        }
    }

    public function deleteCommissionAction()
    {
        $merchant = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $merchants = $this->getModelTable('MerchantTable')->getCommissions( array('commission_id' => $id) );
            if( !empty($merchants) ){
                $merchant = $merchants[0];
                $this->getModelTable('MerchantTable')->deleteCommissions($ids);
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $merchant = $this->getModelTable('MerchantTable')->getCommission( array('commission_id' => $id) );
                if( !empty($merchant) ){
                    $this->getModelTable('MerchantTable')->deleteCommissions($id);
                    $this->updateNamespaceCached();
                }
            }
        }
        if( empty($merchant) ){
            return $this->redirect()->toRoute('cms/merchant');
        }else{
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'commission',
                'id' => $merchant['merchant_id']
            ));
        }
    }

    public function publishCommissionAction()
    {
        $merchant = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            if ( !empty($ids) ) {
                $merchants = $this->getModelTable('MerchantTable')->getCommissions( array('commission_id' => $id) );
                if( !empty($merchants) ){
                    $this->getModelTable('MerchantTable')->updateCommissions($ids, $data);
                    $this->updateNamespaceCached();
                }
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            $data = array(
                'is_published' => 1
            );
            if ( !empty($id) ) {
                $merchant = $this->getModelTable('MerchantTable')->getCommission( array('commission_id' => $id) );
                if( !empty($merchant) ){
                    $this->getModelTable('MerchantTable')->updateCommissions($id, $data);
                    $this->updateNamespaceCached();
                }
            }
        }
        if( empty($merchant) ){
            return $this->redirect()->toRoute('cms/merchant');
        }else{
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'commission',
                'id' => $merchant['merchant_id']
            ));
        }
    }

    public function unpublishCommissionAction()
    {
        $merchant = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            if ( !empty($ids) ) {
                $merchants = $this->getModelTable('MerchantTable')->getCommissions( array('commission_id' => $id) );
                if( !empty($merchants) ){
                    $this->getModelTable('MerchantTable')->updateCommissions($ids, $data);
                    $this->updateNamespaceCached();
                }
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            $data = array(
                'is_published' => 0
            );
            if ( !empty($id) ) {
                $merchant = $this->getModelTable('MerchantTable')->getCommission( array('commission_id' => $id) );
                if( !empty($merchant) ){
                    $this->getModelTable('MerchantTable')->updateCommissions($id, $data);
                    $this->updateNamespaceCached();
                }
            }
        }
        if( empty($merchant) ){
            return $this->redirect()->toRoute('cms/merchant');
        }else{
            return $this->redirect()->toRoute('cms/merchant', array(
                'action' => 'commission',
                'id' => $merchant['merchant_id']
            ));
        }
    }

    public function updateorderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('order');
            if ( !empty($ids) ) {
                $this->getModelTable('MerchantTable')->updateOrder($data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/merchant');
    }

}