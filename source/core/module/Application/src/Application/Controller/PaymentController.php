<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/4/14
 * Time: 2:10 PM
 */
namespace Cms\Controller;

use Cms\Form\PaymentMethodForm;
use Cms\Model\Payment;

class PaymentController extends BackEndController
{
    private $list_code = array(
            "HOME"=>"HOME",
            "ATM"=>"ATM",
            "PAYPAL"=>"PAYPAL",
            "ONEPAY"=>"ONEPAY",
    		"VNPAY"=>"VNPAY",
        );

    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'payment';
    }

    public function indexAction()
    {
        try {
            $rows = $this->getModelTable('PaymentTable')->getAll();
            $this->data_view['payments'] = $rows;
            return $this->data_view;
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms');
        }
    }

    public function addAction()
    {
        $form = new PaymentMethodForm();
        $form->get('code')->setOptions(array(
            'options' => $this->list_code
        ));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $pay = new Payment();
            $form->setInputFilter($pay->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $pay->exchangeArray($form->getData());
                try {
                    $picture_id = $request->getPost('picture_id', '');
                    $this->getModelTable('PaymentTable')->savePayment($pay, $picture_id);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/payment', array('action' => 'index'));
                } catch (\Exception $ex) {
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
            return $this->redirect()->toRoute('cms/payment', array('action' => 'add'));
        }

        try{
            $payment = $this->getModelTable('PaymentTable')->getPaymentMethodById($id);
        }catch(\Exception $ex){
            die($ex->getMessage());
            //return $this->redirect()->toRoute('cms/payment');
        }

        $form = new PaymentMethodForm();
        $form->get('code')->setOptions(array(
            'options' => $this->list_code
        ));
        $form->bind($payment);
        $request = $this->getRequest();
        if($request->isPost()){
            $pay = new Payment();
            $form->setInputFilter($pay->getInputFilter());
            $form->setData($request->getPost());
            $image_src = $payment->image;
            if ($form->isValid()) {
                $pay->exchangeArray($form->getData());
                try {
                    $picture_id = $request->getPost('picture_id', '');
                    $this->getModelTable('PaymentTable')->savePayment($pay, $picture_id);

                    /*strigger change namespace cached*/
                    $this->updateNamespaceCached();

                    return $this->redirect()->toRoute('cms/payment', array('action' => 'index'));
                } catch (\Exception $ex) {
                    die($ex->getMessage());
                }
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['id'] = $id;
        $this->data_view['payment'] = $payment;
        return $this->data_view;

    }

	public function publishAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
		
        if($id){
            $ids = array($id);
            $data = array(
                'is_published' => 1
            );
			
            $this->getModelTable('PaymentTable')->softUpdateData($ids, $data);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();


        }
        return $this->redirect()->toRoute('cms/payment');
    }

    public function unpublishAction()
    {	
		$id = (int)$this->params()->fromRoute('id', 0);
        if($id){
			$ids=array($id);
            $data = array(
                'is_published' => 0
            );
            if (count($ids) > 0) {
                $this->getModelTable('PaymentTable')->softUpdateData($ids, $data);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }

        }
        return $this->redirect()->toRoute('cms/payment');
    }

	
}