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

class QuestionAnswerController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'product';
    }

    public function indexAction()
    {
        $where = array();
        $order = array(
            'fqa.date_create' => 'DESC',
        );
        $total = $this->getModelTable('SettingTable')->countAllQuestion($where, $order);
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        $link = "";
        $objPage = new Paging($total, $page, $page_size, $link);
        $paging = $objPage->getListFooter($link);
        $datas = $this->getModelTable('SettingTable')->getAllQuestion($where, $order, $this->intPage, $this->intPageSize);
        $this->data_view['datas'] = $datas;
        $this->data_view['paging'] = $paging;
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
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            if(!trim($data['answer_content'])){
                $error[] = "Nội dung trả lời không được bỏ trống";
            }else{
                $this->getModelTable('SettingTable')->addAnswer($data);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }
        }
        $answers = $this->getModelTable('SettingTable')->getAnswers($id);
        $this->data_view['question'] = $question;
        $this->data_view['answers'] = $answers;
        $this->data_view['error'] = isset($error) ? $error : false;
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
		if($request->isPost()){
			$ids = $request->getPost('cid');
		}else{
			$id = $this->params()->fromRoute('id', NULL);
			if(!$id){
				$this->redirect()->toRoute('cms/fqa');
			}
			try{
				$question = $this->getModelTable('SettingTable')->getQuestion($id);
				$ids = array($id);
			}catch(\Exception $ex){
				$this->redirect()->toRoute('cms/fqa');
			}
		}
		if(count($ids)){
			$data = array(
                'is_published' => 1
            );
            $this->getModelTable('SettingTable')->softUpdateData($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

		}
		$this->redirect()->toRoute('cms/fqa');
	}
	
	public function unpublishedAction(){
		$request = $this->getRequest();
		if($request->isPost()){
			$ids = $request->getPost('cid');
		}else{
			$id = $this->params()->fromRoute('id', NULL);
			if(!$id){
				$this->redirect()->toRoute('cms/fqa');
			}
			try{
				$question = $this->getModelTable('SettingTable')->getQuestion($id);
				$ids = array($id);
			}catch(\Exception $ex){
				$this->redirect()->toRoute('cms/fqa');
			}
		}
		if(count($ids)){
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