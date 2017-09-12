<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Lib\RssFeed;
use Zend\Mvc\Application;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model;
use Zend\Filter\File\LowerCase;
use Zend\I18n\Translator\Translator;
use Application\View\Helper\Common;

class ErrorController extends FrontEndController
{
    private $_model = null;
    private $_request = null;
    private $_sm = null;

    public function indexAction()
    {
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_description']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_keywords']);

        $is_pjax = $this->params()->fromHeader('X-PJAX', '');
        if( !empty($is_pjax) ){
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            $viewModel->setTemplate("application/error/index");
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

}
