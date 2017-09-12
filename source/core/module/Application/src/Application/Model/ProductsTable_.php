<?php

namespace Application\Model;

//use Application\Model\Products;
//use Application\Model\CategoriesTable;
    /*

    use Zend\Db\TableGateway\AbstractTableGateway;
    use Zend\Db\TableGateway\TableGateway;
    use Zend\Db\Sql\Select;
    use Zend\Db\Sql\Sql;
    use Zend\Db\ResultSet\ResultSet;
    */
//use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;

//use Zend\Db\TableGateway\AbstractTableGateway;
//use Zend\Db\Sql\Select;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

//use Zend\Authentication\Adapter\DbTable;

class ProductsTable extends AbstractTableGateway implements ServiceLocatorAwareInterface
{
    protected $table = null;
    protected $primary = null;
    protected $entity = null;
    protected $metadata = null;
    protected $_html = '';
    public $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    private  $sm = NULL;
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->sm;
    }

    public function fetchAll()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-fetchAll-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results){
            try {
                $results = $this->tableGateway->select(array('website_id'=>$_SESSION['website_id']));
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
        $key = md5('Application-ProductsTable-getExtRequire-'.(is_array($ext_require_id)? implode('-',$ext_require_id) : $ext_require_id).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results){
    		$adapter = $this->tableGateway->getAdapter();
    		$sql = new Sql($adapter);
    		$select = $sql->select();
    		$select->from('products_extensions');
            $select->join ( 'products', 'products.products_id=products_extensions.products_id', array() );
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
	
	public function getArticlesProduct($products_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getArticlesProduct-'.(is_array($products_id)? implode('-',$products_id) : $products_id).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array());
            $select->from('products_articles');
            $select->join('articles', 'products_articles.articles_id=articles.articles_id', array('articles_id', 'articles_title', 'articles_alias', 'articles_sub_content'));
            $select->where(array(
                'articles.is_published' => 1,
                'articles.is_delete' => 0,
                'products_articles.products_id' => $products_id,
                'articles.website_id'=>$_SESSION['website_id']
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
        $key = md5('Application-ProductsTable-getAllFqa-'.(is_array($id)? implode('-',$id) : $id).'-'.$intPage.'-'.$intPageSize.'-Website-'.$_SESSION['domain']);
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
        $key = md5('Application-ProductsTable-getAllFqaChild-'.(is_array($id)? implode('-',$id) : $id).'-'.$intPage.'-'.$intPageSize.'-Website-'.$_SESSION['domain']);
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
        $key = md5('Application-ProductsTable-getAllAnswerForFqa-'.(is_array($fqa_ids)? implode('-',$fqa_ids) : $fqa_ids).'-Website-'.$_SESSION['domain']);
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

    public function getNewProduct()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getNewProduct-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'is_new' => 1,
                'website_id'=>$_SESSION['website_id']
            ));
            $select->order('date_create DESC');
            $select->limit(6);
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

    public function getHotProduct()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getHotProduct-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products');
            $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'is_hot' => 1,
                'products.website_id'=>$_SESSION['website_id']
            ));
            $select->group('products.products_id');
            //$select->order('parent_id ASC');
            $select->limit(5);
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

    public function getProductBanchay(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getProductBanchay-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products');
            $select->join('products_invoice',"products_invoice.products_id=products.products_id", array('total_invoice' => new Expression('count(invoice_id)')));
            $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
            $select->where(array(
                'products.is_published' => 1,
                'products.is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
            ));
            $select->order(array(
                'total_invoice' => 'DESC',
            ));
            $select->group(array('products.products_id'));
            $select->limit(6);
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

    public function getProductCate1($cate_id, $params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getProductCate1-'.(is_array($cate_id)? implode('-',$cate_id) : $cate_id).'-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
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
                $select->from('products');
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

                //$select->order('parent_id ASC');
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
        $key = md5('Application-ProductsTable-getProductCateMore-'.(is_array($cate_id)? implode('-',$cate_id) : $cate_id).'-'.(is_array($id)? implode('-',$id) : $id).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('products');
                $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.categories_id' => $cate_id,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                $select->where->notIn('products_id', array($id));
                $select->group('products.products_id');
                $select->offset(0);
                $select->limit(10);
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
        $key = md5('Application-ProductsTable-getProductCate-'.(is_array($cate_id)? implode('-',$cate_id) : $cate_id).'-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
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
    			$select->from('products');
    			$select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
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
    			));
    			$select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
    			$select->where(array(
    				'products.is_published' => 1,
    				'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
    			));
    			if (isset($cate_id) && $cate_id) {
    				$select->where(array('products.categories_id' => $cate_id));
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
    	
    			//$select->order('parent_id ASC');
    			$select->group('products.products_id');
    			$select->offset($page);
    			$select->limit($page_size);
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    			//$results->buffer();
    			//$results->next();
    			$cache->setItem($key,$results);
    		}catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getProductSearch($cate_id, $params){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getProductSearch-'.(is_array($cate_id)? implode('-',$cate_id) : $cate_id).'-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
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
                $select->from('products');/*
                $select->columns(array(
                    'products_id',
                    'products_code',
                    'categories_id',
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
                ));*/
                $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                if (isset($cate_id) && $cate_id) {
                    $select->where(array('products.categories_id' => $cate_id));
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
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
	
	public function getProductCateView($cate_id, $params)
    {
		$cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getProductCateView-'.(is_array($cate_id)? implode('-',$cate_id) : $cate_id).'-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
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
                $select->from('products');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
                $select->columns(array(
                    'products_id',
                    'products_code',
                    'categories_id',
                    'manufacturers_id',
                    'users_id',
        			'url_crawl',
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
                ));
                $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
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
                    //$select->where(array('products.categories_id' => $cate_id));
        			if(is_array($cate_id)){
        				if(count($cate_id)){
        					$cats = implode(',', $cate_id);
        					$select->where('products.products_id in (select products_id from products where products.is_published=1 and products.is_delete=0 and categories_id IN ('.$cats.'))');
        				}
        			/*
        				select products.products_id,position_view
        from products
        where products_id in (select max(products_id) from products where products.is_published=1 and products.is_delete=0 and position_view!=0 and categories_id=222 group by position_view)
        order by position_view*/
        			}else{
        				$select->where('products_id in (select max(products_id) from products where products.is_published=1 and products.is_delete=0 and position_view!=0 and categories_id = '.$cate_id.' group by position_view)');
        			}
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

                //$select->order('parent_id ASC');
                $select->group('products.products_id');
                $select->offset($page);
                $select->limit($page_size);
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                //$results->buffer();
                //$results->next();
        		//$cache->setItem($key,$results);
        		//}
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getProductAll($params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getProductAll-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
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
                $select->from('products');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
                $select->columns(array(
                    'products_id',
                    'products_code',
                    'categories_id',
                    'manufacturers_id',
                    'users_id',
                    'url_crawl',
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
                ));
                $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id'],
                    //'products.is_viewed' => 1,
                ));

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

                $select->group('products.products_id');
                $select->offset($page);
                $select->limit($page_size);
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                echo $ex->getMessage();die('loi');
                $results = array();
            }
        }
        return $results;
    }
	
	public function getProductGoingOn($params)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getProductGoingOn-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
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
                $select->from('products');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
                $select->columns(array(
                    'products_id',
                    'products_code',
                    'categories_id',
                    'manufacturers_id',
                    'users_id',
                    'users_fullname',
                    'products_title',
                    'products_alias',
                    'products_description',
                    'products_longdescription',
                    'promotion',
        			'url_crawl',
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
                ));
                $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
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
        $key = md5('Application-ProductsTable-getProductByCustom-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
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
                $select->from('products');
                $select->columns(array(
                    'products_id',
                    'products_code',
                    'categories_id',
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
                ));
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

                //$select->order('parent_id ASC');
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
        $key = md5('Application-ProductsTable-getProductHotCat-'.(is_array($catids)? implode('-',$catids) : $catids).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            if(!$catids){
                $results = array();
            }else{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('products');
                $select->where(array(
                    'categories_id' => $catids,
                    'is_published' => 1,
                    'is_delete' => 0,
                    'website_id'=>$_SESSION['website_id']
                ));
                $select->order(array(
                    'rating' => 'DESC',
                    'number_views' => 'DESC',
                    'number_like' => 'DESC',
                ));
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
        $key = md5('Application-ProductsTable-getKeywords-'.$offset.'-'.$limit.'-Website-'.$_SESSION['domain']);
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
        $key = md5('Application-ProductsTable-countTotalProduct-'.(is_array($cate_id)? implode('-',$cate_id) : $cate_id).'-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT products.products_id)")));
                $select->from('products');
                $select->join('categories', 'categories.categories_id=products.categories_id', array());
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
                if (isset($cate_id) && $cate_id) {
                    $select->where(array('products.categories_id' => $cate_id));
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
        $key = md5('Application-ProductsTable-countTotalProductAll-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT products.products_id)")));
                $select->from('products');
                $select->join('categories', 'categories.categories_id=products.categories_id', array());
                $select->where(array(
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
                    'products.website_id'=>$_SESSION['website_id']
                ));
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
        $key = md5('Application-ProductsTable-countTotalProductGoingOn-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT products.products_id)")));
                $select->from('products');
                $select->join('categories', 'categories.categories_id=products.categories_id', array());
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
            throw new \Exception($ex->getMessage());
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
        $select->from('products');
        $select->join('categories', 'categories.categories_id=products.categories_id', array('categories_title'));
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
            $select->order('products_id DESC');
        }

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
        $key = md5('Application-ProductsTable-getProductCateRemarketing-'.(is_array($cate_id)? implode('-',$cate_id) : $cate_id).'-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            try{
                $sql = array();
                $adapter = $this->tableGateway->getAdapter();
                if (is_array($cate_id)) {
                    foreach ($cate_id as $id) {
                        $sql[] = "(SELECT `products`.*, `categories`.`categories_title` AS `categories_title`, count(comments_id) as total_review
                                FROM `products`
                                INNER JOIN `categories` ON `categories`.`categories_id`=`products`.`categories_id`
                                LEFT JOIN `comments` ON `comments`.`comments_product`=`products`.`products_id`
                                WHERE `products`.`is_published` = '1' AND `products`.`is_delete` = '0' AND products.website_id={$_SESSION['website_id']}
                                AND `products`.`categories_id` IN ({$id}) GROUP BY products.products_id ORDER BY `products_id` DESC LIMIT 20)";
                    }
                }
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
        $key = md5('Application-ProductsTable-getTotalProductCate-'.(is_array($cate_id)? implode('-',$cate_id) : $cate_id).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            try{
    			$adapter = $this->tableGateway->getAdapter();
    			$sql = new Sql($adapter);
    			$select = $sql->select()->columns(array('products_id'));
    			$select->from('products');
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
        $key = md5('Application-ProductsTable-getConcernProduct-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
			$adapter = $this->tableGateway->getAdapter();
			$sql = new Sql($adapter);
			$select = $sql->select();
			$select->from('products');
			$select->join('comments', 'comments.comments_product=products.products_id', array('total_review' => new Expression("count(DISTINCT comments.comments_id)")), 'left');
			$select->where(array(
				'is_published' => 1,
				'is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
			));
			$select->group('products.products_id');
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
        $key = md5('Application-ProductsTable-getRow-'.(is_array($id)? implode('-',$id) : $id).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products');
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
        $key = md5('Application-ProductsTable-getImages-'.(is_array($id)? implode('-',$id) : $id).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products_images');
            $select->join ( 'products', 'products.products_id=products_images.products_id', array());
            $select->where(array(
                'is_published' => 1,
                'products_id' => $id,
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

    public function getFeature($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getFeature-'.(is_array($id)? implode('-',$id) : $id).'-Website-'.$_SESSION['domain']);
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
        $key = md5('Application-ProductsTable-getHotDeal-'.(is_array($where)? implode('-',$where) : $where).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $where[] = "products.website_id={$_SESSION['website_id']} AND (products.promotion_description IS NOT NULL ||
                        products.promotion1_description IS NOT NULL ||
                        products.promotion2_description IS NOT NULL ||
                        products.promotion3_description IS NOT NULL)";
            $where = implode(' AND ', $where);
            $sql = "SELECT *
                    FROM products
                    WHERE {$where}
                    ORDER BY date_update DESC";
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

    protected function toAlias($txt)
    {
        if ($txt == '')
            return '';
        $marked = array("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă",

            "ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề",

            "ế", "ệ", "ể", "ễ", "ế",

            "ì", "í", "ị", "ỉ", "ĩ",

            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ",

            "ờ", "ớ", "ợ", "ở", "ỡ",

            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",

            "ỳ", "ý", "ỵ", "ỷ", "ỹ",

            "đ",

            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă",

            "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",

            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",

            "Ì", "Í", "Ị", "Ỉ", "Ĩ",

            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ",

            "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",

            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",

            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",

            "Đ",

            " ", "&", "?", "/", ".", ",", "$", ":", "(", ")", "'", ";", "+", "–", "’");

        $unmarked = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a",

            "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e",

            "e", "e", "e", "e", "e",

            "i", "i", "i", "i", "i",

            "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o",

            "o", "o", "o", "o", "o",

            "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",

            "y", "y", "y", "y", "y",

            "d",

            "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A",

            "A", "A", "A", "A", "A",

            "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",

            "I", "I", "I", "I", "I",

            "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O",

            "O", "O", "O", "O", "O",

            "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",

            "Y", "Y", "Y", "Y", "Y",

            "D",

            "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-");

        $tmp3 = (str_replace($marked, $unmarked, $txt));
        $tmp3 = rtrim($tmp3, "-");
        $tmp3 = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $tmp3);
        $tmp3 = preg_replace('/-+/', '-', $tmp3);
        $tmp3 = strtolower($tmp3);
        return $tmp3;
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
        $key = md5('Application-ProductsTable-getExtensions-'.(is_array($id)? implode('-',$id) : $id).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('products_extensions');
                $select->join ( 'products', 'products.products_id=products_extensions.products_id', array());
                $select->where(array(
                        'products_id' => $id,
                        'ext_require' => 0,
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
        $key = md5('Application-ProductsTable-getExtensionsRequire-'.(is_array($id)? implode('-',$id) : $id).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('products_extensions');
                $select->join ( 'products', 'products.products_id=products_extensions.products_id', array());
                $select->where(array(
                        'products_id' => $id,
                        'ext_require' => 1,
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
        $key = md5('Application-ProductsTable-getReview-'.(is_array($products_id)? implode('-',$products_id) : $products_id).'-Website-'.$_SESSION['domain']);
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
        $key = md5('Application-ProductsTable-getHotProductConvert-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'convert_search' => 0,
                'website_id'=>$_SESSION['website_id']
            ));
            $select->limit(20);
            $select->offset(0);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
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
        $key = md5('Application-ProductsTable-getProductAddSearch-Website-'.(is_array($where)? implode('-',$where) : $where).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array("products_id", "products_title", "seo_keywords"));
            $select->from('products');
            $select->where(array('website_id'=>$_SESSION['website_id']));
            $select->where($where);
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
            die($ex->getMessage());
            return FALSE;
        }
        return true;
    }

    public function getByManus($manuid, $limit){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getByManus-'.(is_array($manuid)? implode('-',$manuid) : $manuid).'-'.$limit.'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products');
            $select->join('comments', 'comments.comments_product=products.products_id', array('total_review' => new Expression("count(DISTINCT comments.comments_id)")), 'left');
            $select->where(array(
                'manufacturers_id' => $manuid,
                'is_published' => 1,
                'is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
            ));
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
        $key = md5('Application-ProductsTable-getRecommendProductByCatId-'.(is_array($catId)? implode('-',$catId) : $catId).'-'.$limit.'-Website-'.$_SESSION['domain']);
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
                    $select->from('products');
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

    public function getRecommendProductById($products_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getRecommendProductById-'.(is_array($products_id)? implode('-',$products_id) : $products_id).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()->columns(array());
                $select->from('products_recommend');
                $select->join('products', 'products_recommend.to_products_id=products.products_id');
                $select->join('manufacturers', 'products.manufacturers_id=manufacturers.manufacturers_id', array('manufacturers_name'));
                $select->where(array(
                    'from_products_id' => $products_id,
                    'products.is_published' => 1,
                    'products.is_delete' => 0,
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

    public function getProductNearPrice($catId, $products_id, $price_sale, $min, $max)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-ProductsTable-getProductNearPrice-'.(is_array($catId)? implode('-',$catId) : $catId).'-'.(is_array($products_id)? implode('-',$products_id) : $products_id).'-'.$price_sale.'-'.$min.'-'.$max.'-Website-'.$_SESSION['domain']);
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
                    'products_longdescription',
                    'seo_keywords',
                    'seo_description',
                    'seo_title',
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
                    'convert_search',
                    'distance_price' => new Expression("abs(price_sale - {$price_sale})")));
                $select->from('products');
                $select->where("price_sale BETWEEN {$min} AND {$max} AND products_id != {$products_id} AND categories_id={$catId} AND is_published=1 AND is_delete=0 AND products.website_id={$_SESSION['website_id']}");
                $select->order(array(
                    'distance_price' => 'ASC',
                ));
                $select->limit(4);
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
        $key = md5('Application-ProductsTable-getRecommendProductById-'.(is_array($ids)? implode('-',$ids) : $ids).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products');
            $select->where(array(
                'manufacturers_id' => $ids,
                'is_published' => 1,
                'is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
            ));
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
        $key = md5('Application-ProductsTable-getProductsByManu-'.(is_array($id)? implode('-',$id) : $id).'-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
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
                $select->from('products');
                $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
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
                ));
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
        $key = md5('Application-ProductsTable-getProductsByManu1-'.(is_array($id)? implode('-',$id) : $id).'-'.$intPage.'-'.$intPageSize.'-'.(is_array($where)? implode('-',$where) : $where).'-Website-'.$_SESSION['domain']);
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
            $select->from('products');
            $select->where(array(
                'manufacturers_id' => $id,
                'is_published' => 1,
                'is_delete' => 0,
                'products.website_id'=>$_SESSION['website_id']
            ));
            if($where && is_array($where)){
                $select->where($where);
            }
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
        $key = md5('Application-ProductsTable-countProductsByManu1-'.(is_array($id)? implode('-',$id) : $id).'-'.(is_array($where)? implode('-',$where) : $where).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression('count(products.products_id)')));
            $select->from('products');
            $select->where(array(
                'manufacturers_id' => $id,
                'is_published' => 1,
                'is_delete' => 0,
                'website_id'=>$_SESSION['website_id']
            ));
            if($where && is_array($where)){
                $select->where($where);
            }
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
        $key = md5('Application-ProductsTable-countProductsByManu-'.(is_array($id)? implode('-',$id) : $id).'-params-'.(is_array($params)? implode('-',$params) : $params).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
    		try{	
    			$adapter = $this->tableGateway->getAdapter();
    			$sql = new Sql($adapter);
    			$select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT products.products_id)")));
    			$select->from('products');
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
        $key = md5('Application-ProductsTable-getArticle-'.(is_array($id)? implode('-',$id) : $id).'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results) {
    		$adapter = $this->tableGateway->getAdapter();
    		$sql = new Sql($adapter);
    		$select = $sql->select();
    		$select->from('articles');
    		$select->where(array(
    			'articles_id' => $id,
                'website_id'=>$_SESSION['website_id']
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
        $adapter->getDriver()->getConnection()->beginTransaction();
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
                                    youtube_video
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
                                    youtube_video
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

            $adapter->getDriver()->getConnection()->commit();
            return $pid;
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
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


}