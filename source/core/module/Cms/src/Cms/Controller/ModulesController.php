<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\ModulesForm;
use Cms\Lib\Paging;
use Cms\Model\Modules;
use Zend\View\Model\ViewModel;

class ModulesController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'modules';
    }

    public function indexAction()
    {
        $total = $this->getModelTable('ModulesTable')->countAll();
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        $link = "";
        $objPage = new Paging($total, $page, $page_size, $link);
        $paging = $objPage->getListFooter($link);
        $modules = $this->getModelTable('ModulesTable')->fetchAll('', '', $this->intPage, $this->intPageSize);
        $this->data_view['modules'] = $modules;
        $this->data_view['paging'] = $paging;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new ModulesForm();
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $g = new Modules();
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $g->exchangeArray($request->getPost());
                try {
                    $permits = $request->getPost('permits');
                    $apis = $request->getPost('apis');
                    $this->getModelTable('ModulesTable')->saveModule($g, $permits, $apis);
                    return $this->redirect()->toRoute('cms/modules');
                } catch (\Exception $ex) {
                    die($ex->getMessage());
                }

            }
        }

        $permissions = $this->getModelTable('PermissionsTable')->fetchAll('', array('module' => 'ASC', 'controller' => 'ASC', 'action' => 'ASC'), 0, 1000);
        $permissions_data = array();
        foreach($permissions as $permit){
            $permissions_data[$permit['module']][] = (array)$permit;
        }
        $apis = $this->getModelTable('ApiTable')->fetchAll('', array('api_module' => 'ASC', 'api_class' => 'ASC', 'api_function' => 'ASC'), 0, 1000);
        $api_data = array();
        foreach($apis as $api){
            $api_data[$api['api_module']][] = (array)$api;
        }
        $this->data_view['form'] = $form;
        $this->data_view['permissions'] = $permissions_data;
        $this->data_view['api'] = $api_data;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/modules', array(
                'action' => 'add'
            ));
        }
        try {
            $g = $this->getModelTable('ModulesTable')->getModule($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/modules', array(
                'action' => 'index'
            ));
        }
        $form = new ModulesForm();
        $form->bind($g);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $permits = $request->getPost('permits');
                    $apis = $request->getPost('apis');
                    $this->getModelTable('ModulesTable')->saveModule($g, $permits, $apis);
                    return $this->redirect()->toRoute('cms/modules');
                } catch (\Exception $ex) {
                }
            }
        }
        $permissions = $this->getModelTable('PermissionsTable')->fetchAll('', array('module' => 'ASC', 'controller' => 'ASC', 'action' => 'ASC'), 0, 1000);
        $permissions_data = array();
        foreach($permissions as $permit){
            $permissions_data[$permit['module']][] = (array)$permit;
        }
        $module_permissions = $this->getModelTable('PermissionsTable')->getModulePermissions($id);
        $module_access = array();
        foreach($module_permissions as $permit){
            $module_access[] = $permit['permissions_id'];
        }

        $apis = $this->getModelTable('ApiTable')->fetchAll('', array('api_module' => 'ASC', 'api_class' => 'ASC', 'api_function' => 'ASC'), 0, 1000);
        $api_data = array();
        foreach($apis as $api){
            $api_data[$api['api_module']][] = (array)$api;
        }

        $module_api = $this->getModelTable('ApiTable')->getModuleApi($id);
        $api_access = array();
        foreach($module_api as $api){
            $api_access[] = $api['api_id'];
        }

        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['group'] = $g;
        $this->data_view['permissions'] = $permissions_data;
        $this->data_view['module_access'] = $module_access;
        $this->data_view['api'] = $api_data;
        $this->data_view['api_access'] = $api_access;
        return $this->data_view;
    }

    public function adminAction(){
        $users = $this->getModelTable('UserTable')->fetchAll($this->intPage, $this->intPageSize, '', array('type' => 'admin',"user_name != '{$_SESSION['CMSMEMBER']['user_name']}'"));
        $this->data_view['users'] = $users;
        return $this->data_view;
    }

}