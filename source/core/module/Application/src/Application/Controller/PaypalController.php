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

class PaypalController extends FrontEndController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function returnAction()
    {
        /*
        Array
        (
            [mc_gross] =&gt; 89.58
            [invoice] =&gt; HD1474268947
            [protection_eligibility] =&gt; Eligible
            [address_status] =&gt; confirmed
            [item_number1] =&gt; 
            [payer_id] =&gt; ZDKFKD6LHQMN4
            [tax] =&gt; 0.00
            [address_street] =&gt; 1 Main St
            [payment_date] =&gt; 00:20:56 Sep 19, 2016 PDT
            [payment_status] =&gt; Completed
            [charset] =&gt; utf-8
            [address_zip] =&gt; 95131
            [mc_shipping] =&gt; 0.00
            [mc_handling] =&gt; 0.00
            [first_name] =&gt; vu
            [mc_fee] =&gt; 2.90
            [address_country_code] =&gt; US
            [address_name] =&gt; vu viet toan
            [notify_version] =&gt; 3.8
            [custom] =&gt; 
            [payer_status] =&gt; verified
            [business] =&gt; oshopvn@gmail.com
            [address_country] =&gt; United States
            [num_cart_items] =&gt; 1
            [mc_handling1] =&gt; 0.00
            [address_city] =&gt; San Jose
            [payer_email] =&gt; vutoan@coz.vn
            [verify_sign] =&gt; AFcWxV21C7fd0v3bYYYRCpSSRl31ADtEm38TYjoGqxwliCoezpppz3jf
            [mc_shipping1] =&gt; 0.00
            [tax1] =&gt; 0.00
            [txn_id] =&gt; 5F278634YX8875600
            [payment_type] =&gt; instant
            [last_name] =&gt; viet toan
            [item_name1] =&gt; Bình nóng lnh PICENZA S30 (Trng)(Bình nóng lnh PICENZA S30 (Trng))
            [address_state] =&gt; CA
            [receiver_email] =&gt; oshopvn@gmail.com
            [payment_fee] =&gt; 2.90
            [quantity1] =&gt; 1
            [receiver_id] =&gt; M5ZMSL5SH58AQ
            [txn_type] =&gt; cart
            [mc_gross_1] =&gt; 89.58
            [mc_currency] =&gt; USD
            [residence_country] =&gt; US
            [test_ipn] =&gt; 1
            [transaction_subject] =&gt; 
            [payment_gross] =&gt; 89.58
            [auth] =&gt; AVKZ-cYqiMv596zR12c7e65-ilnfswiq.d.YQWqRKeZBsuVskvRpz14zgEvahPs9l3XqwHs1NHvY6sw43iJpjLg
        )

        Canceled_Reversal: A reversal has been canceled. For example, you won a dispute with the customer, and the funds for the transaction that was reversed have been returned to you.
        Completed: The payment has been completed, and the funds have been added successfully to your account balance.
        Created: A German ELV payment is made using Express Checkout.
        Denied: You denied the payment. This happens only if the payment was previously pending because of possible reasons described for the pending_reason variable or the Fraud_Management_Filters_x variable.
        Expired: This authorization has expired and cannot be captured.
        Failed: The payment has failed. This happens only if the payment was made from your customer’s bank account.
        Pending: The payment is pending. See pending_reason for more information.
        Refunded: You refunded the payment.
        Reversed: A payment was reversed due to a chargeback or other type of reversal. The funds have been removed from your account balance and returned to the buyer. The reason for the reversal is specified in the ReasonCode element.
        Processed: A payment has been accepted.
        Voided: This authorization has been voided.
        */
        //echo $_SERVER['HTTP_REFERER'];die();
        //echo $host = $_SERVER['HTTP_HOST'];die();

        $id = $this->params()->fromRoute('id', '');
        $status = $_POST['payment_status'];
        $invoice = $_POST['invoice'];
        $mc_gross = $_POST['mc_gross'];
        if( !empty($id) 
            && $id == $invoice ){
            try {
                $invoice = $this->getModelTable('InvoiceTable')->getInvoice($id);
                
                if(!empty($invoice) && !empty($mc_gross) 
                    && $invoice->payment == 'unpaid'
                    && $invoice->payment_code == 'PAYPAL'
                    && floor($mc_gross-$invoice->total_converter) == 0 ){
                    if( strtolower($status) == 'completed' ){
                        $row = array('payment' => 'paid');
                    }else{
                        $row = array('payment' => 'error');
                    }
                    $this->getModelTable('InvoiceTable')->updateData($row, $invoice->invoice_id);
                    $invoice->payment = 'paid';
                    $_SESSION['invoice_id'] = $invoice->invoice_title;
                    return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array('action' => 'success'));
                }
            } catch (\Exception $ex) {}
        }else if( !empty($id) ) {
            $invoice = $this->getModelTable('InvoiceTable')->getInvoice($id);
            if( !empty($invoice) 
                && $invoice->payment == 'unpaid'
                && $invoice->payment_code == 'PAYPAL' ){
                $row = array('payment' => 'cancel');
                $this->getModelTable('InvoiceTable')->updateData($row, $invoice->invoice_id);
                $_SESSION['invoice_id'] = $invoice->invoice_title;
                return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array('action' => 'success'));
            }
        }

        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

    public function cancelAction()
    {
        $id = $this->params()->fromRoute('id', '');
        if( !empty($id) ){
            $invoice = $this->getModelTable('InvoiceTable')->getInvoice($id);
            if( !empty($invoice) 
                && $invoice->payment == 'unpaid'
                && $invoice->payment_code == 'PAYPAL' ){
                $row = array('payment' => 'cancel');
                $this->getModelTable('InvoiceTable')->updateData($row, $invoice->invoice_id);
                $_SESSION['invoice_id'] = $invoice->invoice_title;
                return $this->redirect()->toRoute($this->getUrlRouterLang().'cart', array('action' => 'success'));
            }
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

    public function notifiAction()
    {
        /*$id = $this->params()->fromRoute('id', NULL);
        $status = $_POST['payment_status'];
        $invoice = $_POST['invoice'];
        if($id == $invoice){
            try {
                $invoice = $this->getModelTable('InvoiceTable')->getInvoiceByCodeSha($id);
                if(!empty($invoice)){
                    $row = array('payment' => 'paid');
                    if(strtolower($status) == 'cancel'){
                        $row = array('payment' => 'cancel');
                    }
                    $this->getModelTable('InvoiceTable')->updateData($row, $invoice->invoice_id);
                }
            } catch (\Exception $ex) {}
        }*/
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

} 