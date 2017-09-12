<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 9:27 AM
 */

namespace Application\Controller;


//use Zend\View\Helper\ViewModel;
use Application\Model\User;
use Zend\View\Model\ViewModel;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class VnpayController extends FrontEndController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function returnAction()
    {
        $id = $this->params()->fromRoute('id', '');
        $vnp_SecureHash = $this->params()->fromQuery('vnp_SecureHash', '');
        $invoice_id = $this->params()->fromQuery('vnp_TxnRef', '');
        
        if( !empty($id) 
            && $id == $invoice_id ){
            try {
                $invoice = $this->getModelTable('InvoiceTable')->getInvoice($id);
                
                if( !empty($invoice)
                    && $invoice->payment == 'unpaid'
                    && $invoice->payment_code == 'VNPAY' ){
                    $payment = $this->getModelTable('PaymentTable')->getPayment($invoice->payment_id);
                    if( !empty($payment) ){
                        $hashSecret = $payment->vnp_hashsecret;
                        $params = array();
                        foreach ($_GET as $key => $value) {
                            $params[$key] = $value;
                        }
                        unset($params['vnp_SecureHashType']);
                        unset($params['vnp_SecureHash']);
                        ksort($params);
                        $i = 0;
                        $hashData = "";
                        foreach ($params as $key => $value) {
                            if ($i == 1) {
                                $hashData = $hashData . '&' . $key . "=" . $value;
                            } else {
                                $hashData = $hashData . $key . "=" . $value;
                                $i = 1;
                            }
                        }

                        $secureHash = md5($hashSecret . $hashData);
                        if ($secureHash == $vnp_SecureHash) {

                            $Status = 'error';
                            $error = array();
                            if ( $_GET['vnp_ResponseCode'] == '00') {
                                $Status = 'paid';
                                $vnpTranId = $params["vnp_TransactionNo"];
                            }else{
                                $error = array('vnp_ResponseCode' => $_GET['vnp_ResponseCode']);
                                $Status = 'error';
                                $vnpTranId = $params["vnp_TransactionNo"];
                            }
                            /*$row = array(
                                'payment' => $Status,
                                'vnp_pay_date' => $params['vnp_PayDate'],
                                'vnp_transaction_no' => $vnpTranId
                            );
                            $this->getModelTable('InvoiceTable')->updateData($row, $invoice->invoice_id);*/
                            $invoice->payment = $Status;
                            $_SESSION['invoice_id'] = $invoice->invoice_title;
                            $_SESSION['PAYMENT_ERROR'] = $error;
                            /*return $this->redirect()->toRoute(
                                'cart', 
                                array(
                                    'action' => 'success'
                                ),
                                array( 'query' => array(
                                    'vnp_TransactionNo' => $_GET['vnp_ResponseCode']
                                ))
                            );*/
                            return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array('action' => 'success'));
                        }else{
                            return $this->redirect()->toRoute(
                                $this->getUrlRouterLang().'cart', 
                                array(
                                    'action' => 'error'
                                ),
                                array( 'query' => array(
                                    'vnp_ResponseCode' => 97
                                ))
                            );
                        }
                    }
                }
            } catch (\Exception $ex) {}
        }
        /*if( !empty($id) ) {
            $invoice = $this->getModelTable('InvoiceTable')->getInvoice($id);
            if( !empty($invoice) 
                && $invoice->payment == 'unpaid'
                && $invoice->payment_code == 'PAYPAL' ){
                $row = array('payment' => 'cancel');
                $this->getModelTable('InvoiceTable')->updateData($row, $invoice->invoice_id);
                $_SESSION['invoice_id'] = $invoice->invoice_title;
                return $this->redirect()->toRoute('cart', array('action' => 'success'));
            }
        }*/
        unset($_SESSION['invoice_id']);
        unset($_SESSION['PAYMENT_ERROR']);
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

    public function listenAction()
    {
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'RspCode' => '99', 'msg' => $translator->translate('txt_unknow_error'));
        if( !empty($_REQUEST['vnp_TxnRef']) ){
            try {
                $invoice_id = $_REQUEST['vnp_TxnRef'];
                $invoice = $this->getModelTable('InvoiceTable')->getInvoiceNoJoinWebsite($invoice_id);
                if( !empty($invoice)
                    && $invoice->payment_code == 'VNPAY' ){
                    if( $invoice->payment == 'unpaid' ){
                        $payment = $this->getModelTable('PaymentTable')->getPaymentNoJoinWebsite($invoice->payment_id);
                        if( !empty($payment) ){
                            $hashSecret = $payment->vnp_hashsecret;
                            $params = array();
                            $data = $_REQUEST;
                            foreach ($data as $key => $value) {
                                $params[$key] = $value;
                            }
                            $vnp_SecureHash = $params['vnp_SecureHash'];
                            unset($params['vnp_SecureHashType']);
                            unset($params['vnp_SecureHash']);
                            ksort($params);
                            $i = 0;
                            $hashData = "";
                            foreach ($params as $key => $value) {
                                if ($i == 1) {
                                    $hashData = $hashData . '&' . $key . "=" . $value;
                                } else {
                                    $hashData = $hashData . $key . "=" . $value;
                                    $i = 1;
                                }
                            }
                            $secureHash = md5($hashSecret . $hashData);
                            if ($secureHash == $vnp_SecureHash) {
                                $Status = 'error';
                                if ($params['vnp_ResponseCode'] == '00') {
                                    $Status = 'paid';
                                    $vnpTranId = $params["vnp_TransactionNo"];
                                    $item = array('flag' => TRUE, 'RspCode' => '00', 'Message' => $translator->translate('txt_confirm_success'), 'Signature' => $secureHash);
                                }else{
                                    $Status = 'error';
                                    $vnpTranId = $params["vnp_TransactionNo"];
                                }
                                $row = array(
                                            'payment' => $Status,
                                            'vnp_pay_date' => $params['vnp_PayDate'],
                                            'vnp_transaction_no' => $vnpTranId
                                        );
                                $this->getModelTable('InvoiceTable')->updateDataNoJoinWebsite($row, $invoice->invoice_id);
                            }else{
                                $item = array('flag' => FALSE, 'RspCode' => '97', 'Message' => $translator->translate('txt_chu_ky_khong_hop_le'), 'Signature' => $secureHash);
                            }
                        }else{
                            $item = array('flag' => FALSE, 'RspCode' => '01', 'Message' => $translator->translate('txt_order_not_found'));
                        }
                    }else{
                        $item = array('flag' => FALSE, 'RspCode' => '02', 'Message' => $translator->translate('txt_order_already_confirmed'));
                    }
                }else{
                    $item = array('flag' => FALSE, 'RspCode' => '01', 'Message' => $translator->translate('txt_order_not_found'));
                }
            } catch (\Exception $ex) {
                $item = array('flag' => FALSE, 'RspCode' => '99', 'msg' => $translator->translate('txt_unknow_error'));
            }
        }
        echo json_encode($item);die();
    }

} 