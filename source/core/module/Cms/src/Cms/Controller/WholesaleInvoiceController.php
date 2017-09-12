<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 2:43 PM
 */

namespace Cms\Controller;

use Cms\Form\InvoiceForm;
use Cms\Model\Invoice;
use Zend\View\Model\ViewModel;
use Cms\Lib\Paging;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class WholesaleInvoiceController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'wholesale_invoice';
    }

    public function indexAction()
    {
        //die('123123');
        $params = array();
        $link = '';
        $wholesale_title = $this->params()->fromQuery('wholesale_title', NULL);
        if($wholesale_title){
            $params['wholesale_title'] = $wholesale_title;
            $link .= '&wholesale_title='.$wholesale_title;
        }
        $products_id = $this->params()->fromQuery('wholesale_products_id', NULL);
        if($products_id){
            $params['products_id'] = $products_id;
            $link .= '&products_id='.$products_id;
        }
        $date_create = $this->params()->fromQuery('date_create', NULL);
        if($date_create){
            $params['date_create'] = $date_create;
            $link .= '&date_create='.$date_create;
        }
        $payment = $this->params()->fromQuery('payment', NULL);
        if($payment){
            $params['payment'] = $payment;
            foreach($payment as $pm){
                $link .= '&payment[]='.$pm;
            }
        }
        $delivery = $this->params()->fromQuery('delivery', NULL);
        if($delivery){
            $params['delivery'] = $delivery;
            foreach($delivery as $dlvr){
                $link .= '&delivery[]='.$dlvr;
            }
            //$link .= '&delivery='.$delivery;
        }
        $total = $this->getModelTable('WholesaleInvoiceTable')->countAll($params);
        //echo $total;die;

        $page = $this->params()->fromQuery('page', 0);
        $order_by = $this->params()->fromQuery('order','date_create');
        $order_type = $this->params()->fromQuery('order_type','desc');
        $order_link = $link;
        $link .= "&order={$order_by}&order_type={$order_type}";
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        $objPage = new Paging( $total, $page, $page_size, $link );
        $paging = $objPage->getListFooter ( $link );

        $order = array(
            $order_by => $order_type,
        );
        if(!$order_link){
            $order_link = FOLDERWEB.'/cms/wholesale-invoice';
            if(isset($_GET['page'])){
                $order_link .= '?page='.$_GET['page'].'&';
            }else{
                $order_link .= '?';
            }
        }else{
            $order_link = FOLDERWEB.'/cms/wholesale-invoice?'.trim($order_link,'&');
            if(isset($_GET['page'])){
                $order_link .= '&page='.$_GET['page'].'&';
            }else{
                $order_link .= '&';
            }
        }
        $invoices = $this->getModelTable('WholesaleInvoiceTable')->fetchAll($params,$order, $this->intPage, $this->intPageSize);
//        print_r($invoices);die;
        $this->data_view['invoices'] = $invoices;
        $this->data_view['paging'] = $paging;
        $this->data_view['order_link'] = $order_link;
        return $this->data_view;
    }

}