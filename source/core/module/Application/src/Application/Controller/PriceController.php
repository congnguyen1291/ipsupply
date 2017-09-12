<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Application\Model\Articles;
use Zend\Mvc\Application;
use Zend\View\Model\ViewModel;

class PriceController extends FrontEndController
{
    private $_model   = null;
    private $_request = null;
    private $_sm      = null;
    protected $base  = null;
    public $viewModel = null;
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
    	 define('MODULE_NAME', str_replace('\controller', '', strtolower(__NAMESPACE__)));
        $this->_request = $this->getRequest();
        if (is_null($this->_model)) {
            $sm = $this->getServiceLocator();
            $this->sm = $sm;
            $this->_model = $sm->get('Application\Model\Articlestable');
        }
        return parent::onDispatch($e);
    }
    public function indexAction(){
        
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        $translator = $this->getServiceLocator()->get('translator');
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        if($this->isMasterPage()){
            $this->addJS(FOLDERWEB.'/root_style/frontend/clip-one/assets/plugins/flex-slider/jquery.flexslider.js');
            $this->addJS(FOLDERWEB.'/root_style/frontend/clip-one/assets/plugins/stellar.js/jquery.stellar.min.js');
            $this->addJS(FOLDERWEB.'/root_style/frontend/clip-one/assets/plugins/colorbox/jquery.colorbox-min.js');
            $this->addJS(FOLDERWEB.'/root_style/frontend/clip-one/assets/js/index.js');
			            $this->addCSS(FOLDERWEB.'/root_style/frontend/clip-one/assets/plugins/revolution_slider/rs-plugin/css/settings.css');
            $this->addCSS(FOLDERWEB.'/root_style/frontend/clip-one/assets/plugins/flex-slider/flexslider.css');
            $this->addCSS(FOLDERWEB.'/root_style/frontend/clip-one/assets/plugins/colorbox/example2/colorbox.css');
            $packs = $this->getModelTable('PackTable')->getList();
            $this->data_view['packs'] = $packs;
        }else{
        
        }
		
    	return $this->data_view;
    }

}