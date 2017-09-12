<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;


class InvoiceController extends FrontEndController
{
    public function acceptAction()
    {
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_not_found') );
        $request = $this->getRequest();
        if ($request->isPost() && $hPUser->hasLogin() ) {
            $id = $request->getPost('id', '');
            if( !empty($id) ){
                $invoice = $this->getModelTable('InvoiceTable')->getInvoice($id);
                if( !empty($invoice)
                    && $invoice->delivery != 'accept'
                    && $invoice->delivery != 'delivered' ){
                    $row = array(
                        'invoice_id' => $invoice->invoice_id
                    );
                    $this->getModelTable('InvoiceTable')->accept( $row );
                    $row = array(
                        'invoice_id' => $invoice->invoice_id,
                        'users_id' => $hPUser->getUsersId(),
                        'user_name' => $hPUser->getUserName(),
                        'has_feedback' => 0,
                        'invoice_status' => 'accept',
                        'comment' => '',
                    );
                    $this->getModelTable('InvoiceTable')->updateLog($row);
                    $item = array('flag' => TRUE, 'invoice' => $invoice, 'msg' => $translator->translate('txt_accept_invoice_thanh_cong') );
                }
            }
        }
        echo json_encode($item);
        die();
    }

    public function finishAction()
    {
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_not_found') );
        $request = $this->getRequest();
        if ($request->isPost() && $hPUser->hasLogin() ) {
            $id = $request->getPost('id', '');
            if( !empty($id) ){
                $invoice = $this->getModelTable('InvoiceTable')->getInvoice($id);
                if( !empty($invoice)
                    && $invoice->delivery != 'finish'
                    && $invoice->delivery != 'delivered' ){
                    $row = array(
                        'invoice_id' => $invoice->invoice_id
                    );
                    $this->getModelTable('InvoiceTable')->finish( $row );
                    $row = array(
                        'invoice_id' => $invoice->invoice_id,
                        'users_id' => $hPUser->getUsersId(),
                        'user_name' => $hPUser->getUserName(),
                        'has_feedback' => 0,
                        'invoice_status' => 'finish',
                        'comment' => '',
                    );
                    $this->getModelTable('InvoiceTable')->updateLog($row);
                    $item = array('flag' => TRUE, 'invoice' => $invoice, 'msg' => $translator->translate('txt_finish_invoice_thanh_cong') );
                }
            }
        }
        echo json_encode($item);
        die();
    }
}
