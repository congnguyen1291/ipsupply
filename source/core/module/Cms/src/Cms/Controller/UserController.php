<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/23/14
 * Time: 2:07 PM
 */
namespace Cms\Controller;
use Cms\Form\UserForm;
use Cms\Model\User;
use Zend\View\Model\ViewModel;

use JasonGrimes\Paginator;

class UserController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'user';
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
                $params['full_name'] = $q;
            }else if( $type == 1 ){
                $params['address'] = $q;
            }else if( $type == 2 ){
                $params['phone'] = $q;
            }else if( $type == 3 ){
                $params['user_name'] = $q;
            }
        }

        $total = $this->getModelTable('UserTable')->countAll( $params );
        $users = $this->getModelTable('UserTable')->fetchAll( $params );

        $link = '/cms/user?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['id'] = $id;
        $this->data_view['total'] = $total;
        $this->data_view['users'] = $users;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new UserForm();
        $form->get('submit')->setValue('Thêm mới');
        $groups = $this->getModelTable('PermissionsTable')->fetchAllGroups( );
        $listPublisher = $this->getModelTable('MerchantTable')->getMerchants();
        $publisher = array(
                0 => 'Chọn đại lý'
            );
        foreach($listPublisher as $pub){
            $publisher[$pub['merchant_id']] = $pub['merchant_name'];
        }
        $form->get('merchant_id')->setOptions(array(
            'options' => $publisher
        ));
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $type = $request->getPost('type', '');
            $password = $request->getPost('password', '');
            $repassword = $request->getPost('repassword', '');
            $user_name = $request->getPost('user_name', '');
            $first_name = $request->getPost('first_name', '');
            $last_name = $request->getPost('last_name', '');
            $groups_id = $request->getPost('groups_id', 0);
            $merchant_id = $request->getPost('merchant_id', 0);
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($data);
            if( $form->isValid()
                && !empty($password) && $password == $repassword
                && !empty($first_name) && !empty($last_name) && !empty($user_name) ){
                $user->exchangeArray($data);
                if( $type == 'admin' ){
                    $user->type = 'admin';
                }else{
                    $user->is_administrator = 0;
                }

                if( empty($_SESSION['CMSMEMBER']['is_administrator']) ){
                    $user->is_administrator = 0;
                }else{
                    $user->groups_id = 0;
                }
                try {
                    $name = $first_name .' '. $last_name;
                    $user->full_name = $name;
                    $alias = $this->toAlias($name, '.');
                    $sum = $this->getModelTable('UserTable')->getUserByAlias($alias);
                    if( !empty($sum) ){
                        $alias.= '.'.max( (COUNT($sum)+1), 1);
                    }
                    $user->users_alias = $alias;
                    $id = $this->getModelTable('UserTable')->saveUser($user);
                } catch (\Exception $e ) {}
                return $this->redirect()->toRoute('cms/user');
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['groups'] = $groups;
        return $this->data_view;
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/user', array(
                'action' => 'add'
            ));
        }
        try {
            $user = $this->getModelTable('UserTable')->getUserById($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/user', array(
                'action' => 'index'
            ));
        }
        $form = new UserForm();
        $form->get('submit')->setValue('Cập nhật');
        $groups = $this->getModelTable('PermissionsTable')->fetchAllGroups();

        $listPublisher = $this->getModelTable('MerchantTable')->getMerchants();
        $publisher = array(
                0 => 'Chọn đại lý'
            );
        foreach($listPublisher as $pub){
            $publisher[$pub['merchant_id']] = $pub['merchant_name'];
        }
        $form->get('merchant_id')->setOptions(array(
            'options' => $publisher
        ));

        $form->bind($user);
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $type = $request->getPost('type', '');
            $password = $request->getPost('password', '');
            $repassword = $request->getPost('repassword', '');
            $user_name = $request->getPost('user_name', '');
            $first_name = $request->getPost('first_name', '');
            $last_name = $request->getPost('last_name', '');
            $groups_id = $request->getPost('groups_id', 0);
            $merchant_id = $request->getPost('merchant_id', 0);
            $user = new User();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($data);
            if( $form->isValid()
                && !empty($first_name) && !empty($last_name) && !empty($user_name) ){
                $user->exchangeArray($data);
                if( $type == 'admin' ){
                    $user->type = 'admin';
                }else{
                    $user->is_administrator = 0;
                }

                if( empty($_SESSION['CMSMEMBER']['is_administrator']) ){
                    $user->is_administrator = 0;
                }else{
                    $user->groups_id = 0;
                }
                $name = $first_name .' '. $last_name;
                $user->full_name = $name;
                $alias = $this->toAlias($name, '.');
                $sum = $this->getModelTable('UserTable')->getUserByAlias($alias);
                if( !empty($sum)
                    && (count($sum) > 1 || $sum[0]['users_id'] != $id) ){
                    $alias.= '.'.max( (COUNT($sum)+1), 1);
                }
                $user->users_alias = $alias;
                try {
                    $this->getModelTable('UserTable')->saveUser($user);
                } catch (\Exception $e ) {}
                return $this->redirect()->toRoute('cms/user');
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['user'] = $user;
        $this->data_view['groups'] = $groups;
        $this->data_view['id'] = $id;
        return $this->data_view;
    }

    public function profileAction(){
        $id = $_SESSION['CMSMEMBER']['users_id'];
        if (!$id) {
            return $this->redirect()->toRoute('cms/login', array(
                'action' => 'index'
            ));
        }
        try {
            $user = $this->getModelTable('UserTable')->getUserById($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/login', array(
                'action' => 'index'
            ));
        }
        $form = new UserForm();
        $form->get('submit')->setValue('Cập nhật');
        $cities = $this->getModelTable('UserTable')->loadCities();
        $groups = $this->getModelTable('PermissionsTable')->fetchAllGroups('', '', 0, 1000);
        $groups_user = array();
        foreach($groups as $group){
            $groups_user[$group['groups_id']] = $group['groups_name'];
        }
        $form->get('groups_id')->setOptions(array(
            'value_options' => $groups_user,
        ));
        $form->bind($user);
        $request = $this->getRequest();
        if($request->isPost()){
            $full_name = $request->getPost('full_name');
            $user_name = $request->getPost('user_name');
            $birthday = $request->getPost('birthday');
            $phone = $request->getPost('phone');
            $address = $request->getPost('address');
            $cities_id = $request->getPost('cities_id');
            $districts_id = $request->getPost('districts_id');
            if( !empty($full_name) && !empty($user_name) ){
                $users = $this->getModelTable('UserTable')->getUserByUsername($user_name);
                if( empty($users) 
                    || ( count($users) == 1 && $users[0]['users_id'] == $id  ) ){
                    $row = array(
                        'full_name' => $full_name,
                        'user_name' => $user_name,
                        'birthday' => $birthday,
                        'phone' => $phone,
                        'address' => $address,
                        'cities_id' => $cities_id,
                        'districts_id' => $districts_id,
                    );
                    $this->getModelTable('UserTable')->editUser($row, array('users_id' => $id));
                    return $this->redirect()->toRoute('cms/user', array(
                        'action' => 'profile'
                    ));
                }
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['user'] = $user;
        $this->data_view['cities'] = $cities;
        $this->data_view['id'] = $id;
        return $this->data_view;
    }

    public function editPasswordAction(){
        $id = $_SESSION['CMSMEMBER']['users_id'];
        if (!$id) {
            return $this->redirect()->toRoute('cms/login', array(
                'action' => 'index'
            ));
        }
        try {
            $user = $this->getModelTable('UserTable')->getUserById($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/login', array(
                'action' => 'index'
            ));
        }
        $form = new UserForm();
        $form->get('submit')->setValue('Cập nhật');
        $cities = $this->getModelTable('UserTable')->loadCities();
        $groups = $this->getModelTable('PermissionsTable')->fetchAllGroups('', '', 0, 1000);
        $groups_user = array();
        foreach($groups as $group){
            $groups_user[$group['groups_id']] = $group['groups_name'];
        }
        $form->get('groups_id')->setOptions(array(
            'value_options' => $groups_user,
        ));
        $form->bind($user);
        $request = $this->getRequest();
        if($request->isPost()){
            $password = $request->getPost('password');
            $newpassword = $request->getPost('newpassword');
            $repassword = $request->getPost('re_newpassword');
            if( !empty($password) 
                && !empty($newpassword)
                && $newpassword ==  $repassword ){
                $user_name = $_SESSION['CMSMEMBER']['user_name'];
                $user = $this->getModelTable('UserTable')->getUser($user_name, $password);
                if( !empty($user) && !empty($user->users_id)
                    && $user->users_id == $id ){
                    $row = array(
                        'password' => md5($newpassword),
                    );
                    $this->getModelTable('UserTable')->editUser($row, array('users_id' => $id));
                    $this->getModelTable('UserTable')->logout();
                    return $this->redirect()->toRoute('cms/login', array(
                        'action' => 'index'
                    ));
                }
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['user'] = $user;
        $this->data_view['cities'] = $cities;
        $this->data_view['id'] = $id;
        return $this->data_view;
    }

    public function filterAction(){
        $request = $this->getRequest();
        $params = array();
        if ($request->isPost()) {
            $params = $request->getPost('filter');
        }
        $data = $this->getModelTable('UserTable')->filter($params);
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'users' => $data,
        ));
        return $result;
    }

	public function loadCityAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $country_id = $request->getPost('country_id');
            $data = $this->getModelTable('UserTable')->loadCities($country_id);
            echo json_encode(array( 
                'success' => TRUE,
                'results' => $data
            ));
        }
        die();
    }
	public function loadDistrictAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $cities_id = $request->getPost('cities_id');
            $data=$this->getModelTable('UserTable')->loadDistrict($cities_id);
            echo json_encode(array(
                'success' => TRUE,
                'results' => $data
            ));
        }
        die();
    }

    public function loadWardAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $wards_id = $request->getPost('districts_id');
            $data = $this->getModelTable('UserTable')->loadWard($wards_id);
            echo json_encode(array(
                'success' => TRUE,
                'results' => $data
            ));
        }
        die();
    }

    public function loadCitiesByAreaAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $area_id = $request->getPost('area_id', '');
            if(!empty($area_id)){
                $data = $this->getModelTable('UserTable')->loadCitiesByArea($area_id);
                echo json_encode(array(
                    'success' => TRUE,
                    'results' => $data
                ));
                die();
            }
        }
        echo json_encode(array(
            'success' => FALSE
        ));
        die();
    }

    public function getUserAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $userid = (int)$request->getPost('users_id');
        }else{
            $userid = $this->params()->fromRoute('id', NULL);
        }
        if(!$userid){
            echo json_encode(array(
                'success' => FALSE,
                'msg' => 'Không có user'
            ));
            die();
        }
        try{
            $user = $this->getModelTable('UserTable')->getUserById($userid);
            echo json_encode(array(
                'success' => TRUE,
                'result' => $user,
            ));
            die();
        }catch (\Exception $ex){
            echo json_encode(array(
                'success' => FALSE,
                'msg' => 'Có lỗi xảy ra'
            ));
            die();
        }
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('UserTable')->deleteUsers($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('UserTable')->deleteUsers($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/user');
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
                $this->getModelTable('UserTable')->updateUsers($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('UserTable')->updateUsers($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/user');
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
                $this->getModelTable('UserTable')->updateUsers($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('UserTable')->updateUsers($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/user');
    }

    public function logsAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);
        $id = (int)$this->params()->fromRoute('id', 0);
        if( !empty($id) ){
            $params = array();
            $params['page'] = $page;
            $params['limit'] = $limit;
            $params['id'] = $id;

            $total = $this->getModelTable('UserTable')->countLogUser($params);
            $logs = $this->getModelTable('UserTable')->fetchLogUser( $params );

            $link = '/cms/user/logs/'.$id.'?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
            $paginator = new Paginator($total, $limit, $page, $link);

            $this->data_view['paging'] = $paginator->toHtml();
            $this->data_view['page'] = $page;
            $this->data_view['limit'] = $limit;
            $this->data_view['q'] = $q;
            $this->data_view['type'] = $type;
            $this->data_view['id'] = $id;
            $this->data_view['logs'] = $logs;
            return $this->data_view;
        }
        return $this->redirect()->toRoute('cms/user');
    }

    public function logAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);
        $id = $this->params()->fromQuery('id', 0);
        if( !empty($id) ){
            $params = array();
            $params['page'] = $page;
            $params['limit'] = $limit;
            $params['session_id'] = $id;

            $logs = $this->getModelTable('UserTable')->getLogUser( $params );

            $this->data_view['page'] = $page;
            $this->data_view['limit'] = $limit;
            $this->data_view['q'] = $q;
            $this->data_view['type'] = $type;
            $this->data_view['id'] = $id;
            $this->data_view['logs'] = $logs;
            return $this->data_view;
        }
        return $this->redirect()->toRoute('cms/user');
    }

    public function deleteLogAction()
    {
        $id = $this->params()->fromQuery('id', 0);
        if( !empty($id) ){
            $params = array('session_id' => $id );
            $logs = $this->getModelTable('UserTable')->deleteLog( $params );

        }
        return $this->redirect()->toRoute('cms/user');
    }

}