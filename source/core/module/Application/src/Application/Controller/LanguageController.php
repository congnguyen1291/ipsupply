<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Model;
use Zend\Filter\File\LowerCase;

class LanguageController extends FrontEndController
{
    public function indexAction(){
        $id = $this->params()->fromRoute('id', '');
        if( !empty($id) ){
            $lang = $this->getModelTable('LanguagesTable')->getLanguageByCodeMd5($id);
            if( !empty($lang) ){
                $_SESSION['lang'] = $lang->languages_file;
                $_SESSION['language'] = $lang;
                $_SESSION['languages_id'] = $lang->languages_id;
                $prefixUlrLang = 'vi';
                $rayRN = explode('_', $lang->languages_file);
                if( in_array('en' , $rayRN) ){
                    $prefixUlrLang = 'en';
                }
                $_SESSION['prefixUlrLang'] = $prefixUlrLang;
            }
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home', array( 
                           'controller' => 'Index', 
                           'action' =>  'index'
                        )); 
    }

}
