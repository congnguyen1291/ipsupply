<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;
use Cms\Form\ArticlesForm;
use Cms\Lib\Paging;
use Cms\Model\Articles;
use Zend\Db\Sql\Predicate\In;
use Zend\View\Model\ViewModel;
use Zend\Dom\Query;

use JasonGrimes\Paginator;

class ArticlesController extends BackEndController{
    public function __construct(){
        parent::__construct();
        $this->data_view['current'] = 'articles';
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

        if( !empty($q) ){
            $params['articles_title'] = $q;
        }

        $category = array();
        if( !empty($id) ){
            $params['categories_articles_id'] = $id;
            $category = $this->getModelTable('CategoryArticlesTable')->getCategoryLanguage($id, $language);
        }
        $total = $this->getModelTable('ArticlesTable')->countAll( $params );
        $articles = $this->getModelTable('ArticlesTable')->fetchAll( $params );

        $link = '/cms/articles' .( !empty($id) ? '/index/'.$id : ''). '?page=(:num)'.( !empty($q) ? '&q='.$q.'&type='.$type : '');
        $paginator = new Paginator($total, $limit, $page, $link);

        $this->data_view['articles'] = $articles;
        $this->data_view['category'] = $category;
        $this->data_view['limit'] = $limit;
        $this->data_view['page'] = $page;
        $this->data_view['id'] = $id;
        $this->data_view['paging'] = $paginator->toHtml();
        $this->data_view['q'] = $q;
        $this->data_view['type'] = $type;
        return $this->data_view;
    }

