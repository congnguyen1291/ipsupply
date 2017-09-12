<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\ExtensionForm;
use Cms\Model\Extension;

class ExtensionController extends BackEndController{
    public function __construct(){
        parent::__construct();
        $this->data_view['current'] = 'extension';
    }

    public function indexAction(){
        $this->data_view['extensions'] = $this->getModelTable('ExtensionTable')->fetchAll('is_delete=0', '', $this->intPage, $this->intPageSize);
        return $this->data_view;
    }

    public function addAction(){
        $form = new ExtensionForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ext = new Extension();
            $form->setInputFilter($ext->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $ext->exchangeArray($form->getData());
                if($this->getModelTable('ExtensionTable')->saveExtension($ext)){

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/extension');
                }
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/extension', array(
                'action' => 'add'
            ));
        }
        try {
            $ext = $this->getModelTable('ExtensionTable')->getExtension($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/extension', array(
                'action' => 'index'
            ));
        }

        $form = new ExtensionForm();
        $form->bind($ext);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($ext->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                if($this->getModelTable('ExtensionTable')->saveExtension($ext)){

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();
                    
                    return $this->redirect()->toRoute('cms/extension');
                }
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['extension'] = $ext;
        return $this->data_view;
    }

    public function loadExtensionAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $extids = $request->getPost('extids');
            if(count($extids) > 0){
                $exts = $this->getModelTable('ExtensionTable')->getExtensions($extids);
                $exts = $exts->toArray();
                if(count($exts) > 0){
                    echo json_encode(array(
                        'success' => TRUE,
                        'ext' => $exts,
                    ));
                    die();
                }
            }
            echo json_encode(array(
                'success' => FALSE,
            ));
            die;
        }
    }

}