<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model;
use Zend\Filter\File\LowerCase;

use JasonGrimes\Paginator;

class SearchController extends FrontEndController {
    public function indexAction() {
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        try {
            $translator = $this->getServiceLocator()->get('translator'); 
            $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($this->website['seo_title']);
            $renderer->headMeta()->appendName('description', $this->website['seo_description']);
            $renderer->headMeta()->appendName('keywords', $this->website['seo_keywords']);
            $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');

            $page_size = $this->params()->fromQuery('page_size', 18);
            $page = $this->params()->fromQuery('page', 1);
            $sort = $this->params()->fromQuery('sort', 'new');
            $filter = $this->params()->fromQuery('filter', '');
            $manus = $this->params()->fromQuery('manus', '');
            $feature = $this->params()->fromQuery('feature', '');
            $rating = $this->params()->fromQuery('rating', '');
            $view = $this->params()->fromQuery('view', 0);
            $keyword = $this->params()->fromQuery('keyword', '');

            $cities = $this->params()->fromQuery('cities', '');
            $price = $this->params()->fromQuery('price', '');
            $categories_id = $this->params()->fromQuery('type', '');
            $time = $this->params()->fromQuery('time', '');

            $is_pjax = $this->params()->fromHeader('X-PJAX', '');
            $partial = $this->params()->fromQuery('partial', '');

            if( is_array($feature) ){
                $feature = implode(";", $feature);
            }
            $params = array();
            $params['page'] = $page;
            $params['page_size'] = $page_size;
            $params['manus'] = $manus;
            $params['sort'] = $sort;
            $params['filter'] = $filter;
            $params['feature'] = $feature;
            $params['price'] = $price;
            if( !empty($rating) ){
                $params['rating'] = $rating;
            }
            $params['keyword'] = $keyword;
            $params['cities'] = $cities;
            $params['categories_id'] = $categories_id;
            $params['time'] = $time;

            $this->addLinkPageInfo($this->baseUrl .$this->getUrlPrefixLang().'/search?keyword='.$keyword);
            $this->addParamsPageInfo($params);

            $rows = $this->getModelTable('ProductsTable')->getProductAll($params);
            $total = $this->getModelTable('ProductsTable')->countTotalProductAll($params);

            unset($params['page_size']);
            unset($params['categories_id']);
            $params['type'] = $categories_id;
            $params['page'] = '(:num)';
            $link = $this->baseUrl .$this->getUrlPrefixLang().'/search?'. $helper->buildParamsForUrlFromArray($params);
            $paginator = new Paginator($total, $page_size, $page, $link);
            
            $this->data_view['price'] = $price;
            $this->data_view['type'] = $categories_id;
            $this->data_view['cities'] = $cities;
            $this->data_view['time'] = $time;
            $this->data_view['manus'] = $manus;
            $this->data_view['filter'] = $filter;
            $this->data_view['sort'] = $sort;
            $this->data_view['view'] = $view;
            $this->data_view['rating'] = $rating;
            $this->data_view['page_size'] = $page_size;
            $this->data_view['page'] = $page;
            $this->data_view['feature'] = $feature;
            $this->data_view['total'] = $total;
            $this->data_view['paging'] = $paginator->toHtml();
            $this->data_view['paginator'] = $paginator;
            $this->data_view['rows'] = $rows;
            $this->data_view['keyword'] = $keyword;
            $this->data_view['products'] = $rows;

            if( !empty($is_pjax) ){
                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                if( empty($partial) ){
                    $viewModel->setTemplate("application/search/index");
                }else{
                    $viewModel->setTemplate("application/search/list-product");
                }
                $viewModel->setVariables($this->data_view);
                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);
                if( empty($partial) ){
                    $html = "<html>
                                <head>
                                    <title>{$this->website['seo_title']}</title>
                                    <meta name=\"description\" content=\"{$this->website['seo_description']}\" />
                                    <meta name=\"keywords\" content=\"{$this->website['seo_keywords']}\" />
                                </head>
                                <body>
                                   {$html}
                                </body>";
                }
                echo $html;
                die();
            }

            return $this->data_view;
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }
    }

    public function tagsAction() {
        $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
        //$script->appendFile(FOLDERWEB.'/styles/js/search.js');
        try {
            $translator = $this->getServiceLocator()->get('translator'); 
            $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($this->website['seo_title']);
            $renderer->headMeta()->appendName('description', $this->website['seo_description']);
            $renderer->headMeta()->appendName('keywords', $this->website['seo_keywords']);
            $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');

            
            $page_size = $this->params()->fromQuery('page_size', 18);
            $page = $this->params()->fromQuery('page', 0);
            $sort = $this->params()->fromQuery('sort', 'new');
            $filter = $this->params()->fromQuery('filter', '');
            $manus = $this->params()->fromQuery('manus', '');
            $feature = $this->params()->fromQuery('feature', '');
            $price = $this->params()->fromQuery('price', '');
            $rating = $this->params()->fromQuery('rating', '');
            $view = $this->params()->fromQuery('view', 0);
            $tag = $this->params()->fromRoute('tag', '');
            
            $is_pjax = $this->params()->fromHeader('X-PJAX', '');
            $partial = $this->params()->fromQuery('partial', '');

            if( is_array($feature) ){
                $feature = implode(";", $feature);
            }
            $params = array();
            $params['page'] = $page;
            $params['page_size'] = $page_size;
            $params['manus'] = $manus;
            $params['sort'] = $sort;
            $params['filter'] = $filter;
            $params['feature'] = $feature;
            $params['price'] = $price;
            if( !empty($rating) ){
                $params['rating'] = $rating;
            }
            $params['tag'] = $tag;

            $this->addLinkPageInfo($this->baseUrl .$this->getUrlPrefixLang().'/tags'.$tag );
            $this->addParamsPageInfo($params);

            $rows = $this->getModelTable('ProductsTable')->getProductTag($params);
            $total = $this->getModelTable('ProductsTable')->countProductTag($params);

            unset($params['page_size']);
            unset($params['tag']);
            $params['page'] = '(:num)';
            $link = $this->baseUrl .$this->getUrlPrefixLang().'/tags/'.$tag.'?'. $helper->buildParamsForUrlFromArray($params);
            $paginator = new Paginator($total, $page_size, $page, $link);

            $this->data_view['manus'] = $manus;
            $this->data_view['filter'] = $filter;
            $this->data_view['sort'] = $sort;
            $this->data_view['view'] = $view;
            $this->data_view['rating'] = $rating;
            $this->data_view['page_size'] = $page_size;
            $this->data_view['page'] = $page;
            $this->data_view['feature'] = $feature;
            $this->data_view['total'] = $total;
            $this->data_view['paging'] = $paginator->toHtml();
            $this->data_view['paginator'] = $paginator;
            $this->data_view['tag'] = $tag;
            $this->data_view['products'] = $rows;

            if( !empty($is_pjax) ){
                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                if( empty($partial) ){
                    $viewModel->setTemplate("application/categories/index");
                }else{
                    $viewModel->setTemplate("application/categories/list-product");
                }
                $viewModel->setVariables($this->data_view);
                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);
                if( empty($partial) ){
                    $html = "<html>
                                <head>
                                    <title>{$this->website['seo_title']}</title>
                                    <meta name=\"description\" content=\"{$this->website['seo_description']}\" />
                                    <meta name=\"keywords\" content=\"{$this->website['seo_keywords']}\" />
                                </head>
                                <body>
                                   {$html}
                                </body>";
                }
                echo $html;
                die();
            }

            return $this->data_view;
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }
    }

}
