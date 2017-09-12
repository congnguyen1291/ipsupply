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

class ArticlesController extends FrontEndController
{
    public function indexAction()
    {
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');

        $page_size = $this->params()->fromQuery('page_size', 20);
        $page = $this->params()->fromQuery('page', 0);

        $articles = $this->getModelTable('ArticlesTable')->getAllArticles($page, $page_size);
        $total = $this->getModelTable('ArticlesTable')->countAllArticles();

        $link = $this->baseUrl .$this->getUrlPrefixLang(). '/articles?page=(:num)';
        $paginator = new Paginator($total, $page_size, $page, $link);

        $this->data_view['page_size'] = $page_size;
        $this->data_view['page'] = $page;
        $this->data_view['total'] = $total;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['paginator'] = $paginator;
        $this->data_view['row'] = $articles;
        $this->data_view['articles'] = $articles;

        $this->addLinkPageInfo($this->baseUrl .'/articles' );
        $is_pjax = $this->params()->fromHeader('X-PJAX', '');
        if( !empty($is_pjax) ){
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            $viewModel->setTemplate("application/articles/index");
            $viewModel->setVariables($this->data_view);
            $viewRender = $this->getServiceLocator()->get('ViewRenderer');
            $html = $viewRender->render($viewModel);
            $html = "<html>
                        <head>
                            <title>{$this->website['seo_title']}</title>
                            <meta name=\"description\" content=\"{$this->website['seo_keywords']}\" />
                            <meta name=\"keywords\" content=\"{$this->website['seo_description']}\" />
                        </head>
                        <body>
                           {$html}
                        </body>";
            echo $html;
            die();
        }
        return $this->data_view;
    }

    public function listingAction()
    {
        $page_size = $this->params()->fromQuery('page_size', 20);
        $page = $this->params()->fromQuery('page', 0);
        $id = $this->params()->fromRoute('id', null);

		$categories_articles = $this->getModelTable('CategoriesArticlesTable')->getRow($id);
        if ( $categories_articles ) {
			
            $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($categories_articles['categories_articles_title']);
            $renderer->headMeta()->appendName('description', $categories_articles['seo_description']);
            $renderer->headMeta()->appendName('keywords', $categories_articles['seo_keywords']);

            $params = array();
            $params['page'] = $page;
            $params['page_size'] = $page_size;

            $list = $this->getModelTable('CategoriesArticlesTable')->getAllChildOfCate($id);
            $list[] = $id;
            $articles = $this->getModelTable('ArticlesTable')->getArticlesCate($list, $params);
            $total = $this->getModelTable('ArticlesTable')->countTotalArticlesCate($list, $params);

            $link = $this->baseUrl .$this->getUrlPrefixLang(). '/articles/'.$categories_articles->categories_articles_alias.'-'.$categories_articles->categories_articles_id.'?page=(:num)';
            $paginator = new Paginator($total, $page_size, $page, $link);

            $this->data_view['page_size'] = $page_size;
            $this->data_view['page'] = $page;
            $this->data_view['total'] = $total;
            $this->data_view['category'] = $categories_articles;
            $this->data_view['articles'] = $articles;
            $this->data_view['row'] = $articles;
            $this->data_view['paging'] = $paginator->toHtml();
            $this->data_view['paginator'] = $paginator;

            $this->addLinkPageInfo( $this->baseUrl .'/articles/'.$categories_articles->categories_articles_alias.'-'.$categories_articles->categories_articles_id );
            $is_pjax = $this->params()->fromHeader('X-PJAX', '');
            if( !empty($is_pjax) ){
                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                $viewModel->setTemplate("application/articles/listing");
                $viewModel->setVariables($this->data_view);
                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);
                $html = "<html>
                        <head>
                            <title>{$categories_articles['categories_articles_title']}</title>
                            <meta name=\"description\" content=\"{$categories_articles['seo_description']}\" />
                            <meta name=\"keywords\" content=\"{$categories_articles['seo_keywords']}\" />
                        </head>
                        <body>
                           {$html}
                        </body>";
                echo $html;
                die();
            }

            return $this->data_view;
		} else {
			return $this->redirect()->toRoute($this->getUrlRouterLang().'BaiViet', array(
                'action' => 'index'
            ));
        }
    }

    public function detailAction()
    {
        $id = $this->params()->fromRoute('id', null);
        if( !$id ){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }
        try{
            $article = $this->getModelTable('ArticlesTable')->getRow($id);
        }catch (\Exception $ex){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
        }
        if ( !empty($article) ) {
            $link = $this->baseUrl .$this->getUrlPrefixLang(). '/'.$article->articles_alias.'-'.$article->articles_id.'.html';
            $script = $this->getServiceLocator()->get('viewhelpermanager')->get('inlineScript');
            $images = $this->getServiceLocator()->get('viewhelpermanager')->get('Images');
			$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
			$renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
            $renderer->headTitle($article['articles_title']);
			$renderer->headMeta()->appendName('keyword', $article['keyword_seo']);
			$renderer->headMeta()->appendName('description', $article['description_seo']);
			$renderer->headMeta()->appendName('og:description', $article['description_seo']);
			$renderer->headMeta()->appendName('og:image', $images->getUrlImage($article['thumb_images']));
			$renderer->headMeta()->appendName('og:type', "product");
			$renderer->headMeta()->appendName('og:url', $link);

			$this->getModelTable('ArticlesTable')->updateNumberView($id,($article->number_views+1));

            $this->data_view['row'] = $article;
            $this->data_view['article'] = $article;

            $this->addLinkPageInfo( $link );
            $is_pjax = $this->params()->fromHeader('X-PJAX', '');
            if( !empty($is_pjax) ){
                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                $viewModel->setTemplate("application/articles/detail");
                $viewModel->setVariables($this->data_view);
                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);
                $html = "<html>
                        <head>
                            <title>{$article['articles_title']}</title>
                            <meta name=\"description\" content=\"{$article['description_seo']}\" />
                            <meta name=\"keywords\" content=\"{$article['keyword_seo']}\" />
                        </head>
                        <body>
                           {$html}
                        </body>";
                echo $html;
                die();
            }
            
            return $this->data_view;
        } else {
			return $this->redirect()->toRoute($this->getUrlRouterLang().'index', array(
                'action' => 'index'
            ));
        }
    }

    public function faqAction()
    {
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
        $is_pjax = $this->params()->fromHeader('X-PJAX', '');
        if( !empty($is_pjax) ){
            $viewModel = new ViewModel();
            $viewModel->setTerminal(true);
            $viewModel->setTemplate("application/articles/faq");
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