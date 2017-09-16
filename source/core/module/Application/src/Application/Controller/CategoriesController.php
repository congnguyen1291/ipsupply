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

use JasonGrimes\Paginator;

class CategoriesController extends FrontEndController
{

    public function indexAction()
    {
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
        $price = $this->params()->fromQuery('price', '');
        $rating = $this->params()->fromQuery('rating', '');
        $view = $this->params()->fromQuery('view', 0);

        $is_pjax = $this->params()->fromHeader('X-PJAX', '');
        $partial = $this->params()->fromQuery('partial', '');

        if( is_array($feature) ){
            $feature = implode(";", $feature);
        }
        $params = array();
        $params['manus'] = $manus;
        $params['sort'] = $sort;
        $params['filter'] = $filter;
        $params['feature'] = $feature;
        $params['price'] = $price;
        if( !empty($rating) ){
            $params['rating'] = $rating;
        }
        $params['page'] = $page;
        $params['page_size'] = $page_size;

        $this->addLinkPageInfo($this->baseUrl .$this->getUrlPrefixLang().'/category');
        $this->addParamsPageInfo($params);
 
        $rows = $this->getModelTable('ProductsTable')->getProductAll($params);
        $total = $this->getModelTable('ProductsTable')->countTotalProductAll($params);

        unset($params['page_size']);
        $params['page'] = '(:num)';
        $link = $this->baseUrl .$this->getUrlPrefixLang().'/category?'. $helper->buildParamsForUrlFromArray($params);
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
        $this->data_view['rows'] = $rows;

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
    }

