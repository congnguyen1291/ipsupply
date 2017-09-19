<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class CategoriesArticles extends App
{
    public function getAllCategoriesArticlesSort($offset=0, $limit=0)
    {
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllCategoriesArticlesSort');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesArticlesTable')->getAllCategoriesArticlesSort();
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getAllCategoriesArticlesWithArrayId_Parents()
    {
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllCategoriesArticlesWithArrayIdParents');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesArticlesTable')->getAllCategoriesArticlesWithArrayId_Parents();
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getAllCategoryArticleAndSort()
    {
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllCategoriesArticlesSort');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesArticlesTable')->getAllCategoriesArticlesSort();
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }
    
    public function getOneCategoryArticle($categories_articles_id)
    {
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getOneCategoryArticle('.$categories_articles_id.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesArticlesTable')->getOneCategoryArticle($categories_articles_id);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getAllParentsOfCategory($categories_articles_id, $has_me = false)
    {
        $_has_me = 0;
        if($has_me){
            $_has_me = 1;
        }
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllParentsOfCategory('.$categories_articles_id.','.$_has_me.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesArticlesTable')->getAllParentsOfCategory($categories_articles_id, $has_me);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getAllCategoriesArticlesSortWithKeyValue()
    {
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllCategoriesArticlesSortWithKeyValue');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesArticlesTable')->getAllCategoriesArticlesSortWithKeyValue();
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getAllIdChildOfCategory($categories_articles_id, $has_me = false)
    {
        $_has_me = 0;
        if($has_me){
            $_has_me = 1;
        }
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllChildOfCate('.$categories_articles_id.','.$_has_me.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesArticlesTable')->getAllChildOfCate($categories_articles_id);
            if(!empty($has_me)){
                $results[] = $categories_articles_id;
            }
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getRoot($categories){
        if( !empty($categories) && !empty($categories[0]) ){
            return $categories[0];
        }
        return array();
    }

    public function getCategoriesUrl($categories){
        if( !empty($categories) 
            && !empty($categories['categories_articles_alias'])
            && !empty($categories['categories_articles_id']) ){
            return FOLDERWEB .$this->getUrlPrefixLang(). '/articles/'.$categories['categories_articles_alias'].'-'.$categories['categories_articles_id'];
        }
        return '';
    }

    public function getId($categories){
        if( !empty($categories) && !empty($categories['categories_articles_id']) ){
            return $categories['categories_articles_id'];
        }
        return '';
    }

    public function getTitle($categories){
        if( !empty($categories) && !empty($categories['categories_articles_title']) ){
            return $categories['categories_articles_title'];
        }
        return '';
    }

    public function getNumberArticle($categories){
        if( !empty($categories) 
            && !empty($categories['number_article']) ){
            return $categories['number_article'];
        }
        return 0;
    }

    public function getIcon($categories){
        if( !empty($categories) && !empty($categories['icon']) ){
            return $categories['icon'];
        }
        return '';
    }

    public function getChildrens($categories, $categories_id){
        if( !empty($categories)
            && !empty($categories_id) 
            && !empty($categories[$categories_id]) ){
            return $categories[$categories_id];
        }
        return array();
    }
	
}
