<?php
namespace Cms\Controller;

use Cms\Model\Login;
use Zend\Form\Form;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use Cms\Model\User;
use Cms\Form\LoginForm;

class LoginController extends AbstractActionController{

    protected $domain = '';
    protected $baseUrl = '';
    protected $protocol = '';
    protected $website = array();

    public function __construct()
    {
        
    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e) {

        if( !empty($_SESSION['CMSMEMBER'])
            && !empty($_SESSION['CMSMEMBER']['users_id']) ){
            $this->redirect()->toUrl('/cms');
        }
        $this->domain = "";//$_SERVER['HTTP_HOST'];
        $this->protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $this->baseUrl = $this->protocol.''.$this->domain;
        if (substr($this->domain, 0, 4) == "www.")
            $this->domain = substr($this->domain, 4);
        if(!empty($this->domain)){
            $this->website = $this->getModelTable('WebsitesTable')->getWebsite($this->domain);
        }
        $_SESSION['domain'] = $this->domain;
        $_SESSION['protocol'] = $this->protocol;
        $_SESSION['baseUrl'] = $this->baseUrl;
        $_SESSION['website'] = $this->website;
        
        $lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi_VN';
        $sm = $this->getServiceLocator();
        $translator = $sm->get('translator');
        $path_lang = PATH_MODULE.'/Cms/lang/'.$lang.'.php';
        $translator->addTranslationFile("phparray",$path_lang);

        $sm->get('ViewHelperManager')->get('translate')
            ->setTranslator($translator);

        return parent::onDispatch($e);
    }

    public function indexAction(){
        $form = new LoginForm();
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
			$linkrefer = urldecode($request->getPost('redirect_refer', '/cms'));
            $login = new Login();
            $form->setInputFilter($login->getInputFilter());
            $form->setData($data);
            if($form->isValid()){
                $login->exchangeArray($data);
				$domainname="http://".$_SERVER['HTTP_HOST'];
				
                if($this->getModelTable('UserTable')->login($login)){
					return $this->redirect()->toRoute('cms');
                }
            }
        }
        $view = new ViewModel();
        $view->setTerminal(TRUE);
        $view->setVariables(array(
            'form' => $form,
        ));
        return $view;
    }

    public function logoutAction(){
        if(isset($_SESSION['CMSMEMBER'])){
            unset($_SESSION['CMSMEMBER']);
            if (isset($_COOKIE['CMSMEMBER'])) {
                unset($_COOKIE['CMSMEMBER']);
                setcookie('CMSMEMBER', null, -1);
            }
        }
		return $this->redirect()->toRoute('cms/login');
    }

    public function getModelTable($name)
    {
        if (!isset($this->{$name})) {
            $this->{$name} = NULL;
        }
        if (!$this->{$name}) {
            $sm = $this->getServiceLocator();
            $this->{$name} = $sm->get('Cms\Model\\' . $name);
        }
        return $this->{$name};
    }
}