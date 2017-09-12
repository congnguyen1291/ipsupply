<?php

namespace Application\Controller;

//use Zend\View\Helper\ViewModel;

use Application\lib\Test;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model;
use Zend\Filter\File\LowerCase;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;

class KeywordsController extends FrontEndController {

    protected $base = null;
    
    private $_model = null;
    private $_request = null;
    private $_sm = null;
    private $topMenu = NULL;
    protected $categoryTable = null;
    protected $categoriestable = null;
    protected $productstable = null;
    protected $manufacturerstable = null;

    public function __construct() {
        
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $keyword = $this->params()->fromQuery('keyword', '');
        $keywords = $this->getModelTable('KeywordsTable')->getKeywords($keyword);
        echo json_encode($keywords);
        die();
    }

}
