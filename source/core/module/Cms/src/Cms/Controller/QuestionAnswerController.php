<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\CouponsForm;
use Cms\Lib\Paging;
use Cms\Model\Coupons;
use Zend\View\Model\ViewModel;

use JasonGrimes\Paginator;

class QuestionAnswerController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'product';
    }

    public function indexAction()
    {
    	$language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id', 0);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;

        $total = $this->getModelTable('SettingTable')->countAllQuestion($params);
        $fqas = $this->getModelTable('SettingTable')->getAllQuestion($params);

        $link = '/cms/fqa?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['fqas'] = $fqas;
        $this->data_view['limit'] = $limit;
        $this->data_view['page'] = $page;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function answerAction(){
        $id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/fqa');
        }
        try{
            $question = $this->getModelTable('SettingTable')->getQuestion($id);
        }catch (\Exception $ex){
            return $this->redirect()->toRoute('cms/fqa');
        }
        $error = array();
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            if(!trim($data['answer_content'])){
                $error[] = "Nội dung trả lời không được bỏ trống";
            }else{
                $this->getModelTable('SettingTable')->addAnswer($data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
                return $this->redirect()->toRoute('cms/fqa', array('action' => 'answer', 'id' => $id));
            }
        }
        $answers = $this->getModelTable('SettingTable')->getAnswers($id);
        $this->data_view['question'] = $question;
        $this->data_view['answers'] = $answers;
        $this->data_view['error'] = $error;
        return $this->data_view;
    }
	
	public function deleteAction(){
		$request = $this->getRequest();
		if($request->isPost()){
			$ids = $request->getPost('cid');
			if(count($ids)){
				try{
					$this->getModelTable('SettingTable')->deleteQuestion($ids);

					/*strigger change namespace cached*/
                    $this->updateNamespaceCached();

				}catch(\Exception $ex){
					
				}
			}
		}
		return $this->redirect()->toRoute('cms/fqa');
	}
	
	public function publishedAction(){
		$request = $this->getRequest();
		$ids = array();
		if( $request->isPost() ){
			$ids = $request->getPost('cid', '');
		}else{
			$id = $this->params()->fromRoute('id', '');
			if( !empty($id) ){
				$ids[] = $id;
			}
		}
		if( !empty($ids) ){
			$data = array(
                'is_published' => 1
            );
			try{
				$this->getModelTable('SettingTable')->softUpdateData($ids, $data);

				/*strigger change namespace cached*/
                $this->updateNamespaceCached();

			}catch(\Exception $ex){}
		}
		$this->redirect()->toRoute('cms/fqa');
	}
	
	public function unpublishedAction(){
		$request = $this->getRequest();
		$ids = array();
		if( $request->isPost() ){
			$ids = $request->getPost('cid', '');
		}else{
			$id = $this->params()->fromRoute('id', '');
			if( !empty($id) ){
				$ids[] = $id;
			}
		}
		if( !empty($ids) ){
			$data = array(
                'is_published' => 0
            );
			try{
				$this->getModelTable('SettingTable')->softUpdateData($ids, $data);

				/*strigger change namespace cached*/
                $this->updateNamespaceCached();

			}catch(\Exception $ex){}
		}
		$this->redirect()->toRoute('cms/fqa');
	}
	
	public function deleteAnswerAction(){
		$id = $this->params()->fromRoute('id', NULL);
		if(!$id){
			return $this->redirect()->toRoute('cms/fqa');
		}
		try{
			$answer = $this->getModelTable('SettingTable')->getAnswer($id);
		}catch(\Exception $ex){
			return $this->redirect()->toRoute('cms/fqa');
		}
		try{
			$this->getModelTable('SettingTable')->deleteAnswer($id);

			/*strigger change namespace cached*/
            $this->updateNamespaceCached();

			return $this->redirect()->toRoute('cms/fqa', array('action' => 'answer', 'id' => $answer['fqa_id']));
		}catch(\Exception $ex){}
		return $this->redirect()->toRoute('cms/fqa');
	}

	public function publishedAnswerAction(){
		$request = $this->getRequest();
		$ids = array();
		if( $request->isPost() ){
			$ids = $request->getPost('cid', '');
		}else{
			$id = $this->params()->fromRoute('id', '');
			if( !empty($id) ){
				$ids[] = $id;
			}
		}
		if( !empty($ids) ){
			$data = array(
                'is_published' => 1
            );
			try{
				$this->getModelTable('SettingTable')->updateAnswer($ids, $data);
				/*strigger change namespace cached*/
                $this->updateNamespaceCached();
			}catch(\Exception $ex){}
		}
		$this->redirect()->toRoute('cms/fqa');
	}
	
	public function unpublishedAnswerAction(){
		$request = $this->getRequest();
		$ids = array();
		if( $request->isPost() ){
			$ids = $request->getPost('cid', '');
		}else{
			$id = $this->params()->fromRoute('id', '');
			if( !empty($id) ){
				$ids[] = $id;
			}
		}
		if( !empty($ids) ){
			$data = array(
                'is_published' => 0
            );
			try{
				$this->getModelTable('SettingTable')->updateAnswer($ids, $data);
				/*strigger change namespace cached*/
                $this->updateNamespaceCached();
			}catch(\Exception $ex){}
		}
		$this->redirect()->toRoute('cms/fqa');
	}
	
    public function filterAction()
    {
        $request = $this->getRequest();
        $params = array();
        if ($request->isPost()) {
            $params = $request->getPost('filter');
        }
        $data = $this->getModelTable('SettingTable')->filterQuestion($params);
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'datas' => $data,
        ));
        return $result;
    }
}