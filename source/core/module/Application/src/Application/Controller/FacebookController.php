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

class FacebookController extends FrontEndController {
    
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
                $gender = $request->getPost ('gender', '');
                $id = $request->getPost ('id', '');
                $link = $request->getPost ('link', '');
                $locale = $request->getPost ('locale', '');
                $name = $request->getPost ('name', '');
                $name_format = $request->getPost ('name_format', '');
                $timezone = $request->getPost ('timezone', '');
                $currency = $request->getPost ('currency', '');
                $cover = $request->getPost ('cover', '');
                if ( !empty($id) 
                    && !empty($name) && !empty($first_name)
                    && !empty($last_name) && !empty($link) ){
                    $result = $this->getModelTable('FacebookTable')->login( $id, $email );
                    if ( empty($result) ) {
                        $row = array(
                                'facebook_id' => $id,
                                'email' => $email,
                                'first_name' => $first_name,
                                'last_name' => $last_name,
                                'name' => $name,
                                'name_format' => $name_format,
                                'gender' => $gender,
                                'locale' => $locale,
                                'link' => $link,
                                'timezone' => $timezone,
                                'cover' => $cover,
                                'currency' => $currency,
                            );
                        $fbid = $this->getModelTable('FacebookTable')->saveFacebook( $row );
                        $result = $this->getModelTable('FacebookTable')->login( $id, $email );
                    }
                    if ( !empty($result) ) {
                        $_SESSION ['MEMBER'] = $result;
                        $item = array( 'msg' => $translator->translate('txt_login_thanh_cong') , 'flag'=>TRUE, 'member' => $result );
                    } else {
                        $item = array( 'msg' => $translator->translate('txt_co_loi_xay_ra_vui_long_thu_lai') , 'flag'=>FALSE );
                    }
                }
            }else{
                $item = array( 'msg' => $translator->translate('txt_login_thanh_cong') , 'flag'=>TRUE, 'member' => $result );
            }
        }
        $result = new JsonModel($item);
        return $result;
    }

}
?>
