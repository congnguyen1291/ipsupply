<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\PackForm;
use Cms\Lib\Paging;
use Cms\Model\Pack;
use Zend\View\Model\ViewModel;

class PackController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'pack';
    }

    public function indexAction()
    {
        $total = $this->getModelTable('PackTable')->countAll();
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        $link = "";
        $objPage = new Paging($total, $page, $page_size, $link);
        $paging = $objPage->getListFooter($link);
        $packs = $this->getModelTable('PackTable')->fetchAll('', '', $this->intPage, $this->intPageSize);
        $this->data_view['packs'] = $packs;
        $this->data_view['paging'] = $paging;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new PackForm();
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $p = new Pack();
            $form->setInputFilter($p->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $p->exchangeArray($request->getPost());
                try {
                    $modules = $request->getPost('modules');
                    $this->getModelTable('PackTable')->savePack($p, $modules);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/pack');
                } catch (\Exception $ex) {
                    die($ex->getMessage());
                }

            }
        }
        $modules = $this->getModelTable('ModulesTable')->fetchAll('', array('date_create' => 'ASC', 'date_update' => 'ASC'), 0, 1000);
        $this->data_view['form'] = $form;
        $this->data_view['modules'] = $modules;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/pack', array(
                'action' => 'add'
            ));
        }
        try {
            $p = $this->getModelTable('PackTable')->getPack($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/pack', array(
                'action' => 'index'
            ));
        }
        $form = new PackForm();
        $form->bind($p);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($p->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $modules = $request->getPost('modules');
                    $this->getModelTable('PackTable')->savePack($p, $modules);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    
                    return $this->redirect()->toRoute('cms/pack');
                } catch (\Exception $ex) {
                }
            }
        }
        $modules = $this->getModelTable('ModulesTable')->fetchAll('', array('date_create' => 'ASC', 'date_update' => 'ASC'), 0, 1000);
        $pack_modules = $this->getModelTable('ModulesTable')->getPackModule($id);
        $module_access = array();
        foreach($pack_modules as $pack){
            $module_access[] = $pack['module_id'];
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['pack'] = $p;
        $this->data_view['modules'] = $modules;
        $this->data_view['module_access'] = $module_access;
        return $this->data_view;
    }

}