<?php
namespace Cms\Controller;

use Cms\Lib\Paging;
use Zend\Db\Sql\Predicate\In;
use Zend\View\Model\ViewModel;
use Cms\Form\FeatureForm;
use Cms\Model\Feature;
use Zend\View\Model\JsonModel;

use JasonGrimes\Paginator;

class FeatureController extends BackEndController
{

    protected $featureTable;

    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'feature';
    }

    public function indexAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromRoute('type', 0);
        $id = $this->params()->fromRoute('id', 0);
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;
        $params['parent_id'] = $id;

        if( !empty($q) ){
            if( $type == 0 ){
                $params['feature_title'] = $q;
            }else if( $type == 1  ) {
                $params['feature_color'] = $q;
            }
        }

        $total = $this->getModelTable('FeatureTable')->countAll( $params );
        $features = $this->getModelTable('FeatureTable')->fetchAll( $params );

        $link = '/cms/feature' .( !empty($id) ? '/index/'.$id : ''). '?page=(:num)'.( !empty($q) ? '&q='.$q.( !empty($type) ? '&type='.$type : '') : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $feature = array();
        if( !empty($id) ){
            $feature = $this->getModelTable('FeatureTable')->getFeature($id);
        }

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['id'] = $id;
        $this->data_view['feature'] = $feature;
        $this->data_view['features'] = $features;
        $this->data_view['type'] = $type;
        $this->data_view['q'] = $q;
        return $this->data_view;
    }

    public function loadfeatureAction(){
        $request = $this->getRequest();
        $features = array();
        $checked_array = array();
        if($request->isPost()){
            $catid = $request->getPost('catid');
            $features = $this->getModelTable('FeatureTable')->getByCatId($catid);
            $features = $this->multiLevelData(FALSE, $features, 'feature_id', 'parent_id', 'feature_title');
        }
        echo $this->getModelTable('FeatureTable')->getHtmlFeature($features, $checked_array, 0);
        die();
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'features' => $features,
            'checked'  => $checked_array,
        ));
        return $result;
    }

    public function addAction()
    {
        $parent_id = (int)$this->params()->fromRoute('id', 0);
        $form = new FeatureForm();
        $form->get('submit')->setValue('Lưu lại');
        $fAll = $this->getModelTable('FeatureTable')->fetchAll();
        $features = $this->multiLevelData(TRUE, $fAll, 'feature_id', 'parent_id', 'feature_title');
        $features = array_merge(array(0=>'__ROOT__'), $features);
        $form->get('parent_id')->setOptions(array(
            'options' => $features,
        ));
        if( !empty($parent_id) ){
            $form->get('parent_id')->setValue($parent_id);
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $feature_type = $request->getPost('feature_type', 0);
            $is_value = $request->getPost('is_value', 0);
            $feature_color = $request->getPost('feature_color', '');
            $feature_file = $request->getPost('feature_file', '');
            $feature = new Feature();
            $form->setInputFilter($feature->getInputFilter());
            $form->setData($request->getPost());
            if ( $form->isValid() ) {
                $feature->exchangeArray($form->getData());
                try{
                    if( empty($feature->feature_type) ){
                        $feature->is_value = 0;
                        $feature->feature_color = '';
                        $feature->feature_file = '';
                    }else{
                        if( empty($feature->is_value) ){
                            $feature->feature_file = '';
                        }else{
                            $feature->feature_color = '';
                        }
                    }
                    $id = $this->getModelTable('FeatureTable')->saveFeature($feature);
                    return $this->redirect()->toRoute('cms/feature', array(
                                'action' => 'index',
                                'id' => $feature->parent_id
                            ));
                }catch(\Exception $ex){}
            }
        }
        $this->data_view['parent_id'] = $parent_id;
        $this->data_view['form'] = $form;
        return $this->data_view;
    }
    
    public function addAjaxAction()
    {

        $request = $this->getRequest();
        if ($request->isPost()) {
            $feature_title = $request->getPost('feature_title', '');
            if(!empty($feature_title)){
                $data = $request->getPost();
                $data['website_id']= $this->website->website_id;
                $feat = new Feature();
                $feat->exchangeArray($data);

                $feature_id = $this->getModelTable('FeatureTable')->saveFeature($feat);
                if(!empty($feature_id)){
                    $feat->feature_id = $feature_id;
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    echo json_encode(array(
                        'flag' => TRUE,
                        'msg' => 'Thêm mới thành công',
                        'id_feature' => $feat->feature_id,
                        'title_feature' => $feat->feature_title,
                    ));
                    die;
                }
            }else{
                echo json_encode(array(
                    'flag' => FALSE,
                    'msg' => 'Bạn chưa nhập tên thuộc tính'
                ));
                die;
            }
        }
        echo json_encode(array(
            'flag' => FALSE,
            'msg' => 'Api không hỗ trợ'
        ));
        die;
    }

    public function addWithCategoryAjaxAction()
    {

        $request = $this->getRequest();
        if ($request->isPost()) {
            $feature_title = $request->getPost('feature_title', '');
            if(!empty($feature_title)){
                $data = $request->getPost();
                $feat = $this->getModelTable('FeatureTable')->addCategoryFeature($data);
                if( !empty($feat) ){
                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    echo json_encode(array(
                        'flag' => TRUE,
                        'msg' => 'Thêm mới thành công',
                        'id_feature' => $feat['feature_id'],
                        'title_feature' => $feat['feature_title'],
                        'feature' => $feat,
                    ));
                    die;
                }
            }else{
                echo json_encode(array(
                    'flag' => FALSE,
                    'msg' => 'Bạn chưa nhập tên thuộc tính'
                ));
                die;
            }
        }
        echo json_encode(array(
            'flag' => FALSE,
            'msg' => 'Api không hỗ trợ'
        ));
        die;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/feature', array(
                'action' => 'add'
            ));
        }
        try {
            $feat = $this->getModelTable('FeatureTable')->getFeature($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/feature', array(
                'action' => 'index'
            ));
        }
        $form = new FeatureForm();
        $fAll = $this->getModelTable('FeatureTable')->fetchAll();
        $features = $this->multiLevelData(TRUE, $fAll, 'feature_id', 'parent_id', 'feature_title');
        $features = array_merge(array(0=>'__ROOT__'), $features);
        $form->get('parent_id')->setOptions(array(
            'options' => $features,
        ));
        $form->bind($feat);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $feature_type = $request->getPost('feature_type', 0);
            $is_value = $request->getPost('is_value', 0);
            $feature_color = $request->getPost('feature_color', '');
            $feature_file = $request->getPost('feature_file', '');
            $feature = new Feature();
            $form->setInputFilter($feature->getInputFilter());
            $form->setData($request->getPost());
            if ( $form->isValid() ) {
                $feature->exchangeArray($form->getData());
                try{
                    if( empty($feature->feature_type) ){
                        $feature->is_value = 0;
                        $feature->feature_color = '';
                        $feature->feature_file = '';
                    }else{
                        if( empty($feat->is_value) ){
                            $feature->feature_file = '';
                        }else{
                            $feature->feature_color = '';
                        }
                    }
                    $id = $this->getModelTable('FeatureTable')->saveFeature($feature);
                    return $this->redirect()->toRoute('cms/feature', array(
                                'action' => 'index',
                                'id' => $feature->parent_id
                            ));
                }catch(\Exception $ex){}
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['feat'] = $feat;
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
            $this->getModelTable('FeatureTable')->softUpdateData($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();


        }
        return $this->redirect()->toRoute('cms/feature');
    }

    public function undeleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_delete' => 0
            );
            $this->getModelTable('FeatureTable')->softUpdateData($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();


        }
        return $this->redirect()->toRoute('cms/feature');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
		$ids = array();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
        }else{
			$id = $this->params()->fromRoute('id', NULL);
			if(!$id){
				return $this->redirect()->toRoute('cms/feature');
			}
			try{
				$feat = $this->getModelTable('FeatureTable')->getFeature($id);
				$ids = array($id);
			}catch(\Exception $ex){
				return $this->redirect()->toRoute('cms/feature');
			}
		}
		if(count($ids)){
			$data = array(
				'is_published' => 1
			);
			try{
				$this->getModelTable('FeatureTable')->softUpdateData($ids, $data);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

			}catch(\Exception $ex) {
				
			}
		}
        return $this->redirect()->toRoute('cms/feature');
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
		$ids = array();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            
        }else{
			$id = $this->params()->fromRoute('id', NULL);
			if(!$id){
				return $this->redirect()->toRoute('cms/feature');
			}
			try{
				$feat = $this->getModelTable('FeatureTable')->getFeature($id);
				$ids = array($id);
			}catch(\Exception $ex){
				return $this->redirect()->toRoute('cms/feature');
			}
		}
		if (count($ids) > 0) {
			$data = array(
				'is_published' => 0
			);
			try{
				$this->getModelTable('FeatureTable')->softUpdateData($ids, $data);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

			}catch(\Exception $ex){
				die($ex->getMessage());
			}
		}
        return $this->redirect()->toRoute('cms/feature');
    }

    public function updateorderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('order');
            $this->getModelTable('FeatureTable')->updateorderData($data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/feature');
    }
}