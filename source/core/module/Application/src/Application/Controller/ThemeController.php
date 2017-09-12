<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;

use MatthiasMullie\Minify;


class ThemeController extends FrontEndController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
    	$translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);

    	$price = $this->params()->fromQuery('price', '');
    	$collection = $this->params()->fromQuery('collection', '');
    	$sort = $this->params()->fromQuery('sort', '');
    	$search = $this->params()->fromQuery('search', '');
    	$where = '';
    	if( !empty($price) && $price == 'free' ){
    		$where .= 'template.templete_price = 0';
    	}else if( !empty($price) && $price == 'paid'  ){
    		$where .= 'template.templete_price > 0';
    	}

    	$template = array();
    	if( !empty($collection) ){
    		$template = $this->getModelTable('CategoryTemplatesTable')->getCategory($collection);
    		$where .= ( empty($where) ? '' : ' AND ').'template.categories_template_id = '.$collection;
    	}

    	$order = '';
    	if( !empty($sort) && $sort == 1 ){
    		$order = array(
	            'templete_price' => 'ASC',
	        );
    	}else if( !empty($sort) && $sort == 2  ){
    		$order = array(
	            'templete_price' => 'DESC',
	        );
    	}else if( !empty($sort) && $sort == 3  ){
    		$order = array(
	            'template_name' => 'ASC',
	        );
    	}else if( !empty($sort) && $sort == 4  ){
    		$order = array(
	            'template_name' => 'DESC',
	        );
    	}else if( !empty($sort) && $sort == 5  ){
    		$order = array(
	            'total_websites' => 'DESC',
	        );
    	}

    	if( !empty($search) ){
    		$where .= ( empty($where) ? '' : ' AND ').'template.template_name LIKE \'%'.$search.'%\'';
    	}

    	$list_category = $this->getModelTable('CategoryTemplatesTable')->getAll();
    	$templetes = $this->getModelTable('TemplatesTable')->getList($where, $order, 0 , 12);
    	$hots = $this->getModelTable('TemplatesTable')->getTemplatesHots('', 0 , 3);

    	$this->data_view['list_category'] = $list_category;
    	$this->data_view['templetes'] = $templetes;
    	$this->data_view['template'] = $template;
    	$this->data_view['hots'] = $hots;
    	$this->data_view['price'] = $price;
    	$this->data_view['collection'] = $collection;
    	$this->data_view['sort'] = $sort;
    	$this->data_view['search'] = $search;

        return $this->data_view;
    }

    public function detailAction()
    {
    	$translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);

        $alias = $this->params()->fromRoute('alias', '');
        $id = $this->params()->fromRoute('id', 0);
        $theme = $this->getModelTable('TemplatesTable')->getTemplateById($id);
        if ($theme) {
	        $category = $this->getModelTable('CategoryTemplatesTable')->getCategory($theme->categories_template_id);
	        $hots = $this->getModelTable('TemplatesTable')->getTemplatesHots('template.categories_template_id='.$theme->categories_template_id, 0 , 12);
	        $this->data_view['theme'] = $theme;
	        $this->data_view['category'] = $category;
	    	$this->data_view['hots'] = $hots;
	        return $this->data_view;
	    }
	    return $this->redirect()->toRoute($this->getUrlRouterLang().'home');
    }

}
