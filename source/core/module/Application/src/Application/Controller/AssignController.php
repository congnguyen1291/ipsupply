<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;


class AssignController extends FrontEndController
{
    public function notificationAction()
    {
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_not_found') );
        $request = $this->getRequest();
        if ( $hPUser->isMerchant() ) {
            $member = $hPUser->getMember();
            $users_id = $member['users_id'];
            $assigns = $this->getModelTable('AssignTable')->getAssigns( array('is_read' => 0, 'users_id' => $users_id) );
            $item = array('flag' => TRUE, 'assigns' => $assigns, 'msg' => $translator->translate('txt_unimportant_assign_thanh_cong') );
        }
        echo json_encode($item);
        die();
    }

    public function summaryAction()
    {
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'data' => array('total' => 0, 'pending' => 0, 'cancel' => 0, 'finish' => 0, 'read' => 0, 'important' => 0));
        $request = $this->getRequest();
        if ( $hPUser->hasLogin() ) {
            $member = $hPUser->getMember();
            $users_id = $member['users_id'];
            $total = $this->getModelTable('AssignTable')->getTotalAssigns( array('users_id' => $users_id) );
            $totalPending = $this->getModelTable('AssignTable')->getTotalAssigns( array('users_id' => $users_id, 'assign_merchant_status' => 'pending') );
            $totalAccept = $this->getModelTable('AssignTable')->getTotalAssigns( array('users_id' => $users_id, 'assign_merchant_status' => 'accept') );
            $totalCancel = $this->getModelTable('AssignTable')->getTotalAssigns( array('users_id' => $users_id, 'assign_merchant_status' => 'cancel') );
            $totalFinish = $this->getModelTable('AssignTable')->getTotalAssigns( array('users_id' => $users_id, 'assign_merchant_status' => 'finish') );
            $totalIsRead = $this->getModelTable('AssignTable')->getTotalAssigns( array('users_id' => $users_id, 'is_read' => 0) );
            $totalIsImportant = $this->getModelTable('AssignTable')->getTotalAssigns( array('users_id' => $users_id, 'is_important' => 0) );
            $data = array(
                    'total' => $total,
                    'accept' => $totalAccept,
                    'pending' => $totalPending,
                    'cancel' => $totalCancel,
                    'finish' => $totalFinish,
                    'read' => $totalIsRead,
                    'important' => $totalIsImportant,
                );
            $item = array('flag' => TRUE, 'data' => $data);
        }
        echo json_encode($item);
        die();
    }

    public function acceptAction()
    {
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_not_found') );
        $request = $this->getRequest();
        if ($request->isPost() && $hPUser->hasLogin() ) {
            $id = $request->getPost('id', '');
            if( !empty($id) ){
                $assign = $this->getModelTable('AssignTable')->getAssign( array('assign_id' => $id) );
                if( !empty($assign) 
                    && $assign->assign_merchant_status == 'pending'
                    && $assign->assign_status == 'pending'
                    && $assign->delivery == 'processing' ){
                    $row = array(
                            'assign_id' => $id,
                            'users_id' => $hPUser->getUsersId(),
                            'invoice_id' => $assign->invoice_id,
                            'merchant_id' => $hPUser->getMerchantId(),
                        );
                    $this->getModelTable('AssignTable')->accept( $row );
                    $row = array(
                        'invoice_id' => $assign->invoice_id,
                        'users_id' => $hPUser->getUsersId(),
                        'user_name' => $hPUser->getUserName(),
                        'has_feedback' => 0,
                        'invoice_status' => 'accept',
                        'comment' => '',
                    );
                    $this->getModelTable('InvoiceTable')->updateLog($row);
                    $item = array('flag' => TRUE, 'assign' => $assign, 'msg' => $translator->translate('txt_accept_assign_thanh_cong') );
                }
            }
        }
        echo json_encode($item);
        die();
    }

    public function cancelAction()
    {
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_not_found') );
        $request = $this->getRequest();
        if ($request->isPost() && $hPUser->hasLogin() ) {
            $id = $request->getPost('id', '');
            if( !empty($id) ){
                $assign = $this->getModelTable('AssignTable')->getAssign( array('assign_id' => $id) );
                if( !empty($assign) 
                    && $assign->assign_merchant_status == 'pending'
                    && $assign->assign_status == 'pending'
                    && $assign->delivery == 'processing' ){
                    $row = array(
                            'assign_id' => $id,
                            'invoice_id' => $assign->invoice_id,
                            'users_id' => $hPUser->getUsersId(),
                            'merchant_id' => $hPUser->getMerchantId(),
                        );
                    $this->getModelTable('AssignTable')->cancel( $row );
                    $item = array('flag' => TRUE, 'assign' => $assign, 'msg' => $translator->translate('txt_cancel_assign_thanh_cong') );
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
                $assign = $this->getModelTable('AssignTable')->getAssign( array('assign_id' => $id) );
                if( !empty($assign) 
                    && $assign->assign_merchant_status == 'accept'
                    && $assign->assign_status == 'accept'
                    && $assign->delivery == 'accept' ){
                    $row = array(
                            'assign_id' => $id,
                            'users_id' => $hPUser->getUsersId(),
                            'invoice_id' => $assign->invoice_id,
                            'merchant_id' => $hPUser->getMerchantId(),
                        );
                    $this->getModelTable('AssignTable')->finish( $row );
                    $row = array(
                        'invoice_id' => $assign->invoice_id,
                        'users_id' => $hPUser->getUsersId(),
                        'user_name' => $hPUser->getUserName(),
                        'has_feedback' => 0,
                        'invoice_status' => 'finish',
                        'comment' => '',
                    );
                    $this->getModelTable('InvoiceTable')->updateLog($row);
                    $item = array('flag' => TRUE, 'assign' => $assign, 'msg' => $translator->translate('txt_delivered_assign_thanh_cong') );
                }
            }
        }
        echo json_encode($item);
        die();
    }

    public function readAction()
    {
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_not_found') );
        $request = $this->getRequest();
        if ($request->isPost() && $hPUser->hasLogin() ) {
            $id = $request->getPost('id', '');
            if( !empty($id) ){
                $assign = $this->getModelTable('AssignTable')->getAssign( array('assign_id' => $id, 'users_id' => $hPUser->getUsersId()) );
                if( !empty($assign)
                    && empty($assign->is_read) ){
                    $row = array(
                            'assign_id' => $id,
                            'invoice_id' => $assign->invoice_id,
                            'users_id' => $hPUser->getUsersId(),
                            'merchant_id' => $hPUser->getMerchantId(),
                        );
                    $this->getModelTable('AssignTable')->read( $row );
                    $item = array('flag' => TRUE, 'assign' => $assign, 'msg' => $translator->translate('txt_read_assign_thanh_cong') );
                }
            }
        }
        echo json_encode($item);
        die();
    }

    public function unreadAction()
    {
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_not_found') );
        $request = $this->getRequest();
        if ($request->isPost() && $hPUser->hasLogin() ) {
            $id = $request->getPost('id', '');
            if( !empty($id) ){
                $assign = $this->getModelTable('AssignTable')->getAssign( array('assign_id' => $id, 'users_id' => $hPUser->getUsersId()) );
                if( !empty($assign)
                    && !empty($assign->is_read) ){
                    $row = array(
                            'assign_id' => $id,
                            'invoice_id' => $assign->invoice_id,
                            'users_id' => $hPUser->getUsersId(),
                            'merchant_id' => $hPUser->getMerchantId(),
                        );
                    $this->getModelTable('AssignTable')->unread( $row );
                    $item = array('flag' => TRUE, 'assign' => $assign, 'msg' => $translator->translate('txt_unread_assign_thanh_cong') );
                }
            }
        }
        echo json_encode($item);
        die();
    }

    public function importantAction()
    {
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_not_found') );
        $request = $this->getRequest();
        if ($request->isPost() && $hPUser->hasLogin() ) {
            $id = $request->getPost('id', '');
            if( !empty($id) ){
                $assign = $this->getModelTable('AssignTable')->getAssign( array('assign_id' => $id, 'users_id' => $hPUser->getUsersId()) );
                if( !empty($assign)
                    && empty($assign->is_important) ){
                    $row = array(
                            'assign_id' => $id,
                            'invoice_id' => $assign->invoice_id,
                            'users_id' => $hPUser->getUsersId(),
                            'merchant_id' => $hPUser->getMerchantId(),
                        );
                    $this->getModelTable('AssignTable')->important( $row );
                    $item = array('flag' => TRUE, 'assign' => $assign, 'msg' => $translator->translate('txt_important_assign_thanh_cong') );
                }
            }
        }
        echo json_encode($item);
        die();
    }

    public function unimportantAction()
    {
        $hPUser = $this->getServiceLocator()->get('viewhelpermanager')->get('User');
        $translator = $this->getServiceLocator()->get('translator');
        $item = array('flag' => FALSE, 'msg' => $translator->translate('txt_not_found') );
        $request = $this->getRequest();
        if ($request->isPost() && $hPUser->hasLogin() ) {
            $id = $request->getPost('id', '');
            if( !empty($id) ){
                $assign = $this->getModelTable('AssignTable')->getAssign( array('assign_id' => $id, 'users_id' => $hPUser->getUsersId()) );
                if( !empty($assign)
                    && !empty($assign->is_important) ){
                    $row = array(
                            'assign_id' => $id,
                            'invoice_id' => $assign->invoice_id,
                            'users_id' => $hPUser->getUsersId(),
                            'merchant_id' => $hPUser->getMerchantId(),
                        );
                    $this->getModelTable('AssignTable')->unimportant( $row );
                    $item = array('flag' => TRUE, 'assign' => $assign, 'msg' => $translator->translate('txt_unimportant_assign_thanh_cong') );
                }
            }
        }
        echo json_encode($item);
        die();
    }
}
