<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\PermissionsForm;
use Cms\Form\PermisstionsForm;
use Cms\Lib\Paging;
use Cms\Model\Permissions;
use Zend\View\Model\ViewModel;

use JasonGrimes\Paginator;

class PermissionsController extends BackEndController
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
            $params['permissions_name'] = $q;
        }

        $total = $this->getModelTable('PermissionsTable')->countAll( $params );
        $permissions = $this->getModelTable('PermissionsTable')->fetchAll( $params );

        $link = '/cms/permission?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['permissions'] = $permissions;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new PermissionsForm();
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $p = new Permissions();
            $form->setInputFilter($p->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $p->exchangeArray($request->getPost());
                try {
                    $this->getModelTable('PermissionsTable')->savePermission($p);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/permission');
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
            return $this->redirect()->toRoute('cms/permission', array(
                'action' => 'add'
            ));
        }
        try {
            $p = $this->getModelTable('PermissionsTable')->getPermission($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/permission', array(
                'action' => 'index'
            ));
        }
        $form = new PermissionsForm();
        $form->bind($p);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($p->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $this->getModelTable('PermissionsTable')->savePermission($p);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/permission');
                } catch (\Exception $ex) {}
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['permission'] = $p;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('PermissionsTable')->deletePermissions($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('PermissionsTable')->deletePermissions($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/permission');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('PermissionsTable')->updatePermissions($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('PermissionsTable')->updatePermissions($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/permission');
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
                $this->getModelTable('PermissionsTable')->updatePermissions($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('PermissionsTable')->updatePermissions($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/permission');
    }

}