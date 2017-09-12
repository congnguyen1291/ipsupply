<?php

/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 2:47 PM
 */
namespace Application\Controller;

use Application\Model\User;
use Zend\Mvc\Controller\AbstractActionController;
use Application\Form\RegisterForm;
use Application\Form\LoginForm;
use Zend\View\Model\JsonModel;
//use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
//use Openid\Google;

class GoogleController extends FrontEndController {
    
    public function indexAction() {
        die();
    }

    public function loginAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $request = $this->getRequest ();
        $item = array( 'msg' => '', 'flag'=>FALSE );
        if ( $request->isPost () ) {
            if( empty($_SESSION ['MEMBER']) ){
                $email = $request->getPost ('email', '');
                $first_name = $request->getPost ('first_name', '');
                $last_name = $request->getPost ('last_name', '');
                $id = $request->getPost ('id', '');
                $name = $request->getPost ('name', '');
                $cover = $request->getPost ('cover', '');
                if (    !empty($id) && !empty($name) 
                        && !empty($first_name) && !empty($last_name) ){
                    $result = $this->getModelTable('GoogleTable')->login( $id, $email );
                    if ( empty($result) ) {
                        $row = array(
                                'google_id' => $id,
                                'email' => $email,
                                'first_name' => $first_name,
                                'last_name' => $last_name,
                                'name' => $name,
                                'cover' => $cover
                            );
                        $fbid = $this->getModelTable('GoogleTable')->saveGoogle( $row );
                        $result = $this->getModelTable('GoogleTable')->login( $id, $email );
                    }
                    if ( !empty($result) ) {
                        $_SESSION ['MEMBER'] = $result;
                        $item = array( 'msg' => $translator->translate('txt_login_thanh_cong') , 'flag'=>TRUE, 'member' => $result );
                    } else {
                        $item = array( 'msg' => $translator->translate('txt_co_loi_xay_ra_vui_long_thu_lai') , 'flag'=>FALSE );
                    }
                }
            }else{
                $item = array( 'msg' => $translator->translate('txt_login_thanh_cong') , 'flag'=>TRUE, 'member' => $_SESSION ['MEMBER'] );
            }
        }
        $result = new JsonModel($item);
        return $result;
    }

}
?>
