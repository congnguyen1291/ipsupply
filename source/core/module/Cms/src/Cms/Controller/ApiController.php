<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\ApiForm;
use Cms\Lib\Paging;
use Cms\Model\Api;
use Zend\View\Model\ViewModel;

class ApiController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'api';
    }

    public function indexAction()
    {
        $total = $this->getModelTable('ApiTable')->countAll();
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        $link = "";
        $objPage = new Paging($total, $page, $page_size, $link);
        $paging = $objPage->getListFooter($link);
        $api = $this->getModelTable('ApiTable')->fetchAll('', '', $this->intPage, $this->intPageSize);
        $this->data_view['api'] = $api;
        $this->data_view['paging'] = $paging;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new ApiForm();
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $g = new Api();
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $g->exchangeArray($request->getPost());
                try {
                    $this->getModelTable('ApiTable')->saveApi($g);
                    return $this->redirect()->toRoute('cms/api');
                } catch (\Exception $ex) {
                    die($ex->getMessage());
                }

            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/api', array(
                'action' => 'add'
            ));
        }
        try {
            $g = $this->getModelTable('ApiTable')->getApi($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/api', array(
                'action' => 'index'
            ));
        }
        $form = new ApiForm();
        $form->bind($g);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $this->getModelTable('ApiTable')->saveApi($g);
                    return $this->redirect()->toRoute('cms/api');
                } catch (\Exception $ex) {
                }
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['api'] = $g;
        return $this->data_view;
    }

}