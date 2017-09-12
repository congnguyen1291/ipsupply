<?php
namespace Cms\Controller;


use Cms\Form\GoldTimerForm;
use Cms\Model\GoldTimer;

class GoldTimerController extends BackEndController{
    public function __construct(){
        parent::__construct();
        $this->data_view['current'] = "goldtimer";
    }

    public function indexAction(){
        $gold_timers = $this->getModelTable('GoldTimerTable')->fetchAll('','', $this->intPage, $this->intPageSize);
        $this->data_view['gold_timers'] = $gold_timers;
        return $this->data_view;
    }

    public function addAction(){
        $form = new GoldTimerForm();
        $request = $this->getRequest();
        if($request->isPost()){
            $gold = new GoldTimer();
            $form->setInputFilter($gold->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                try{
                    $gold->exchangeArray($form->getData());
                    $products = $request->getPost('products');
                    $this->getModelTable('GoldTimerTable')->saveGoldTimer($gold, $products);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/goldtimer');
                }catch(\Exception $ex){
                    die($ex->getMessage());
                }
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction(){
        $id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/goldtimer',array('action' => 'add'));
        }
        try{
            $gold_timer = $this->getModelTable('GoldTimerTable')->getGoldTimer($id);
//            $gold_timer->date_start = $gold_timer->date_start ." 00:00:00";
//            $gold_timer->date_end = $gold_timer->date_end ." 00:00:00";
        }catch (\Exception $ex){
            return $this->redirect()->toRoute('cms/goldtimer');
        }
        $form = new GoldTimerForm();
        $form->bind($gold_timer);
        $request = $this->getRequest();
        if($request->isPost()){
            $form->setInputFilter($gold_timer->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                try{
                    $products = $request->getPost('products');
                    $this->getModelTable('GoldTimerTable')->saveGoldTimer($gold_timer, $products);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/goldtimer');
                }catch(\Exception $ex){
                    die($ex->getMessage());
                }
            }
        }
        $products = $this->getModelTable('GoldTimerTable')->getProducts($id);
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
        $this->data_view['gold_timer'] = $gold_timer;
        $this->data_view['products'] = $products;
        return $this->data_view;
    }
	
	public function publishAction(){
		$request = $this->getRequest();
		if($request->isPost()){
			$ids = $request->getPost('cid');
		}else{
			$id = $this->params()->fromRoute('id', NULL);
			if(!$id){
				$this->redirect()->toRoute('cms/goldtimer');
			}
			try{
				$question = $this->getModelTable('GoldTimerTable')->getGoldTimer($id);
				$ids = array($id);
			}catch(\Exception $ex){
				$this->redirect()->toRoute('cms/goldtimer');
			}
		}
		if(count($ids)){
			$data = array(
                'is_published' => 1
            );
            $this->getModelTable('GoldTimerTable')->softUpdateData($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

		}
		$this->redirect()->toRoute('cms/goldtimer');
	}
	
	public function unpublishAction(){
		$request = $this->getRequest();
		if($request->isPost()){
			$ids = $request->getPost('cid');
		}else{
			$id = $this->params()->fromRoute('id', NULL);
			if(!$id){
				$this->redirect()->toRoute('cms/goldtimer');
			}
			try{
				$question = $this->getModelTable('GoldTimerTable')->getGoldTimer($id);
				$ids = array($id);
			}catch(\Exception $ex){
				$this->redirect()->toRoute('cms/goldtimer');
			}
		}
		if(count($ids)){
			$data = array(
                'is_published' => 0
            );
            $this->getModelTable('GoldTimerTable')->softUpdateData($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

		}
		$this->redirect()->toRoute('cms/goldtimer');
	}

}