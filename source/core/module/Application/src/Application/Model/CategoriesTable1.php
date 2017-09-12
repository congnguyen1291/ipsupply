<?php
namespace Application\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Filter\File\LowerCase;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\GreaterThan;

use Application\Model\AppTable;

class CategoriesTable extends AppTable {

    protected $primary = null;
    protected $entity = null;
    protected $metadata = null;
    protected $_html = '';
    protected $cids = array();
    protected $_breadCrumb = array();
    protected $parentId = null;
	
	public function getAllGoldTimerCurrent($cats, $intPage, $intPageSize){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllGoldTimerCurrent('.(is_array($cats) ? implode('-',$cats) : $cats).$intPage.';'.$intPageSize.')');
        $results = $cache->getItem($key);
        if(!$results){
            if ($intPage <= 1) {
                $intPage = 0;
            } else {
                $intPage = ($intPage - 1) * $intPageSize;
            }
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('totalDate' => new Expression("abs(DATEDIFF(NOW(), CONCAT(date_end,' ', time_end)))"),'date_start','date_end','time_start','time_end'));
            $select->from('gold_timer');
            $select->join('gold_timer_detail', 'gold_timer_detail.gold_timer_id=gold_timer.gold_timer_id',array('products_id', 'price_sale','price'));
            $select->join('products', 'gold_timer_detail.products_id=products.products_id', array('thumb_image','products_title','products_alias', 'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->join('categories', 'products.categories_id=categories.categories_id',array());
            $select->where('date_start <= NOW() AND date_end >= NOW()');
            if(count($cats)){
                $select->where(array(
                    'categories.categories_id' => $cats,
                ));
            }
            $select->where(array(
                'products.is_published' => 1,
                'products.is_delete' => 0,
                'categories.is_published' => 1,
                'categories.is_delete' => 0,
                'gold_timer.is_published' => 1,
                'products.is_available' => 1,
                'products.website_id'=>$_SESSION['website_id'],
            ));
            $select->order(array(
                'totalDate' => 'ASC',
            ));
            $select->offset($intPage);
            $select->limit($intPageSize);
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
	
	public function getAllCategoriesHasGoldTimer(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllCategoriesHasGoldTimer');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('totalProduct' => new Expression("count(gold_timer_detail.products_id)"),'*'));
            $select->from('categories');
            $select->join('products', 'products.categories_id=categories.categories_id',array());
            $select->join('gold_timer_detail', 'gold_timer_detail.products_id=products.products_id', array());
            $select->join('gold_timer', 'gold_timer_detail.gold_timer_id=gold_timer.gold_timer_id',array());
            $select->where('date_start <= NOW() AND date_end >= NOW()');
            $select->where(array(
                'categories.is_published' => 1,
                'categories.is_delete' => 0,
                'products.is_published' => 1,
                'products.is_available' => 1,
                'products.is_delete' => 0,
                'gold_timer.is_published' => 1,
                'products.website_id'=>$_SESSION['website_id'],
            ));
            $select->order(array(
                'totalProduct' => 'DESC',
            ));
            $select->group('categories.categories_id');
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

    public function fetchAll()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $results = $this->tableGateway->select();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
	
	public function getProductsIsGoingon($catid){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getProductsIsGoingon('.(is_array($catid) ? implode('-',$catid) : $catid).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'categories_id' => $catid,
                'is_goingon' => 1,
                'products.website_id'=>$_SESSION['website_id'],
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
	
	public function getProductsIsNew($catid){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getProductsIsNew('.(is_array($catid) ? implode('-',$catid) : $catid).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'categories_id' => $catid,
                'is_new' => 1,
                'is_goingon' => 0,
                'is_available' => 1,
                'products.website_id'=>$_SESSION['website_id'],
            ));
            //$select->where('quantity > 0');
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
	
	public function getCategoryArticleTech(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getCategoryArticleTech');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('categories_articles_id','parent_id','is_published','is_delete',
                                    'is_technical_category','date_create','date_update','ordering','is_faq','is_static'));
            $select->from('categories_articles');
            $select->join('categories_articles_languages', 'categories_articles_languages.categories_articles_id=categories_articles.categories_articles_id',array('categories_articles_title','categories_articles_alias','seo_keywords','seo_description'));
            $select->where(array(
                'categories_articles.is_published' => 1,
                'categories_articles.is_delete' => 0,
                'categories_articles.is_technical_category' => 1,
                'categories_articles.website_id'=>$_SESSION['website_id'],
                'categories_articles_languages.languages_id'=>$_SESSION['language']['languages_id']
            ));
            $select->limit(1);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                if(!$results){
                    return FALSE;
                }
                $results = (array)$results;
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
	}

    public function getCategories($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getCategories('.(is_array($id) ? implode('-',$id) : $id).')');
        $results = $cache->getItem($key);
        if(!$results){
            $id = (int)$id;
            try{
                $rowset = $this->tableGateway->select(array('id' => $id));
                $results = $rowset->current();
                if (!$results) {
                    $results = array();
                }
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

	public function getMyCategories($id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getCategories('.(is_array($id) ? implode('-',$id) : $id).')');
        $results = $cache->getItem($key);
        if(!$results){
    		$id = (int)$id;
    		try{
    			$rowset = $this->tableGateway->select(array('categories_id' => $id));
    			$results = (array)$rowset->current();
    			$cache->setItem($key, $results);
    		}catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
	}
	
    public function deleteAlbum($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }

    public function getMenusLeftChildCategories($rows, $id)
    {
        if (isset($rows[$id])) {
            $this->_html .= '<ul class="menu-sub" >';
            foreach ($rows[$id] as $row) {
                //$link = FOLDERWEB . "/categories/{$row['categories_alias']}-{$row['categories_id']}";
                $link = FOLDERWEB . "/category/{$row['categories_alias']}-{$row['categories_id']}";
                $this->_html .= "<li><a href=\"{$link}\" class=\"name\" >{$row['categories_title']}</a>";
                if (isset($rows[$row['categories_id']])) {
                    $this->getMenusLeftChildCategories($rows, $row['categories_id']);
                }
                $this->_html .= "</li>";
            }
            $this->_html .= '</ul>';
        }
        return $this->_html;
    }

    public function getAllRootCat(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllRootCat');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('categories_id','categories_title','categories_alias','icon'));
            $select->from('categories');
            $select->where(array(
                'parent_id' => 0,
                'is_published' => 1,
                'is_delete' => 0,
                'website_id'=>$_SESSION['website_id'],
            ));
            $select->order(array('ordering' => 'ASC'));
            $selectString = $sql->getSqlStringForSqlObject($select);
            try{
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getBannerTop($catid){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getBannerTop('.(is_array($catid) ? implode('-',$catid) : $catid).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('categories_banners');
            $select->join('categories', 'categories_banners.categories_id=gold_timer.categories_id',array());
            $select->where(array(
                'categories_banners.categories_id' => $catid,
                'categories_banners.box_num' => 5,
                'categories.website_id'=>$_SESSION['website_id'],
            ));
            $select->limit(1);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $results = (array)$results;
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
    private $all_cat_child = array();
    public function getAllChildCategories($catid){
        $limitParent = 4;
        $rows = $this->getAllCategories();
        $childs = $this->getChilds($rows, $catid);
        return array(
            'tree' => $childs,
            'all_cats' => $this->all_cat_child,
        );
    }

    /*public function getChilds($rows, $parent, $limit){
        $limitChild = 5;
        if(count($rows) == 0){
            return array();
        }
        $childs = array();
        foreach($rows as $row){
            if($row['parent_id'] == $parent && count($childs) < $limit){
                $childs[] = $row;
            }
        }
        if(count($childs) == 0){
            return array();
        }
        foreach($childs as $key => $child){
            $this->all_cat_child[] = $child['categories_id'];
            $childs[$key]['children'] = $this->getChilds($rows, $child['categories_id'],$limitChild);
        }
        return $childs;
    }*/

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
            $this->all_cat_child[] = $child['categories_id'];
            $childs[$key]['children'] = $this->getChilds($rows, $child['categories_id']);
        }
        return $childs;
    }

    public function getDataHomepage($cat_root, $limit){
        $cache = $this->getServiceLocator()->get('cache');
        $list_id  = array();
        foreach ($cat_root as $key => $cat) {
            $list_id[] = $cat['categories_id'];
        }
        $key = md5($this->getNamspaceCached().':CategoriesTable:getDataHomepage('.implode('-',$list_id).'-'.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
				
                $rows = $this->getAllCategories();
                $results = array();
                if(!empty($rows)){
                    foreach($cat_root as $cat){
                        $this->all_cat_child = array();
                        $children = $this->getChilds($rows, $cat['categories_id']);
                        $ids = $this->all_cat_child;
                        $ids[] = $cat['categories_id'];
                        $results[$cat['categories_id']]['children'] = $children;
                        $results[$cat['categories_id']]['banners'] = $this->getBannerForCat($cat['categories_id']);
                        $results[$cat['categories_id']]['products'] = $this->getProductsForCat($ids);
                        $results[$cat['categories_id']]['manus'] = $this->getAllManu($ids);
                    }
                    $cache->setItem($key, $results);
                }
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getBannerForCat($catid){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getBannerForCat('.(is_array($catid) ? implode('-',$catid) : $catid).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('categories_banners');
            $select->join('categories', 'categories_banners.categories_id=categories.categories_id',array());
            if(!is_array($catid)){
                $catid = array($catid);
            }
            $catid = implode(',', $catid);
            $select->where("categories_banners.is_published=1 AND categories_banners.box_num = 1 AND categories_banners.categories_id IN({$catid}) AND categories.website_id= {$_SESSION['website_id']}");
            $select->order(array(
                'categories_banners.ordering' => 'ASC',
            ));
            //$select->limit(4);
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

    public function getProductsForCat($catid){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getProductsForCat('.(is_array($catid) ? implode('-',$catid) : $catid).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('comments','comments.comments_product=products.products_id',array('total_review' => new Expression('count(comments_id)')),'left');
            if(!is_array($catid)){
                $catid = array($catid);
            }
            if(count($catid) == 0){
                return array();
            }
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'categories_id' => $catid,
                'products.website_id'=>$_SESSION['website_id'],
            ));
            $select->order(array(
                'products_id' => 'desc',
            ));
            $select->group('products.products_id');
            $select->limit(8);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                //die($selectString);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getMenusLeftCategories1()
    {
        $rows = $this->getAllCategories();
        $newList = array();
        if ($rows) {
            $html = '<ul class="list-cate">';
            foreach ($rows as $row) {
                $newList[$row['parent_id']][] = $row;
            }
            //ksort($newList);
            foreach ($newList[0] as $row) {
                //$link = FOLDERWEB . "/categories/{$row['categories_alias']}-{$row['categories_id']}";
                $link = FOLDERWEB . "/category/{$row['categories_alias']}-{$row['categories_id']}";
                $html .= "<li><a href=\"{$link}\" class=\"name\" >{$row['categories_title']}<i class=\"icon i-next\" ></i></a>";
                if (isset($newList[$row['categories_id']])) {
                    $html .= $this->getMenusLeftChildCategories($newList, $row['categories_id']);
                }
                $html .= '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }

    public function getMenusLeftCategories()
    {
        $limit = 7;
        $rows = $this->getAllCategories();
        $newList = array();
        if ($rows) {
            $html = '<ul class="list-cate">';
            foreach ($rows as $row) {
                $newList[$row['parent_id']][] = $row;
            }
            //ksort($newList);
            $index = 0;

            $total = count($newList[0]);
            foreach ($newList[0] as $row) {
                $link = FOLDERWEB . "/category/{$row['categories_alias']}-{$row['categories_id']}";
                if ($index < $limit) {
                    //$link = FOLDERWEB . "/categories/{$row['categories_alias']}-{$row['categories_id']}";
                    $html .= "<li><a href=\"{$link}\" class=\"name\" >{$row['categories_title']}<i class=\"icon i-next\" ></i></a>";
                    if (isset($newList[$row['categories_id']])) {
                        $html .= $this->getMenusLeftChildCategories($newList, $row['categories_id']);
                    }
                    $html .= '</li>';
                } else {
                    if ($index == $limit && $total == $limit) {
                        $html .= "<li><a href='#' class='name'>Danh mục khác<i class=\"icon i-next\" ></i></a>";
                        $html .= "<ul class='menu-sub'>";
                        $html .= "<li><a href=\"{$link}\" class=\"name\" >{$row['categories_title']}<i class=\"icon i-next\" ></i></a>";
                        if (isset($newList[$row['categories_id']])) {
                            $html .= $this->getMenusLeftChildCategories($newList, $row['categories_id']);
                        }
                        $html .= "</li>";
                        $html .= "</ul>";
                        $html .= "</li>";
                    } else {
                        if ($index == $limit) {
                            $html .= "<li><a href='#' class='name'>Danh mục khác<i class=\"icon i-next\" ></i></a>";
                            $html .= "<ul class='menu-sub'>";
                            $html .= "<li><a href=\"{$link}\" class=\"name\" >{$row['categories_title']}<i class=\"icon i-next\" ></i></a>";
                            if (isset($newList[$row['categories_id']])) {
                                $html .= $this->getMenusLeftChildCategories($newList, $row['categories_id']);
                            }
                            $html .= "</li>";
                        } elseif ($index < $total) {
                            $html .= "<li><a href=\"{$link}\" class=\"name\" >{$row['categories_title']}<i class=\"icon i-next\" ></i></a>";
                            if (isset($newList[$row['categories_id']])) {
                                $html .= $this->getMenusLeftChildCategories($newList, $row['categories_id']);
                            }
                            $html .= "</li>";
                        } else {
                            $html .= "<li><a href=\"{$link}\" class=\"name\" >{$row['categories_title']}<i class=\"icon i-next\" ></i></a>";
                            if (isset($newList[$row['categories_id']])) {
                                $html .= $this->getMenusLeftChildCategories($newList, $row['categories_id']);
                            }
                            $html .= "</li>";
                            $html .= "</ul>";
                            $html .= "<li>";
                        }
                    }
                }
                $index++;
            }

            $html .= '</ul>';
        }
        return $html;
    }
	
    public function getAllCategoriesSort()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllCategoriesSort');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getAllCategories();
        		$listCategory = array();
                $map = array();
        		if(COUNT($rows)>0){
        			foreach ($rows as $item ) {
        				$idParentCategory = $item['parent_id'];

                        if( isset($map['map'][$idParentCategory]) ){
                            $map['map'][$item['categories_id']] =  $map['map'][$idParentCategory];
                            $map['map'][$item['categories_id']][] =  $idParentCategory;
                        }else{
                            $map['map'][$item['categories_id']] =  array($idParentCategory);
                        }
                        $item['map'] = $map['map'][$item['categories_id']];
                        
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

    public function getAllCategoriesSortWithKeyValue()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllCategoriesSortKeyValue');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getAllCategories();
                $listCategory = array();
                if(COUNT($rows)>0){
                    foreach ($rows as $item ) {
                        $categories_id = $item['categories_id'];
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

    public function getAllParentsOfCategory($categories_id, $has_me = false)
    {
        $_has_me = 0;
        if($has_me){
            $_has_me = 1;
        }
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllParentsOfCategory('.$categories_id.','.$_has_me.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getAllCategoriesSortWithKeyValue();
                $list = array();
                if(!empty($rows) && !empty($rows[$categories_id])){
                    $category = $rows[$categories_id];
                    if($has_me){
                        $list[] = $rows[$categories_id];
                    }
                    while (!empty($category) 
                            && !empty($category['parent_id']) && $category['parent_id'] >0
                            && !empty($rows[$category['parent_id']])) {
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

    public function getAllCategories($cate_id="")
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllCategories('.(is_array($cate_id) ? implode('-',$cate_id) : $cate_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('categories');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'website_id' => $_SESSION['website_id'],
            ));
            $select->order(array(
                'parent_id' => 'ASC',
                'ordering' => 'ASC',
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


    public function fetchRow($select = null)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:fetchRow');
        $results = $cache->getItem($key);
        if(!$results){
            $statement = $this->adapter->createStatement();
            $select->prepareStatement($this->adapter, $statement);
            $select->where(array(
                'website_id'=>$_SESSION['website_id'],
            ));
            try{
                $resultSet = new ResultSet();
                $resultSet->initialize($statement->execute());
                $results = $resultSet->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getParentCategories()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getParentCategories');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('categories');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'website_id'=>$_SESSION['website_id'],
            ));
            $select->order('ordering ASC');

            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $this->multiLevelData(FALSE, $results, 'categories_id', 'parent_id', 'categories_title');
            $cache->setItem($key,$results);
        }
        return $results;
    }

    public function getProcedure($name, $params = array())
    {
        if ($params) {
            $t = array();
            foreach ($params as $r) {
                $r = '"' . $r . '"';
                $t[] = $r;
            }
            $params = implode(',', $t);
        } else {
            $params = '';
        }
        $adapter = $this->tableGateway->getAdapter();

        $sql = "CALL `{$name}`({$params});";
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        return $result;
    }

    public function getCategoriesIdOfCateOnArraySort($categories_id, $rows)
    {
        $results = array();
        if (!empty($rows[$categories_id])) {
            foreach ($rows[$categories_id] as $cat) {
                $results[] = $cat['categories_id'];
                if (!empty($rows[$cat['categories_id']])) {
                    $list_id = $this->getCategoriesIdOfCateOnArraySort($cat['categories_id'], $rows);
                    if( !empty($list_id) ){
                        $results = array_merge($results, $list_id);
                    }
                }
            }
        }
        return $results;
    }

    public function getAllChildOfCate($cate_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllChildOfCate('.(is_array($cate_id) ? implode('-',$cate_id) : $cate_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
            	$results = array();
                $rows = $this->getAllCategoriesSort();
                $results = $this->getCategoriesIdOfCateOnArraySort($cate_id, $rows);
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllIdCategory()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllIdCategory');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getAllCategories();
                if ($rows) {
                    $results = array();
                    foreach ($rows as $row) {
                        $results[] = $row['categories_id'];
                    }
                    $cache->setItem($key, $results);
                }
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
        $key = md5($this->getNamspaceCached().':CategoriesTable:getRow('.(is_array($cate_id) ? implode('-',$cate_id) : $cate_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('categories');
                $select->where(array(
                    'is_published' => 1,
                    'is_delete' => 0,
                    'categories_id' => $cate_id,
                    'website_id'=>$_SESSION['website_id'],
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

    public function getRows($cate_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getRows('.(is_array($cate_id) ? implode('-',$cate_id) : $cate_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('categories');
                $select->where(array(
                    'is_published' => 1,
                    'is_delete' => 0,
                    'categories_id' => $cate_id,
                    'website_id'=>$_SESSION['website_id'],
                ));
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

    public function getChildFirstRows($idcat){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getChildFirstRows('.(is_array($idcat) ? implode('-',$idcat) : $idcat).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getAllCategories();
                $results = array();
                if(count($rows)){
                    foreach($rows as $row){
                        if($row['parent_id'] == $idcat){
                            $results[] = $row;
                        }
                    }
                }
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

     public function getBreadCrumb($cate_id = '', $breadCrumbMore = '')
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getBreadCrumb('.(is_array($cate_id) ? implode('-',$cate_id) : $cate_id).';'.$breadCrumbMore.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $breadCrumb = '';
                if ($cate_id) {
                    $adapter = $this->adapter;
                    $sql = new Sql($adapter);
                    $select = $sql->select();
                    $select->from('categories');
                    $select->where(array(
                        'is_published' => 1,
                        'is_delete' => 0,
                        'categories_id' => $cate_id,
                        'website_id'=>$_SESSION['website_id'],
                    ));
                    $selectString = $sql->getSqlStringForSqlObject($select);
                    $rows = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

                    if ($rows) {
                        foreach ($rows as $index=>$row) {
                            $this->parentId = $row['categories_id'];
                            if ($row['parent_id'] > 0) {
                                self::getBreadCrumb($row['parent_id']);
                            }
                            $link = FOLDERWEB . "/category/{$row['categories_alias']}-{$row['categories_id']}";
                            $this->_breadCrumb[] = "<a href=\"{$link}\" class=\"item-breakcum ".(count($this->_breadCrumb)%2 == 0 ? 'odd' : 'even')."\" title=\"{$row['categories_title']}\" ><span class=\"txt\" >{$row['categories_title']} </span></a>";

                        }
                    }
                    ksort($this->_breadCrumb);
                    $breadCrumb = implode('', $this->_breadCrumb);
                }
                $results = "<div class=\"breakcum-top clearfix\" ><a href=\"" . FOLDERWEB . "\" class=\"item-breakcum first even\" title=\"Trang chủ\"><span class=\"txt\" >Trang chủ </span></a>{$breadCrumb}{$breadCrumbMore}</div>";
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = '';
            }
        }
        return $results;

    }

    public function countTotalProduct($cat_ids){
		$cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:countTotalProduct('.(is_array($cat_ids) ? implode('-',$cat_ids) : $cat_ids).')');
        $results = $cache->getItem($key);
        if(!$results){
			$adapter = $this->tableGateway->getAdapter();
			$sql = new Sql($adapter);
			$select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT products.products_id)")));
			$select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
			if(count($cat_ids)>0){
				$select->where(array(
					'is_delete' => 0,
					'is_published' => 1,
					'categories_id' => $cat_ids,
                    'website_id'=>$_SESSION['website_id'],
				));
			}else{
				$select->where(array(
					'is_delete' => 0,
					'is_published' => 1
				));
			}
            try{
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    			$row = $results->current();
    			$results= $row['total'];
    			$cache->setItem($key, $results);
    		}catch(\Exception $ex){
                $results = 0;
            }
        }
        return $results;
    }

    private $all_cat_current = array();
    public function getLeftMenuPageCategory($cate_id)
    {
        $html = '';
		$menumobile="";
		
        if ($this->parentId) {
            $category = $this->getRow($this->parentId);
            if ($category) {
                $rows = $this->getAllCategories($cate_id);
                $newList = array();
                if ($rows) {
                    foreach ($rows as $row) {
                        $newList[$row['parent_id']][] = $row;
                    }
                }
                $html = '<ul class="list-cate-left" >';
				$menumobile='<ul>';
                $link = FOLDERWEB . "/category/{$row['categories_alias']}-{$category['categories_id']}";
                if ($cate_id == $this->parentId) {
                    $select = 'class="active"';
                } else {
                    $select = '';
                }

                $html .= "<li {$select}><a href=\"{$link}\" class=\"txt-categories-title-left\" >{$category['categories_title']}</a>";
				$menumobile.="<li><a href=\"{$link}\" class=\"txt\" >{$category['categories_title']}</a>";
                if (isset($newList[$this->parentId])) {
                    $html .= '<ul class="sub" >';
					$menumobile.='<ul>';
                    foreach ($newList[$this->parentId] as $item) {
                        //$link = FOLDERWEB . "/categories/{$row['categories_alias']}-{$row['categories_id']}";
                        $link = FOLDERWEB . "/category/{$item['categories_alias']}-{$item['categories_id']}";

                        if (isset($newList[$item['categories_id']]) && count($newList[$item['categories_id']]) > 0) {
                            $select .= ' has-sub';
                        }

                        if ($cate_id == $item['categories_id']) {
                            $select = 'class="active"';
                        } else {
                            $select = '';
                        }

                        $html .= "<li {$select}><a href=\"{$link}\" class=\"txt-categories-title-left\" >{$item['categories_title']} </a>";
						$menumobile.="<li {$select} ><a href=\"{$link}\" class=\"txt-categories-title-left\" >{$item['categories_title']}</a>";
                        $this->all_cat_current[] = $item['categories_id'];
                        if (isset($newList[$item['categories_id']]) && count($newList[$item['categories_id']]) > 0) {
                            $this->_html = '';
                            $html .= $this->getLeftMenuPageChlidCategory($newList, $item['categories_id'], $cate_id);
							$menumobile.=$this->getLeftMenuPageChlidCategoryMobile($newList, $item['categories_id'], $cate_id);
                        }
                        $html .= "</li>";
						$menumobile.='</li>';
                    }
                    $html .= '</ul>';
					$menumobile.='</ul>';
                }
                $html .= '</li>';
                $html .= '</ul>';
				$menumobile .= '</li>';
                $menumobile .= '</ul>';
				//$html.='<nav id="menu">'.$menumobile.'</nav>';
            }
        }
        return array(
            'all_category' => $this->all_cat_current,
            'html' => $html,
        );
    }
	public function getLeftMenuPageChlidCategoryMobile($rows, $id, $cate_id)
    {
        if (isset($rows[$id])) {
            $this->_html .= '<ul class="sub" >';

            foreach ($rows[$id] as $row) {
                //$link = FOLDERWEB . "/categories/{$row['categories_alias']}-{$row['categories_id']}";
                $link = FOLDERWEB . "/category/{$row['categories_alias']}-{$row['categories_id']}";
                if ($cate_id == $row['categories_id']) {
                    $select = 'class="active"';
                } else {
                    $select = '';
                }

                if (isset($rows[$row['categories_id']])) {
                    $t = '<i class="icon i-down" ></i>';
                } else {
                    $t = '';
                }
                $this->_html .= "<li {$select}><a href=\"{$link}\" class=\"txt\" >{$row['categories_title']} {$t}</a>";
                $this->all_cat_current[] = $row['categories_id'];
                if (isset($rows[$row['categories_id']])) {
                    $this->getLeftMenuPageChlidCategory($rows, $row['categories_id'], $cate_id);
                }
                $this->_html .= "</li>";
            }
            $this->_html .= '</ul>';
        }
        return $this->_html;
    }
    public function getLeftMenuPageChlidCategory($rows, $id, $cate_id)
    {
        if (isset($rows[$id])) {
            $this->_html .= '<ul class="sub" >';

            foreach ($rows[$id] as $row) {
                //$link = FOLDERWEB . "/categories/{$row['categories_alias']}-{$row['categories_id']}";
                $link = FOLDERWEB . "/category/{$row['categories_alias']}-{$row['categories_id']}";
                if ($cate_id == $row['categories_id']) {
                    $select = 'class="active"';
                } else {
                    $select = '';
                }

                if (isset($rows[$row['categories_id']])) {
                    $t = '<i class="icon i-down" ></i>';
                } else {
                    $t = '';
                }
                $this->_html .= "<li {$select}><a href=\"{$link}\" class=\"txt\" >{$row['categories_title']} {$t}</a>";
                $this->all_cat_current[] = $row['categories_id'];
                if (isset($rows[$row['categories_id']])) {
                    $this->getLeftMenuPageChlidCategory($rows, $row['categories_id'], $cate_id);
                }
                $this->_html .= "</li>";
            }
            $this->_html .= '</ul>';
        }
        return $this->_html;
    }

    public function getFeature($cate_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getFeature('.(is_array($cate_id) ? implode('-',$cate_id) : $cate_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                if(!empty($cate_id)){
                    $adapter = $this->tableGateway->getAdapter();
                    $sql = new Sql($adapter);
                    $select = $sql->select();
                    $select->from('feature');
                    $select->join('categories_feature', ' categories_feature.feature_id=feature.feature_id', array('total' => new Expression("count(DISTINCT products.products_id)")));
                    $select->join('products_feature', 'products_feature.feature_id=feature.feature_id',array());
                    $select->join('products', 'products.products_id=products_feature.products_id', array());
                    $select->where(array(
                        'feature.is_published' => 1,
                        'feature.is_delete' => 0,
                        'products.is_published' => 1,
                        'products.is_delete' => 0,
                        'categories_feature.categories_id' => $cate_id,
                        'products.categories_id' => $cate_id,
                        'products.website_id'=>$_SESSION['website_id'],
                    ));
                    $select->group('feature.feature_id');
                    $selectString = $sql->getSqlStringForSqlObject($select);
                    $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                    $resultSet = new ResultSet();
                    $resultSet->initialize($results);

                    $results = $resultSet->toArray();
                    $cache->setItem($key, $results);
                }else{
                    $results = array();
                }
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getHtmlLeftFilterFeature($cate_id, $exists_feature = NULL)
    {
        $html = '';
        $rows = $this->getFeature($cate_id);

        $min = null;
        if ($rows) {
            $newList = array();
            foreach ($rows as $row) {
                $newList[$row['parent_id']][] = $row;

                if ($min === null) {
                    $min = $row['parent_id'];
                } elseif ($row['parent_id'] < $min) {
                    $min = $row['parent_id'];
                }
            }

            //var_dump($newList);die;
            foreach ($newList[$min] as $row) {
				if(isset($newList[$row['feature_id']]) && COUNT($newList[$row['feature_id']])>0){
					$html .= "<div class=\"title\" data-sys=\"ibox-filter-{$row['feature_id']}\" >{$row['feature_title']}<i class=\"icon i-down-ca\" ></i></div><div class=\"data close\" id=\"ibox-filter-{$row['feature_id']}\" ><div class=\"list-brand-type\" >";
					if (isset($newList[$row['feature_id']])) {
						$this->_html = '';
						$html .= $this->getHtmlLeftChildFilterFeature($newList, $row['feature_id'], $exists_feature);
					}
					$html .= '</div></div>';
				}
            }
        }
        return $html;
    }

    public function getHtmlLeftChildFilterFeature($rows, $id, $exists_feature = NULL)
    {
        $max = 5;
        if (isset($rows[$id])) {
            foreach ($rows[$id] as $key => $row) {
                if($key < $max){
                    if ($exists_feature) {
                        $this->_html .= "<div class=\"item-brand\" ><input type=\"checkbox\" class=\"cbrand\" " . (in_array($row['feature_id'], $exists_feature) ? 'checked' : '') . " value=\"{$row['feature_id']}\" name=\"feature[{$row['parent_id']}][]\"  id='feature".$row['feature_id']."'/><label for='feature{$row['feature_id']}'> {$row['feature_title']}({$row['total']})</label></div>";
                    }else{
                        $this->_html .= "<div class=\"item-brand\" ><input type=\"checkbox\" class=\"cbrand\" value=\"{$row['feature_id']}\" name=\"feature[{$row['parent_id']}][]\" id='feature".$row['feature_id']."' /> <label for='feature".$row['feature_id']."'>{$row['feature_title']}({$row['total']})</label></div>";
                    }
                }else{
                    if ($exists_feature) {
                        $this->_html .= "<div class=\"item-brand feature-more\" ".(!in_array($row['feature_id'], $exists_feature) ? "style='display:none'" : '')." ><input type=\"checkbox\" class=\"cbrand\" " . (in_array($row['feature_id'], $exists_feature) ? 'checked' : '') . " value=\"{$row['feature_id']}\" name=\"feature[{$row['parent_id']}][]\" /><label for='feature".$row['feature_id']."'> {$row['feature_title']}({$row['total']})</label></div>";
                    } else {
                        $this->_html .= "<div class=\"item-brand feature-more\" ".(!in_array($row['feature_id'], $exists_feature) ? "style='display:none'" : '')." ><input type=\"checkbox\" class=\"cbrand\" value=\"{$row['feature_id']}\" name=\"feature[{$row['parent_id']}][]\" id='feature".$row['feature_id']."'/><label for='feature".$row['feature_id']."'> {$row['feature_title']}({$row['total']})</label></div>";
                    }
                }
            }
            if(count($rows[$id]) > $max){
                $this->_html .= "<a href='javascript:;' class='see_more_feature'>More &bigtriangledown;</a>";
            }
        }
        return $this->_html;
    }

    public function getAllCategoriesMenuTop()
    {
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
        $rows = $this->getAllCategories();
        $html = "";
        if ($rows) {
            $html = '<ul class="list-cate">';
            foreach ($rows as $row) {
                if ($row['parent_id'] == 0) {
                    $htmlSub = $helper->getMenuRoot($row['categories_id']);
                    $link = FOLDERWEB . "/category/{$row['categories_alias']}-{$row['categories_id']}";
                    $html .= "<li class='root-cat ".($htmlSub ? 'loaded' : '')."' item-id='{$row['categories_id']}' ><a href='{$link}'  class='name' >{$row['categories_title']}<i class='icon i-next' ></i></a>";
                    //$html .= $this->getAllChildrenCategoriesMenuTop($row, $rows);
                    $html .= $htmlSub ? $htmlSub : '';
                    $html .= "</li>";
                }
            }
            $html .= '</ul>';
        }
        return $html;
    }
	

    public function getAllChildrenCategoriesMenuTop($cat, $categories = array())
    {
        $html = "";
        $hasChild = FALSE;
        foreach ($categories as $cat1) {
            if ($cat1['parent_id'] == $cat['categories_id']) {
                $hasChild = TRUE;
                break;
            }
        }
        if ($hasChild) {
            $count1 = 0;
            $html .= "<div class='sub-menu-lv' >
									<div class='ct-sub-menu-lv' >
										<ul class='lv_9 cl-box' >";
            $first = "first";
            foreach ($categories as $cat2) {
                if ($cat2['parent_id'] == $cat['categories_id'] && $count1++ < 3) {
                    $link = FOLDERWEB . "/category/{$cat2['categories_alias']}-{$cat2['categories_id']}";
                    $html .= "<li class='{$first}'>";
                    $html .= "<a href='{$link}' class='lb_' >{$cat2['categories_title']}</a>";
                    $hasChild2 = FALSE;
                    foreach ($categories as $cat3) {
                        if ($cat3['parent_id'] == $cat2['categories_id']) {
                            $hasChild2 = TRUE;
                            break;
                        }
                    }
                    if ($hasChild2) {
                        $count2 = 0;
                        $hasChild2 = FALSE;
                        $html .= "<div class='list-cate' >";
                        foreach ($categories as $cat4) {
                            if ($cat4['parent_id'] == $cat2['categories_id'] && $count2++ < 3) {
                                $link = FOLDERWEB . "/category/{$cat4['categories_alias']}-{$cat4['categories_id']}";
                                $html .= "<a href='{$link}' class='lb_' >{$cat4['categories_title']}</a>";
                            }
                        }
                        $count2 = 0;
                        $html .= "</div>";
                    }
                    $html .= "</li>";
                    $first = "";
                }
            }
            $count1 = 0;
            $html .= "</ul>";
            $html .= "</div>";
            $html .= "</div>";
        }
        return $html;
    }

    protected function treerecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1, $id_col, $parentid_col)
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

    protected function multiLevelData($return_data = TRUE, $rows, $id_col, $parentid_col, $title_col, $root_title = '__ROOT__')
    {
        if ($rows) {
            $children = array();
            foreach ($rows as $v) {
                $v = get_object_vars($v);
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

    public function addSubscriptionUser($email, $cat)
    {
        if ($cat == -1) {
            $cat = '';
        }
        $sql = "INSERT INTO `news_letters`(email,categories_id,news_letter_groups_id) (SELECT '{$email}','{$cat}', MIN(news_letter_groups_id) FROM news_letter_groups WHERE is_default=1) ON DUPLICATE KEY UPDATE `categories_id`='{$cat}' AND website_id ={$_SESSION['website_id']} ";// VALUES ('{$email}', '{$cat}') ON DUPLICATE KEY UPDATE `categories_id`='{$cat}';";
        try {
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            return TRUE;
        } catch (\Exception $ex) {
            return FALSE;
        }

    }

    public function getBannersSlideshow($list){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getBannersSlideshow('.(is_array($list) ? implode('-',$list) : $list).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('categories_banners');
            $select->join('categories', 'categories_banners.categories_id=gold_timer.categories_id',array());
            $select->where(array(
                'categories_id' => $list,
                'box_num' => 6,
                'is_published' => 1,
                'categories.website_id'=>$_SESSION['website_id']
            ));
            $select->where('file IS NOT NULL');
            $selectString = $sql->getSqlStringForSqlObject($select);
            try{
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
	
	public function getAllManu($cats){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllManu('.(is_array($cats) ? implode('-',$cats) : $cats).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            try{
                if(!count($cats)){
                    return FALSE;
                }
                $cats = implode(',', $cats);
                $sql = "SELECT DISTINCT manufacturers.*
                        FROM categories
                        INNER JOIN products ON products.categories_id=categories.categories_id
                        INNER JOIN manufacturers ON products.manufacturers_id=manufacturers.manufacturers_id
                        WHERE products.is_published=1 AND products.is_delete=0 AND categories.is_published=1 AND products.website_id={$_SESSION['website_id']} AND categories.is_delete=0 AND categories.categories_id IN ({$cats}) AND manufacturers.is_published=1 AND manufacturers.is_delete=0
                        ORDER BY manufacturers.ordering LIMIT 14";

                $results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
	}

    public function getAllManuWithCat($cats){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesTable:getAllManuWithCat('.(is_array($cats) ? implode('-',$cats) : $cats).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            try{
                if(!count($cats)){
                    return FALSE;
                }
                $cats = implode(',', $cats);
                $sql = "SELECT DISTINCT manufacturers.manufacturers_id,manufacturers.manufacturers_name,manufacturers.thumb_image,manufacturers.ordering,manufacturers.description,categories.categories_id
                        FROM categories
                        INNER JOIN products ON products.categories_id=categories.categories_id
                        INNER JOIN manufacturers ON products.manufacturers_id=manufacturers.manufacturers_id
                        WHERE products.is_published=1 AND products.is_delete=0 AND categories.is_published=1 AND products.website_id={$_SESSION['website_id']} AND categories.is_delete=0 AND categories.categories_id IN ({$cats}) AND manufacturers.is_published=1 AND manufacturers.is_delete=0
                        ORDER BY manufacturers.ordering";
                $results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getCategoryOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('categories');
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

    public function getOneCategoryNoWebsite($categories_id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('categories');
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0,
            'categories_id' => $categories_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->current();
        return $results;
    }

    public function getAllCategoriesOfWebsiteAndSort($website_id)
    {
        $rows = $this->getCategoryOfWebsite($website_id);
        $listCategory = array();
        if( !empty($rows)>0){
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

    public function getCategoriesFeaturesOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('categories_feature');
        $select->join('categories', 'categories_feature.categories_id = categories.categories_id', array());
        $select->join('feature', 'categories_feature.feature_id = feature.feature_id', array());
        $select->where(array(
            'categories_feature.is_published' => 1,
            'categories.is_published' => 1,
            'categories.is_delete' => 0,
            'categories.website_id' => $website_id,
            'feature.website_id'=>$website_id,
            'feature.is_delete'=>0,
            'feature.is_published'=>1
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertCategoryFeatures($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('categories_feature');
        $insert->columns(array('categories_id','feature_id','is_published'));
        $insert->values(array(
            'categories_id' => $data['categories_id'],
            'feature_id' => $data['feature_id'],
            'is_published' => $data['is_published'],
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            
        }
    }

    public function deleteCategoryFeatures($categories_id, $feature_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $delete  = $sql->delete('categories_feature');
        $delete->where(array(
            'categories_id' => $categories_id,
            'feature_id' => $feature_id,
        ));
        try{
            $deleteString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            
        }
    }

    public function insertCategory($data)
    {
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }

    public function removeAllCategoryOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

}