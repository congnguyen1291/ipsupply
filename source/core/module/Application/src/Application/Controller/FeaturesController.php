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
use Zend\View\Model\ViewModel;
use Application\Model;
use Zend\Filter\File\LowerCase;

class FeaturesController extends FrontEndController
{

    public function indexAction()
    {
        $features = $this->getModelTable('FeatureTable')->getAllFeatureAndSort();
        echo json_encode($features);
        die;
    }

    public function detailAction()
    {
		$features = array();
        $id = $this->params()->fromRoute('id', null);
        $categories = $this->getModelTable('CategoriesTable')->getRow($id);
        if ( $categories ) {
            $features = $this->getModelTable('FeatureTable')->getAllFeatureOfCategoryAndSort($id);
        }
        echo json_encode($features);
        die;
    }
}