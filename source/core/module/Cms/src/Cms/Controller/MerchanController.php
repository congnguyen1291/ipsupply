<?php
namespace Cms\Controller;


use Cms\Form\MerchanForm;
use Cms\Model\Merchan;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class MerchanController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'merchan';
    }

    public function indexAction()
    {
        $total = $this->getModelTable('MerchanTable')->getTotalMerchans();
        $merchans = $this->getModelTable('MerchanTable')->getMerchans();

        $this->data_view['total'] = $total;
        $this->data_view['merchans'] = $merchans;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new MerchanForm();
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $merchan = new Merchan();
            $form->setInputFilter($merchan->getInputFilter());
            $form->setData($data);
            if( $form->isValid() ){
                $merchan->exchangeArray($data);
                try {
                    $merchan_name = $request->getPost('merchan_name', '');
                    $alias = $this->toAlias($merchan_name, '.');
                    $sum = $this->getModelTable('MerchanTable')->getMerchans( array('merchan_alias' => $alias) );
                    if( !empty($sum) ){
                        $alias.= '.'.max( (COUNT($sum)+1), 1);
                    }
                    $merchan->merchan_alias = $alias;
                    $id = $this->getModelTable('MerchanTable')->createMerchan($merchan);
                    return $this->redirect()->toRoute('cms/merchan');
                } catch (\Exception $e ) {}
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/merchan', array(
                'action' => 'add'
            ));
        }
        try {
            $merchan = $this->getModelTable('MerchanTable')->getMerchan( array('merchan_id' => $id) );
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/merchan', array(
                'action' => 'index'
            ));
        }
        $form = new MerchanForm();
        $form->get('submit')->setValue('Cập nhật');
        $form->bind($merchan);
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $merchan = new Merchan();
            $form->setInputFilter($merchan->getInputFilter());
            $form->setData($data);
            if( $form->isValid() ){
                $merchan->exchangeArray($data);
                try {
                    $merchan_name = $request->getPost('merchan_name', '');
                    $alias = $this->toAlias($merchan_name, '.');
                    $sum = $this->getModelTable('MerchanTable')->getMerchans( array('merchan_alias' => $alias) );
                    if( !empty($sum)
                        && (count($sum) > 1 || $sum[0]['merchan_id'] != $id) ){
                        $alias.= '.'.max( (COUNT($sum)+1), 1);
                    }
                    $merchan->merchan_alias = $alias;
                    $id = $this->getModelTable('MerchanTable')->createMerchan($merchan);
                    return $this->redirect()->toRoute('cms/merchan');
                } catch (\Exception $e ) {}
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['merchan'] = $merchan;
        $this->data_view['id'] = $id;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('MerchanTable')->deleteMerchan($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('MerchanTable')->deleteMerchan($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/merchan');
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
                $this->getModelTable('MerchanTable')->updateMerchan($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('MerchanTable')->updateMerchan($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/merchan');
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
                $this->getModelTable('MerchanTable')->updateMerchan($ids, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('MerchanTable')->updateMerchan($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/merchan');
    }

    public function updateorderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('order');
            if ( !empty($ids) ) {
                $this->getModelTable('MerchanTable')->updateOrder($data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/merchan');
    }

}