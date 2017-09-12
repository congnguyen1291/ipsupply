<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;

use Application\Model\AppTable;

class CategoriesArticlesTable extends AppTable {

    public function removeAllArticleCategoryOfWebsite($website_id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('categories_articles_id'));
        $select->from('categories_articles');
        $select->where(array(
            'categories_articles.website_id' => $website_id
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try{
            $ids = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
            $this->getModelTable('CategoriesArticlesLanguagesTable')->removeAllCategoriesArticlesLanguagesWithCategoriesArticlesId($ids);
            $this->tableGateway->delete(array('website_id' => $website_id));
        }catch(\Exception $ex){}
    }

    public function fetchAll()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('categories_articles');
            $selectString = $sql->getSqlStringForSqlObject($select);
            try{
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllCategoriesArticles()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllCategoriesArticles');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('categories_articles_id','parent_id','is_published','is_delete',
                                    'is_technical_category','date_create','date_update','ordering','is_faq','is_static'));
            $select->from('categories_articles');
            $select->join('categories_articles_translate', 'categories_articles_translate.categories_articles_id=categories_articles.categories_articles_id',array('categories_articles_title','categories_articles_alias','seo_keywords','seo_description'));
            $select->where(array(
                'categories_articles.is_published' => 1,
                'categories_articles.is_delete' => 0,
				'categories_articles.is_static' => 0,
                'categories_articles.website_id' => $this->getWebsiteId(),
                'categories_articles_translate.language'=>$this->getLanguagesId()
            ));
            $select->order(array(
                'categories_articles.parent_id' => 'ASC',
                'categories_articles.ordering' => 'ASC',
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            try{
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllCategoriesArticlesSort()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllCategoriesArticlesSort');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getAllCategoriesArticles();
                $listCategory = array();
                if(COUNT($rows)>0){
                    foreach ($rows as $item ) {
                        $idParentCategory = $item['parent_id'];
                        if( $item['categories_articles_id'] == $item['parent_id']){
                            $idParentCategory = 0;
                        }
                        if (isset($listCategory[$idParentCategory]) && !empty($listCategory[$idParentCategory]) ) {
                            $listCategory[$idParentCategory][] = $item;
                        } else {
                            $listCategory[$idParentCategory] = array($item);
                        }
                    }
                }
                $results = $listCategory;
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllCategoriesArticlesSortWithKeyValue()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllCategoriesArticlesSortWithKeyValue');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getAllCategoriesArticles();
                $listCategory = array();
                if(COUNT($rows)>0){
                    foreach ($rows as $item ) {
                        $categories_id = $item['categories_articles_id'];
                        $listCategory[$categories_id] = $item;
                    }
                }
                $results = $listCategory;
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllParentsOfCategory($categories_articles_id, $has_me = false)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllParentsOfCategory('.$categories_articles_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getAllCategoriesArticlesSortWithKeyValue();
                $list = array();
                if(!empty($rows) && !empty($rows[$categories_articles_id])){
                    $category = $rows[$categories_articles_id];
                    if(!empty($has_me)){
                        $list[] = $rows[$categories_articles_id];
                    }
                    while (!empty($category) 
                            && !empty($category['parent_id']) && $category['parent_id'] >0
                            && !empty($rows[$category['parent_id']])) {
                        if( $category['parent_id'] == $category['categories_articles_id'] )
                            break;
                        $list[] = $rows[$category['parent_id']];
                        $category = $rows[$category['parent_id']];
                    }
                }
                $results = $list;
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllCategoriesArticlesWithArrayId_Parents()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllCategoriesArticlesWithArrayIdParents');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getAllCategoriesArticles();
                $listCategory = array();
                if(COUNT($rows)>0){
                    foreach ($rows as $item ) {
                        $idParentCategory = $item['parent_id'];
                        $listCategory[$item['categories_articles_id']] = $idParentCategory;
                    }
                }
                $results = $listCategory;
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllChildOfCate($cate_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getAllChildOfCate('.(is_array($cate_id) ? implode('-',$cate_id) : $cate_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
            	$results = array();
                $rows = $this->getAllCategoriesArticlesSort();
                if (!empty($rows[$cate_id])) {
                    foreach ($rows[$cate_id] as $cat) {
                        $results[] = $cat['categories_articles_id'];
                    }
                }
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getDemo($rows, $id)
    {
        if (isset($rows[$id])) {
            foreach ($rows[$id] as $v) {
                $this->cids[] = $v;
                if (isset($rows[$v])) {
                    $this->getDemo($rows, $v);
                }
            }
        }
        return $this->cids;
    }

    public function getRow($cate_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getRow('.(is_array($cate_id) ? implode('-',$cate_id) : $cate_id).')');
        $results = $cache->getItem($key);
        //die($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->columns(array('categories_articles_id','parent_id','is_published','is_delete',
                                    'is_technical_category','date_create','date_update','ordering','is_faq','is_static'));
                $select->from('categories_articles');
                $select->join('categories_articles_translate', 'categories_articles_translate.categories_articles_id=categories_articles.categories_articles_id',array('categories_articles_title','categories_articles_alias','seo_keywords','seo_description'));
                $select->where(array(
                    'categories_articles.is_published' => 1,
                    'categories_articles.is_delete' => 0,
                    'categories_articles.categories_articles_id' => $cate_id,
                    'categories_articles.website_id'=>$this->getWebsiteId(),
                    'categories_articles_translate.language'=>$this->getLanguagesId()
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                //die($selectString);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){

                $results = array();
            }
        }
        return $results;
    }

    public function getOneCategoryArticle($cate_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesArticlesTable:getOneCategoryArticle('.$cate_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->columns(array('categories_articles_id','parent_id','is_published','is_delete',
                                    'is_technical_category','date_create','date_update','ordering','is_faq','is_static'));
                $select->from('categories_articles');
                $select->join('categories_articles_translate', 'categories_articles_translate.categories_articles_id=categories_articles.categories_articles_id',array('categories_articles_title','categories_articles_alias','seo_keywords','seo_description'));
                $select->where(array(
                    'categories_articles.is_published' => 1,
                    'categories_articles.is_delete' => 0,
                    'categories_articles.categories_articles_id' => $cate_id,
                    'categories_articles.website_id'=>$this->getWebsiteId(),
                    'categories_articles_translate.language'=>$this->getLanguagesId()
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){

                $results = array();
            }
        }
        return $results;
    }

    public function getArticleCategoryOfWebsite($website_id) {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('categories_articles_id','parent_id','is_published','is_delete',
                                    'is_technical_category','date_create','date_update','ordering','is_faq','is_static'));
        $select->from('categories_articles');
        $select->join('categories_articles_translate', 'categories_articles_translate.categories_articles_id=categories_articles.categories_articles_id',array('categories_articles_title','categories_articles_alias','seo_keywords','seo_description'));
        $select->where(array(
            'categories_articles.is_published' => 1,
            'categories_articles.is_delete' => 0,
            'categories_articles.categories_articles_id' => $id,
            'categories_articles.website_id'=>$website_id,
            'categories_articles_translate.language'=>$this->getLanguagesId()
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $resultSet = new ResultSet();
            $resultSet->initialize($results);
            $results = (is_array($id)?$resultSet->toArray() : $resultSet->current());
        }catch(\Exception $ex){
            $results = array();
        }
        return $results;
    }

    public function getAllCategoriesArticlesOfWebsiteAndSort($website_id)
    {
        $rows = $this->getArticleCategoryOfWebsite();
        $listCategory = array();
        if(COUNT($rows)>0){
            foreach ($rows as $item ) {
                $idParentCategory = $item['parent_id'];
                if (isset($listCategory[$idParentCategory]) && !empty($listCategory[$idParentCategory]) ) {
                    $listCategory[$idParentCategory][] = $item;
                } else {
                    $listCategory[$idParentCategory] = array($item);
                }
            }
        }
        return $listCategory;
    }

    public function insertArticleCategory($data)
    {
        try{
            $this->tableGateway->insert($data);
            return $this->getLastestId();
        }catch(\Exception $ex){
             echo $ex->getMessage();die();
        }
    }

    public function getLastestId(){
        return $this->tableGateway->getLastInsertValue();
    }
	
}