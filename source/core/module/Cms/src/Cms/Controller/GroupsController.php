<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\GroupsForm;
use Cms\Lib\Paging;
use Cms\Model\Groups;
use Zend\View\Model\ViewModel;

use JasonGrimes\Paginator;

class GroupsController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'group';
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
            $params['groups_name'] = $q;
        }

        $total = $this->getModelTable('PermissionsTable')->countAllGroups( $params );
        $groups = $this->getModelTable('PermissionsTable')->fetchAllGroups( $params );

        $link = '/cms/group?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['groups'] = $groups;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new GroupsForm();
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $g = new Groups();
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $g->exchangeArray($request->getPost());
                try {
                    $permits = $request->getPost('permits');
                    $this->getModelTable('PermissionsTable')->saveGroup($g, $permits);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/group');
                } catch (\Exception $ex) {
                    die($ex->getMessage());
                }
            }
        }

        $params = array();
        $params['order'] = array('module' => 'ASC', 'controller' => 'ASC', 'action' => 'ASC');
        $permissions = $this->getModelTable('PermissionsTable')->fetchAll( $params );
        $permissions_data = array();
        foreach($permissions as $permit){
            $permissions_data[$permit['module']][] = (array)$permit;
        }
        $this->data_view['form'] = $form;
        $this->data_view['permissions'] = $permissions_data;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/group', array(
                'action' => 'add'
            ));
        }
        try {
            $g = $this->getModelTable('PermissionsTable')->getGroup($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/group', array(
                'action' => 'index'
            ));
        }
        $form = new GroupsForm();
        $form->bind($g);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $permits = $request->getPost('permits');
                    $this->getModelTable('PermissionsTable')->saveGroup($g, $permits);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/group');
                } catch (\Exception $ex) {
                }
            }
        }
        $params = array();
        $params['order'] = array('module' => 'ASC', 'controller' => 'ASC', 'action' => 'ASC');
        $permissions = $this->getModelTable('PermissionsTable')->fetchAll( $params );
        $permissions_data = array();
        foreach($permissions as $permit){
            $permissions_data[$permit['module']][] = (array)$permit;
        }
        $group_permissions = $this->getModelTable('PermissionsTable')->getGroupPermissions($id);
        $group_access = array();
        foreach($group_permissions as $permit){
            $group_access[] = $permit['permissions_id'];
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['group'] = $g;
        $this->data_view['permissions'] = $permissions_data;
        $this->data_view['group_access'] = $group_access;
        return $this->data_view;
    }

    public function adminAction(){
        $users = $this->getModelTable('UserTable')->fetchAll($this->intPage, $this->intPageSize, '', array('type' => 'admin',"user_name != '{$_SESSION['CMSMEMBER']['user_name']}'"));
        $this->data_view['users'] = $users;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('PermissionsTable')->deleteGroups($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('PermissionsTable')->deleteGroups($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/group');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('PermissionsTable')->updateGroups($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('PermissionsTable')->updateGroups($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/group');
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
                $this->getModelTable('PermissionsTable')->updateGroups($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('PermissionsTable')->updateGroups($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/group');
    }

}