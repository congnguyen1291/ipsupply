<?php

namespace Application\Model;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Application\Model\AppTable;

class ProductsTable extends AppTable{

    public function removeAllProductOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function updateProduct($row, $where)
    {
        $this->tableGateway->update($row, $where);
    }

    public function fetchAll()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
            try {
                $results = $this->tableGateway->select(array('website_id'=>$_SESSION['website_id']));
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAdapter()
    {
        $adapter = $this->tableGateway->getAdapter();
        return $adapter;

    }
	
	public function getExtRequire($ext_require_id){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($ext_require_id)){
            $stri_key = $this->createKeyCacheFromArray($ext_require_id);
        }else{
            $stri_key = $ext_require_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getExtRequire('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products_extensions');
            $select->join('products', 'products.products_id=products_extensions.products_id', array() );
            $select->where(array(
                'id' => $ext_require_id,
                'ext_require' => 1,
                'products.website_id'=>$_SESSION['website_id']
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = (array)$results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getExtensionsProduct($products_id){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($ext_require_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getExtensionsProduct('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products_extensions');
            $select->join ( 'products', 'products.products_id=products_extensions.products_id', array() );
            $select->where(array(
                'products.products_id' => $products_id,
                'products.website_id'=>$_SESSION['website_id']
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

    public function getTypeProduct($products_id){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($ext_require_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getTypeProduct('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products_type');
            $select->join ( 'products', 'products.products_id=products_type.products_id', array() );
            $select->where(array(
                'products.products_id' => $products_id,
                'products.website_id'=>$_SESSION['website_id']
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

    public function getTagsProduct($products_id, $offset = 0, $limit = 5){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getTagsProduct('.$stri_key.','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('tags');
            $select->join ( 'products', new Expression('FIND_IN_SET(tags.tags_id, products.tags)>0'), array() );
            $select->where(array(
                'products.products_id' => $products_id,
                'tags.website_id'=>$_SESSION['website_id']
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

    public function getTagsProductInCategory($categories_id, $offset = 0, $limit = 5){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getTagsProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
    		$adapter = $this->tableGateway->getAdapter();
    		$sql = new Sql($adapter);
    		$select = $sql->select();
    		$select->from('tags');
            $select->join ( 'products', new Expression('FIND_IN_SET(tags.tags_id, products.tags)>0'), array() );
			$select->join('products_category', 'products_category.products_id=products.products_id', array());		
    		$select->where(array(
    			'products_category.categories_id' => $categories_id,
                'tags.website_id'=>$_SESSION['website_id']
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
	
	public function getArticlesProduct($products_id){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getArticlesProduct('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array());
            $select->from('products_articles');
            $select->join('articles', 'products_articles.articles_id=articles.articles_id', array('articles_id'));
            $select->join('articles_languages', 'articles_languages.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
            $select->where(array(
                'articles.is_published' => 1,
                'articles.is_delete' => 0,
                'products_articles.products_id' => $products_id,
                'articles.website_id'=>$_SESSION['website_id'],
                'articles_languages.languages_id'=>$_SESSION['language']['languages_id']
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
	}
	
	public function getAllFqa($id, $intPage, $intPageSize){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getAllFqa('.$stri_key.';'.$intPage.';'.$intPageSize.')');
        $results = $cache->getItem($key);
        if(!$results) {
            if ($intPage <= 1) {
                $intPage = 0;
            } else {
                $intPage = ($intPage - 1) * $intPageSize;
            }
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('fqa');
            $select->join('users', 'users.users_id=fqa.users_id', array('full_name', 'user_name', 'avatar'), 'left');
            $select->where(array(
                'products_id' => $id,
                'fqa.is_published' => 1,
                'fqa.id_parent' => 0,
                'users.website_id'=>$_SESSION['website_id']
            ));
            $select->limit($intPageSize);
            $select->offset($intPage);
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $resultsPa = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $resultsPa = $resultsPa->toArray();
                $results = $resultsPa;
                if (COUNT($resultsPa) > 0) {
                    $fqa_ids = array_map(function ($a) {
                            return $a['id'];
                        }, $resultsPa);
                    $child = $this->getAllFqaChild($fqa_ids, $intPage, $intPageSize);
                    //print_r($child);die();
                    if (COUNT($child) > 0) {
                        $results = array_merge($resultsPa, $child);
                    }
                }
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
	}
	
	public function getAllFqaChild($id, $intPage, $intPageSize){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getAllFqaChild('.$stri_key.';'.$intPage.';'.$intPageSize.')');
        $results = $cache->getItem($key);
        if(!$results) {
    		if ($intPage <= 1) {
    			$intPage = 0;
    		} else {
    			$intPage = ($intPage - 1) * $intPageSize;
    		}
    		$adapter = $this->tableGateway->getAdapter();
    		$sql = new Sql($adapter);
    		$select = $sql->select();
    		$select->from('fqa');
    		$select->join('users', 'users.users_id=fqa.users_id', array('full_name','user_name','avatar'),'left');
    		$select->where(array(
    			'fqa.is_published' => 1,
    			'fqa.id_parent' => $id,
                'users.website_id'=>$_SESSION['website_id']
    		));
    		$select->limit($intPageSize);
    		$select->offset($intPage);
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
	
	public function getAllAnswerForFqa($fqa_ids){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($fqa_ids)){
            $stri_key = $this->createKeyCacheFromArray($fqa_ids);
        }else{
            $stri_key = $fqa_ids;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getAllAnswerForFqa('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('answer_questions');
            $select->join('users', 'users.users_id=answer_questions.users_id', array('full_name', 'user_name', 'avatar'), 'left');
            $select->where(array(
                'fqa_id' => $fqa_ids,
                'answer_questions.is_published' => 1,
                'users.website_id'=>$_SESSION['website_id']
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
	}

    public function getNewProduct($offset=0, $limit = 5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:getNewProduct('.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'is_new' => 1,
                'website_id'=>$_SESSION['website_id']
            ));
            $select->order('products.ordering ASC');
            $select->limit(4);
            $select->offset(0);
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

    public function getNewProductInCategory($cate_id, $offset=0, $limit = 5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getNewProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array(
                    'products_id',
                    'products_code',
                    'categories_id',
                    'url_crawl',
                    'manufacturers_id',
                    'users_id',
                    'users_fullname',
                    'products_title',
                    'products_alias',
                    'products_description',
                    'promotion',
                    'promotion_description',
                    'promotion_ordering',
                    'is_published',
                    'is_delete',
                    'is_new',
                    'is_hot',
                    'is_available',
                    'is_goingon',
                    'is_viewed',
                    'position_view',
                    'date_create',
                    'date_update',
                    'price',
                    'price_sale',
                    'ordering',
                    'quantity',
                    'thumb_image',
                    'number_views',
                    'vat',
                    'rating',
                    'number_like',
                    'total_sale',
                    'wholesale',
                    'type_view',
					'is_delete',
                    'is_published',
                    'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');

                //$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
				
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.is_new' => 1,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                if ( !empty($cate_id) ) {
                    $select->where(array('products_category.categories_id' => $cate_id));
                }
                $select->order('products.ordering ASC');
                $select->group('products.products_id');
                $select->offset($offset);
                $select->limit($limit);
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

    public function getDealProduct($offset = 0, $limit=5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:getDealProduct('.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('products_id','products_code','categories_id','manufacturers_id','users_id','users_fullname','products_title','products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon','date_create','date_update','price','price_sale', 'ordering','quantity','thumb_image','rating','number_like','total_sale','wholesale','type_view',
					'is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'website_id'=>$_SESSION['website_id']
            ));
            $select->where("products.promotion <>''");
            $select->order('products.ordering ASC');
            $select->limit($limit);
            $select->offset($offset);
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

    public function getSaleProduct($offset = 0,$limit=5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:getSaleProduct('.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('products_id','products_code','categories_id','manufacturers_id','users_id','users_fullname','products_title','products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon','date_create','date_update','price','price_sale', 'ordering','quantity','thumb_image','rating','number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'website_id'=>$_SESSION['website_id']
            ));
            $select->where(" (products.price > products.price_sale || products_type.price > products_type.price_sale) ");
            $select->order('products.ordering ASC');
            $select->limit($limit);
            $select->offset($offset);
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

    public function getHotProduct($offset = 0, $limit=5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:getHotProduct('.$offset .';'. $limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('products_id','products_code','categories_id','manufacturers_id','users_id','users_fullname','products_title','products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon','date_create','date_update','price','price_sale', 'ordering','quantity','thumb_image','rating','number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'is_hot' => 1,
                'products.website_id'=>$_SESSION['website_id']
            ));
            $select->group('products.products_id');
            $select->order('products.ordering ASC');
            if(!empty($limit)){
                $select->limit($limit);
                $select->offset($offset);
            }
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

    public function getGoingOnProduct($offset = 0 ,$limit= 5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:getGoingOnProduct('.$offset .';'. $limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('products_id','products_code','categories_id','manufacturers_id','users_id','users_fullname','products_title','products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon','date_create','date_update','price','price_sale', 'ordering','quantity','thumb_image','rating','number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'is_goingon' => 1,
                'products.website_id'=>$_SESSION['website_id']
            ));
            $select->group('products.products_id');
            $select->order('products.ordering ASC');
            if(!empty($limit)){
                $select->limit($limit);
                $select->offset($offset);
            }
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

    public function getGoingOnProductInCategory($categories_id, $offset = 0, $limit = 5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getGoingOnProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');

                //$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.is_goingon' => 1,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                $select->where(" (products.price > products.price_sale || products_type.price > products_type.price_sale) ");
                if ( !empty($cate_id) ) {
                    $select->where(array('products_category.categories_id' => $cate_id));
                }
                $select->order('products.ordering ASC');
                $select->group('products.products_id');
                $select->offset($offset);
                $select->limit($limit);
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

    public function getProductBanchay($offset=0, $limit = 0){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductBanchay('.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('products_invoice',"products_invoice.products_id=products.products_id", array('total_invoice' => new Expression('count(invoice_id)')));
            $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
            $select->where(array(
                'products.is_published' => 1,
                'products.is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
            ));
            $select->order(array(
                'total_invoice' => 'DESC',
                'products.ordering' => 'ASC',
            ));
            $select->group(array('products.products_id'));
            $select->limit($limit);
            $select->offset($offset);
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

    /*bán chạy nhất : nằm trong nhiều hóa đơn nhất*/
    public function getProductBestseller($offset=0, $limit = 5){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductBestseller('.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('products_invoice',"products_invoice.products_id=products.products_id", array('total_invoice' => new Expression('count(invoice_id)')));
            $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
            $select->where(array(
                'products.is_published' => 1,
                'products.is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
            ));
            $select->order(array(
                'total_invoice' => 'DESC',
                'products.ordering' => 'ASC',
            ));
            $select->group(array('products.products_id'));
            $select->limit($limit);
            $select->offset($offset);
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

    public function getProductBestsellerInCategory($categories_id , $offset = 0, $limit = 5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductBestsellerInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('products_invoice',"products_invoice.products_id=products.products_id", array('total_invoice' => new Expression('count(invoice_id)')));
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');

                //$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                $select->where(" (products.price > products.price_sale || products_type.price > products_type.price_sale) ");
                if ( !empty($categories_id) ) {
                    $select->where(array('products_category.categories_id' => $categories_id));
                }
                $select->order(array(
                    'total_invoice' => 'DESC',
                    'products.ordering' => 'ASC',
                ));
                $select->group('products.products_id');
                $select->offset($offset);
                $select->limit($limit);
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

    /*mua nhiều nhất : có số lượng bán nhiều nhất */
    public function getProductBuyMost($offset=0, $limit = 5){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductBuyMost('.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('products_invoice',"products_invoice.products_id=products.products_id", array('total_product' => new Expression('SUM(products_invoice.quantity)')));
            $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
            $select->where(array(
                'products.is_published' => 1,
                'products.is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
            ));
            $select->order(array(
                'total_product' => 'DESC',
                'products.ordering' => 'ASC',
            ));
            $select->group(array('products.products_id'));
            $select->limit($limit);
            $select->offset($offset);
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

    public function getProductBuyMostInCategory($categories_id , $offset = 0, $limit = 5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($categories_id)){
            $stri_key = $this->createKeyCacheFromArray($categories_id);
        }else{
            $stri_key = $categories_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductBuyMostInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('products_invoice',"products_invoice.products_id=products.products_id", array('total_product' => new Expression('SUM(products_invoice.quantity)')));
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');

               //$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                $select->where(" (products.price > products.price_sale || products_type.price > products_type.price_sale) ");
                if ( !empty($categories_id) ) {
                    $select->where(array('products_category.categories_id' => $categories_id));
                }
                $select->order(array(
                    'total_product' => 'DESC',
                    'products.ordering' => 'ASC',
                ));
                $select->group('products.products_id');
                $select->offset($offset);
                $select->limit($limit);
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

    public function getProductCate1($cate_id, $params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key .= '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductCate1('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                if (isset($params['page_size'])) {
                    $page_size = $params['page_size'];
                } else {
                    $page_size = 20;
                }

                if (isset($params['page'])) {
                    $page = $params['page'];
                } else {
                    $page = 20;
                }

                if ($page <= 1) {
                    $page = 0;
                } else {
                    $page = ($page - 1) * $page_size;
                }

                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.categories_id' => $cate_id,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                if (isset($params['keyword'])) {
                    $select->where->like('products_title', '%' . $params['keyword'] . '%');
                }

                if (isset($params['filter']) && $params['filter'] != '') {
                    switch ($params['filter']) {
                        case 'price_asc' :
                            $select->order('price_sale ASC');
                            break;

                        case 'price_desc' :
                            $select->order('price_sale DESC');
                            break;

                        case 'new' :
                            $select->order('date_create DESC');
                            break;

                        case 'old' :
                            $select->order('date_create ASC');
                            break;

                        case 'az' :
                            $select->order('products_title ASC');
                            break;

                        case 'za' :
                            $select->order('products_title DESC');
                            break;
                        case 'most':
                            $select->order('total_sale DESC');
                            break;
                        case 'deal':
                            $select->where(array(
                                '(promotion_description IS NOT NULL OR
                                 promotion1_description IS NOT NULL OR
                                 promotion2_description IS NOT NULL OR
                                 promotion3_description IS NOT NULL)'
                            ));
                    }
                } else {
                    $select->order('date_create DESC');
                }

                //$params[''];

                $select->order('products.ordering ASC');
                $select->offset($page);
                $select->limit($page_size);


                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getProductCateMore($cate_id, $id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        if(is_array($id)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($id);
        }else{
            $stri_key .= '-'.$id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductCateMore('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
               //$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products_category.categories_id' => $cate_id,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                $select->where->notIn('products_id', array($id));
                $select->group('products.products_id');
                $select->offset(0);
                $select->limit(10);
                $select->order('products.ordering ASC');
                $selectString = $sql->getSqlStringForSqlObject($select);
                //echo $selectString;
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getProductCate($cate_id, $params)
    {
  		$cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key .= '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductCate('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
    			if (isset($params['page_size'])) {
    				$page_size = $params['page_size'];
    			} else {
    				$page_size = 20;
    			}

    			if (isset($params['page'])) {
    				$page = $params['page'];
    			} else {
    				$page = 0;
    			}

    			if ($page <= 1) {
    				$page = 0;
    			} else {
    				$page = ($page - 1) * $page_size;
    			}

    			$adapter = $this->tableGateway->getAdapter();
    			$sql = new Sql($adapter);
    			$select = $sql->select();
    			$select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
    			$select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
    			$select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
				//$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
    			$select->where(array(
    				'products.is_published' => 1,
    				'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
    			));
    			if (isset($cate_id) && $cate_id) {
    				$select->where(array('products_category.categories_id' => $cate_id));
    			}
    			if (isset($params['keyword'])) {
    				$keyword = $this->toAlias($params['keyword']);
    				$select->where->like('products.products_alias', '%' . $keyword . '%');
    				$timeout = 60;
    				if (!isset($_SESSION[$keyword])) {
    					$_SESSION[$keyword] = time();
    					try {
    						$this->insertKeyword($params['keyword']);
    					} catch (\Exception $ex) {
    					}
    				} elseif (time() - $_SESSION[$keyword] > $timeout) {
    					unset($_SESSION[$keyword]);
    				}
    			}

    			if (isset($params['manus'])) {
    				$select->where(array(
    					'products.manufacturers_id' => $params['manus']
    				));
    			}
    			if (isset($params['rating'])) {
    				$select->where(array(
    					'products.rating' => $params['rating']
    				));
    			}
    			if (isset($params['feature'])) {
    				/*
    				$select->join('products_feature', 'products_feature.products_id=products.products_id', array());
    				$features = $params['feature'];
    				$select->where(array(
    					'products_feature.feature_id' => $features
    				));*/
    				$features = $params['feature'];
    				foreach ($features as $key => $value) {
    					$select->join(array("f{$key}" => "products_feature"), "f{$key}.products_id=products.products_id", array());
    					$select->where(array(
    						"f{$key}.feature_id" => $value
    					));
    				}
    			}
    			if (isset($params['price'])) {
    				$select->where('products.price_sale BETWEEN ' . $params['price']['min'] . ' AND ' . $params['price']['max']);
    			}

    			if (isset($params['filter']) && $params['filter'] != '') {
    				switch ($params['filter']) {
    					case 'price_asc' :
    						$select->order('products.price_sale ASC');
    						break;
    	
    					case 'price_desc' :
    						$select->order('products.price_sale DESC');
    						break;
    	
    					case 'new' :
    						$select->order('products.date_create DESC');
    						break;
    	
    					case 'old' :
    						$select->order('products.date_create ASC');
    						break;
    	
    					case 'az' :
    						$select->order('products.products_title ASC');
    						break;
    	
    					case 'za' :
    						$select->order('products.products_title DESC');
    						break;
    				}
    			} else {
    				$select->order('products.products_id DESC');
    			}

    			if (isset($params['fillmore']) && $params['fillmore'] != '') {
    				switch ($params['fillmore']) {
    					case 'most':
    						$select->order('products.total_sale DESC');
    						break;
    					case 'deal':
    						$select->where(array(
    							'(products.promotion_description IS NOT NULL OR
    							 products.promotion1_description IS NOT NULL OR
    							 products.promotion2_description IS NOT NULL OR
    							 products.promotion3_description IS NOT NULL)'
    						));
    				}
    			}

    			//$params[''];
    	
    			$select->order('products.ordering ASC');
    			$select->group('products.products_id');
    			$select->offset($page);
    			$select->limit($page_size);
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
    			//$results->buffer();
    			//$results->next();
    			$cache->setItem($key,$results);
    		}catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getHotProductInCategory($cate_id, $offset=0, $limit=5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getHotProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
				//$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'is_hot' => 1,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                if ( !empty($cate_id) ) {
                    $select->where(array('products_category.categories_id' => $cate_id));
                }
                
                $select->order('products.ordering ASC');
                $select->group('products.products_id');
                $select->offset($offset);
                $select->limit($limit);
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $e){
                $results = array();
            }
        }
        return $results;
    }

    public function getDealProductInCategory($cate_id, $offset=0, $limit = 5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getDealProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');

                //$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                $select->where("products.promotion <>''");
                if ( !empty($cate_id) ) {
                    $select->where(array('products_category.categories_id' => $cate_id));
                }
                $select->order('products.ordering ASC');
                $select->group('products.products_id');
                $select->offset($offset);
                $select->limit($limit);
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

    public function getSaleProductInCategory($cate_id, $offset = 0, $limit = 5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getSaleProductInCategory('.$stri_key.','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
				//$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                $select->where(" (products.price > products.price_sale || products_type.price > products_type.price_sale) ");
                if ( !empty($cate_id) ) {
                    $select->where(array('products_category.categories_id' => $cate_id));
                }
                $select->order('products.ordering ASC');
                $select->group('products.products_id');
                $select->offset($offset);
                $select->limit($limit);
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

    public function getProductSearch($cate_id, $params){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key .= '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductSearch('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                if (isset($params['page_size'])) {
                    $page_size = $params['page_size'];
                } else {
                    $page_size = 20;
                }

                if (isset($params['page'])) {
                    $page = $params['page'];
                } else {
                    $page = 20;
                }

                if ($page <= 1) {
                    $page = 0;
                } else {
                    $page = ($page - 1) * $page_size;
                }

                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                //$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                if (isset($cate_id) && $cate_id) {
                    $select->where(array('products_category.categories_id' => $cate_id));
                }
                if (isset($params['keyword'])) {
                    $keyword = $this->toAlias($params['keyword']);
                    $select->where->like('products.products_alias', '%' . $keyword . '%');
                    $timeout = 60;
                    if (!isset($_SESSION[$keyword])) {
                        $_SESSION[$keyword] = time();
                        try {
                            $this->insertKeyword($params['keyword']);
                        } catch (\Exception $ex) {
                        }
                    } elseif (time() - $_SESSION[$keyword] > $timeout) {
                        unset($_SESSION[$keyword]);
                    }
                }

                if (isset($params['manus'])) {
                    $select->where(array(
                        'products.manufacturers_id' => $params['manus']
                    ));
                }
                if (isset($params['rating'])) {
                    $select->where(array(
                        'products.rating' => $params['rating']
                    ));
                }
                if (isset($params['feature'])) {
                    $features = $params['feature'];
                    foreach ($features as $key => $value) {
                        $select->join(array("f{$key}" => "products_feature"), "f{$key}.products_id=products.products_id", array());
                        $select->where(array(
                            "f{$key}.feature_id" => $value
                        ));
                    }
                }
                if (isset($params['price'])) {
                    $select->where('products.price_sale BETWEEN ' . $params['price']['min'] . ' AND ' . $params['price']['max']);
                }

                if (isset($params['filter']) && $params['filter'] != '') {
                    switch ($params['filter']) {
                        case 'price_asc' :
                            $select->order('products.price_sale ASC');
                            break;

                        case 'price_desc' :
                            $select->order('products.price_sale DESC');
                            break;

                        case 'new' :
                            $select->order('products.date_create DESC');
                            break;

                        case 'old' :
                            $select->order('products.date_create ASC');
                            break;

                        case 'az' :
                            $select->order('products.products_title ASC');
                            break;

                        case 'za' :
                            $select->order('products.products_title DESC');
                            break;
                    }
                } else {
                    $select->order('products_id DESC');
                }

                if (isset($params['fillmore']) && $params['fillmore'] != '') {
                    switch ($params['fillmore']) {
                        case 'most':
                            $select->order('products.total_sale DESC');
                            break;
                        case 'deal':
                            $select->where(array(
                                '(products.promotion_description IS NOT NULL OR
                                 products.promotion1_description IS NOT NULL OR
                                 products.promotion2_description IS NOT NULL OR
                                 products.promotion3_description IS NOT NULL)'
                            ));
                    }
                }
                $select->group('products.products_id');
                $select->offset($page);
                $select->limit($page_size);
                $select->order('products.ordering ASC');
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

    public function getProductTag($params){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key .= '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductTag('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view','is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                //$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                $select->join('tags', new Expression('FIND_IN_SET(`tags`.`tags_id`, `products`.`tags`)>0'), array());
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));

                if ( !empty($params['categories_id']) ) {
                    $categories_id = $params['categories_id'];
                    if( !is_array($params['categories_id'])){
                        $categories_id = explode(';', $categories_id);
                    }
                    $select->where(array('products_category.categories_id' => $categories_id));
                }

                if ( !empty($params['tag']) ) {
                    $tag = $this->toAlias($params['tag']);
                    $select->where->like('tags.tags_alias', '%' . $tag . '%');
                }

                if ( !empty($params['manus']) ) {
                    $select->where(array(
                        'products.manufacturers_id' => $params['manus']
                    ));
                }

                if ( isset($params['rating']) ) {
                    $select->where(array(
                        'products.rating' => $params['rating']
                    ));
                }

                if ( !empty($params['feature']) ) {
                    $feature = $params['feature'];
                    if( !is_array($params['feature']) )
                        $feature = explode(';', $params['feature']);
                    $features = $this->getModelTable('FeatureTable')->getFeatureByID($feature);
                    $stacks = array();
                    foreach (   $features as $key => $value) {
                        if( empty($stacks[$value['parent_id']]) ){
                            $stacks[$value['parent_id']] =  array($value['feature_id']);
                        }else{
                            $stacks[$value['parent_id']][] = $value['feature_id'];
                        }
                    }
                    if( !empty($stacks) ){
                        $where_stack = array();
                        foreach ($stacks as $key => $stack) {
                            $select->join( array('ft'.$key => 'products_feature') , 'ft'.$key. '.products_id=products.products_id', array());
                            $where_stack[] = 'ft' .$key. '.feature_id IN ('. implode(',', $stack).')';
                        }
                        $select->where( implode(' AND ', $where_stack) );
                    }
                }

                if ( !empty($params['price']) ) {
                    $price = explode(';', $params['price']);
                    if (    count($price) == 2 
                            && is_numeric($price[0]) 
                            && is_numeric($price[1])) {
                        $select->where('products.price_sale BETWEEN ' . $price[0] . ' AND ' . $price[1]);
                    }
                }

                if (  empty($params['sort']) ) {
                    switch ($params['sort']) {
                        case 'price_asc' :
                            $select->order('products.price_sale ASC');
                            break;

                        case 'price_desc' :
                            $select->order('products.price_sale DESC');
                            break;

                        case 'new' :
                            $select->order('products.date_create DESC');
                            break;

                        case 'old' :
                            $select->order('products.date_create ASC');
                            break;

                        case 'az' :
                            $select->order('products.products_title ASC');
                            break;

                        case 'za' :
                            $select->order('products.products_title DESC');
                            break;
                        default :
                            $select->order('products.ordering ASC');
                            break;
                    }
                } else {
                    $select->order('products.ordering ASC');
                }

                if ( !empty($params['filter']) ) {
                    switch ( $params['filter'] ) {
                        case 'most':
                            $select->order('products.total_sale DESC');
                            break;
                        case 'deal':
                            $select->where(array(
                                '(products.promotion_description IS NOT NULL OR
                                 products.promotion1_description IS NOT NULL OR
                                 products.promotion2_description IS NOT NULL OR
                                 products.promotion3_description IS NOT NULL)'
                            ));
                            break;
                    }
                }

                $select->group('products.products_id');
                $select->offset($page);
                $select->limit($page_size);
                $select->order('products.ordering ASC');
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $e){
                $results = array();
            }
        }
        return $results;
    }
	
	public function getProductCateView($cate_id, $params)
    {
		$cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key .= '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductCateView('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
		
                if (isset($params['page_size'])) {
                    $page_size = $params['page_size'];
                } else {
                    $page_size = 20;
                }

                if (isset($params['page'])) {
                    $page = $params['page'];
                } else {
                    $page = 20;
                }

                if ($page <= 1) {
                    $page = 0;
                } else {
                    $page = ($page - 1) * $page_size;
                }

                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');

                //$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				 //$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
				$select->join('products_category', 'products_category.products_id=products.products_id', array());		
				$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id'],
        			//'products.is_viewed' => 1,
                ));
        		/*
        		$select->where('products.position_view != 0');
        		*/
                if (isset($cate_id) && $cate_id) {
                    $select->where(array('products_category.categories_id' => $cate_id));
					/*
        			if(is_array($cate_id)){
        				if(count($cate_id)){
        					$cats = implode(',', $cate_id);
        					$select->where('products.products_id in (select products_id from products where products.is_published=1 and products.is_delete=0 and categories_id IN ('.$cats.'))');
        				}
        			}else{
        				$select->where('products_id in (select max(products_id) from products where products.is_published=1 and products.is_delete=0 and position_view!=0 and categories_id = '.$cate_id.' group by position_view)');
        			} */
                }
                if (isset($params['keyword'])) {
                    $keyword = $this->toAlias($params['keyword']);
                    $select->where->like('products.products_alias', '%' . $keyword . '%');
                    $timeout = 60;
                    if (!isset($_SESSION[$keyword])) {
                        $_SESSION[$keyword] = time();
                        try {
                            $this->insertKeyword($params['keyword']);
                        } catch (\Exception $ex) {
                        }
                    } elseif (time() - $_SESSION[$keyword] > $timeout) {
                        unset($_SESSION[$keyword]);
                    }
                }

                if (isset($params['manus'])) {
                    $select->where(array(
                        'products.manufacturers_id' => $params['manus']
                    ));
                }
                if (isset($params['rating'])) {
                    $select->where(array(
                        'products.rating' => $params['rating']
                    ));
                }
                if (isset($params['feature'])) {
                    $features = $params['feature'];
                    foreach ($features as $key => $value) {
                        $select->join(array("f{$key}" => "products_feature"), "f{$key}.products_id=products.products_id", array());
                        $select->where(array(
                            "f{$key}.feature_id" => $value
                        ));
                    }
                }
                if (isset($params['price'])) {
                    $select->where('products.price_sale BETWEEN ' . $params['price']['min'] . ' AND ' . $params['price']['max']);
                }

                if (isset($params['filter']) && $params['filter'] != '') {
                    switch ($params['filter']) {
                        case 'price_asc' :
                            $select->order('products.price_sale ASC');
                            break;

                        case 'price_desc' :
                            $select->order('products.price_sale DESC');
                            break;

                        case 'new' :
                            $select->order('products.date_create DESC');
                            break;

                        case 'old' :
                            $select->order('products.date_create ASC');
                            break;

                        case 'az' :
                            $select->order('products.products_title ASC');
                            break;

                        case 'za' :
                            $select->order('products.products_title DESC');
                            break;
                    }
                } else {
                    $select->order('products.products_id DESC');
                }

                if (isset($params['fillmore']) && $params['fillmore'] != '') {
                    switch ($params['fillmore']) {
                        case 'most':
                            $select->order('products.total_sale DESC');
                            break;
                        case 'deal':
                            $select->where(array(
                                '(products.promotion_description IS NOT NULL OR
                                 products.promotion1_description IS NOT NULL OR
                                 products.promotion2_description IS NOT NULL OR
                                 products.promotion3_description IS NOT NULL)'
                            ));
                    }
                }
                //$params[''];
                $select->order('products.ordering ASC');
                $select->group('products.products_id');
                $select->offset($page);
                $select->limit($page_size);
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

    public function getProductAll($params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
	
        if(is_array($params)){
            $stri_key = $this->createKeyCacheFromArray($params);
        }else{
            $stri_key = $params;
        }
		
        $keycache = md5($this->getNamspaceCached().':ProductsTable:getProductAll('.$stri_key.')');
		
        $results = $cache->getItem($keycache);
		//if(isset($_GET["demo"])){
		if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1)'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
               // $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
			    $select->join('products_category', 'products_category.products_id=products.products_id', array());
                $select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id'],
                ));
                if ( !empty($params['categories_id']) ) {
                    $categories_id = $params['categories_id'];
                    if( !is_array($params['categories_id'])){
                        $categories_id = explode(';', $categories_id);
                    }
                    $select->where(array('products_category.categories_id' => $categories_id));
                }

                if ( !empty($params['keyword']) ) {
                    $keyword = $this->toAlias($params['keyword']);
                    $select->where->like('products.products_alias', '%' . $keyword . '%');
                }

                if ( !empty($params['manus']) ) {
                    $select->where(array(
                        'products.manufacturers_id' => $params['manus']
                    ));
                }

                if ( isset($params['rating']) ) {
                    $select->where(array(
                        'products.rating' => $params['rating']
                    ));
                }
                if ( !empty($params['feature']) ) {
                    $feature = $params['feature'];
                    if( !is_array($params['feature']) )
                        $feature = explode(';', $params['feature']);
                    $features = $this->getModelTable('FeatureTable')->getFeatureByID($feature);

                    $stacks = array();
                    foreach (   $features as $key => $value) {
                        if( empty($stacks[$value['parent_id']]) ){
                            $stacks[$value['parent_id']] =  array($value['feature_id']);
                        }else{
                            $stacks[$value['parent_id']][] = $value['feature_id'];
                        }
                    }

                    if( !empty($stacks) ){
                        $where_stack = array();
                        foreach ($stacks as $key => $stack) {
                            $select->join( array('ft'.$key => 'products_feature') , 'ft'.$key. '.products_id=products.products_id', array());
                            $where_stack[] = 'ft' .$key. '.feature_id IN ('. implode(',', $stack).')';
                        }
                        $select->where( implode(' AND ', $where_stack) );
                    }
                }
                if ( !empty($params['price']) ) {
                    $price = explode(';', $params['price']);
                    if (    count($price) == 2 
                            && is_numeric($price[0]) 
                            && is_numeric($price[1])) {
                        $select->where('products.price_sale BETWEEN ' . $price[0] . ' AND ' . $price[1]);
                    }
                }
				
                if (!empty($params['sort'])) {
                    switch ($params['sort']) {
                        case 'price_asc' :
                            $select->order('products.price_sale ASC');
                            break;
                        case 'price_desc' :
                            $select->order('products.price_sale DESC');
                            break;
                        case 'new' :
                            $select->order('products.date_create DESC');
                            break;
                        case 'old' :
                            $select->order('products.date_create ASC');
                            break;
                        case 'az' :
                            $select->order('products.products_title ASC');
                            break;
                        case 'za' :
                            $select->order('products.products_title DESC');
                            break;
                        default :
                            $select->order('products.ordering ASC');
                            break;
                    }
                } else {
                    $select->order('products.ordering ASC');
                }

                if ( !empty($params['filter']) ) {
                    switch ( $params['filter'] ) {
                        case 'most':
                            $select->order('products.total_sale DESC');
                            break;
                        case 'deal':
                            $select->where(array(
                                '(products.promotion_description IS NOT NULL OR
                                 products.promotion1_description IS NOT NULL OR
                                 products.promotion2_description IS NOT NULL OR
                                 products.promotion3_description IS NOT NULL)'
                            ));
                            break;
                    }
                }
                $select->group('products.products_id');
                if ( isset($params['page_size']) &&  isset($params['page']) ) {
					$page_size=$params['page_size'];
                    $page = (((int)$params['page'] > 1) ? ((int)$params['page'] - 1) * $page_size : 0);
                    $select->offset($page);
                    $select->limit($params['page_size']);
                }
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($keycache, $results);
            }catch(\Exception $ex){
                $results = array();
            }
		}
        return $results;
    }
	
	public function getProductGoingOn($params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($params)){
            $stri_key = $this->createKeyCacheFromArray($params);
        }else{
            $stri_key = $params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductGoingOn('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                if (isset($params['page_size'])) {
                    $page_size = $params['page_size'];
                } else {
                    $page_size = 20;
                }

                if (isset($params['page'])) {
                    $page = $params['page'];
                } else {
                    $page = 20;
                }

                if ($page <= 1) {
                    $page = 0;
                } else {
                    $page = ($page - 1) * $page_size;
                }
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'), 'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');

				// $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
			    //Edit category muilti select
				$select->join('products_category', 'products_category.products_id=products.products_id', array());
                $select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                //End edit
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                
        		$select->where(array('products.is_goingon' => 1));
                
        		if (isset($params['keyword'])) {
                    $keyword = $this->toAlias($params['keyword']);
                    $select->where->like('products.products_alias', '%' . $keyword . '%');
                    $timeout = 60;
                    if (!isset($_SESSION[$keyword])) {
                        $_SESSION[$keyword] = time();
                        try {
                            $this->insertKeyword($params['keyword']);
                        } catch (\Exception $ex) {
                        }
                    } elseif (time() - $_SESSION[$keyword] > $timeout) {
                        unset($_SESSION[$keyword]);
                    }
                }

                if (isset($params['manus'])) {
                    $select->where(array(
                        'products.manufacturers_id' => $params['manus']
                    ));
                }
                if (isset($params['rating'])) {
                    $select->where(array(
                        'products.rating' => $params['rating']
                    ));
                }
                if (isset($params['feature'])) {
                    /*
                    $select->join('products_feature', 'products_feature.products_id=products.products_id', array());
                    $features = $params['feature'];
                    $select->where(array(
                        'products_feature.feature_id' => $features
                    ));*/
                    $features = $params['feature'];
                    foreach ($features as $key => $value) {
                        $select->join(array("f{$key}" => "products_feature"), "f{$key}.products_id=products.products_id", array());
                        $select->where(array(
                            "f{$key}.feature_id" => $value
                        ));
                    }
                }
                if (isset($params['price'])) {
                    $select->where('products.price_sale BETWEEN ' . $params['price']['min'] . ' AND ' . $params['price']['max']);
                }

                if (isset($params['filter']) && $params['filter'] != '') {
                    switch ($params['filter']) {
                        case 'price_asc' :
                            $select->order('products.price_sale ASC');
                            break;

                        case 'price_desc' :
                            $select->order('products.price_sale DESC');
                            break;

                        case 'new' :
                            $select->order('products.date_create DESC');
                            break;

                        case 'old' :
                            $select->order('products.date_create ASC');
                            break;

                        case 'az' :
                            $select->order('products.products_title ASC');
                            break;

                        case 'za' :
                            $select->order('products.products_title DESC');
                            break;
                    }
                } else {
                    $select->order('products.ordering ASC');
                }
                if (isset($params['fillmore']) && $params['fillmore'] != '') {
                    switch ($params['fillmore']) {
                        case 'most':
                            $select->order('products.total_sale DESC');
                            break;
                        case 'deal':
                            $select->where(array(
                                '(products.promotion_description IS NOT NULL OR
                                 products.promotion1_description IS NOT NULL OR
                                 products.promotion2_description IS NOT NULL OR
                                 products.promotion3_description IS NOT NULL)'
                            ));
                    }
                }
                //$params[''];
                //$select->order('parent_id ASC');
                $select->group('products.products_id');
                $select->offset($page);
                $select->limit($page_size);
                $selectString = $sql->getSqlStringForSqlObject($select);
        		//echo $selectString;die();
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getProductByCustom($params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($params)){
            $stri_key = $this->createKeyCacheFromArray($params);
        }else{
            $stri_key = $params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductByCustom('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                if (isset($params['page_size'])) {
                    $page_size = $params['page_size'];
                } else {
                    $page_size = 20;
                }

                if (isset($params['page'])) {
                    $page = $params['page'];
                } else {
                    $page = 20;
                }

                if ($page <= 1) {
                    $page = 0;
                } else {
                    $page = ($page - 1) * $page_size;
                }

                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view', 'is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                if (isset($params['keyword'])) {
                    $keyword = $this->toAlias($params['keyword']);
                    $select->where->like('products.products_alias', '%' . $keyword . '%');
                }
                $select->where(array(
                    'is_delete' => 0,
                    'is_published' => 1,
                    'website_id'=>$_SESSION['website_id']
                ));
                //$params[''];
                $select->order('products.ordering ASC');
				$select->group('products.products_id');
                $select->offset(0);
                $select->limit(4);
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

    public function getProductHotCat($catids){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($catids)){
            $stri_key = $this->createKeyCacheFromArray($catids);
        }else{
            $stri_key = $catids;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductHotCat('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            if(!$catids){
                $results = array();
            }else{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view','is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
				// $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
			    //Edit category muilti select
				$select->join('products_category', 'products_category.products_id=products.products_id', array());
                //End edit
                $select->where(array(
                    'products_category.categories_id' => $catids,
                    'is_published' => 1,
                    'is_delete' => 0,
                    'website_id'=>$_SESSION['website_id']
                ));
                $select->order(array(
                    'rating' => 'DESC',
                    'number_views' => 'DESC',
                    'number_like' => 'DESC',
                ));
				$select->group('products.products_id');
                $select->limit(3);
                $selectString = $sql->getSqlStringForSqlObject($select);
                try{
                    $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                    $results = $results->toArray();
                    $cache->setItem($key,$results);
                }catch (\Exception $ex){
                    $results = array();
                }
            }
        }
        return $results;
    }

    public function getKeywords($offset, $limit){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:getKeywords('.$offset.';'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('search_keywords');
            $select->offset($offset);
            $select->limit($limit);
            $select->order(array(
                'total_search' => 'DESC',
                'website_id'=>$_SESSION['website_id']
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setITem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function countTotalProduct($cate_id, $params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key = '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:countTotalProduct('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT products.products_id)")));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
				// $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
			    //Edit category muilti select
				$select->join('products_category', 'products_category.products_id=products.products_id', array());
                //$select->join('categories', 'categories.categories_id=products_category.categories_id', array());
                //End edit
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                if (isset($cate_id) && $cate_id) {
                    //$select->where(array('products.categories_id' => $cate_id));
					//edit category product
					$select->where(array('products_category.categories_id' => $cate_id));
                }
                if (isset($params['keyword'])) {
                    $keyword = $this->toAlias($params['keyword']);
                    $select->where->like('products_alias', '%' . $keyword . '%');
                }
                if (isset($params['manus'])) {
                    $select->where(array(
                        'products.manufacturers_id' => $params['manus']
                    ));
                }
                if (isset($params['feature'])) {
                    $features = $params['feature'];
                    foreach ($features as $key => $value) {
                        $select->join(array("f{$key}" => "products_feature"), "f{$key}.products_id=products.products_id", array());
                        $select->where(array(
                            "f{$key}.feature_id" => $value
                        ));
                    }
                }
                if (isset($params['rating'])) {
                    $select->where(array(
                        'products.rating' => $params['rating']
                    ));
                }
                if (isset($params['price'])) {
                    $select->where('products.price_sale BETWEEN ' . $params['price']['min'] . ' AND ' . $params['price']['max']);
                }

                if (isset($params['fillmore']) && $params['fillmore'] != '') {
                    switch ($params['fillmore']) {
                        case 'deal':
                            $select->where(array(
                                '(promotion_description IS NOT NULL OR
                                 promotion1_description IS NOT NULL OR
                                 promotion2_description IS NOT NULL OR
                                 promotion3_description IS NOT NULL)'
                            ));
                    }
                }
			//	$select->group('products.products_id');
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $result = $results->toArray();
                $results = 0;
                if (count($result) > 0) {
                    $results = $result[0]['total'];
                }
                $cache->setITem($key, $results);
            }catch (\Exception $ex){
                $results = 0;
            }
        }
        return $results;
    }

    public function countProductTag($params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key = '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:countProductTag('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT products.products_id)")));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
				// $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
			    //Edit category muilti select
				$select->join('products_category', 'products_category.products_id=products.products_id', array());
               // $select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                //End edit
                $select->join('tags', new Expression('FIND_IN_SET(`tags`.`tags_id`, `products`.`tags`)>0'), array());
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                
                if ( !empty($params['categories_id']) ) {
                    $categories_id = $params['categories_id'];
                    if( !is_array($params['categories_id'])){
                        $categories_id = explode(';', $categories_id);
                    }
					//$select->where(array('products.categories_id' => $categories_id));
					//edit category product
					$select->where(array('products_category.categories_id' => $categories_id));
                }

                if ( !empty($params['tag']) ) {
                    $tag = $this->toAlias($params['tag']);
                    $select->where->like('tags.tags_alias', '%' . $tag . '%');
                }

                if ( !empty($params['manus']) ) {
                    $select->where(array(
                        'products.manufacturers_id' => $params['manus']
                    ));
                }

                if ( isset($params['rating']) ) {
                    $select->where(array(
                        'products.rating' => $params['rating']
                    ));
                }

                if ( !empty($params['feature']) ) {
                    $feature = $params['feature'];
                    if( !is_array($params['feature']) )
                        $feature = explode(';', $params['feature']);
                    $features = $this->getModelTable('FeatureTable')->getFeatureByID($feature);
                    $stacks = array();
                    foreach (   $features as $key => $value) {
                        if( empty($stacks[$value['parent_id']]) ){
                            $stacks[$value['parent_id']] =  array($value['feature_id']);
                        }else{
                            $stacks[$value['parent_id']][] = $value['feature_id'];
                        }
                    }
                    if( !empty($stacks) ){
                        $where_stack = array();
                        foreach ($stacks as $key => $stack) {
                            $select->join( array('ft'.$key => 'products_feature') , 'ft'.$key. '.products_id=products.products_id', array());
                            $where_stack[] = 'ft' .$key. '.feature_id IN ('. implode(',', $stack).')';
                        }
                        $select->where( implode(' AND ', $where_stack) );
                    }
                }

                if ( !empty($params['price']) ) {
                    $price = explode(';', $params['price']);
                    if (    count($price) == 2 
                            && is_numeric($price[0]) 
                            && is_numeric($price[1])) {
                        $select->where('products.price_sale BETWEEN ' . $price[0] . ' AND ' . $price[1]);
                    }
                }

                if ( !empty($params['filter']) ) {
                    switch ( $params['filter'] ) {
                        case 'most':
                            $select->order('products.total_sale DESC');
                            break;
                        case 'deal':
                            $select->where(array(
                                '(products.promotion_description IS NOT NULL OR
                                 products.promotion1_description IS NOT NULL OR
                                 products.promotion2_description IS NOT NULL OR
                                 products.promotion3_description IS NOT NULL)'
                            ));
                            break;
                    }
                }
				//$select->group('products.products_id');
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $result = $results->toArray();
                $results = 0;
                if (count($result) > 0) {
                    $results = $result[0]['total'];
                }
                $cache->setITem($key, $results);
            }catch (\Exception $ex){
                $results = 0;
            }
        }
        return $results;
    }

    public function countTotalProductAll($params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($params)){
            $stri_key = $this->createKeyCacheFromArray($params);
        }else{
            $stri_key = $params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:countTotalProductAll1('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT products.products_id)")));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                //$select->join('categories', 'categories.categories_id=products.categories_id', array());
				// $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
			    //Edit category muilti select
				$select->join('products_category', 'products_category.products_id=products.products_id', array());
                //$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                //End edit
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));

                if ( !empty($params['categories_id']) ) {
                    $categories_id = $params['categories_id'];
                    if( !is_array($params['categories_id'])){
                        $categories_id = explode(';', $categories_id);
                    }
                    $select->where(array('products_category.categories_id' => $categories_id));
					//edit category_id
					//$select->where(array('products_category.categories_id' => $categories_id));
					//Edit category muilti select
					//$select->join('products_category', 'products_category.products_id=products.products_id', array());
					//End edit
                }
                
                if ( !empty($params['keyword']) ) {
                    $keyword = $this->toAlias($params['keyword']);
                    $select->where->like('products.products_alias', '%' . $keyword . '%');
                }

                if ( !empty($params['manus']) ) {
                    $select->where(array(
                        'products.manufacturers_id' => $params['manus']
                    ));
                }

                if ( isset($params['rating']) ) {
                    $select->where(array(
                        'products.rating' => $params['rating']
                    ));
                }

                if ( !empty($params['feature']) ) {
                    $feature = $params['feature'];
                    if( !is_array($params['feature']) )
                        $feature = explode(';', $params['feature']);
                    $features = $this->getModelTable('FeatureTable')->getFeatureByID($feature);
                    $stacks = array();
                    foreach (   $features as $key => $value) {
                        if( empty($stacks[$value['parent_id']]) ){
                            $stacks[$value['parent_id']] =  array($value['feature_id']);
                        }else{
                            $stacks[$value['parent_id']][] = $value['feature_id'];
                        }
                    }
                    if( !empty($stacks) ){
                        $where_stack = array();
                        foreach ($stacks as $key => $stack) {
                            $select->join( array('ft'.$key => 'products_feature') , 'ft'.$key. '.products_id=products.products_id', array());
                            $where_stack[] = 'ft' .$key. '.feature_id IN ('. implode(',', $stack).')';
                        }
                        $select->where( implode(' AND ', $where_stack) );
                    }
                }

                if ( !empty($params['price']) ) {
                    $price = explode(';', $params['price']);
                    if (    count($price) == 2 
                            && is_numeric($price[0]) 
                            && is_numeric($price[1])) {
                        $select->where('products.price_sale BETWEEN ' . $price[0] . ' AND ' . $price[1]);
                    }
                }

                if ( !empty($params['filter']) ) {
                    switch ( $params['filter'] ) {
                        case 'most':
                            $select->order('products.total_sale DESC');
                            break;
                        case 'deal':
                            $select->where(array(
                                '(products.promotion_description IS NOT NULL OR
                                 products.promotion1_description IS NOT NULL OR
                                 products.promotion2_description IS NOT NULL OR
                                 products.promotion3_description IS NOT NULL)'
                            ));
                            break;
                    }
                }
			//	$select->group('products.products_id');
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $result = $results->toArray();
                $results = 0;
                if (count($result) > 0) {
                    $results = $result[0]['total'];
                }
                $cache->setITem($key, $results);
            }catch (\Exception $ex){
                $results = 0;
            }
        }
        return $results;
    }
	
	public function countTotalProductGoingOn($params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($params)){
            $stri_key = $this->createKeyCacheFromArray($params);
        }else{
            $stri_key = $catids;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:countTotalProductGoingOn('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT products.products_id)")));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                //$select->join('categories', 'categories.categories_id=products.categories_id', array());
				//Edit category muilti select
				$select->join('products_category', 'products_category.products_id=products.products_id', array());
                //$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
                //End edit
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                $select->where(array('products.is_goingon' => 1));
                if (isset($params['keyword'])) {
                    $keyword = $this->toAlias($params['keyword']);
                    $select->where->like('products_alias', '%' . $keyword . '%');
                }
                if (isset($params['manus'])) {
                    $select->where(array(
                        'products.manufacturers_id' => $params['manus']
                    ));
                }
                if ( !empty($params['feature']) && is_array($params['feature']) ) {
                    $select->join("products_feature", "products_feature.products_id=products.products_id", array());
                    $select->where(array(
                        "products_feature.feature_id" => $params['feature']
                    ));
                }
                if (isset($params['rating'])) {
                    $select->where(array(
                        'products.rating' => $params['rating']
                    ));
                }
                if (isset($params['price'])) {
                    $select->where('products.price_sale BETWEEN ' . $params['price']['min'] . ' AND ' . $params['price']['max']);
                }

                if (isset($params['fillmore']) && $params['fillmore'] != '') {
                    switch ($params['fillmore']) {
                        case 'deal':
                            $select->where(array(
                                '(promotion_description IS NOT NULL OR
                                 promotion1_description IS NOT NULL OR
                                 promotion2_description IS NOT NULL OR
                                 promotion3_description IS NOT NULL)'
                            ));
                    }
                }
				//$select->group('products.products_id');
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $result = $results->toArray();
                $results = 0;
                if (count($result) > 0) {
                    $results = $result[0]['total'];
                }
                $cache->setITem($key, $results);
            }catch (\Exception $ex){
                $results = 0;
            }
        }
        return $results;
    }

    public function insertKeyword($keyword)
    {
        try {
            $adapter = $this->tableGateway->getAdapter();
            if (trim($keyword)) {
                $keyword = trim($keyword);
                $sql = "INSERT INTO search_keywords(`website_id`,`keyword_title`,`total_search`) VALUES ({$_SESSION['website_id']},'{$keyword}', 1) ON DUPLICATE KEY UPDATE total_search=total_search+1";
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }
        } catch (\Exception $ex) {
           // throw new \Exception($ex->getMessage());
			die();
        }
    }

    public function getSqlProductCate($cate_id, $params)
    {
        if (isset($params['page_size'])) {
            $page_size = $params['page_size'];
        } else {
            $page_size = 20;
        }

        if (isset($params['page'])) {
            $page = $params['page'];
        } else {
            $page = 20;
        }

        if ($page <= 1) {
            $page = 0;
        } else {
            $page = ($page - 1) * $page_size;
        }

        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating',  'number_like','total_sale','wholesale','type_view','is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
        $select->from('products');
        $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
        //$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
		//Edit category muilti select
		$select->join('products_category', 'products_category.products_id=products.products_id', array());
		$select->join('categories', 'categories.categories_id=products_category.categories_id', array('categories_title'));
		//End edit
        $select->where(array(
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.categories_id' => $cate_id
        ));

        if (isset($params['filter']) && $params['filter'] != '') {
            switch ($params['filter']) {
                case 'price_asc' :
                    $select->order('price_sale ASC');
                    break;

                case 'price_desc' :
                    $select->order('price_sale DESC');
                    break;

                case 'new' :
                    $select->order('date_create DESC');
                    break;

                case 'old' :
                    $select->order('date_create ASC');
                    break;

                case 'az' :
                    $select->order('products_title ASC');
                    break;

                case 'za' :
                    $select->order('products_title DESC');
                    break;
            }
        } else {
            $select->order('products.ordering ASC');
        }
		$select->group('products.products_id');
        //$params[''];

        //$select->order('parent_id ASC');
        //$select->limit($page_size);
        //$select->offset($page);

        $selectString = $sql->getSqlStringForSqlObject($select);
        return $selectString;

    }

    public function getProductCateRemarketing($cate_id, $params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key .= '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductCateRemarketing('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $sql = array();
                $adapter = $this->tableGateway->getAdapter();
                if (is_array($cate_id)) {
                    foreach ($cate_id as $id) {
                        $sql[] = "(SELECT `products`.*, `categories`.`categories_title` AS `categories_title`, count(comments_id) as total_review, (SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 ) AS total_price_extention
                                FROM `products`
                                INNER JOIN `categories` ON `categories`.`categories_id`=`products`.`categories_id`
                                LEFT JOIN `comments` ON `comments`.`comments_product`=`products`.`products_id`
                                WHERE `products`.`is_published` = '1' AND `products`.`is_delete` = '0' AND products.website_id={$_SESSION['website_id']}
                                AND `products`.`categories_id` IN ({$id}) GROUP BY products.products_id ORDER BY `products_id` DESC LIMIT 20)";
                    }
                }
				$select->group('products.products_id');
                $results = array();
                if ($sql) {
                    $sql = implode(' UNION ', $sql);
                    $results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                    $results = $results->toArray();
                    $cache->setItem($key, $results);
                }
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
    public function getTotalProductCate($cate_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($cate_id)){
            $stri_key = $this->createKeyCacheFromArray($cate_id);
        }else{
            $stri_key = $cate_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getTotalProductCate('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
    			$adapter = $this->tableGateway->getAdapter();
    			$sql = new Sql($adapter);
    			$select = $sql->select()->columns(array('products_id'));
    			$select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
    			$select->where(array(
    				'is_published' => 1,
    				'is_delete' => 0,
    				'categories_id' => $cate_id,
                    'website_id'=>$_SESSION['website_id']
    			));
    			//$select->order('parent_id ASC');
    	
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			//echo $selectString;
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
        		$results = count($results);
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = 0;
            }
        }
        return $results;
		
    }

    public function getConcernProduct()
    {
		$cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:getConcernProduct');
        $results = $cache->getItem($key);
        if(!$results) {
			$adapter = $this->tableGateway->getAdapter();
			$sql = new Sql($adapter);
			$select = $sql->select();
            $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view','is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
			$select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
			$select->join('comments', 'comments.comments_product=products.products_id', array('total_review' => new Expression("count(DISTINCT comments.comments_id)")), 'left');
			$select->where(array(
				'is_published' => 1,
				'is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
			));
			$select->group('products.products_id');
            $select->order('products.ordering ASC');
			$select->limit(20);
			$select->offset(0);
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

    public function getRow($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getRow('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('comments', 'comments.comments_product=products.products_id', array('total_review' => new Expression('count(comments_id)')), 'left');
            $select->join('manufacturers', 'products.manufacturers_id=manufacturers.manufacturers_id', array('manufacturers_name', 'manufacturers_id'));
            $select->where(array(
                'products.is_published' => 1,
                'products.is_delete' => 0,
                'products.products_id' => $id,
                'products.website_id'=>$_SESSION['website_id']
            ));
            
            $select->group('products.products_id');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = (is_array($id) ? $resultSet->toArray() : $resultSet->current());
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
	//Get product detail by alias
	public function getRowAlias($alias)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($alias)){
            $stri_key = $this->createKeyCacheFromArray($alias);
        }else{
            $stri_key = $alias;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getRow('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('comments', 'comments.comments_product=products.products_id', array('total_review' => new Expression('count(comments_id)')), 'left');
            $select->join('manufacturers', 'products.manufacturers_id=manufacturers.manufacturers_id', array('manufacturers_name', 'manufacturers_id'));
            $select->where(array(
                'products.is_published' => 1,
                'products.is_delete' => 0,
                'products.products_alias' => $alias,
                'products.website_id'=>$_SESSION['website_id']
            ));
            
            $select->group('products.products_id');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = $resultSet->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
    public function getProductsWithType($id, $product_type)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductsWithType('.$stri_key.';'.$product_type.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title', 'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon', 'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating', 'number_like','total_sale','wholesale','type_view','is_delete', 'is_published','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`products_type_id` = '.$product_type),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'));
            $select->join('comments', 'comments.comments_product=products.products_id', array('total_review' => new Expression('count(comments_id)')), 'left');
            $select->join('manufacturers', 'products.manufacturers_id=manufacturers.manufacturers_id', array('manufacturers_name', 'manufacturers_id'));
            $select->where(array(
                'products.is_published' => 1,
                'products.is_delete' => 0,
                'products.products_id' => $id,
                'products.website_id'=>$_SESSION['website_id']
            ));
            
            $select->group('products.products_id');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = (is_array($id) ? $resultSet->toArray() : $resultSet->current());
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getImages($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getImages('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products_images');
            $select->join ( 'products', 'products.products_id=products_images.products_id', array('title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->where(array(
                'products.is_published' => 1,
                'products.products_id' => $id,
                'products.website_id'=>$_SESSION['website_id']
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = $resultSet->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getImagesOfProduct($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getImagesOfProduct('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products_images');
            $select->join ( 'products', 'products.products_id=products_images.products_id', array());
            $select->where(array(
                'products.products_id' => $id
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = $resultSet->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function updateImagesProduct($row, $where)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $update = $sql->update();
        $update->table( 'products_images' );
        $update->set( $row );
        $update->where( $where );
        $selectString = $sql->getSqlStringForSqlObject($update);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }

    public function getFeature($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getFeature('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*'));
            $select->from('feature');
            $select->join('products_feature', 'products_feature.feature_id=feature.feature_id', array('value'));
            $select->where(array(
                'feature.is_published' => 1,
                'feature.is_delete' => 0,
                'products_feature.products_id' => $id,
                'feature.website_id'=>$_SESSION['website_id']
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = $resultSet->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getHtmlFeature($data, $parentid = 0, $class = 'feature_list',$lv_=1)
    {
		//print_r($data);die();
        if ($data) {
            $html = "<ul class='cl-box feature_list property-".($lv_==1?'0':$lv_)."' >";
            foreach ($data as $ii=>$row) {
                if ($row['parent_id'] == $parentid) {
                    if ($row['children'] > 0) {
                        $html .= "<li class='has-chid ' >";
							$html .= "<ul class='cl-box  property-".$lv_."' >";
								$html .= "<li class='lb-chld' ><span>" . $row['feature_title'] . "</span></li>";
								$html .= "<li class='ct-chld'  >";
									$html .= $this->getHtmlFeature($data, $row['feature_id'], '',($lv_+1));
								$html .= "</li>";
							$html .= "</ul>";
                        $html .= "</li>";
                    } elseif ($row['parent_id'] != 0) {
							if ($row['is_value'] == 0) {
								$html .= "<li class='end-chld' ><span>{$row['feature_title']}</span></li>";
							} else {
								$html .= "<li class='end-chld' ><span>{$row['value']}</span></li>";
							}
                    } else {
                        if ($row['is_value'] != 0) {
                            $html .= "<li class='has-chid' >";
								$html .= "<ul class='cl-box  property-".$lv_."'  >";
									$html .= "<li class='lb-chld' ><span>{$row['feature_title']}</span></li>";
									$html .= "<li class='ct-chld' >";
										$html .= "<ul class='l-property-".($lv_+1)."' >";
											$html .= "<li class='end-chld'  ><span>{$row['value']}</span></li>";
										$html .= "</ul>";
									$html .= "</li>";
								$html .= "</ul>";
                            $html .= "</li>";

                        }
                    }
                }
            }
            $html .= "</ul>";
            return $html;
        }
        return '';

    }

    public function getHotDeal($where = array())
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($where)){
            $stri_key = $this->createKeyCacheFromArray($where);
        }else{
            $stri_key = $where;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getHotDeal('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $where[] = "products.website_id={$_SESSION['website_id']} AND (products.promotion_description IS NOT NULL ||
                        products.promotion1_description IS NOT NULL ||
                        products.promotion2_description IS NOT NULL ||
                        products.promotion3_description IS NOT NULL)";
            $where = implode(' AND ', $where);
            $sql = "SELECT 'products_id','products_code','categories_id','url_crawl','manufacturers_id','users_id','users_fullname','products_title',                    'products_alias','products_description','promotion','promotion_description','promotion_ordering','is_new','is_hot','is_available','is_goingon',                    'is_viewed','position_view','date_create','date_update','price','price_sale','ordering','quantity','thumb_image','number_views','vat','rating',                    'number_like','total_sale','wholesale','type_view' FROM products WHERE {$where} ORDER BY date_update DESC";
            try {
                $adapter = $this->tableGateway->getAdapter();
                $results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function treerecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1, $id_col, $parentid_col)
    {
        if (@$children [$id] && $level <= $maxlevel) {
            foreach ($children [$id] as $v) {
                $id = $v [$id_col];
                if ($type) {
                    $pre = '';
                    $spacer = '|____';
                } else {
                    $pre = '- ';
                    $spacer = '__';
                }
                if ($v [$parentid_col] == 0) {
                    $txt = $v ['name'];
                } else {
                    $txt = $pre . $v ['name'];
                }
                $list [$id] = $v;
                $list [$id] ['treename'] = "$indent$txt";
                $list [$id] ['children'] = count(@$children [$id]);
                $list = self::treerecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type, $id_col, $parentid_col);
            }
        }
        return $list;
    }

    public function multiLevelData($return_data = TRUE, $rows, $id_col, $parentid_col, $title_col, $root_title = '__ROOT__')
    {
        if ($rows) {
            $children = array();
            foreach ($rows as $v) {
                $pt = $v[$parentid_col] ? $v[$parentid_col] : 0;
                $v ['name'] = $v [$title_col];
                $list = @$children [$pt] ? $children [$pt] : array();
                array_push($list, $v);
                $children [$pt] = $list;
            }
            $list = self::treerecurse(0, '', array(), $children, 10, 0, 1, $id_col, $parentid_col);
            if (!$return_data) {
                return $list;
            }
            $data[0] = $root_title;
            foreach ($list as $category) {
                $data[$category [$id_col]] = $category ['treename'];
            }
            return $data;
        }
    }

    public function likeProduct($users_id, $products_id)
    {
        try {
            if (!$this->isLikeProduct($users_id, $products_id)) {
                $adapter = $this->tableGateway->getAdapter();
                $sql = "INSERT INTO like_products(users_id,products_id) VALUES({$users_id},{$products_id})";
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }
        } catch (\Exception $ex) {
            return FALSE;
        }
        return TRUE;
    }

    public function unlikeProduct($users_id, $products_id)
    {
        try {
            if ($this->isLikeProduct($users_id, $products_id)) {
                $adapter = $this->tableGateway->getAdapter();
                $sql = "DELETE FROM like_products WHERE users_id={$users_id} AND products_id={$products_id}";
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }
        } catch (\Exception $ex) {
            return FALSE;
        }
        return TRUE;
    }

    public function isLikeProduct($users_id, $products_id)
    {
        try {
            $adapter = $this->tableGateway->getAdapter();
            $sql = "SELECT * FROM like_products WHERE users_id={$users_id} AND products_id={$products_id}";
            $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            if ($result->count() > 0) {
                return TRUE;
            }
            return FALSE;
        } catch (\Exception $ex) {
            return FALSE;
        }
    }

    public function getExtensions($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getExtensions('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('products_extensions');
                $select->join ( 'products', 'products.products_id=products_extensions.products_id', array());
                $select->where(array(
                        'products_extensions.id' => $id,
                        'products.website_id'=>$_SESSION['website_id']
                    ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function getExtensionsAlwaysProduct($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getExtensionsAlwaysProduct('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('products_extensions');
                $select->join ( 'products', new Expression('products.products_id=products_extensions.products_id AND products_extensions.is_always = 1'), array());
                $select->where(array(
                        'products.products_id' => $id,
                        'products.website_id'=>$_SESSION['website_id']
                    ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function getExtensionsTransportationProduct($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getExtensionsTransportationProduct('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('products_transportation');
                $select->join ( 'products', new Expression('products.products_id=products_transportation.products_id'), array());
                $select->where(array(
                    'products.products_id' => $id,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function getExtensionsRequire($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getExtensionsRequire('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('products_extensions');
                $select->join ( 'products', 'products.products_id=products_extensions.products_id', array());
                $select->where(array(
                        'products_extensions.id' => $id,
                        'products.website_id'=>$_SESSION['website_id']
                    ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function getReview($products_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getReview('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = "SELECT comments.comments_rating,comments.comments_content,users.full_name, users.avatar, comments.comments_datecrerate as date_create
                    FROM comments
                    INNER JOIN users ON comments_member=users.users_id
                    WHERE comments_product={$products_id} AND comments.website_id={$_SESSION['website_id']}";
                $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function getHotProductConvert()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ProductsTable:getHotProductConvert');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'convert_search' => 0,
                'website_id'=>$_SESSION['website_id']
            ));
            $select->order('products.ordering ASC');
            $select->limit(20);
            $select->offset(0);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function getProductAddSearch($where)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($where)){
            $stri_key = $this->createKeyCacheFromArray($where);
        }else{
            $stri_key = $where;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductAddSearch('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array("products_id", "products_title", "seo_keywords", 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array('website_id'=>$_SESSION['website_id']));
            $select->where($where);
            $select->order('products.ordering ASC');
            $select->limit(20);
            $select->offset(0);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = $resultSet->toArray();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function updateAddsearch($products_id)
    {
        try {
            $adapter = $this->tableGateway->getAdapter();
            $sql = "update products set convert_search=1 WHERE products_id={$products_id} AND website_id={$_SESSION['website_id']} ";
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $ex) {
            //die($ex->getMessage());
            return FALSE;
        }
        return true;
    }

    public function getByManus($manuid, $limit){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($manuid)){
            $stri_key = $this->createKeyCacheFromArray($manuid);
        }else{
            $stri_key = $manuid;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getByManus('.$stri_key.';'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('comments', 'comments.comments_product=products.products_id', array('total_review' => new Expression("count(DISTINCT comments.comments_id)")), 'left');
            $select->where(array(
                'manufacturers_id' => $manuid,
                'is_published' => 1,
                'is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
            ));
            $select->order('products.ordering ASC');
            $select->group('products.products_id');
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

    public function getRecommendProductByCatId($catId, $limit)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($catId)){
            $stri_key = $this->createKeyCacheFromArray($catId);
        }else{
            $stri_key = $catId;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getRecommendProductByCatId('.$stri_key.';'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()->columns(array('categories_id' => 'to_categories_id'));
                $select->from('categories_recommend');
                $select->join('categories', 'categories_recommend.to_categories_id=categories.categories_id', array('categories_title', 'categories_alias'));
                $select->where(array(
                    'from_categories_id' => $catId,
                    'categories.website_id'=>$_SESSION['website_id']
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $cat_data = array();
                foreach ($results as $cat) {
                    $select = $sql->select();
                    $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
                    $select->from('products');
                    $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                    $select->where(array(
                        'categories_id' => $cat->categories_id,
                        'is_published' => 1,
                        'is_delete' => 0,
                        'website_id'=>$_SESSION['website_id']
                    ));
                    $select->limit($limit);
                    $selectString = $sql->getSqlStringForSqlObject($select);
                    $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                    $cat_data[$cat->categories_id]['categories_title'] = $cat->categories_title;
                    $cat_data[$cat->categories_id]['categories_alias'] = $cat->categories_alias;
                    $cat_data[$cat->categories_id]['results'] = $results->toArray();
                }
                $results = $cat_data;
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function getRecommendProductById($products_id, $offset = 0, $limit = 5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($products_id)){
            $stri_key = $this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key = $products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getRecommendProductById('.$stri_key.','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()->columns(array());
                $select->from('products_recommend');
                $select->join('products', 'products_recommend.to_products_id=products.products_id', array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'), 'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('manufacturers', 'products.manufacturers_id=manufacturers.manufacturers_id', array('manufacturers_name'));
                $select->where(array(
                    'from_products_id' => $products_id,
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                $select->order('products.ordering ASC');
				$select->group('products.products_id');
                $select->offset($offset);
                $select->limit($limit);
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function getProductNearPrice($catId, $products_id, $price_sale, $min, $max, $offset = 0, $limit = 5)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($catId)){
            $stri_key = $this->createKeyCacheFromArray($catId);
        }else{
            $stri_key = $catId;
        }
        if(is_array($products_id)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($products_id);
        }else{
            $stri_key .= '-'.$products_id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductNearPrice11('.$stri_key.';'.$price_sale.';'.$min.';'.$max.';'.$offset.';'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            try {
                $sql = new Sql($adapter); 
                $select = $sql->select()->columns(array( 
                    'products_id',
                    'categories_id',
                    'products_code',
                    'manufacturers_id',
                    'users_id',
					'url_crawl',
                    'users_fullname',
                    'products_title',
                    'products_alias',
                    'products_description',
                    'seo_keywords',
                    'seo_description',
                    'seo_title',
                    'promotion',
                    'promotion_description',
                    'promotion_ordering',
                    'is_published',
                    'is_delete',
                    'is_new',
                    'is_hot',
                    'is_available',
                    'date_create',
                    'date_update',
                    'price',
					'is_goingon',
                    'price_sale',
                    'ordering',
                    'quantity',
                    'thumb_image',
                    'number_views',
                    'vat',
                    'rating',
                    'number_like',
                    'total_sale',
                    'convert_search',
                    'wholesale',
                    'type_view',
                    'distance_price' => new Expression("abs(products.price_sale - {$price_sale})"),
                    't_distance_price' => new Expression("abs(products_type.price_sale - {$price_sale})"),
                    'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->where(" ((products.price_sale BETWEEN {$min} AND {$max}) OR (products_type.price_sale BETWEEN {$min} AND {$max})) AND products.products_id != {$products_id} AND products.categories_id={$catId} AND products.is_published=1 AND products.is_delete=0 AND products.website_id={$_SESSION['website_id']}");
                $select->order(array(
                    'distance_price' => 'ASC',
                ));
				$select->group('products.products_id');
                $select->offset($offset);
                $select->limit($limit);
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function getProductByManus($ids){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($ids)){
            $stri_key = $this->createKeyCacheFromArray($ids);
        }else{
            $stri_key = $ids;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductByManus('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array(
                'manufacturers_id' => $ids,
                'is_published' => 1,
                'is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
            ));
            $select->order('products.ordering ASC');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
	
	public function getProductsByManu($id, $params){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key .= '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductsByManu('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            try{
        		if (isset($params['page_size'])) {
                    $page_size = $params['page_size'];
                } else {
                    $page_size = 20;
                }

                if (isset($params['page'])) {
                    $page = $params['page'];
                } else {
                    $page = 20;
                }

                if ($page <= 1) {
                    $page = 0;
                } else {
                    $page = ($page - 1) * $page_size;
                }

                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->columns(array(
                    'products_id',
                    'products_code',
                    'categories_id',
                    'url_crawl',
                    'manufacturers_id',
                    'users_id',
                    'users_fullname',
                    'products_title',
                    'products_alias',
                    'products_description',
                    'products_longdescription',
                    'promotion',
                    'promotion1',
                    'promotion2',
                    'promotion3',
                    'promotion_description',
                    'promotion1_description',
                    'promotion2_description',
                    'promotion3_description',
                    'promotion_ordering',
                    'promotion1_ordering',
                    'promotion2_ordering',
                    'promotion3_ordering',
                    'is_published',
                    'is_delete',
                    'is_new',
                    'is_hot',
                    'is_available',
                    'date_create',
                    'date_update',
                    'price',
                    'price_sale',
                    'ordering',
                    'quantity',
                    'thumb_image',
                    'number_views',
                    'vat',
                    'rating',
                    'number_like',
                    'total_sale',
                    'wholesale',
                    'type_view',
                    'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');

                $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
        		$select->where(array('products.manufacturers_id' => $id));
                if (isset($params['keyword'])) {
                    $keyword = $this->toAlias($params['keyword']);
                    $select->where->like('products.products_alias', '%' . $keyword . '%');
                    $timeout = 60;
                    if (!isset($_SESSION[$keyword])) {
                        $_SESSION[$keyword] = time();
                        try {
                            $this->insertKeyword($params['keyword']);
                        } catch (\Exception $ex) {
                        }
                    } elseif (time() - $_SESSION[$keyword] > $timeout) {
                        unset($_SESSION[$keyword]);
                    }
                }

                if (isset($params['manus'])) {
                    $select->where(array(
                        'products.manufacturers_id' => $params['manus']
                    ));
                }
                if (isset($params['rating'])) {
                    $select->where(array(
                        'products.rating' => $params['rating']
                    ));
                }
                if (isset($params['feature'])) {
                    /*
                    $select->join('products_feature', 'products_feature.products_id=products.products_id', array());
                    $features = $params['feature'];
                    $select->where(array(
                        'products_feature.feature_id' => $features
                    ));*/
                    $features = $params['feature'];
                    foreach ($features as $key => $value) {
                        $select->join(array("f{$key}" => "products_feature"), "f{$key}.products_id=products.products_id", array());
                        $select->where(array(
                            "f{$key}.feature_id" => $value
                        ));
                    }
                }
                if (isset($params['price'])) {
                    $select->where('products.price_sale BETWEEN ' . $params['price']['min'] . ' AND ' . $params['price']['max']);
                }

                if (isset($params['filter']) && $params['filter'] != '') {
                    switch ($params['filter']) {
                        case 'price_asc' :
                            $select->order('products.price_sale ASC');
                            break;

                        case 'price_desc' :
                            $select->order('products.price_sale DESC');
                            break;

                        case 'new' :
                            $select->order('products.date_create DESC');
                            break;

                        case 'old' :
                            $select->order('products.date_create ASC');
                            break;

                        case 'az' :
                            $select->order('products.products_title ASC');
                            break;

                        case 'za' :
                            $select->order('products.products_title DESC');
                            break;
                    }
                } else {
                    $select->order('products.ordering ASC');
                }

                if (isset($params['fillmore']) && $params['fillmore'] != '') {
                    switch ($params['fillmore']) {
                        case 'most':
                            $select->order('products.total_sale DESC');
                            break;
                        case 'deal':
                            $select->where(array(
                                '(products.promotion_description IS NOT NULL OR
                                 products.promotion1_description IS NOT NULL OR
                                 products.promotion2_description IS NOT NULL OR
                                 products.promotion3_description IS NOT NULL)'
                            ));
                    }
                }

                //$params[''];

                //$select->order('parent_id ASC');
                $select->group('products.products_id');
                $select->offset($page);
                $select->limit($page_size);
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        		$results = $results->toArray();
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
	}

    public function getProductsByManu1($id, $intPage, $intPageSize, $where = 0){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getProductsByManu1('.$stri_key.';'.$intPage.';'.$intPageSize.';'.(is_array($where)? implode('-',$where) : $where).')');
        $results = $cache->getItem($key);
        if(!$results) {
            if ($intPage <= 1) {
                $intPage = 0;
            } else {
                $intPage = ($intPage - 1) * $intPageSize;
            }
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array(
                'manufacturers_id' => $id,
                'is_published' => 1,
                'is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
            ));
            if($where && is_array($where)){
                $select->where($where);
            }
            $select->order('products.ordering ASC');
            $select->limit($intPageSize);
            $select->offset($intPage);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
				
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
    public function countProductsByManu1($id, $where = 0){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        if(is_array($where)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($where);
        }else{
            $stri_key .= '-'.$where;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:countProductsByManu1('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression('count(products.products_id)')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array(
                'manufacturers_id' => $id,
                'is_published' => 1,
                'is_delete' => 0,
                'website_id'=>$_SESSION['website_id']
            ));
            if($where && is_array($where)){
                $select->where($where);
            }
            $select->order('products.ordering ASC');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = (array)$results->current();
                $results = $results['total'];
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = 0;
            }
        }
        return $results;
    }
	
	public function countProductsByManu($id, $params){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        if(is_array($params)){
            $stri_key .= '-'.$this->createKeyCacheFromArray($params);
        }else{
            $stri_key .= '-'.$params;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:countProductsByManu('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
    		try{	
    			$adapter = $this->tableGateway->getAdapter();
    			$sql = new Sql($adapter);
    			$select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT products.products_id)")));
    			$select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
    			$select->join('categories', 'categories.categories_id=products.categories_id', array());
    			$select->where(array(
    				'products.is_published' => 1,
    				'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
    			));
    			$select->where(array('products.manufacturers_id' => $id));
    			if (isset($params['keyword'])) {
    				$keyword = $this->toAlias($params['keyword']);
    				$select->where->like('products_alias', '%' . $keyword . '%');
    			}
    			if (isset($params['manus'])) {
    				$select->where(array(
    					'products.manufacturers_id' => $params['manus']
    				));
    			}
    			if (isset($params['feature'])) {
    				$features = $params['feature'];
    				foreach ($features as $key => $value) {
    					$select->join(array("f{$key}" => "products_feature"), "f{$key}.products_id=products.products_id", array());
    					$select->where(array(
    						"f{$key}.feature_id" => $value
    					));
    				}
    			}
    			if (isset($params['rating'])) {
    				$select->where(array(
    					'products.rating' => $params['rating']
    				));
    			}
    			if (isset($params['price'])) {
    				$select->where('products.price_sale BETWEEN ' . $params['price']['min'] . ' AND ' . $params['price']['max']);
    			}

    			if (isset($params['fillmore']) && $params['fillmore'] != '') {
    				switch ($params['fillmore']) {
    					case 'deal':
    						$select->where(array(
    							'(promotion_description IS NOT NULL OR
    							 promotion1_description IS NOT NULL OR
    							 promotion2_description IS NOT NULL OR
    							 promotion3_description IS NOT NULL)'
    						));
    				}
    			}
    			//$select->join('comments', 'comments.comments_product=products.products_id', array('total_review' => new Expression("count(DISTINCT comments.comments_id)")), 'left');
    	//        $select->group('products.products_id');
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			//die($selectString);
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    			$result = $results->toArray();
                $results = 0;
    			if (count($result) > 0) {
    				$results = $result[0]['total'];
    			}
    			$cache->setItem($key, $results);
		    }catch(\Exception $ex){
                $results = 0;
            }
        }
        return $results;
	}
	
	public function getArticle($id){
        $cache = $this->getServiceLocator()->get('cache');
        $stri_key = '';
        if(is_array($id)){
            $stri_key = $this->createKeyCacheFromArray($id);
        }else{
            $stri_key = $id;
        }
        $key = md5($this->getNamspaceCached().':ProductsTable:getArticle('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results) {
    		$adapter = $this->tableGateway->getAdapter();
    		$sql = new Sql($adapter);
    		$select = $sql->select();
    		$select->columns(array('articles_id','users_id','website_id','users_fullname',
                                    'categories_articles_id','is_new','is_hot','is_published','is_delete',
                                    'date_create','date_update','ordering','number_views','thumb_images','is_faq','is_static'));
            $select->from('articles');
            $select->join('articles_languages', 'articles_languages.articles_id=articles.articles_id',array('articles_title','articles_alias','articles_sub_content','articles_content','keyword_seo','description_seo'));
    		$select->where(array(
    			'articles.articles_id' => $id,
                'articles.website_id'=>$_SESSION['website_id'],
                'articles_languages.languages_id'=>$_SESSION['language']['languages_id']
    		));
    		try{
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    			$results = (array)$results->current();
    			$cache->setItem($key, $results);
    		}catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
	}

    //cho tao website
    public function copyProduct($product){
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = "INSERT INTO products(
                                    website_id,
                                    products_code,
                                    categories_id,
                                    manufacturers_id,
                                    users_id,
                                    users_fullname,
                                    products_title,
                                    products_alias,
                                    products_description,
                                    products_longdescription,
                                    seo_keywords,
                                    seo_description,
                                    seo_title,
                                    promotion,
                                    promotion1,
                                    promotion2,
                                    promotion3,
                                    promotion_description,
                                    promotion1_description,
                                    promotion2_description,
                                    promotion3_description,
                                    promotion_ordering,
                                    promotion1_ordering,
                                    promotion2_ordering,
                                    promotion3_ordering,
                                    is_published,
                                    is_delete,
                                    is_new,
                                    is_hot,
                                    is_available,
                                    is_goingon,
                                    date_create,
                                    date_update,
                                    price,
                                    price_sale,
                                    ordering,
                                    quantity,
                                    thumb_image,
                                    number_views,
                                    vat,
                                    rating,
                                    number_like,
                                    total_sale,
                                    convert_search,
                                    youtube_video,
                                    tags,
                                    type_view
                                    )
                                SELECT
                                    {$product->website_id},
                                    '".time()."',
                                    {$product->categories_id},
                                    {$product->manufacturers_id},
                                    {$product->users_id},
                                    '{$product->users_fullname}',
                                    products_title,
                                    products_alias,
                                    products_description,
                                    products_longdescription,
                                    seo_keywords,
                                    seo_description,
                                    seo_title,
                                    promotion,
                                    promotion1,
                                    promotion2,
                                    promotion3,
                                    promotion_description,
                                    promotion1_description,
                                    promotion2_description,
                                    promotion3_description,
                                    promotion_ordering,
                                    promotion1_ordering,
                                    promotion2_ordering,
                                    promotion3_ordering,
                                    is_published,
                                    is_delete,
                                    is_new,
                                    is_hot,
                                    is_available,
                                    is_goingon,
                                    date_create,
                                    date_update,
                                    price,
                                    price_sale,
                                    ordering,
                                    quantity,
                                    thumb_image,
                                    number_views,
                                    vat,
                                    rating,
                                    number_like,
                                    total_sale,
                                    convert_search,
                                    youtube_video,
                                    tags,
                                    type_view
                                FROM products
                                WHERE products_id={$product->products_id}";
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            $pid = $adapter->getDriver()->getLastGeneratedValue();
            $this->insertCopyFeature($pid, $product->products_id, $product->website_id);
            $this->insertCopyExt($pid, $product->products_id);
            $folderProducts = PATH_BASE_ROOT . DS . 'custom' . DS . 'domain_1' . DS . 'products';
            if (!is_dir($folderProducts)) {
                mkdir($folderProducts, 0777);
            }
            $folder1 = PATH_BASE_ROOT . DS . 'custom' . DS . 'domain_1' . DS . 'products' . DS . 'fullsize';
            if (!is_dir($folder1)) {
                mkdir($folder1, 0777);
            }
            $folder2 = $folder1.DS."product{$product->products_id}";
            $folder1 .= DS . "product{$pid}";

            if (!is_dir($folder1)) {
                mkdir($folder1, 0777);
            }
            $list_image = array();
            $file_images = glob($folder2.DS.'*.*');
            foreach($file_images as $img){
                $file = explode('.', basename($img));
                $ext = end($file);
                $newFileName = $product->products_alias . '-' . time() . '-' . $this->randText(6) . '.' . $ext;
                $listImages[basename($img)] = $newFileName;
                $file = $folder2.DS.basename($img);
                if (is_file($file)) {
                    $newfile1 = $folder1 . "/" . $newFileName;
                    copy($file, $newfile1);
                    $list_image[] = "/custom/domain_1/products/fullsize/product{$pid}/{$newFileName}";
                }
            }

            if (count($list_image) > 0) {
                $this->insertImages($pid, $list_image);
                unset($list_image);
            }
            
            $html = mb_convert_encoding($product->products_description, 'HTML-ENTITIES', "UTF-8");
            // Create a new DOM document
            $dom = new \DOMDocument ();
            $dom->encoding = 'utf-8';
            @$dom->loadHTML($html);
            $imgs = $dom->getElementsByTagName('img');
            if (count($imgs) > 0) {
                foreach ($imgs as $img) {
                    $src = $img->getAttribute('src');
                    $fileNameTemp = explode('/', $src);
                    $fileNameTemp = end($fileNameTemp);
                    if (isset($listImages[$fileNameTemp])) {
                        $src = str_replace("custom/domain_1/products/fullsize/product{$product->products_id}/" . $fileNameTemp, "custom/domain_1/products/fullsize/product{$pid}/{$listImages[$fileNameTemp]}", $src);
                        $img->setAttribute('src', $src);
                    }

                }
            }

            $html = $dom->saveXML($dom->getElementsByTagName('body')->item(0));
            $data_product ['products_description'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html);
            $data_product ['products_description'] = htmlentities($data_product ['products_description'], ENT_QUOTES, 'UTF-8');


            $html1 = mb_convert_encoding($product->products_longdescription, 'HTML-ENTITIES', "UTF-8");
            @$dom->loadHTML($html1);
            $imgs = $dom->getElementsByTagName('img');
            if (count($imgs) > 0) {
                foreach ($imgs as $img) {
                    $src = $img->getAttribute('src');
                    $fileNameTemp = explode('/', $src);
                    $fileNameTemp = end($fileNameTemp);
                    if (isset($listImages[$fileNameTemp])) {
                        $src = str_replace("custom/domain_1/products/fullsize/product{$product->products_id}/" . $fileNameTemp, "custom/domain_1/products/fullsize/product{$pid}/{$listImages[$fileNameTemp]}", $src);
                        $img->setAttribute('src', $src);
                    }

                }
            }

            $html1 = $dom->saveXML($dom->getElementsByTagName('body')->item(0));
            $data_product ['products_longdescription'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html1);
            $data_product ['products_longdescription'] = htmlentities($data_product ['products_longdescription'], ENT_QUOTES, 'UTF-8');

            $thumb_image = $product->thumb_image;
            $thumb_image = explode('/', $thumb_image);
            $thumb_image = end($thumb_image);
            if (isset($listImages[$thumb_image])) {
                $src = "/custom/domain_1/products/fullsize/product{$pid}/{$listImages[$thumb_image]}";
                $data_product['thumb_image'] = $src;
            }
            $this->updateContent($pid, $data_product);
            return $pid;
        }catch(\Exception $ex){
        }
    }

    public function insertImages($id, $data)
    {
        $sql = "INSERT INTO products_images(`products_id`,`images`,`is_published`,`ordering`) VALUES ";
        $val = array();
        foreach ($data as $key => $img) {
            $val[] = "({$id}, '{$img}', 1, {$key})";
        }
        $val = implode(',', $val);
        $sql .= $val;
        try {
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function randText($characters)
    {
        $possible = '1234567890abcdefghjkmnpqrstvwxyzABCDEFGHJKMNPQRSTVWXYZ';
        $code = '';
        $i = 0;
        while ($i < $characters) {
            $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $i++;
        }
        return $code;
    }

    public function updateContent($id, $data)
    {
        $set = array();
        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $pre = "";
            } else {
                $pre = "'";
            }
            $set[] = "{$key}={$pre}{$value}{$pre}";
        }
        $set = implode(',', $set);
        $sql = "UPDATE products SET {$set} WHERE products_id={$id}";
        
        try {
            $adapter = $this->tableGateway->getAdapter();
            return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function insertCopyFeature($new_pid, $old_pid,$website_id){
        $features = $this->getFeatureOfWebsite($old_pid,$website_id);
        if ($features->count() > 0) {
            $sql = 'INSERT INTO products_feature(`products_id`,`feature_id`, `value`) VALUES ';
            $val = array();
            foreach ($features as $feat) {
                $val[] = "({$new_pid}, {$feat->feature_id},'{$feat->value}')";
            }
            $val = implode(',', $val);
            $sql .= $val;
            try {
            if(isset($sql) && $val){
                $adapter = $this->tableGateway->getAdapter();
                //die($sql);
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        }
        
    }

    public function insertCopyExt($new_pid, $old_pid){
        $exts = $this->getExtensionByProductId($old_pid);
        if (count($exts) > 0) {
            $sql = 'INSERT INTO products_extensions(`ext_id`,`products_id`,`ext_name`, `price`, `ext_description`) VALUES ';
            $val = array();
            foreach ($exts as $ext) {
                $val[] = "('{$ext['ext_id']}',{$new_pid}, '{$ext['ext_name']}', {$ext['price']}, '{$ext['ext_description']}')";
            }
            $val = implode(',', $val);
            $sql .= $val;
        }
        try {
            $adapter = $this->tableGateway->getAdapter();
            if (count($exts) > 0) {
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getFeatureOfWebsite($id, $website_id)
    {
        $sql = "SELECT feature.*, products_feature.value
                FROM feature
                INNER JOIN products_feature ON products_feature.feature_id = feature.feature_id
                WHERE products_feature.products_id={$id} AND website_id = {$website_id}";
        try {
            $adapter = $this->tableGateway->getAdapter();
            return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getExtensionByProductId($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_extensions')
            ->where(array(
                'products_id' => $id,
                'ext_require' => 0,
            ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getProductOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products');
        //$select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','price', 'price_sale', 'quantity', 'is_available'));
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0,
            'website_id' => $website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function getProductInCategoriesOfWebsite($website_id, $categories_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products');
        //$select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','price', 'price_sale', 'quantity', 'is_available'));
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0,
            'website_id' => $website_id,
            'categories_id' => $categories_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function getProductsTypesOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_type');
        $select->join('products', 'products_type.products_id = products.products_id', array());
        $select->where(array(
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.website_id' => $website_id
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertProductsTypes($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('products_type');
        $insert->columns(array('products_id','type_name','price','price_sale','quantity','is_available','thumb_image','is_default'));
        $insert->values(array(
            'products_id' => $data['products_id'],
            'type_name' => $data['type_name'],
            'price' => $data['price'],
            'price_sale' => $data['price_sale'],
            'quantity' => $data['quantity'],
            'is_available' => $data['is_available'],
            'thumb_image' => $data['thumb_image'],
            'is_default' => $data['is_default']
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
          //  throw new \Exception($ex->getMessage());
		  die();
        }
    }

    public function getProductsFeaturesOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_feature');
        $select->join('products', 'products_feature.products_id = products.products_id', array());
        $select->join('feature', 'products_feature.feature_id = feature.feature_id', array());
        $select->where(array(
            'products_feature.is_published' => 1,
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.website_id' => $website_id,
            'feature.website_id'=>$website_id,
            'feature.is_delete'=>0,
            'feature.is_published'=>1
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertProductsFeatures($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('products_feature');
        $insert->columns(array('products_id','feature_id','value','is_value','is_published'));
        $insert->values(array(
            'products_id' => $data['products_id'],
            'feature_id' => $data['feature_id'],
            'value' => $data['value'],
            'is_value' => $data['is_value'],
            'is_published' => $data['is_published']
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
         //   throw new \Exception($ex->getMessage());
			die();
        }
    }

    public function deleteProductsFeatures($products_id, $feature_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $delete  = $sql->delete('products_feature');
        $delete->where(array(
            'products_id' => $products_id,
            'feature_id' => $feature_id
        ));
        try{
            $deleteString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            //throw new \Exception($ex->getMessage());
			die();
        }
    }

    public function getProductsExtensionsOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_extensions');
        $select->join('products', 'products_extensions.products_id = products.products_id', array());
        $select->where(array(
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.website_id' => $website_id
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertProductsExtensions($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('products_extensions');
        $insert->columns(array('ext_id','products_id','ext_name','ext_require','price','ext_description','quantity','is_available','is_always','type','refer_product_id'));
        $insert->values(array(
            'ext_id' => $data['ext_id'],
            'products_id' => $data['products_id'],
            'ext_name' => $data['ext_name'],
            'ext_require' => $data['ext_require'],
            'price' => $data['price'],
            'ext_description' => $data['ext_description'],
            'quantity' => $data['quantity'],
            'is_available' => $data['is_available'],
            'is_always' => $data['is_always'],
            'type' => $data['type'],
            'refer_product_id' => $data['refer_product_id'],
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
           die();
//		   throw new \Exception($ex->getMessage());
        }
    }

    public function deleteProductsExtensions($products_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $delete  = $sql->delete('products_extensions');
        $delete->where(array(
            'products_id' => $products_id
        ));
        try{
            $deleteString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            die();
//		   throw new \Exception($ex->getMessage());
        }
    }

    public function getProductsImagesOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_images');
        $select->join('products', 'products_images.products_id = products.products_id', array());
        $select->where(array(
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.website_id' => $website_id
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertProductsImages($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('products_images');
        $insert->columns(array('products_id','images','is_published','date_create','ordering','check_convert'));
        $insert->values(array(
            'products_id' => $data['products_id'],
            'images' => $data['images'],
            'is_published' => $data['is_published'],
            'date_create' => $data['date_create'],
            'ordering' => $data['ordering'],
            'check_convert' => $data['check_convert']
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
             die();
//		   throw new \Exception($ex->getMessage());
        }
    }

    public function deleteProductsImages($products_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $delete  = $sql->delete('products_images');
        $delete->where(array(
            'products_id' => $products_id
        ));
        try{
            $deleteString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
             die();
//		   throw new \Exception($ex->getMessage());
        }
    }

    public function insertProduct($data)
    {
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }


}