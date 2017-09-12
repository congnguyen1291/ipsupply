<?php

namespace Application\Model;

use Application\Model\Manufacturers;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class ManufacturersTable extends AppTable {

    public function removeAllManufacturersOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function fetchAll()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ManufacturersTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
            try {
                $resultSet = $this->tableGateway->select(array(
                    'is_published' => 1,
                    'is_delete' => 0,
                    'website_id'=>$_SESSION['website_id']
                ));
                $results = $resultSet->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllManus($intPage, $intPageSize){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ManufacturersTable:getAllManus('.$intPage.';'.$intPageSize.')');
        $results = $cache->getItem($key);
        if(!$results){
            if ($intPage <= 1) {
                $intPage = 0;
            } else {
                $intPage = ($intPage - 1) * $intPageSize;
            }
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('manufacturers');
            $select->join('products', 'products.manufacturers_id=manufacturers.manufacturers_id', array('numberProduct' => new Expression('COUNT(products.products_id)')),'left');
            $select->where(array(
                'manufacturers.is_published' => 1,
                'manufacturers.is_delete' => 0,
                //'products.is_published' => 1,
                //'products.is_delete' => 0,
                'manufacturers.website_id'=>$this->getWebsiteId()
            ));
            $select->group('manufacturers.manufacturers_id');
            $select->order('numberProduct DESC');
            $select->limit($intPageSize);
            $select->offset($intPage);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllManuBK($intPage, $intPageSize){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ManufacturersTable:getAllManuBK('.$intPage.';'.$intPageSize.')');
        $results = $cache->getItem($key);
        if(!$results){
            if ($intPage <= 1) {
                $intPage = 0;
            } else {
                $intPage = ($intPage - 1) * $intPageSize;
            }
            $adapter = $this->tableGateway->getAdapter();
            $selectString = "SELECT manufacturers.*,count(products.products_id) as numberProduct
                    FROM manufacturers
                    LEFT JOIN products ON products.manufacturers_id=manufacturers.manufacturers_id AND products.is_published=1 AND products.is_delete=0
                    WHERE manufacturers.is_published=1 AND manufacturers.is_delete=0 AND manufacturers.website_id = {$this->getWebsiteId()}
                    GROUP BY manufacturers.manufacturers_id
                    ORDER BY numberProduct DESC
                    LIMIT {$intPageSize}
                    OFFSET {$intPage}";
            try{
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function countAllManu(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ManufacturersTable:countAllManu');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('total' => new Expression('COUNT( DISTINCT manufacturers.manufacturers_id)')));
            $select->from('manufacturers');
    		$select->join('products', 'products.manufacturers_id=manufacturers.manufacturers_id',array());
            $select->where(array(
                'manufacturers.is_published' => 1,
                'manufacturers.is_delete' => 0,
    			'products.is_published' => 1,
    			'products.is_delete' => 0,
                'manufacturers.website_id'=>$this->getWebsiteId()
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = (array)$results->current();
                $results = $results['total'];
                $cache->setItem($key,$results);
            }catch (\Exception $ex){
                $results = 0;
            }
        }
        return $results;
    }

    public function getRow($manuid){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ManufacturersTable:getRow('.$manuid.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('manufacturers');
            $select->where(array(
                'manufacturers_id' => $manuid,
                'is_delete' => 0,
                'is_published' => 1,
                'website_id'=>$this->getWebsiteId()
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = (array)$results->current();
                $cache->setItem($key,$results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getRows($cat_id=array()) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ManufacturersTable:getRows('.(is_array($cat_id)? implode('-',$cat_id) : $cat_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('manufacturers');
                $select->join('products', new Expression('products.manufacturers_id=manufacturers.manufacturers_id AND products.is_published = 1 AND products.is_delete = 0'), array('total' => new Expression("count(DISTINCT products.products_id)")), 'left');
                if(!empty($cat_id)){
                    $select->join('categories', 'products.categories_id=categories.categories_id', array());
                    $select->where(array(
                        'manufacturers.is_published' => 1,
                        'manufacturers.is_delete' => 0,
                        'categories.categories_id' => $cat_id,
                        'manufacturers.website_id'=>$this->getWebsiteId()
                    )); 
                }else{
                    $select->where(array(
                        'manufacturers.is_published' => 1,
                        'manufacturers.is_delete' => 0,
                        'manufacturers.website_id'=>$this->getWebsiteId()
                    ));
                }
                
                $select->order('manufacturers_name ASC');
                $select->group('manufacturers.manufacturers_id');

                $selectString = $sql->getSqlStringForSqlObject($select);//echo $selectString;die();
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = $resultSet->toArray();
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

	public function getProductsByManus($manuids, $params){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ManufacturersTable:getProductsByManus('.(is_array($manuids)? implode('-',$manuids) : $manuids).';'.(is_array($params)? implode('-',$params) : $params).')');
        $results = $cache->getItem($key);
        if(!$results){
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
                    'manufacturers_id',
                    'url_crawl',
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
                    'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');

                $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
        		if(isset($params['catid'])){
        			$select->where(array(
        				'products.categories_id' => $params['catid'],
        			));
        		}
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$this->getWebsiteId()
                ));
                if (isset($manuids) && count($manuids)) {
                    $select->where(array('products.manufacturers_id' => $manuids));
                }
        		/*
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
        		*/
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
                    $select->order('date_create DESC');
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
                //$results->buffer();
                //$results->next();
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
	}
	
	public function countProductsByManus($manuids, $params){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ManufacturersTable:countProductsByManus('.(is_array($manuids)? implode('-',$manuids) : $manuids).';'.(is_array($params)? implode('-',$params) : $params).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->quantifier('DISTINCT');
                $select->from('products');
                $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
                //$select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
                $select->columns(array(
        			'products_id',
                    //'total' => new Expression('count(products.products_id)')
                ));
                $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
        		if(isset($params['catid'])){
        			$select->where(array(
        				'products.categories_id' => $params['catid'],
        			));
        		}
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$this->getWebsiteId()
                ));
                if (isset($manuids) && count($manuids)) {
                    $select->where(array('products.manufacturers_id' => $manuids));
                }
        		/*
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
        		*/
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
                    $select->order('date_create DESC');
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
                $selectString = $sql->getSqlStringForSqlObject($select);
        		//die($selectString);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        		$results = $results->toArray();
        		$results = count($results);
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = 0;
            }
        }
        return $results;
	}
	
	public function loadAllFeatureByManu($manuid = array(), $idcat = NULL){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ManufacturersTable:loadAllFeatureByManu('.(is_array($manuid)? implode('-',$manuid) : $manuid).';'.(is_array($idcat)? implode('-',$idcat) : $idcat).')');
        $results = $cache->getItem($key);
        if(!$results){
    		$adapter = $this->tableGateway->getAdapter();
    		$sql = new Sql($adapter);
    		$select = $sql->select()->columns(array());
    		$select->quantifier('DISTINCT');
    		$select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
    		$select->join('manufacturers','products.manufacturers_id=manufacturers.manufacturers_id', array());
    		$select->join('products_feature','products_feature.products_id=products.products_id', array());
    		$select->join('feature','products_feature.feature_id=feature.feature_id');
    		if(count($manuid)){
    			$select->where(array(
    				'manufacturers.manufacturers_id' => $manuid,
    			));
    		}
    		$select->where(array(
    			'manufacturers.is_published' => 1,
    			'manufacturers.is_delete' => 0,
    			'feature.is_published' => 1,
    			'feature.is_delete' => 0,
                'products.is_published' => 1,
                'products.is_delete' => 0,
                'products.website_id'=>$this->getWebsiteId()
    		));

            if($idcat){
                $select->where(array(
                    'products.categories_id' => $idcat,
                ));
            }
    		try{
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    			$results = $results->toArray();
    			$results = $this->getDataFeature($results);
                $cache->setItem($key, $results);
    		}catch(\Exception $ex){
    			$results = array();
    		}
        }
        return $results;
		
	}
	
	private function getDataFeature($rows){
		$listFeature = array();
		if(COUNT($rows)>0){
			foreach ($rows as $item ) {
				$idParentFeature = $item['parent_id'];
				if (isset($listFeature[$idParentFeature]) && !empty($listFeature[$idParentFeature]) ) {
					$listFeature[$idParentFeature][] = $item;
				} else {
					$listFeature[$idParentFeature] = array($item);
				}
			}
		}
        return $listFeature;
	}


    public function getManufacturersOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('manufacturers');
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

    public function insertManufacturers($data){
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }

}