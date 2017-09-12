<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\BannerSizeForm;
use Cms\Lib\Paging;
use Cms\Model\BannerSize;
use Zend\View\Model\ViewModel;

class BannerSizeController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'banner-size';
    }

    public function indexAction()
    {
        $total = $this->getModelTable('BannerSizeTable')->countAll();
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        $link = "";
        $objPage = new Paging($total, $page, $page_size, $link);
        $paging = $objPage->getListFooter($link);
        $sizes = $this->getModelTable('BannerSizeTable')->fetchAll('', '', $this->intPage, $this->intPageSize);
        $this->data_view['sizes'] = $sizes;
        $this->data_view['paging'] = $paging;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new BannerSizeForm();
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $g = new BannerSize();
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $g->exchangeArray($request->getPost());
                try {
                    $this->getModelTable('BannerSizeTable')->saveBannerSize($g);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/BannerSize');
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
            return $this->redirect()->toRoute('cms/BannerSize', array(
                'action' => 'add'
            ));
        }
        try {
            $g = $this->getModelTable('BannerSizeTable')->getBannerSize($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/BannerSize', array(
                'action' => 'index'
            ));
        }
        $form = new BannerSizeForm();
        $form->bind($g);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($g->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $this->getModelTable('BannerSizeTable')->saveBannerSize($g);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    
                    return $this->redirect()->toRoute('cms/BannerSize');
                } catch (\Exception $ex) {
                }
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['position'] = $g;
        return $this->data_view;
    }

}