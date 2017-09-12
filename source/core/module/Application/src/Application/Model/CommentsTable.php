<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class CommentsTable extends AppTable {

	public function fetchAll() {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CommentsTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('comments');
            $select->where(array(
                'website_id'=>$this->getWebsiteId(),
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = new ResultSet();
                $results->initialize($results);
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = null;
            }
        }
        return $results;
	}
	public function insertComments(Comments $comments) {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $data = array (
                    'website_id'=>$this->getWebsiteId(),
                    'comments_member' => $comments->member,
                    'comments_product' => $comments->product,
                    'comments_parent' => $comments->parent,
                    'comments_type' => $comments->type,
                    'comments_content' => $comments->content,
                    'comments_rating' => $comments->rating,
                    'comments_number' => $comments->number,
                    'comments_datecrerate' => $comments->date_crerate,
                    'comments_status' => $comments->status
            );
            $this->tableGateway->insert ( $data );
            $lastId = $this->getLastestId ();
            $reviews = $this->getReview($comments->product);
            $review_data = array(
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
            );
            $total = 0;
            foreach($reviews as $r){
                $review_data[$r['comments_rating']]++;
                $total++;
            }
            $total_avg = 0;
            foreach($review_data as $key => $value){
                $total_avg += $value * $key;
            }

            $rating = ceil($total_avg/$total);
            $sql = new Sql($adapter);
            $update = $sql->update('products');
            $update->where(array(
                'products_id' => $comments->product,
                'website_id'=>$this->getWebsiteId(),
            ));
            $update->set(array(
                'rating' => $rating,
            ));
            $updateString = $sql->getSqlStringForSqlObject($update);
            $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
            $adapter->getDriver()->getConnection()->commit();
            return $lastId;
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
	}

    public function getReview($products_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CommentsTable:getReview('.(is_array($products_id)? implode('-',$products_id) : $products_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = "SELECT comments.comments_rating,comments.comments_content,users.full_name, users.avatar, comments.comments_datecrerate as date_create
                        FROM comments
                        INNER JOIN users ON comments_member=users.users_id
                        WHERE comments_product={$products_id} AND comments.website_id = {$this->getWebsiteId()} ";
                $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

	public function countCommentOfUser($users_id) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:countCommentOfUser('.(is_array($users_id)? implode('-',$users_id) : $users_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT comments.comments_id)")));
            $select->from('comments');
            $select->join('products', 'products.products_id=comments.comments_product', array());
            $select->where(array(  
                            'comments.comments_member'=>$users_id,
                            'products.website_id'=>$this->getWebsiteId()
                        ));
            $select->group('comments.comments_id');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $result = $results->toArray();
                $results = 0;
                if ( count($result) > 0) {
                    $results = $result[0]['total'];
                }
                $cache->setITem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getCommentOfUser($user_id, $offset = 0, $limit= 5) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getCommentOfUser('.(is_array($user_id)? implode('-',$user_id) : $user_id).','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('comments');
            $select->join('products', 'products.products_id=comments.comments_product', array(
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
                    'ordering',
                    'thumb_image',
                    'number_views',
                    'rating',
                    'number_like',
                    'total_sale',
                    'wholesale',
                    'type_view',
                    'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array(  
                            'comments.comments_member'=>$user_id,
                            'products.website_id'=>$this->getWebsiteId()
                        ));
            $select->group('comments.comments_id');
            $select->offset($offset);
            $select->limit($limit);
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
}