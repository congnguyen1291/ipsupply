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

use JasonGrimes\Paginator;

class PaymentController extends BackEndController
{
    private $list_code = array(
            "HOME"=>"HOME",
            "ATM"=>"ATM",
            "PAYPAL"=>"PAYPAL",
            "ONEPAY"=>"ONEPAY",
            "VNPAY"=>"VNPAY",
    		"VISA"=>"VISA",
        );

    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'payment';
    }

    public function indexAction()
    {
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;
        
        if( !empty($q) ){
            $params['payment_name'] = $q;
        }

        $total = $this->getModelTable('PaymentTable')->countAll( $params );
        $payments = $this->getModelTable('PaymentTable')->getAll( $params );

        $link = '/cms/payment?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['payments'] = $payments;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
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

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('PaymentTable')->deletePayments($ids);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('PaymentTable')->deletePayments($id);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/payment');
    }

	public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('PaymentTable')->updatePayments($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('PaymentTable')->updatePayments($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/payment');
    }

    public function unpublishAction()
    {   
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('PaymentTable')->updatePayments($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('PaymentTable')->updatePayments($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/payment');
    }

    public function sandboxAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_sandbox' => 1
            );
            $this->getModelTable('PaymentTable')->updatePayments($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_sandbox' => 1
                );
                $this->getModelTable('PaymentTable')->updatePayments($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/payment');
    }

    public function unsandboxAction()
    {	
		$request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_sandbox' => 0
            );
            $this->getModelTable('PaymentTable')->updatePayments($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_sandbox' => 0
                );
                $this->getModelTable('PaymentTable')->updatePayments($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/payment');
    }

    public function autoOrderAction()
    {
        $payments = $this->getModelTable('PaymentTable')->getAll();
        foreach ($payments as $key => $payment) {
            $row = array();
            $row['ordering'] = $key;
            $this->getModelTable('PaymentTable')->updatePayments($payment['payment_id'], $row);
        }
        /*strigger change namespace cached*/
        $this->updateNamespaceCached();
        return $this->redirect()->toRoute('cms/payment', array(
                'action' => 'index'
            ));
    }

    public function updateorderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('ordering');
            $this->getModelTable('PaymentTable')->updateOrder($data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }
        return $this->redirect()->toRoute('cms/payment');
    }

	
}