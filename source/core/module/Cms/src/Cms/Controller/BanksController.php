<?php
namespace Cms\Controller;


use Cms\Form\BanksForm;
use Cms\Lib\Paging;
use Cms\Model\Banks;
use Zend\View\Model\ViewModel;

class BanksController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'banks';
    }

    public function indexAction()
    {
//        $total = $this->getModelTable('BanksTable')->countAll('banks.is_delete=0');
//        $page = isset($_GET['page']) ? $_GET['page'] : 0;
//        $this->intPage = $page;
//        $page_size = $this->intPageSize;
//        $link = "";
//        $objPage = new Paging( $total, $page, $page_size, $link );
//        $paging = $objPage->getListFooter ( $link );
//        $banks = $this->getModelTable('BanksTable')->fetchAll('','', $this->intPage, $this->intPageSize);
        $this->data_view['data'] = $this->getModelTable('BanksTable')->getAllRate($this->intPage, $this->intPageSize);
        $this->data_view['banks'] = $this->getModelTable('BanksTable')->fetchAll('', '', 0, 100);
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new BanksForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $bank = new Banks();
            $form->setInputFilter($bank->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $bank->exchangeArray($form->getData());
                    $image_file = $request->getFiles('file_image');
                    $this->getModelTable('BanksTable')->saveBanks($bank, $image_file);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/banks');
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
        $id = $this->params()->fromRoute('id', NULL);
        if (!$id) {
            return $this->redirect()->toRoute('cms/banks', array('action' => 'add'));
        }
        try {
            $bank = $this->getModelTable('BanksTable')->getById($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/banks');
        }
        $form = new BanksForm();
        $form->bind($bank);
        $request = $this->getRequest();
        $old_name = $bank->thumb_image;
        if ($request->isPost()) {
            $form->setInputFilter($bank->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                try {
                    $image_file = $request->getFiles('file_image');

                    $this->getModelTable('BanksTable')->saveBanks($bank, $image_file, $old_name);
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/banks');
                } catch (\Exception $ex) {
                    die($ex->getMessage());
                }
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['id'] = $id;
        $this->data_view['bank'] = $bank;
        return $this->data_view;
    }

    public function addConfigAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            try {
                $this->getModelTable('BanksTable')->addConfig($data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            } catch (\Exception $ex) {
            }
        }
        return $this->redirect()->toRoute('cms/banks');
    }

    public function deleteConfigAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            try {
                $this->getModelTable('BanksTable')->deleteConfig($ids);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            } catch (\Exception $ex) {
            }
        }
        return $this->redirect()->toRoute('cms/banks');
    }

    public function deleteBankAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $banks_id = $request->getPost('banks_id');
            try{
                $this->getModelTable('BanksTable')->deleteBank($banks_id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }catch(\Exception $ex){
            }
        }
        return $this->redirect()->toRoute('cms/banks');
    }

    public function filterAction(){
        $request = $this->getRequest();
        $params = array();
        if($request->isPost()){
            $params['banks_config.banks_id'] = $request->getPost('banks_id');
        }
        $data = $this->getModelTable('BanksTable')->filter($params);
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'data' => $data,
        ));
        return $result;
    }

}