<?php
namespace Cms\Controller;

//use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends BackEndController{
    public function __construct(){
        parent::__construct();
    }
    public function indexAction(){
    	$total_invoice = $this->getModelTable('InvoiceTable')->countAll();
    	$total_product = $this->getModelTable('ProductTable')->countAll();
    	$total_articles = $this->getModelTable('ArticlesTable')->countAll();
    	$total_user = $this->getModelTable('UserTable')->countAll(array('type' => 'user'));

    	$date_from = date("Y-m-d");
        $today_timestamp =  strtotime('tomorrow - 1 second');
        $date_to = date("Y-m-d H:i:s",$today_timestamp);
    	$subtotal_today = $this->getModelTable('InvoiceTable')->sumSubTotalInvoiceByDay($date_from, $date_to);
    	$total_today = $this->getModelTable('InvoiceTable')->countSubTotalInvoiceByDay($date_from, $date_to);
    	$this->data_view['subtotal_today'] =$subtotal_today;
    	$this->data_view['total_today'] =$total_today;

    	$yesterday_timestamp =  strtotime('today - 1 day');
        $date_from = date("Y-m-d",$yesterday_timestamp);
        $today_timestamp =  strtotime('today - 1 second');
        $date_to = date("Y-m-d H:i:s",$today_timestamp);
        $subtotal_yesterday = $this->getModelTable('InvoiceTable')->sumSubTotalInvoiceByDay($date_from, $date_to);
        $total_yesterday = $this->getModelTable('InvoiceTable')->countSubTotalInvoiceByDay($date_from, $date_to);
    	$this->data_view['subtotal_yesterday'] =$subtotal_yesterday;
    	$this->data_view['total_yesterday'] =$total_yesterday;

    	$yesterday_timestamp =  strtotime('today - 7 day');
        $date_from = date("Y-m-d",$yesterday_timestamp);
        $date_to = date("Y-m-d H:i:s");
        $subtotal_last_seven_days = $this->getModelTable('InvoiceTable')->sumSubTotalInvoiceByDay($date_from, $date_to);
        $total_last_seven_days = $this->getModelTable('InvoiceTable')->countSubTotalInvoiceByDay($date_from, $date_to);
    	$this->data_view['subtotal_last_seven_days'] =$subtotal_last_seven_days;
    	$this->data_view['total_last_seven_days'] =$total_last_seven_days;

    	$yesterday_timestamp =  strtotime('today - 30 day');
        $date_from = date("Y-m-d",$yesterday_timestamp);
        $date_to = date("Y-m-d H:i:s");
        $subtotal_last_thirty_days = $this->getModelTable('InvoiceTable')->sumSubTotalInvoiceByDay($date_from, $date_to);
        $total_last_thirty_days = $this->getModelTable('InvoiceTable')->countSubTotalInvoiceByDay($date_from, $date_to);
    	$this->data_view['subtotal_last_thirty_days'] =$subtotal_last_thirty_days;
    	$this->data_view['total_last_thirty_days'] =$total_last_thirty_days;

    	$yesterday_timestamp =  strtotime('today - 90 day');
        $date_from = date("Y-m-d",$yesterday_timestamp);
        $date_to = date("Y-m-d H:i:s");
        $subtotal_last_ninety_days = $this->getModelTable('InvoiceTable')->sumSubTotalInvoiceByDay($date_from, $date_to);
        $total_last_ninety_days = $this->getModelTable('InvoiceTable')->countSubTotalInvoiceByDay($date_from, $date_to);
    	$this->data_view['subtotal_last_ninety_days'] =$subtotal_last_ninety_days;
    	$this->data_view['total_last_ninety_days'] =$total_last_ninety_days;

    	$this->data_view['total_invoice'] =$total_invoice;
    	$this->data_view['total_product'] =$total_product;
    	$this->data_view['total_articles'] =$total_articles;
    	$this->data_view['total_user'] =$total_user;
        return $this->data_view;
    }

}