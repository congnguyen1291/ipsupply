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

class LoginController extends FrontEndController {
    
    public function indexAction() {
        if( !empty($_SESSION ['MEMBER']) ){
            return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile', array ('action' => 'index' ) );
        }
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
		$translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_description']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_keywords']);
        $form = new LoginForm ();
        $request = $this->getRequest ();
        if ($request->isPost ()) {
            $username = $request->getPost ('email', '');
            if( empty($username) ){
                $username = $request->getPost ('user_name', '');
            }
            $password = $request->getPost ('password', '');
            if ( !empty($username) && !empty($password) ) {
                $result = $this->getModelTable('UserTable')->login ( $username, $password );
                if ( !empty($result) ) {
                    $_SESSION ['MEMBER'] = $result;                    
                    if ( !empty($_GET ['redirect']) ) {
                        $url = urldecode ( $_GET ['redirect'] );
                        return $this->redirect ()->toUrl ( $url );
                    } else {
                        return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile', array ('action' => 'index' ) );
                    }
                } else {
                    return $this->redirect ()->toUrl ( $_SERVER['HTTP_REFERER'] );
                }
                
            }
        }
        $this->data_view['form'] = $form;

        $this->addLinkPageInfo($this->baseUrl .$this->getUrlPrefixLang().'/sign-in' );
        $is_pjax = $this->params()->fromHeader('X-PJAX', '');
        if( !empty($is_pjax) ){
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            $viewModel->setTemplate("application/login/index");
            $viewModel->setVariables($this->data_view);
            $viewRender = $this->getServiceLocator()->get('ViewRenderer');
            $html = $viewRender->render($viewModel);
            $html = "<html>
                        <head>
                            <title>{$this->website['seo_title']}</title>
                            <meta name=\"description\" content=\"{$this->website['seo_description']}\" />
                            <meta name=\"keywords\" content=\"{$this->website['seo_keywords']}\" />
                        </head>
                        <body>
                           {$html}
                        </body>";
            echo $html;
            die();
        }

        return $this->data_view;
    }

    public function loginAction() {
        if( !empty($_SESSION ['MEMBER']) ){
            return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile', array ('action' => 'index' ) );
        }
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_description']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_keywords']);
        $form = new LoginForm ();
        $request = $this->getRequest ();

        if ($request->isPost ()) {
            $username = $request->getPost ('email', '');
            if( empty($username) ){
                $username = $request->getPost ('user_name', '');
            }
            $password = $request->getPost ( 'password' );
            if ( !empty($username) && !empty($password) ) {
                $result = $this->getModelTable('UserTable')->login ( $username, $password );
                if ( !empty($result) ) {
                    $_SESSION ['MEMBER'] = $result;
                    if ( !empty($_GET ['redirect']) ) {
                        $url = urldecode ( $_GET ['redirect'] );
                        return $this->redirect ()->toUrl ( $url );
                    } else {
                        return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile', array ('action' => 'index' ) );
                    }
                } else {
                    return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'login', array ('action' => 'register' ) );
                }
            }
        }
        $this->data_view['form'] = $form;

        $this->addLinkPageInfo($this->baseUrl .$this->getUrlPrefixLang().'/sign-in' );
        $is_pjax = $this->params()->fromHeader('X-PJAX', '');
        if( !empty($is_pjax) ){
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            $viewModel->setTemplate("application/login/login");
            $viewModel->setVariables($this->data_view);
            $viewRender = $this->getServiceLocator()->get('ViewRenderer');
            $html = $viewRender->render($viewModel);
            $html = "<html>
                        <head>
                            <title>{$this->website['seo_title']}</title>
                            <meta name=\"description\" content=\"{$this->website['seo_description']}\" />
                            <meta name=\"keywords\" content=\"{$this->website['seo_keywords']}\" />
                        </head>
                        <body>
                           {$html}
                        </body>";
            echo $html;
            die();
        }

        return $this->data_view;
    }

    public function registerAction() {
        if( !empty($_SESSION ['MEMBER']) ){
            return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile', array ('action' => 'index' ) );
        }
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_description']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_keywords']);
        
        $form = new RegisterForm ();
        $request = $this->getRequest ();
        if ( $request->isPost () ) {
            $full_name = $request->getPost ( 'full_name', '' );
            $first_name = $request->getPost ( 'first_name', '' );
			$redirect = $request->getPost ( 'redirect', '' );
            $last_name = $request->getPost ( 'last_name', '' );
            $user_name = $request->getPost ( 'user_name', '' );
            $password = $request->getPost ( 'password', '' );
            $repassword = $request->getPost ( 'repassword', '' );
            $phone = $request->getPost ( 'phone', '' );
            $address = $request->getPost ( 'address', '' );
            if( empty($full_name) 
                && !empty($first_name)
                && !empty($last_name) ){
                $full_name = $first_name.' '.$last_name;
            }
            $user = $this->getModelTable('UserTable')->getUserByUserame($user_name);
            if( empty($user)
                && !empty($full_name) && !empty($user_name) 
                && !empty($phone) && !empty($address) 
                && !empty($password) && !empty($repassword)
                && $password == $repassword ){
                $alias = $this->toAlias($full_name);
                $total = $this->getModelTable('UserTable')->getTotalFreAlias($alias);
                $row = array(
                    'website_id'         => $this->website->website_id,
                    'first_name'         => $first_name,
                    'last_name'         => $last_name,
                    'full_name'         => $full_name,
                    'user_name'  => $user_name,
                    'password'      => md5($password),
                    'users_alias'      => $alias.'.'.$total['total'],
                    'phone'      => $phone,
                    'address'      => $address,
                    'is_published'      => 1,
                    'is_delete'      => 0,
                    'is_administrator'      => 0,
                    'type'      => 'user',
                );
                $users_id = $this->getModelTable('UserTable')->createUser($row);
                if (!empty($users_id)) {
                    $_SESSION ['MEMBER'] = $this->getModelTable('UserTable')->login( $user_name, $password);
                    if(!empty($_GET['redirect']) || $redirect!=""){
						if( $redirect==''){
							$redirect = urldecode ($_GET['redirect']);
						}
                        return $this->redirect ()->toUrl( $redirect );
                    } else {
                        return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile', array ('action' => 'index' ) );
                    }
                }
            }
        }
        $this->data_view['form'] = $form;

        $this->addLinkPageInfo($this->baseUrl .$this->getUrlPrefixLang().'/sign-up' );
        $is_pjax = $this->params()->fromHeader('X-PJAX', '');
        if( !empty($is_pjax) ){
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            $viewModel->setTemplate("application/login/register");
            $viewModel->setVariables($this->data_view);
            $viewRender = $this->getServiceLocator()->get('ViewRenderer');
            $html = $viewRender->render($viewModel);
            $html = "<html>
                        <head>
                            <title>{$this->website['seo_title']}</title>
                            <meta name=\"description\" content=\"{$this->website['seo_description']}\" />
                            <meta name=\"keywords\" content=\"{$this->website['seo_keywords']}\" />
                        </head>
                        <body>
                           {$html}
                        </body>";
            echo $html;
            die();
        }

        return $this->data_view;
    }

    public function logoutAction() {
        unset ($_SESSION ['MEMBER']);
        return $this->redirect ()->toUrl ( $_SERVER ['HTTP_REFERER'] );
    }

    public function facebookAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $request = $this->getRequest ();
        if ($request->isPost ()) {
            $data = $request->getPost ();
            $dataSave = array ();
            $dataSave ['facebookId'] = $data ['id'];
            $dataSave ['user_name'] = ! empty ( $data ['email'] ) ? $data ['email'] : (! empty ( $data ['username'] ) ? $data ['username'] : $data ['id']);
            $dataSave ['password'] = rand ( 0, 1000 );
            $dataSave ['full_name'] = ! empty ( $data ['name'] ) ? $data ['name'] : ('user-' . $data ['id']);
            $dataSave ['users_alias'] = ! empty ( $data ['name'] ) ? $data ['name'] : ('user-' . $data ['id']);
            $dataSave ['birthday'] = '';
            $dataSave ['phone'] = ! empty ( $data ['phone'] ) ? $data ['phone'] : 0;
            $dataSave ['address'] = '';
            $dataSave ['cities_id'] = 1;
            $dataSave ['districts_id'] = 1;
            $dataSave ['is_published'] = 1;
            $dataSave ['is_delete'] = 0;
            $dataSave ['date_create'] = date ( 'Y-m-d H:i:s' );
            $dataSave ['date_update'] = date ( 'Y-m-d H:i:s' );
            $dataSave ['type'] = 'user';
            $result = $this->getModelTable ( 'UserTable' )->facebook_login ( $dataSave );
            if ($result) {
                $_SESSION ['MEMBER'] = $result;
                echo json_encode ( array ('success' => TRUE, 'msg' => $translator->translate('txt_login_thanh_cong'), 'data' => $result ) );
            } else {
                echo json_encode ( array ('success' => FALSE,
                    'msg' => $translator->translate('txt_khong_login_thanh_cong'),
                ));
            }
            die();
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

	public function loginAjackAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $form = new LoginForm ();
        $request = $this->getRequest ();
        if ($request->isPost ()) {
            $username = $request->getPost ('email', '');
            if( empty($username) ){
                $username = $request->getPost ('user_name', '');
            }
            $password = $request->getPost ('password', '');
            if ( !empty($username) && !empty($password) ){
                $result = $this->getModelTable('UserTable')->login ( $username, $password );
                if ( !empty($result) ) {
                    $_SESSION ['MEMBER'] = $result;                    
                    $result = new JsonModel(array(
						'html' => $translator->translate('txt_login_thanh_cong'),
						'flag'=>true,
					));	
                } else {
                    $result = new JsonModel(array(
						'html' => $translator->translate('txt_co_loi_xay_ra_vui_long_thu_lai'),
                        'flag'=>false,
					));
                }
            }
        }
        return $result;
    }

    public function registerAjackAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $result = new JsonModel(array(
            'html' => $translator->translate('txt_co_loi_xay_ra_vui_long_thu_lai'),
            'flag'=>false,
        ));

        $request = $this->getRequest ();
        if ( $request->isPost () ) {
            $full_name = $request->getPost ( 'full_name', '' );
            $first_name = $request->getPost ( 'first_name', '' );
            $last_name = $request->getPost ( 'last_name', '' );
            $user_name = $request->getPost ( 'user_name', '' );
            $password = $request->getPost ( 'password', '' );
            $repassword = $request->getPost ( 'repassword', '' );
            $phone = $request->getPost ( 'phone', '' );
            $address = $request->getPost ( 'address', '' );
            if( empty($full_name) 
                && !empty($first_name)
                && !empty($last_name) ){
                $full_name = $first_name.' '.$last_name;
            }
            $user = $this->getModelTable('UserTable')->getUserByUserame($user_name);
            if( empty($user)
                && !empty($full_name) && !empty($user_name) 
                && !empty($phone) && !empty($address) 
                && !empty($password) && !empty($repassword)
                && $password == $repassword ){
                $alias = $this->toAlias($full_name);
                $total = $this->getModelTable('UserTable')->getTotalFreAlias($alias);
                $row = array(
                    'website_id'         => $this->website->website_id,
                    'first_name'         => $first_name,
                    'last_name'         => $last_name,
                    'full_name'         => $full_name,
                    'user_name'  => $user_name,
                    'password'      => md5($password),
                    'users_alias'      => $alias.'.'.$total['total'],
                    'phone'      => $phone,
                    'address'      => $address,
                    'is_published'      => 1,
                    'is_delete'      => 0,
                    'is_administrator'      => 0,
                    'type'      => 'user',
                );

                $users_id = $this->getModelTable('UserTable')->createUser($row);
                if ( !empty($users_id) ) {
                    $_SESSION ['MEMBER'] = $this->getModelTable('UserTable')->login ( $user_name, $password);
                    $result = new JsonModel(array(
                        'html' => $translator->translate('txt_login_thanh_cong'),
                        'flag'=>true,
                    ));
                }
            }
        }

        return $result;
    }

    public function signupAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $ajax = $this->params()->fromQuery('ajax', 0);

        $request = $this->getRequest ();
        if ( $request->isPost () ) {
            $user = $request->getPost ( 'user', array() );
            if( !empty($user)
                && !empty($user['first_name']) && !empty($user['last_name']) 
                && !empty($user['email']) && !empty($user['address']) 
                && !empty($user['phone']) && !empty($user['country_id']) 
                && !empty($user['password']) && !empty($user['repassword'])
                && $user['password'] == $user['repassword'] ){

                $full_name = $user['first_name'].' '.$user['last_name'];

                $_user = $this->getModelTable('UserTable')->getUserByUserame($user['email']);
                if( empty($_user) ){
                    $alias = $this->toAlias($full_name);
                    $total = $this->getModelTable('UserTable')->getTotalFreAlias($alias);
                    $row = array(
                        'website_id'         => $this->website->website_id,
                        'first_name'         => $user['first_name'],
                        'last_name'         => $user['last_name'],
                        'full_name'         => $full_name,
                        'user_name'  => $user['email'],
                        'password'      => md5($user['password']),
                        'users_alias'      => $alias.'.'.$total['total'],
                        'phone'      => $user['phone'],
                        'country_id'      => $user['country_id'],
                        'address'      => $user['address'],
                        'address01' => empty($user['address01']) ? '' : $user['address01'],
                        'city' => empty($user['city']) ? '' : $user['city'],
                        'state' => empty($user['state']) ? '' : $user['state'],
                        'suburb' => empty($user['suburb']) ? '' : $user['suburb'],
                        'region' => empty($user['region']) ? '' : $user['region'],
                        'province' => empty($user['province']) ? '' : $user['province'],
                        'zipcode' => empty($user['zipcode']) ? '' : $user['zipcode'],
                        'cities_id' => empty($user['cities_id']) ? 0 : $user['cities_id'],
                        'districts_id' => empty($user['districts_id']) ? 0 : $user['districts_id'],
                        'wards_id' => empty($user['wards_id']) ? 0 : $user['wards_id'],
                        'is_published'      => 1,
                        'is_delete'      => 0,
                        'is_administrator'      => 0,
                        'type'      => 'user',
                    );

                    $users_id = $this->getModelTable('UserTable')->createUser($row);
                    if ( !empty($users_id) ) {
                        $_SESSION ['MEMBER'] = $this->getModelTable('UserTable')->login ( $user['email'], $user['password']);
                        if( !empty($ajax) ){
                            $result = array(
                                'html' => $translator->translate('txt_login_thanh_cong'),
                                'flag'=>true,
                                'users_id'=>$users_id,
                                'row'=>$row
                            );
                            echo json_encode($result);
                            die;
                        }else if( !empty($_GET['redirect']) ){
                            $redirect = urldecode ($_GET['redirect']);
                            return $this->redirect ()->toUrl( $redirect );
                        }
                        return $this->redirect ()->toRoute ( $this->getUrlRouterLang().'profile', array ('action' => 'index' ) );
                    }else{
                        if( !empty($ajax) ){
                            $result = array(
                                'html' => $translator->translate('txt_tao_tai_khoan_khong_thanh_cong'),
                                'users_id'=>$users_id,
                                'row'=>$row,
                                'flag'=>FALSE,
                            );
                            echo json_encode($result);
                            die;
                        }
                    }
                }else{
                    if( !empty($ajax) ){
                        $result = array(
                            'html' => $translator->translate('txt_nguoi_dung_da_ton_tai'),
                            'flag'=>FALSE,
                        );
                        echo json_encode($result);
                        die;
                    }
                }
            }else{
                if( !empty($ajax) ){
                    $result = array(
                        'html' => $translator->translate('txt_co_loi_xay_ra_vui_long_thu_lai'),
                        'flag'=>FALSE,
                    );
                    echo json_encode($result);
                    die;
                }
            }
        }
        if( !empty($ajax) ){
            $result = array(
                'html' => $translator->translate('txt_un_support'),
                'flag'=>FALSE,
            );
            echo json_encode($result);
            die;
        }
        return $this->data_view;
    }

    public function openIdAction() {
    	//$authService = $this->sm->get('AuthenticationService');
    	$authService = new AuthenticationService();
    	//die;
    	//$staff = $authService->getStorage()->read();
    	
    	if (!$authService->hasIdentity()) {
    		$type = $this->params('type', 'google');
    		switch ($type) {
    			case 'google':
    				$model = new \Google\Api();
    				$dataGoogle = $model->getMe();
    				if (is_string($dataGoogle)) {
    					if ($dataGoogle == 'cancel') {
    						echo "<script>window.close();</script>";
    						return;
    					}
    					$this->redirect()->toUrl($dataGoogle);
    					return;
    				}
    				
    				$email = $dataGoogle['email'];
    				$user = $this->getModelTable('UserTable')->getUserByUserame ( $email );
    
    				if (!$user) {
    					$alias = $dataGoogle['name'];
    					//$avatar = time () . '_' . $this->nelo()->toAlias($dataGoogle['name']);
    					
    					$dataSave = array ();
    					$dataSave ['googleId'] = $dataGoogle ['id'];
    					$dataSave ['user_name'] = $dataGoogle['email'];
    					$dataSave ['password'] = rand ( 0, 1000 );
    					$dataSave ['full_name'] = $dataGoogle['name'];
    					$dataSave ['users_alias'] = $alias;
    					$dataSave ['birthday'] = '';
    					$dataSave ['phone'] = '';
    					$dataSave ['address'] = '';
    					$dataSave ['cities_id'] = 0;
    					$dataSave ['districts_id'] = 0;
    					$dataSave ['is_published'] = 0;
    					$dataSave ['is_delete'] = 0;
    					$dataSave ['date_create'] = date ( 'Y-m-d H:i:s' );
    					$dataSave ['date_update'] = date ( 'Y-m-d H:i:s' );
    					$dataSave ['type'] = 'user';
    					$dataSave ['avatar'] = '';
    					$dataSave ['open_id'] = 'GOOGLE';
    					$dataSave ['open_id_url'] = $dataGoogle['link'];
    					
    					if($this->getModelTable ( 'UserTable' )->register ( $dataSave )){
    						$_SESSION ['MEMBER'] = $dataSave;
    					}    					
    
    				} else {
    					$_SESSION ['MEMBER'] = $user;
    				}
    				break;
    		}
    	}
    	return $this->redirect ()->toUrl ( $_SERVER['HTTP_REFERER'] );
    }

}
?>
