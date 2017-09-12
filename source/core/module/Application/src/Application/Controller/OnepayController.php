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

class OnepayController extends FrontEndController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function returnAction()
    {
        if( !empty($_SERVER['HTTP_REFERER']) ){
            $referer = $_SERVER['HTTP_REFERER'];
            $referer_parts = parse_url($referer);
            if( $referer_parts['host'] == 'onepay.vn' ) {
                //echo "good";
            } else {
                //echo "bad";
            }
        }
        $id = $this->params()->fromRoute('id', '');
        $vpc_Txn_Secure_Hash = $this->params()->fromQuery('vpc_SecureHash', '');
        $invoice_id = $this->params()->fromQuery('vpc_MerchTxnRef', '');
        $vpc_AcqResponseCode = $this->params()->fromQuery('vpc_AcqResponseCode', '');
        $vpc_TxnResponseCode = $this->params()->fromQuery('vpc_TxnResponseCode', '');
        $vpc_TransactionNo = $this->params()->fromQuery('vpc_TransactionNo', '');

        if($id == $invoice_id ){
            unset($_GET["vpc_SecureHash"]);
            try {
                $invoice = $this->getModelTable('InvoiceTable')->getInvoice($id);
                
                if( !empty($invoice)
                    && $invoice->payment == 'unpaid'
                    && $invoice->payment_code == 'ONEPAY' ){
                    $payment = $this->getModelTable('PaymentTable')->getPayment($invoice->payment_id);
                    if( !empty($payment) ){
                        $SECURE_SECRET = $payment->vpc_hashcode;
                    }
                    if ( strlen($SECURE_SECRET) > 0 
                        && $vpc_TxnResponseCode != "7" 
                        && $vpc_TxnResponseCode != "No Value Returned") {
                        ksort($_GET);
                        $md5HashData = "";
                        foreach ($_GET as $key => $value) {
                            if ($key != "vpc_SecureHash"
                                && (strlen($value) > 0) 
                                && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
                                $md5HashData .= $key . "=" . $value . "&";
                            }
                        }
                        $md5HashData = rtrim($md5HashData, "&");
                        if (strtoupper ( $vpc_Txn_Secure_Hash ) == strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$SECURE_SECRET)))) {

                            $Status = 'error';
                            if ($_GET["vpc_TxnResponseCode"] == '0') {
                                $Status = 'paid';
                                $vnpTranId = $vpc_TransactionNo;
                            }else{
                                $Status = 'error';
                                $vnpTranId = $vpc_TransactionNo;
                            }
                            $row = array(
                                'payment' => $Status,
                                'vpc_transaction_no' => $vnpTranId
                            );
                            $this->getModelTable('InvoiceTable')->updateData($row, $invoice->invoice_id);
                            $invoice->payment = 'paid';
                            $_SESSION['invoice_id'] = $invoice->invoice_title;
                            return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array('action' => 'success'));
                        }
                    }
                }
            } catch (\Exception $ex) {}
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

} 