    public function internationalAction()
    {
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
        $price = $this->params()->fromQuery('price', '');
        $rating = $this->params()->fromQuery('rating', '');
        $view = $this->params()->fromQuery('view', 0);
        $id = $this->params()->fromRoute('id', 0);

        $is_pjax = $this->params()->fromHeader('X-PJAX', '');
        $partial = $this->params()->fromQuery('partial', '');

        if( is_array($feature) ){
            $feature = implode(";", $feature);
        }
        $params = array();
        $params['manus'] = $manus;
        $params['sort'] = $sort;
        $params['filter'] = $filter;
        $params['feature'] = $feature;
        $params['price'] = $price;
        if( $rating == 0 || !empty($rating) ){
            $params['rating'] = $rating;
        }
        $params['page'] = $page;
        $params['page_size'] = $page_size;

        $this->addLinkPageInfo($this->baseUrl .$this->getUrlPrefixLang().'/category');
        $this->addParamsPageInfo($params);
        $total = 0;
        $rows = array();
        if( !empty($id) ){
            $params['country_id'] = $id;
            $rows = $this->getModelTable('ProductsTable')->getProductsIgnoreLocation($params);
            $total = $this->getModelTable('ProductsTable')->getTotalProductIgnoreLocation($params);
        }

        unset($params['page_size']);
        $params['page'] = '(:num)';
        $link = $this->baseUrl .$this->getUrlPrefixLang().'/international'.(!empty($id) ? '/id'.$id : '').'.html?'. $helper->buildParamsForUrlFromArray($params);
        $paginator = new Paginator($total, $page_size, $page, $link);

        $this->data_view['id'] = $id;
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

        if( !empty($is_pjax) ){
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            if( empty($partial) ){
                $viewModel->setTemplate("application/categories/international");
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
    }

    public function detailAction()
    {
		$translator = $this->getServiceLocator()->get('translator');
        $id = $this->params()->fromRoute('id', null);
        $categories = $this->getModelTable('CategoriesTable')->getRow($id);
        if ( $categories ) {
            $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
            $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
            $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
			
			$page = $this->params()->fromQuery('page', 1);
			$pageadd = '';
			if(!empty($page) or $page>=2){
				$pageadd=" ".$translator->translate('page')." ".$page;
			}
            $headTitle = $categories->categories_title.$pageadd;
			if ( !empty($categories->seo_title)) {
				$headTitle = $categories->seo_title.$pageadd;
            }
            $renderer->headTitle($headTitle);
            if ( !empty($categories->seo_keywords)) {
                $renderer->headMeta()->appendName('keywords', $categories->seo_keywords.$pageadd);
            }

            if ( !empty($categories->seo_description) ) {
                $renderer->headMeta()->appendName('description', $categories->seo_description);
            }

            $page_size = $this->params()->fromQuery('page_size', 18);
            $page = $this->params()->fromQuery('page', 0);
            $sort = $this->params()->fromQuery('sort', 'new');
            $filter = $this->params()->fromQuery('filter', '');
            $manus = $this->params()->fromQuery('manus', '');
            $feature = $this->params()->fromQuery('feature', '');
            $price = $this->params()->fromQuery('price', '');
            $rating = $this->params()->fromQuery('rating', '');
            $view = $this->params()->fromQuery('view', 0);

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
            $this->addLinkPageInfo($this->baseUrl .$this->getUrlPrefixLang().'/'.$categories->categories_alias.'-'.$categories->categories_id.'s.html');
            $params['categories_id'] = $id;
            $this->addParamsPageInfo($params);
            unset($params['categories_id']);
            $list = $this->getModelTable('CategoriesTable')->getAllChildOfCate($id);
            $list[] = $id;
            $params['categories_id'] = $list;
            $rows = $this->getModelTable('ProductsTable')->getProductAll($params);
            $total = $this->getModelTable('ProductsTable')->countTotalProductAll($params);

            unset($params['page_size']);
            unset($params['categories_id']);
            $params['page'] = '(:num)';
            $link = $this->baseUrl .$this->getUrlPrefixLang().'/'.$categories->categories_alias.'-'.$categories->categories_id.'s.html?'. $helper->buildParamsForUrlFromArray($params);
            $paginator = new Paginator($total, $page_size, $page, $link);
            
            $this->data_view['id'] = $id;
            $this->data_view['list'] = $list;
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
            $this->data_view['category'] = $categories;

            if( !empty($is_pjax) ){
                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                if( empty($partial) ){
                    $viewModel->setTemplate("application/categories/detail");
                }else{
                    $viewModel->setTemplate("application/categories/list-product");
                }
                $viewModel->setVariables($this->data_view);
                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);
                if( empty($partial) ){
                    $html = "<html>
                            <head>
                                <title>{$headTitle}</title>
                                <meta name=\"description\" content=\"{$categories->seo_description}\" />
                                <meta name=\"keywords\" content=\"{$categories->seo_keywords}\" />
                            </head>
                            <body>
                               {$html}
                            </body>";
                }
                echo $html;
                die();
            }

            return $this->data_view;
        }

        return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }


    public function hotsAction()
    {
        $page_size = $this->params()->fromQuery('page_size', 6);
        $page = $this->params()->fromQuery('page', 1);
 
        $rows = $this->getModelTable('ProductsTable')->getHotProduct($page, $page_size);
        $total = $this->getModelTable('ProductsTable')->getTotalHotProduct();

        $link = $this->baseUrl .$this->getUrlPrefixLang().'/category/hots?page=(:num)';
        $paginator = new Paginator($total, $page_size, $page, $link);

        $this->data_view['page'] = $page;
        $this->data_view['total'] = $total;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['paginator'] = $paginator;
        $this->data_view['rows'] = $rows;
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate("application/categories/hots");
        $viewModel->setVariables($this->data_view);
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRender->render($viewModel);
        $item = array(
                'total' => $total,
                'page_size' => $page_size,
                'page' => $page,
                'pages' => $paginator->getPages(),
                'rows' => $rows,
                'paging' => $paginator->toHtml(),
                'html' => $html
            );
        echo json_encode($item);
        die();
    }

    public function newsAction()
    {
        $page_size = $this->params()->fromQuery('page_size', 6);
        $page = $this->params()->fromQuery('page', 1);
 
        $rows = $this->getModelTable('ProductsTable')->getNewProduct($page, $page_size);
        $total = $this->getModelTable('ProductsTable')->getTotalNewProduct();

        $link = $this->baseUrl .$this->getUrlPrefixLang().'/category/hots?page=(:num)';
        $paginator = new Paginator($total, $page_size, $page, $link);

        $this->data_view['page'] = $page;
        $this->data_view['total'] = $total;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['paginator'] = $paginator;
        $this->data_view['rows'] = $rows;
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate("application/categories/hots");
        $viewModel->setVariables($this->data_view);
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRender->render($viewModel);
        $item = array(
                'total' => $total,
                'page_size' => $page_size,
                'page' => $page,
                'pages' => $paginator->getPages(),
                'rows' => $rows,
                'paging' => $paginator->toHtml(),
                'html' => $html
            );
        echo json_encode($item);
        die();
    }

    public function dealsAction()
    {
        $page_size = $this->params()->fromQuery('page_size', 6);
        $page = $this->params()->fromQuery('page', 1);
 
        $rows = $this->getModelTable('ProductsTable')->getDealProduct($page, $page_size);
        $total = $this->getModelTable('ProductsTable')->getTotalDealProduct();

        $link = $this->baseUrl .$this->getUrlPrefixLang().'/category/hots?page=(:num)';
        $paginator = new Paginator($total, $page_size, $page, $link);

        $this->data_view['page'] = $page;
        $this->data_view['total'] = $total;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['paginator'] = $paginator;
        $this->data_view['rows'] = $rows;
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate("application/categories/hots");
        $viewModel->setVariables($this->data_view);
        $viewRender = $this->getServiceLocator()->get('ViewRenderer');
        $html = $viewRender->render($viewModel);
        $item = array(
                'total' => $total,
                'page_size' => $page_size,
                'page' => $page,
                'pages' => $paginator->getPages(),
                'rows' => $rows,
                'paging' => $paginator->toHtml(),
                'html' => $html
            );
        echo json_encode($item);
        die();
    }

}