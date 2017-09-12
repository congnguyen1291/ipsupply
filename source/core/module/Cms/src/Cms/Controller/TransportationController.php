<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 11:49 AM
 */

namespace Cms\Controller;

use Cms\Form\TransportationForm;
use Cms\Model\Transportation;
use Zend\Db\Sql\Predicate\In;
use Zend\View\Model\ViewModel;

class TransportationController extends BackEndController{
    private $transportation_type = array(0 => 'Tính phí',1 => 'Miễn phí', 2 => 'Nhận hàng tại shop' );
    private $price_type = array(0 => 'Fixed',1 => 'Number of products', 2 => 'Total cart' );
    private $shipping_class = array(0 => 'Normal',1 => 'Special' );

    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'trans';
    }

    public function indexAction()
    {
        $trans = $this->getModelTable('TransportationTable')->fetchAll('','', $this->intPage, $this->intPageSize);
        $this->data_view['trans'] = $trans;
        return $this->data_view;
    }

    public function addAction()
    {
        $form = new TransportationForm();
        $form->get('transportation_type')->setOptions(array(
            'options' => $this->transportation_type
        ));
        $form->get('shipping_class')->setOptions(array(
            'options' => $this->shipping_class
        ));
        $form->get('submit')->setValue('Thêm mới');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $trans = new Transportation();
            $form->setInputFilter($trans->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $trans->exchangeArray($form->getData());
                $this->getModelTable('TransportationTable')->saveTransportation($trans);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                // Redirect to list of albums
                return $this->redirect()->toRoute('cms/trans');
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/trans', array(
                'action' => 'add'
            ));
        }
        try {
            $trans = $this->getModelTable('TransportationTable')->getTransportation($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/trans', array(
                'action' => 'index'
            ));
        }
        $form = new TransportationForm();
        $form->get('transportation_type')->setOptions(array(
            'options' => $this->transportation_type
        ));
        $form->get('shipping_class')->setOptions(array(
            'options' => $this->shipping_class
        ));
        $form->bind($trans);
        $form->get('submit')->setAttribute('value', 'Edit');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($trans->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getModelTable('TransportationTable')->saveTransportation($trans);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                // Redirect to list of albums
                return $this->redirect()->toRoute('cms/trans');
            }else{
                //print_r($form->getMessages());die();
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        return $this->data_view;
    }


    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_delete' => 1
            );
            $this->getModelTable('CategoryTable')->softUpdateData($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();


        }
        return $this->redirect()->toRoute('cms/category');
    }

    public function updateFeatureAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/category', array(
                'action' => 'index'
            ));
        }
        try {
            $cat = $this->getModelTable('CategoryTable')->getCategory($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/category', array(
                'action' => 'index'
            ));
        }
        $current_features = $this->getModelTable('CategoryTable')->getAllFeatureChecked($id);
        if(count($current_features) < 1){
            $current_features = $this->getModelTable('CategoryTable')->getAllFeatureChecked($cat->parent_id);

        }

        $checked_array = array();
        foreach($current_features as $row){
            $checked_array[] = $row->feature_id;
        }
        $features = $this->getModelTable('FeatureTable')->fetchAll();
        $features = $this->multiLevelData(FALSE, $features, 'feature_id', 'parent_id', 'feature_title');

//        echo "<pre>";
//        print_r($features);
//        echo "</pre>";
//        exit();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $newdata = $request->getPost('featureid');
            $this->getModelTable('CategoryTable')->updateFeatureData($id, $checked_array, $newdata);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

            return $this->redirect()->toRoute('cms/category');
        }
        $this->data_view['id'] = $id;
        $this->data_view['cat'] = $cat;
        $this->data_view['features'] = $features;
        $this->data_view['checked'] = $checked_array;
        return $this->data_view;
    }

    public function undeleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_delete' => 0
            );
            $this->getModelTable('CategoryTable')->softUpdateData($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/category');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('CategoryTable')->softUpdateData($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();


        }
        return $this->redirect()->toRoute('cms/category');
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
                $this->getModelTable('CategoryTable')->softUpdateData($ids, $data);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }

        }
        return $this->redirect()->toRoute('cms/category');
    }

    public function updateorderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('order');
            $this->getModelTable('CategoryTable')->updateorderData($data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/category');
    }

    public function filterAction(){
        $request = $this->getRequest();
        $cats = array();
        if($request->isPost()){
            $data_filter = $request->getPost();
            $cats = $this->getModelTable('CategoryTable')->filter($data_filter);
            $cats = $this->multiLevelData(FALSE, $cats, 'categories_id', 'parent_id', 'categories_title');
        }
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'cats' => $cats
        ));
        return $result;
    }

    public function ajackFilterAction()
    {
        $request = $this->getRequest();
        $transportation = array();

        if ($request->isPost()) {
            $data_filter = $request->getPost('query');
            $transportation = $this->getModelTable('TransportationTable')->find($data_filter);

        }
        echo json_encode($transportation);
        die();
    }

    public function ajaxTransportationAction()
    {
        $request = $this->getRequest();
        $transportation = array();
        if($request->isPost()){
            $ids = $request->getPost('id');
            $transportation = $this->getModelTable('TransportationTable')->getTransportationByIds($ids);
        }
        echo json_encode($transportation);
        die();
    }
} 