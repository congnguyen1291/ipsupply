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

class DomainController extends FrontEndController
{
    private $_model = null;
    private $_request = null;
    private $_sm = null;

    private $backlist = array('photos.coz.vn', 'cdn.coz.vn', 'static.coz.vn');

    public function indexAction()
    {
        $request = $this->getRequest();
        $domain = $this->params()->fromQuery('p', null);
        if (!empty($domain)) {
            $sub_domain = trim($this->toAlias($domain).'.'.MASTERPAGE);
            if( !in_array( $sub_domain , $this->backlist) ){
                $website = $this->getModelTable('WebsitesTable')->getWebsite($sub_domain);
                if(!empty($website)){
                    echo json_encode(array(
                        'flag' => FALSE,
                        'type' => 1,
                        'website' => $website,
                        'domain' => $sub_domain
                    ));
                    die();
                }else{
                    $result = $this->getModelTable('DomainTable')->getList($domain);
                    if(!empty($result)){
                        echo json_encode(array(
                            'flag' => FALSE,
                            'type' => 2,
                            'data' => $result,
                            'domain' => $sub_domain
                        ));
                        die();
                    }
                }
                echo json_encode(array(
                    'flag' => TRUE,
                    'domain' => $sub_domain
                ));
                die();
            }else{
                echo json_encode(array(
                    'flag' => FALSE,
                    'type' => 3,
                    'data' => $this->backlist,
                    'domain' => $sub_domain
                ));
                die();
            }
        }
        echo json_encode(array(
            'flag' => FALSE,
            'type' => 0
        ));
        die();
    }

}
