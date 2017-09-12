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

use Zend\Debug\Debug;
use Zend\Feed\Writer\Entry;
use Zend\Feed\Writer\Feed;
use Zend\Feed\Writer\Renderer\Feed\Rss;

use PHPSitemap\Sitemap;


class IndexController extends FrontEndController
{
    private $_model = null;
    private $_request = null;
    private $_sm = null;

    /**
     * @var $categoryTable \Application\Model\CategoriesTable
     */
    protected $categoryTable = null;
    protected $categoriestable = null;
    protected $productstable = null;
    protected $base = null;


    public function successAction()
    {

    }

    public function cancelAction()
    {
        $a = json_encode($_REQUEST);
        @file_put_contents('./tientran2.txt', $a."\r\n", FILE_APPEND);
        die;
    }

    public function acceptAction()
    {
        $a = json_encode($_REQUEST);
        @file_put_contents('./tientran3.txt', $a."\r\n", FILE_APPEND);
        die();
    }

    public function indexAction()
    {
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_description']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_keywords']);
        $this->addLinkPageInfo($this->baseUrl.$this->getUrlPrefixLang());
        $is_pjax = $this->params()->fromHeader('X-PJAX', '');
        if( !empty($is_pjax) ){
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            $viewModel->setTemplate("application/index/index");
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

    public function subscriptionAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $cat = $request->getPost('cat');
            $email = $request->getPost('email_subscription');
            if (trim($email) != '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->getMyTable('CategoriesTable')->addSubscriptionUser($email, $cat);
                echo json_encode(array(
                    'success' => TRUE,
                    'msg' => 'Thanks for your subscribe',
                ));
            } else {
                echo json_encode(array(
                    'success' => FALSE,
                    'msg' => 'Your email is not valid',
                ));
            }
            die();
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

    public function rssAction(){
        header("Content-type: text/xml; charset=utf-8");
        $rssfeed = new RssFeed('','',FOLDERWEB,'thienthientan');
        echo $rssfeed->create_feed();
        die;
    }

    public function cronSendEmailAction(){
        try{
            $this->getModelTable('UserTable')->doSendWeek();
            $this->getModelTable('UserTable')->doSendDay();
            $this->getModelTable('UserTable')->doSendMonth();
            echo json_encode(array(
                'success' => TRUE,
                'msg' => "Sent!",
            ));
        }catch (\Exception $ex){
            echo json_encode(array(
                'success' => FALSE,
                'msg' => $ex->getMessage(),
            ));
        }
        die();
    }

    public function getMyTable($table)
    {
        $t = strtolower($table);
        if (!isset($this->{$t})) {
            $sm = $this->getServiceLocator();
            $this->$t = $sm->get("Application\\Model\\" . $table);
        }
        return $this->$t;
    }

    public function sitemapAction(){

        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $articlesHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Articles');
        $categoriesHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Categories');
        $categoriesArticlesHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('CategoriesArticles');
        $sitemapFolder = PATH_BASE_ROOT . '/sitemap/';
        if( !is_dir($sitemapFolder) ){
            @mkdir ( $sitemapFolder, 0777 );
        }
        $x_modified = FALSE;
        $xmlSitemap = $sitemapFolder.'sitemap.xml';
        if ( file_exists($xmlSitemap) ) {
            $date_check = date("Y-m-d H:i:s");
            $date_create = date ("Y-m-d H:i:s", filemtime($xmlSitemap));

            $to_time = strtotime($date_check);
            $from_time = strtotime($date_create);
            //$diff_minute = round(abs($to_time - $from_time) / 60,2);
            //$diff_hour = round(abs($to_time - $from_time) / (60*60),2);
            $diff_day = round(abs($to_time - $from_time) / (60*60*24),2);
            if( $diff_day < 1 ){
                $x_modified = TRUE;
            }
        }

        if( !$x_modified ){
            $sitemap = new Sitemap($this->baseUrl.$this->getUrlPrefixLang());
            $sitemap->setDomain($this->baseUrl.$this->getUrlPrefixLang());
            $sitemap->setPath($sitemapFolder);
            $sitemap->setFilename('sitemap');
            $sitemap->addItem('/', '1.0', 'daily', 'Today');

            if( !empty($this->categories) ){
                foreach ($this->categories as $key => $categories01) {
                    foreach ($categories01 as $key => $category) {
                        $sitemap->addItem(str_replace(FOLDERWEB, '', $categoriesHelper->getCategoriesUrl($category)), '0.9', 'daily', $category['date_create'] );
                    }
                }
            }

            $params = array();
            $rows = $this->getModelTable('ProductsTable')->getProductAll($params);
            if( !empty($rows) ){
                foreach ($rows as $key => $p) {
                    $sitemap->addItem(str_replace(FOLDERWEB, '', $productsHelper->getLink($p)), '0.9', 'daily', $p['date_create'] );
                }
            }

            $categoriesArticles = $categoriesArticlesHelper->getAllCategoryArticleAndSort();
            if( !empty($categoriesArticles) ){
                foreach ($categoriesArticles as $key => $categories01) {
                    foreach ($categories01 as $key => $category) {
                        $sitemap->addItem(str_replace(FOLDERWEB, '', $categoriesArticlesHelper->getCategoriesUrl($category)), '0.9', 'daily', $category['date_create'] );
                    }
                }
            }

            $articles = $this->getModelTable('ArticlesTable')->fetchAll();
            if( !empty($articles) ){
                foreach ($articles as $key => $new) {
                    $sitemap->addItem(str_replace(FOLDERWEB, '', $articlesHelper->getUrl($new)) , '0.8', 'daily', $articlesHelper->getDateCreate($new) );
                }
            }

            $sitemap->addItem('/contact', '0.6', 'yearly', $this->website['date_create']);
            $sitemap->createSitemapIndex($this->baseUrl.$this->getUrlPrefixLang().'/sitemap.xml' , 'Today');
        }

        try{
            $xml = file_get_contents($xmlSitemap);
            header ("Content-type: text/xml; charset=utf-8");
            echo $xml;
        }catch(\Exception $ex){}
        die();
    }

}