    public function addAction(){
        $listlanguage=  $this->getModelTable('LanguagesTable')->getLanguages();
        $form = new ArticlesForm();
        $form->get('submit')->setValue('Lưu lại');
        $cats = $this->getModelTable('CategoryArticlesTable')->fetchAll();
        $data_cats = $this->multiLevelData(TRUE, $cats, 'categories_articles_id', 'parent_id', 'categories_articles_title', '- Chọn danh mục -');
        $form->get('categories_articles_id')->setOptions(array(
            'options' => $data_cats
        ));
        $form->get('users_id')->setAttributes(array(
            'value' => $_SESSION['CMSMEMBER']['users_id']
        ));
        $form->get('users_fullname')->setAttributes(array(
            'value' => $_SESSION['CMSMEMBER']['full_name']
        ));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $article = new Articles();
            $form->setInputFilter($article->getInputFilter());
            $form->setData($request->getPost());
            if ( $form->isValid() ) {
                $article->exchangeArray($form->getData());
                $language = $request->getPost('language', '1');
                $picture_id = $request->getPost('picture_id');
                $article->language=$language;
                $article->picture_id=$picture_id;
                try{
                    $id = $this->getModelTable('ArticlesTable')->saveArticle($article);
                    $this->getModelTable('ArticlesTable')->saveArticleTranslate($article, $id);
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/articles');
                }catch(\Exception $ex){}
            }
        }
        $this->data_view['form'] = $form;
        $this->data_view['language_list'] = $listlanguage;
        return $this->data_view;
    }

    public function editAction(){
        $id = (int)$this->params()->fromRoute('id', 0);
        $language = $this->params()->fromQuery('language', 1);
        if (!$id) {
            return $this->redirect()->toRoute('cms/articles', array(
                'action' => 'add'
            ));
        }
        try {
            $article = $this->getModelTable('ArticlesTable')->getArticleLanguage($id, $language);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/articles', array(
                'action' => 'index'
            ));
        }
        $listlanguage=  $this->getModelTable('LanguagesTable')->getLanguages();
        $images = $this->getModelTable('ArticlesTable')->getImageList($id);

        $form = new ArticlesForm();
        $cats = $this->getModelTable('CategoryArticlesTable')->fetchAll( array('languages_id' => $language) );
        $data_cats = $this->multiLevelData(TRUE, $cats, 'categories_articles_id', 'parent_id', 'categories_articles_title');
        $form->get('categories_articles_id')->setOptions(array(
            'options' => $data_cats
        ));
        if( !empty($article->tags) ){
            $arr_tags = $this->getModelTable('TagsTable')->getTagsOfProduct($article->tags);
            $tags = array();
            foreach ($arr_tags as $key => $val) {
                $tags[] = $val['tags_name'];
            }
            $article->tags = implode(',', $tags);
        }

        $form->bind($article);
        $form->get('submit')->setAttribute('value', 'Edit');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($article->getInputFilter());
            $form->setData($request->getPost());
            if ( $form->isValid() ) {
                $language = $request->getPost('language', '1');
                $picture_id = $request->getPost('picture_id');
                $article->language=$language;
                $article->picture_id=$picture_id;
                try{
                    $id = $this->getModelTable('ArticlesTable')->saveArticle($article);
                    $this->getModelTable('ArticlesTable')->saveArticleTranslate($article, $id);
                    $this->updateNamespaceCached();
                    return $this->redirect()->toRoute('cms/articles');
                }catch(\Exception $ex){}
            }
        }

        $this->data_view['form'] = $form;
        $this->data_view['article'] = $article;
        $this->data_view['images'] = $images;
        $this->data_view['id'] = $id;
        $this->data_view['language_list'] = $listlanguage;
        $this->data_view['langselected'] = $language;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $this->getModelTable('ArticlesTable')->deleteArticles($ids);
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $this->getModelTable('ArticlesTable')->deleteArticles($id);
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/articles');
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('ArticlesTable')->updateArticles($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 1
                );
                $this->getModelTable('ArticlesTable')->updateArticles($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/articles');
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('ArticlesTable')->updateArticles($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_published' => 0
                );
                $this->getModelTable('ArticlesTable')->updateArticles($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/articles');
    }

    public function newAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_new' => 1
            );
            $this->getModelTable('ArticlesTable')->updateArticles($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_new' => 1
                );
                $this->getModelTable('ArticlesTable')->updateArticles($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/articles');
    }

    public function unnewAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_new' => 0
            );
            $this->getModelTable('ArticlesTable')->updateArticles($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_new' => 0
                );
                $this->getModelTable('ArticlesTable')->updateArticles($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/articles');
    }

    public function hotAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_hot' => 1
            );
            $this->getModelTable('ArticlesTable')->updateArticles($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_hot' => 1
                );
                $this->getModelTable('ArticlesTable')->updateArticles($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/articles');
    }

    public function unhotAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_hot' => 0
            );
            $this->getModelTable('ArticlesTable')->updateArticles($ids, $data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }else{
            $id = (int)$this->params()->fromRoute('id', 0);
            if ( !empty($id) ) {
                $data = array(
                    'is_hot' => 0
                );
                $this->getModelTable('ArticlesTable')->updateArticles($id, $data);
                /*strigger change namespace cached*/
                $this->updateNamespaceCached();
            }
        }
        return $this->redirect()->toRoute('cms/articles');
    }

    public function autoOrderAction()
    {
        $articles = $this->getModelTable('ArticlesTable')->fetchAll();
        foreach ($articles as $key => $article) {
            $row = array();
            $row['ordering'] = $key;
            $this->getModelTable('ArticlesTable')->updateArticles($article['articles_id'], $row);
        }
        /*strigger change namespace cached*/
        $this->updateNamespaceCached();
        return $this->redirect()->toRoute('cms/articles', array(
                'action' => 'index'
            ));
    }

    public function updateorderAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost('ordering');
            $this->getModelTable('ArticlesTable')->updateOrder($data);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
        }
        return $this->redirect()->toRoute('cms/articles');
    }

    public function deleteimageAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $id = intval($request->getPost('itemid'));
            $image = $request->getPost('filename');
            $folder = 'article'.$id;
            $filename = explode('/',$image);
            $filename = end($filename);
            $file = PATH_BASE_ROOT.DS.'custom'.DS.'domain_1'.DS.'articles'.DS.'fullsize'.DS.$folder.DS.$filename;
            if(is_file($file)){
                @unlink($file);
            }
            $this->getModelTable('ArticlesTable')->deleteImageArticle($id, $image);
            /*strigger change namespace cached*/
            $this->updateNamespaceCached();
            echo json_encode(array(
                'success' => TRUE,
                'msg'     => 'Xóa thành công'
            ));
            die();
        }
        echo json_encode(array(
            'success' => FALSE,
            'error_code'     => '501'
        ));
        die();
    }

    public function filterAction(){
		$request = $this->getRequest();
		$articles = array();
		if ($request->isPost()) {
			$data_filter = $request->getPost();
			$articles = $this->getModelTable('ArticlesTable')->filter($data_filter);
		}
		$result = new ViewModel();
		$result->setTerminal(true);
		$result->setVariables(array(
			'articles' => $articles
		));
		return $result;
    }

    public function findArticlesAction(){
        $str = $_GET['query'];
        $articles = $this->getModelTable('ArticlesTable')->findArticles($str);
        echo json_encode(array(
            'suggestions' => $articles
        ));
        die;
    }

    public function singleDropdownAction(){
        $request = $this->getRequest();
        $page = $this->params()->fromQuery('page', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $is_static = $request->getPost('is_static', 0);
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;
        $params['is_static'] = $is_static;

        $articles_title = $this->params()->fromQuery('articles_title', '');
        if( !empty($articles_title) ){
            $params['articles_title'] = $articles_title;
        }
        
        $total = $this->getModelTable('ArticlesTable')->countAll($params);
        $articles = $this->getModelTable('ArticlesTable')->fetchAll($params);

        $link = 'dropMenuLoadDataArticle(this, (:num));';
        $paginator = new Paginator($total, $limit, $page, $link);

        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'articles_title' => $articles_title,
            'total' => $total,
            'articles' => $articles,
            'is_static' => $is_static,
            'paginator' => $paginator,
        ));

        return $result;
    }

    public function singleSuggestAction(){
        $page = $this->params()->fromQuery('page', 1);
        $limit = $this->params()->fromQuery('limit', 20);
        $is_static = $this->params()->fromQuery('is_static', 0);
        $params = array();
        $params['page'] = $page;
        $params['limit'] = $limit;
        $params['is_static'] = $is_static;

        $articles_title = $this->params()->fromQuery('articles_title', '');
        if( !empty($articles_title) ){
            $params['articles_title'] = $articles_title;
        }
        
        $total = $this->getModelTable('ArticlesTable')->countAll($params);
        $articles = $this->getModelTable('ArticlesTable')->fetchAll($params);

        $link = 'dropMenuLoadDataArticle(this, (:num));';
        $paginator = new Paginator($total, $limit, $page, $link);

        $result = new ViewModel();
        $result->setTerminal(true);
        $result->setVariables(array(
            'articles_title' => $articles_title,
            'total' => $total,
            'articles' => $articles,
            'is_static' => $is_static,
            'paginator' => $paginator,
        ));

        return $result;
    }

}