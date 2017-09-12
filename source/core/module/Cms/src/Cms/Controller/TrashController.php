<?php
namespace Cms\Controller;

use Zend\View\Model\ViewModel;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

use JasonGrimes\Paginator;

class TrashController extends BackEndController{

    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'trash';
    }
    public function indexAction(){
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);
        
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

}