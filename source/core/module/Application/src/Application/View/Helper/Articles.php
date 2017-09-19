<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Articles  extends App
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

    public function getCategoriesArticlesById($id)
    {
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getRow('.(is_array($id) ? implode('-',$id) : $id).')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('CategoriesArticlesTable')->getRow($id);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getTopArticles($offset=0, $limit=5)
    {
        $key = md5($this->getNamspaceCached().':ArticlesTable:getTopArticles('.$offset.';'.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ArticlesTable')->getTopArticles($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getArticleOther($articles_id, $categories_articles_id)
    {
        $whereother = 'articles.articles_id != ' .$articles_id. ' AND articles.categories_articles_id = ' . $categories_articles_id;
        $key = md5($this->getNamspaceCached().':ArticlesTable:getAllLimit('.(is_array($where) ? implode("-",$where) : $where).')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ArticlesTable')->getAllLimit($whereother);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $articles;
    }

    public function getArticleForEachCategories($cat_root, $limit = 5)
    {
        $list_id  = array();
        foreach ($cat_root as $key => $cat) {
            $list_id[] = $cat['categories_articles_id'];
        }
        $key = md5($this->getNamspaceCached().':ArticlesTable:getArticleForEachCategories('.implode('-',$list_id).'-'.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ArticlesTable')->getArticleForEachCategories($cat_root, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }
	// CN them getArticleForEachCategoriesAll
	public function getArticleForEachCategoriesAll($cat_root, $limit = 5)
    {
        $list_id  = array();
        foreach ($cat_root as $key => $cat) {
            $list_id[] = $cat;
        }
        $key = md5($this->getNamspaceCached().':ArticlesTable:getArticleForEachCategoriesAll('.implode('-',$list_id).'-'.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ArticlesTable')->getArticleForEachCategoriesAll($cat_root, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }
	// end
    public function getNewsArticles($offset=0, $limit=5)
    {
        $key = md5($this->getNamspaceCached().':ArticlesTable:getNewsArticles('.$offset.';'.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ArticlesTable')->getNewsArticles($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getHotsArticles($offset=0, $limit=5)
    {
        $key = md5($this->getNamspaceCached().':ArticlesTable:getHotsArticles('.$offset.';'.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ArticlesTable')->getHotsArticles($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getStaticArticles($offset=0, $limit=5)
    {
        $key = md5($this->getNamspaceCached().':ArticlesTable:getStaticArticles('.$offset.';'.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ArticlesTable')->getStaticArticles($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getFqaArticles($offset=0, $limit=5)
    {
        $key = md5($this->getNamspaceCached().':ArticlesTable:getFqaArticles('.$offset.';'.$limit.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ArticlesTable')->getFqaArticles($offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getArticlesInCategory($categories_articles_id, $offset=0, $limit=5)
    {
        $stri_key = '';
        if(is_array($categories_articles_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_articles_id);
        }else{
            $stri_key = $categories_articles_id;
        }
        $stri_key .= '-'.$offset.'-'.$limit;

        $key = md5($this->getNamspaceCached().':ArticlesTable:getArticlesInCategory('.$stri_key.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ArticlesTable')->getArticlesInCategory($categories_articles_id, $offset, $limit);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $articles;
    }

    public function getRow( $articles_id=0 )
    {
        $key = md5($this->getNamspaceCached().':ArticlesTable:getRow('.$articles_id.')');
        $results = $this->view->Datas()->popIDataHashMap($key);
        if( !$results ) {
            $results = $this->getModelTable('ArticlesTable')->getRow($articles_id);
            $this->view->Datas()->pushIDataHashMap($key, $results);
        }
        return $results;
    }

    public function getCategoriesId($article){
        if( !empty($article) && !empty($article['categories_articles_id']) ){
            return $article['categories_articles_id'];
        }
        return '';
    }

    public function getContent($article){
        if( !empty($article) && !empty($article['articles_content']) ){
            return html_entity_decode($article['articles_content'], ENT_QUOTES, 'UTF-8');
        }
        return '';
    }

    public function getDescription($article){
        if( !empty($article) && !empty($article['articles_sub_content']) ){
            return $article['articles_sub_content'];
        }
        return '';
    }

    public function getTitle($article){
        if( !empty($article) && !empty($article['articles_title']) ){
            return $article['articles_title'];
        }
        return '';
    }

    public function getImage($article){
        if( !empty($article) && !empty($article['thumb_images']) ){
            return $article['thumb_images'];
        }
        return '';
    }

    public function getUrl($article){
        if( !empty($article) 
            && !empty($article['articles_alias'])
            && !empty($article['articles_id']) ){
            return FOLDERWEB .$this->getUrlPrefixLang().'/' .$article['articles_alias']. '-' .$article['articles_id']. '.html';
        }
        return '';
    }

    public function getRating($article){
        if( !empty($article) 
            && !empty($article['rating']) ){
            return $article['rating'];
        }
        return 0;
    }

    public function getDateCreate($article){
        if( !empty($article) 
            && !empty($article['date_create']) ){
            return $article['date_create'];
        }
        return '';
    }

    public function getUsersFullname($article){
        if( !empty($article) 
            && !empty($article['users_fullname']) ){
            return $article['users_fullname'];
        }
        return '';
    }
    
}
