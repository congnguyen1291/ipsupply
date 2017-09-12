<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\TableGateway\TableGateway;

use Application\Model\AppTable;

class ArticlesTable extends AppTable{
    private $all_cat_child = array();

    public function removeAllArticleOfWebsite($website_id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('articles_id'));
        $select->from('articles');
        $select->where(array(
            'articles.website_id' => $website_id
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try{
            $ids = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
            $this->getModelTable('ArticlesLanguagesTable')->removeAllArticlesLanguagesWithArticlesId($ids);
            $this->tableGateway->delete(array('website_id' => $website_id));
        }catch(\Exception $ex){}
    }

    public function fetchAll()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_fullname','categories_articles_id','date_create','date_update','thumb_images'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content'));
            $select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>0,
                    'articles.is_static'=>0,
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles_translate.language'=>$this->getLanguagesId()
            ));
            $select->order('articles.articles_id desc');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
    
    public function getAllArticles($page=0, $page_size=20) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getAllArticles('.$page.';'.$page_size.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
			$select->columns(array('articles_id','users_fullname','categories_articles_id','date_create','date_update','thumb_images'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content'));
            $select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>0,
                    'articles.is_static'=>0,
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles_translate.language'=>$this->getLanguagesId()
            ));
            $select->offset($page);
            $select->limit($page_size);
            $select->order('articles.articles_id desc');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function countAllArticles() {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:countAllArticles');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT articles.articles_id)")));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array());
            $select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>0,
                    'articles.is_static'=>0,
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles_translate.language'=>$this->getLanguagesId()
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $result = $results->toArray();
                $results = 0;
                if (count($result) > 0) {
                    $results = $result[0]['total'];
                }
                $cache->setITem($key, $results);
            }catch(\Exception $ex){
                $results = 0;
            }
        }
        return $results;
    }

    public function getArticlesInCategory($categories_articles_id, $offset=0, $limit=5) {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($categories_articles_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_articles_id);
        }else{
            $stri_key = $categories_articles_id;
        }
        $stri_key .= '-'.$offset.'-'.$limit;

        $key = md5($this->getNamspaceCached().':ArticlesTable:getArticlesInCategory('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            $select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>0,
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles.categories_articles_id'=>$categories_articles_id,
                    'articles_translate.language'=>$this->getLanguagesId()
            ));
            $select->offset($offset);
            $select->limit($limit);
            $select->order('articles.date_create ASC');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getArticlesCate($list, $params, $all=0) {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = $all;
        if(is_array($list)){
            $stri_key .= $this->createKeyCacheFromArray($list);
        }else{
            $stri_key .= $list;
        }
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key .= '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ArticlesTable:getArticlesCate('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            if(isset($all) && $all==0){
				$select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>0,
                    'articles.is_static'=>0,
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles.categories_articles_id'=>$list,
                    'articles_translate.language'=>$this->getLanguagesId()
				));
			}else{
				$select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>0,
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles.categories_articles_id'=>$list,
                    'articles_translate.language'=>$this->getLanguagesId()
				));
			}
			
            //$select->offset($page);
            //$select->limit($page_size);
            $select->order('articles.date_create ASC');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function countTotalArticlesCate($list, $params) {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($list)){
            $stri_key = $this->createKeyCacheFromArray($list);
        }else{
            $stri_key = $list;
        }
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key .= '-'.$params;
        }
        $key = md5($this->getNamspaceCached().'ArticlesTable:countTotalArticlesCate('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT articles.articles_id)")));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array());
            $select->where(array(
                'articles.is_published' => 1,
                'articles.is_delete' => 0,
                'articles.is_faq'=>0,
                'articles.is_static'=>0,
                'articles.website_id'=>$this->getWebsiteId(),
                'articles.categories_articles_id'=>$list,
                'articles_translate.language'=>$this->getLanguagesId()
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $result = $results->toArray();
                $results = 0;
                if (count($result) > 0) {
                    $results = $result[0]['total'];
                }
                $cache->setITem($key, $results);
            }catch(\Exception $ex){
                $results = 0;
            }
        }
        return $results;
    }

    public function getTopArticles($offset=0, $limit = 5) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getTopArticles('.$offset.';'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            $select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>0,
                    'articles.is_static'=>0,
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles_translate.language'=>$this->getLanguagesId()
            ));
            $select->order('articles.date_create DESC');
            $select->limit($limit);
            $select->offset($offset);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getNewsArticles($offset=0, $limit = 5) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getNewsArticles('.$offset.';'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            $select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>0,
                    'articles.is_new'=>1,
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles_translate.language'=>$this->getLanguagesId()
            ));
            $select->order('articles.date_create DESC');
            $select->limit($limit);
            $select->offset($offset);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getHotsArticles($offset=0, $limit = 5) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getHotsArticles('.$offset.';'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            $select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>0,
                    'articles.is_hot'=>1,
					'articles.is_static'=>0,
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles_translate.language'=>$this->getLanguagesId()
            ));
            $select->order('articles.date_create DESC');
            $select->limit($limit);
            $select->offset($offset);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getStaticArticles($offset=0, $limit = 5) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getStaticArticles('.$offset.';'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            $select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>0,
                    'articles.is_static'=>1,
                    'website_id'=>$this->getWebsiteId(),
                    'articles_translate.language'=>$this->getLanguagesId()
            ));
            $select->order('articles.date_create DESC');
            $select->limit($limit);
            $select->offset($offset);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getFqaArticles($offset=0, $limit = 5) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getFqaArticles('.$offset.';'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            $select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>1,
					'articles.is_static'=>0,
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles_translate.language'=>$this->getLanguagesId()
            ));
            $select->order('articles.date_create DESC');
            $select->limit($limit);
            $select->offset($offset);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllFaq() {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().'ArticlesTable:getAllFaq');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            $select->where(array(
                    'articles.is_published' => 1,
                    'articles.is_delete' => 0,
                    'articles.is_faq'=>'1',
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles_translate.language'=>$this->getLanguagesId()
            ));
            $select->order('articles.date_create ASC');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

	public function getTopFaq() {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getTopFaq');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
    		$select->columns(array('articles_id','number_views','date_update'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias'));
    		$select->where(array(
                'articles.is_published' => 1,
                'articles.is_delete' => 0,
                'articles.categories_articles_id' => 5,
                'articles.website_id'=>$this->getWebsiteId(),
                'articles_translate.language'=>$this->getLanguagesId()
            ));
    		$select->order('articles.date_update DESC');
            $select->limit(5);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getRow($id) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getRow('.$id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            $select->where(array(
                'articles.is_published' => 1,
                'articles.is_delete' => 0,
                'articles.articles_id' => $id,
                'articles.website_id'=>$this->getWebsiteId(),
                'articles_translate.language'=>$this->getLanguagesId()
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = (is_array($id)?$resultSet->toArray() : $resultSet->current());
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllLimit($where) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getAllLimit('.(is_array($where) ? implode("-",$where) : $where).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
    		$select->columns(array('articles_id','number_views','date_update'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias'));
            $select->where(array('articles.website_id'=>$this->getWebsiteId(),
                'articles.is_static'=>0,
                'articles.is_delete' => 0,
                'articles_translate.language'=>$this->getLanguagesId()));
            $select->where($where);
            $select->order('articles.date_create desc');
			$select->limit(10);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getRows($where, $limit){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getRows('.implode('-',$where).'-'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            $sql = new Sql($this->adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            $select->where(array(
                    'articles.website_id'=>$this->getWebsiteId(),
                    'articles_translate.language'=>$this->getLanguagesId()));
            $select->where($where);
			$select->order('articles.date_create desc');
            $select->limit($limit);
            $adapter = $this->adapter;
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                throw new \Exception($ex);
            }
        }
        return $results;
    }
	
	public function getCategoriyArticleById($id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getCategoriyArticleById('.$id.')');
        $results = $cache->getItem($key);
        if(!$results){
    		$adapter = $this->adapter;
    		$sql = new Sql($adapter);
    		$select = $sql->select();
            $select->columns(array('categories_articles_id','parent_id','is_published','is_delete',
                                    'is_technical_category','date_create','date_update','ordering','is_faq','is_static'));
            $select->from('categories_articles');
            $select->join('categories_articles_translate', 'categories_articles_translate.categories_articles_id=categories_articles.categories_articles_id',array('categories_articles_title','categories_articles_alias','seo_keywords','seo_description'));
    		$select->where(array(
    			'categories_articles.categories_articles_id' => $id,
    			'categories_articles.is_published' => 1,
    			'categories_articles.is_delete' => 0,
				'categories_articles.is_static'=>0,
                'categories_articles.website_id'=>$this->getWebsiteId(),
                'categories_articles_translate.language'=>$this->getLanguagesId()
    		));
    		try{
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    			$results = (array)$results->current();
    			$cache->setItem($key,$results);
    		}catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
	}
	
	public function getArticlesByCat($id, $intPage, $intPageSize){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getArticlesByCat('.$id.';'.$intPage.';'.$intPageSize.')');
        $results = $cache->getItem($key);
        if(!$results){
            if ($intPage <= 1) {
                $intPage = 0;
            } else {
                $intPage = ($intPage - 1) * $intPageSize;
            }
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            $select->where(array(
                'articles.categories_articles_id' => $id,
                'articles.is_published' => 1,
                'articles.is_delete' => 0,
				'articles.is_static'=>0,
                'articles.website_id'=>$this->getWebsiteId(),
                'articles_translate.language'=>$this->getLanguagesId()
            ));
            $select->limit($intPageSize);
            $select->offset($intPage);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                
            }
        }
        return $results;
	}
	
	public function getArticlesByCatTopView($id, $intPage, $intPageSize){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:getArticlesByCatTopView('.$id.';'.$intPage.';'.$intPageSize.')');
        $results = $cache->getItem($key);
        if(!$results){
    		if ($intPage <= 1) {
    			$intPage = 0;
    		} else {
    			$intPage = ($intPage - 1) * $intPageSize;
    		}
    		$adapter = $this->adapter;
    		$sql = new Sql($adapter);
    		$select = $sql->select();
    		$select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
    		$select->where(array(
    			'articles.categories_articles_id' => $id,
    			'articles.is_published' => 1,
    			'articles.is_delete' => 0,
				'articles.is_static'=>0,
                'articles.website_id'=>$this->getWebsiteId(),
                'articles_translate.language'=>$this->getLanguagesId()
    		));
    		$select->order('articles.number_views DESC');
    		$select->limit($intPageSize);
    		$select->offset($intPage);
    		try{
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    			$results = $results->toArray();
    			$cache->setItem($key,$results);
    		}catch(\Exception $ex){
                
            }
        }
        return $results;
	}
	
	public function countArticlesByCat($id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesTable:countArticlesByCat('.$id.')');
        $results = $cache->getItem($key);
        if(!$results){
    		$adapter = $this->adapter;
    		$sql = new Sql($adapter);
    		$select = $sql->select()->columns(array('total' => new Expression('count(articles.articles_id)')));
    		$select->from('articles');
            $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array());
    		$select->where(array(
    			'articles.categories_articles_id' => $id,
    			'articles.is_published' => 1,
    			'articles.is_delete' => 0,
				'articles.is_static'=>0,
                'articles.website_id'=>$this->getWebsiteId(),
                'articles_translate.language'=>$this->getLanguagesId()
    		));
    		try{
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    			$results = (array)$results->current();
    			$results = $results['total'];
                $cache->setItem($key,$results);
    		}catch(\Exception $ex){
                $results = 0;
            }
        }
        return $results;
	}
	
	public function updateNumberView($id,$num)
    {
		if(!empty($id)){
			try{
				$adapter = $this->adapter;
				$sql = new Sql($adapter);
				$update = $sql->update('articles');
				$update->set(array(
					'number_views' => $num,
				));
				$update->where(array(
					'articles_id' => $id,
                    'website_id'=>$this->getWebsiteId()
				));
				$updateString = $sql->getSqlStringForSqlObject($update);
				$results = $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
				return true;
			}catch(\Exception $ex){
				return false;
			}
		}
        
    }

    public function getArticleOfWebsite($website_id) {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
        $select->from('articles');
        $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
        $select->where(array(
            'articles.is_published' => 1,
            'articles.is_delete' => 0,
            'articles.website_id'=>$website_id,
            'articles_translate.language'=>$this->getLanguagesId()
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
        }catch(\Exception $ex){
            $results = array();
        }
        return $results;
    }

    public function insertArticle($data)
    {
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }

    public function getChilds($rows, $parent ){
        if(count($rows) == 0){
            return array();
        }
        $childs = array();
        foreach($rows as $row){
            if($row['parent_id'] == $parent ){
                $childs[] = $row;
            }
        }
        if(count($childs) == 0){
            return array();
        }
        foreach($childs as $key => $child){
            $this->all_cat_child[] = $child['categories_articles_id'];
            $childs[$key]['children'] = $this->getChilds($rows, $child['categories_articles_id']);
        }
        return $childs;
    }

    public function getArticleForEachCategories($cat_root, $limit){
        $cache = $this->getServiceLocator()->get('cache');
        $list_id  = array();
        foreach ($cat_root as $key => $cat) {
            $list_id[] = $cat['categories_articles_id'];
        }
        $key = md5($this->getNamspaceCached().':ArticlesTable:getArticleForEachCategories('.implode('-',$list_id).'-'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getModelTable('CategoriesArticlesTable')->getAllCategoriesArticles();
                $results = array();

                if(!empty($rows)){
                    foreach($cat_root as $cat){
                        $this->all_cat_child = array();
                        $children = $this->getChilds($rows, $cat['categories_articles_id']);
                        $ids = $this->all_cat_child;
                        $ids[] = $cat['categories_articles_id'];
                        $results[$cat['categories_articles_id']]['children'] = $children;
                        $results[$cat['categories_articles_id']]['articles'] = $this->getArticlesCate($ids);
                    }
                    $cache->setItem($key, $results);
                }
            }catch(\Exception $e){
                echo $e->getMessage();die();
                $results = array();
            }
        }
        return $results;
    }
    public function getArticleForEachCategoriesAll($cat_root, $limit){
        $cache = $this->getServiceLocator()->get('cache');
        $list_id  = array();
        foreach ($cat_root as $key => $cat) {
            $list_id[] = $cat;
        }
        $key = md5($this->getNamspaceCached().':ArticlesTable:getArticleForEachCategoriesAll('.implode('-',$list_id).'-'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getModelTable('CategoriesArticlesTable')->getAllCategoriesArticles();
                $results = array();
                if(!empty($rows)){
                    foreach($cat_root as $cat){
                        $this->all_cat_child = array();
                        $children = $this->getChilds($rows, $cat['categories_articles_id']);
                        $ids = $this->all_cat_child;
                        $ids[] = $cat;
                        $results[$cat]['children'] = $children;
                        $results[$cat]['articles'] = $this->getArticlesCate($ids,'',1);
                    }
                    $cache->setItem($key, $results);
                }
            }catch(\Exception $e){
                echo $e->getMessage();die();
                $results = array();
            }
        }
        return $results;
    }
    public function getTagsArticle($articles_id){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($articles_id)){
            $stri_key = $this->createKeyCacheFromArray($articles_id);
        }else{
            $stri_key = $articles_id;
        }
        $key = md5($this->getNamspaceCached().':ArticlesTable:getTagsArticle('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('tags');
            $select->join ( 'articles', new Expression('FIND_IN_SET(tags.tags_id, articles.tags)>0'), array() );
            $select->where(array(
                'articles.articles_id' => $articles_id,
                'tags.website_id'=>$this->getWebsiteId()
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getTagsArticleInCategory($categories_articles_id, $offset = 0, $limit = 5){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($categories_articles_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_articles_id);
        }else{
            $stri_key = $categories_articles_id;
        }
        $key = md5($this->getNamspaceCached().':ArticlesTable:getTagsArticleInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('tags');
            $select->join ( 'articles', new Expression('FIND_IN_SET(tags.tags_id, articles.tags)>0'), array() );
            $select->where(array(
                'articles.categories_articles_id' => $categories_articles_id,
                'tags.website_id'=>$this->getWebsiteId()
            ));
            $select->offset($offset);
            $select->limit($limit);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getArticlesImagesOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('articles_images');
        $select->join('articles', 'articles_images.articles_id = articles.articles_id', array());
        $select->where(array(
            'articles.is_published' => 1,
            'articles.is_delete' => 0,
            'articles.website_id'=>$website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertArticlesImages($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('articles_images');
        $insert->columns(array('articles_id','image','is_published','date_create','ordering'));
        $insert->values(array(
            'articles_id' => $data['articles_id'],
            'image' => $data['image'],
            'is_published' => $data['is_published'],
            'date_create' => $data['date_create'],
            'ordering' => $data['ordering']
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            
        }
    }

    public function deleteArticlesImages($articles_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $delete  = $sql->delete('articles_images');
        $delete->where(array(
            'articles_id' => $articles_id
        ));
        try{
            $deleteString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            
        }
    }
    
}