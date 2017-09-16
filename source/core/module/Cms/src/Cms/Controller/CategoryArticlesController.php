<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 10:05 AM
 */

namespace Cms\Controller;

use Cms\Form\CategoryArticlesForm;
use Cms\Model\CategoryArticles;
use Zend\Db\Sql\Predicate\In;
use Zend\View\Model\ViewModel;

use JasonGrimes\Paginator;

class CategoryArticlesController extends BackEndController{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'carticles';
    }

    public function indexAction(){
        $language = $this->params()->fromQuery('language', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $page = $this->params()->fromQuery('page', 1);
        $id = $this->params()->fromRoute('id', 0);
        $q = $this->params()->fromQuery('q', '');
        $type = $this->params()->fromQuery('type', 0);

        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;
        $params['parent_id'] = $id;

        if( !empty($q) ){
            $params['categories_articles_title'] = $q;
        }

        $total = $this->getModelTable('CategoryArticlesTable')->countAll($params);
        $categories = $this->getModelTable('CategoryArticlesTable')->fetchAll($params);

        $link = '/cms/category-articles' .( !empty($id) ? '/index/'.$id : ''). '?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $category = array();
        if( !empty($id) ){
            $category = $this->getModelTable('CategoryArticlesTable')->getCategoryLanguage($id, $language);
        }

        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['category'] = $category;
        $this->data_view['categories'] = $categories;
        $this->data_view['page'] = $page;
        $this->data_view['limit'] = $limit;
        $this->data_view['id'] = $id;
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction(){
        $parent_id = $this->params()->fromRoute('id', 0);
        $listlanguage=  $this->getModelTable('LanguagesTable')->getLanguages();

        $form = new CategoryArticlesForm();
        $form->get('submit')->setValue('Lưu lại');
        $cats = $this->getModelTable('CategoryArticlesTable')->fetchAll();
        $opCategories = $this->multiLevelData(TRUE, $cats, 'categories_articles_id', 'parent_id', 'categories_articles_title');
        if( empty($opCategories) ){
            $opCategories = array(
                    0 => 'ROOT'
                );
        }
        $form->get('parent_id')->setOptions(array(
            'options' => $opCategories
        ));
        if( !empty($parent_id) ){
            $form->get('parent_id')->setValue( $parent_id );
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $categoryArticles = new CategoryArticles();
            $form->setInputFilter($categoryArticles->getInputFilter());
            $form->setData($request->getPost());
            if ( $form->isValid() ) {
                $categoryArticles->exchangeArray($form->getData());
                $language = $request->getPost('language', '1');
                $categoryArticles->language=$language;
                try{
                    $id = $this->getModelTable('CategoryArticlesTable')->saveCategory($categoryArticles);
                    $this->getModelTable('CategoryArticlesTable')->saveCategoryTranslate($categoryArticles, $id);
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/carticles', array('action' => 'index', 'id' => $categoryArticles->parent_id));
                }catch(\Exception $ex){
                    die($ex->getMessage());
                }
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['language_list'] = $listlanguage;
        $this->data_view['parent_id'] = $parent_id;
        return $this->data_view;
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        $language = $this->params()->fromQuery('language', 1);
        if (!$id) {
            return $this->redirect()->toRoute('cms/carticles', array(
                'action' => 'add'
            ));
        }
        try {
            $cat = $this->getModelTable('CategoryArticlesTable')->getCategoryLanguage($id, $language);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/carticles', array(
                'action' => 'index'
            ));
        }
        if(empty($cat)){
            return $this->redirect()->toRoute('cms/carticles', array(
                'action' => 'index'
            ));
        }

        $listlanguage=  $this->getModelTable('LanguagesTable')->getLanguages();
        $form = new CategoryArticlesForm();
        $cats = $this->getModelTable('CategoryArticlesTable')->fetchAll( array('languages_id' => $language) );
        $form->get('parent_id')->setOptions(array(
            'options' => $this->multiLevelData(TRUE, $cats, 'categories_articles_id', 'parent_id', 'categories_articles_title')
        ));
        $form->bind($cat);
        $form->get('submit')->setAttribute('value', 'Edit');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $categoryArticles = new CategoryArticles();
            $form->setInputFilter($categoryArticles->getInputFilter());
            $form->setData($request->getPost());
            if ( $form->isValid() ) {
                $categoryArticles->exchangeArray($form->getData());
                $language = $request->getPost('language', '1');
                $categoryArticles->language=$language;
                try{
                    $id = $this->getModelTable('CategoryArticlesTable')->saveCategory($categoryArticles);
                    $this->getModelTable('CategoryArticlesTable')->saveCategoryTranslate($categoryArticles, $id);
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/carticles', array('action' => 'index', 'id' => $categoryArticles->parent_id));
                }catch(\Exception $ex){}
            }
        }
        $this->data_view['id'] = $id;
        $this->data_view['category'] = $cat;
        $this->data_view['form'] = $form;
        $this->data_view['language_list'] = $listlanguage;
        $this->data_view['langselected'] = $language;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('CategoryArticlesTable')->deleteCategory($ids);
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('CategoryArticlesTable')->deleteCategory($id);
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/carticles');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('CategoryArticlesTable')->updateCategories($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('CategoryArticlesTable')->updateCategories($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/carticles');
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('CategoryArticlesTable')->updateCategories($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('CategoryArticlesTable')->updateCategories($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/carticles');
    }

    public function staticAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_static' => 1
            );
            $this->getModelTable('CategoryArticlesTable')->updateCategories($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_static' => 1
                );
                $this->getModelTable('CategoryArticlesTable')->updateCategories($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/carticles');
    }

    public function unstaticAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_static' => 0
            );
            $this->getModelTable('CategoryArticlesTable')->updateCategories($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_static' => 0
                );
                $this->getModelTable('CategoryArticlesTable')->updateCategories($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/carticles');
    }

    public function stepUpdateOrderAction($categories, $index )
    {
        if( !empty($categories[$index]) ){
            foreach ($categories[$index] as $key => $category) {
                $row = array();
                $row['ordering'] = $key;
                $this->getModelTable('CategoryArticlesTable')->updateCategories($category['categories_articles_id'], $row);
                if( !empty($categories[$category['categories_articles_id']]) ){
                    $this->stepUpdateOrderAction($categories, $category['categories_articles_id']);
                }
            }
        }
    }

    public function autoOrderAction()
    {
        $categories = $this->getModelTable('CategoryArticlesTable')->getAllCategoriesSort();
        if( !empty($categories) ){
            $this->stepUpdateOrderAction($categories, 0);
        }
        /*strigger change namespace cached*/
        $this->updateNamespaceCached();
        return $this->redirect()->toRoute('cms/carticles', array(
            'action' => 'index'
        ));
    }

    public function updateorderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('ordering');
            $this->getModelTable('CategoryArticlesTable')->updateOrder($data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }
        return $this->redirect()->toRoute('cms/carticles');
    }

    public function singleDropdownAction(){
        $params = array();
        $categories = $this->getModelTable('CategoryArticlesTable')->getAllCategoriesSort($params);
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'categories' => $categories,
        ));
        return $result;
    }

    public function singleSuggestAction(){
        $language = $this->params()->fromQuery('language', 1);

        $page = $this->params()->fromQuery('page', 0);
        $categories_articles_title = $this->params()->fromQuery('categories_articles_title', NULL);
        $this->intPage = $page;
        $where = 'categories_articles_translate.language='.$language;
        if($categories_articles_title){
            $categories_articles_alias = $this->toAlias($categories_articles_title);
            $where = " categories_articles.categories_articles_alias LIKE '%{$categories_articles_alias}%' ";
        }

        $total = $this->getModelTable('CategoryArticlesTable')->countAll($where);
        $categories = $this->getModelTable('CategoryArticlesTable')->fetchAll($where, 'ordering', $this->intPage, $this->intPageSize);
        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'total' => $total,
            'page' => $page,
            'intPageSize' => $this->intPageSize,
            'categories' => $categories,
        ));

        return $result;
    }

} 