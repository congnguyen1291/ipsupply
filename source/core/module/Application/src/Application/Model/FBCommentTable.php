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
use Zend\Db\Adapter\ParameterContainer;

use Application\Model\AppTable;

class FBCommentTable  extends AppTable {

    public function getComment($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($id);
        $key = md5($this->getNamspaceCached().':FBCommentTable:getComment('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('fb_comments');
            $select->where(array(
                'fb_comments.id' => $id,
                'fb_comments.website_id' => $this->getWebsiteId()
            ));
            $select->order('fb_comments.id desc');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getComments( $params = array() )
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($params);
        $key = md5($this->getNamspaceCached().':FBCommentTable:getComments('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('fb_comments');
            $select->where(array(
                'fb_comments.id_parent' => 0,
                'fb_comments.website_id' => $this->getWebsiteId()
            ));

            if( !empty($params['page_id']) ){
                $select->where(array(
                    'fb_comments.page_id' => $params['page_id']
                ));
            }

            if( !empty($params['post_id']) ){
                $select->where(array(
                    'fb_comments.post_id' => $params['post_id']
                ));
            }

            if( isset($params['page'])
                && !empty($params['page_size']) ){
                $intPage = 0;
                if ( $params['page'] > 1) {
                    $intPage = ($params['page'] - 1) * $params['page_size'];
                }
                $select->offset($intPage);
                $select->limit($params['page_size']);
            }

            $select->order('fb_comments.date_modifier desc, fb_comments.date desc');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $comments = array();
                if( !empty($results) ){
                    foreach ($results as $key => $result) {
                        $params = array();
                        $params['page_id'] = $result['page_id'];
                        $params['id_parent'] = $result['id'];
                        $from = $this->getModelTable('FBUserTable')->getUser( $result['user_id'] );
                        $result['from'] = $from;

                        $childs = array();
                        $subcoments = $this->getSubComments($params);
                        $is_readed = $result['is_readed'];
                        $is_important = $result['is_important'];
                        foreach ($subcoments as $ksub => $subcoment) {
                            $from = $this->getModelTable('FBUserTable')->getUser( $subcoment['user_id'] );
                            $subcoment['from'] = $from;
                            if( !empty($subcoment['is_readed']) ){
                                $is_readed = 1;
                            }
                            if( !empty($subcoment['is_important']) ){
                                $is_important = 1;
                            }
                            $feed = $this->getModelTable('FBFeedTable')->getFeed( $subcoment['post_id'] );
                            $subcoment['feed'] = $feed;
                            $childs[] = $subcoment;
                        }
                        $result['is_readed'] = $is_readed;
                        $result['is_important'] = $is_important;
                        $result['comments'] = $childs;
                        $feed = $this->getModelTable('FBFeedTable')->getFeed( $result['post_id'] );
                        $result['feed'] = $feed;
                        $comments[] = $result;
                    }
                }
                
                /*usort($comments, function($a, $b) {
                    $retval = 0;
                    if( strtotime($a['date'])  >  strtotime($b['date']) ){
                        $retval = -1;
                    }else if( strtotime($a['date'])  <  strtotime($b['date']) ){
                        $retval = 1;
                    }
                    if ($retval == 0) {
                        if( $a['is_readed']  >  $b['is_readed'] ){
                            $retval = -1;
                        }else if($a['is_readed']  <  $b['is_readed']){
                            $retval = 1;
                        }
                    }
                    return $retval;
                });*/
                $results = $comments;
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getSubComments( $params = array() )
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($params);
        $key = md5($this->getNamspaceCached().':FBCommentTable:getSubComments('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('fb_comments');
            $select->where(array(
                'fb_comments.website_id' => $this->getWebsiteId()
            ));

            if( !empty($params['page_id']) ){
                $select->where(array(
                    'fb_comments.page_id' => $params['page_id']
                ));
            }

            if( !empty($params['id_parent']) ){
                $select->where(array(
                    'fb_comments.id_parent' => $params['id_parent']
                ));
            }

            if( !empty($params['page'])
                && !empty($params['page_size']) ){
                $intPage = 0;
                if ( $params['page'] > 1) {
                    $intPage = ($params['page'] - 1) * $params['page_size'];
                }
                $select->offset($intPage);
                $select->limit($params['page_size']);
            }

            $select->order('fb_comments.date_modifier desc, fb_comments.date desc');
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

    public function countTotalComments( $params = array() )
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($params);
        $key = md5($this->getNamspaceCached().':FBCommentTable:countTotalComments('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('fb_comments');
            $select->columns(array('total' => new Expression("count(DISTINCT fb_comments.id)")));
            $select->where(array(
                'fb_comments.id_parent' => 0,
                'fb_comments.website_id' => $this->getWebsiteId()
            ));

            if( !empty($params['page_id']) ){
                $select->where(array(
                    'fb_comments.page_id' => $params['page_id']
                ));
            }

            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $row = $results->current();
                $results = 0;
                if( !empty($row) ){
                    $results= $row['total'];
                }
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getCommentIsReadedInArray($ids)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($ids);
        $key = md5($this->getNamspaceCached().':FBCommentTable:getCommentIsReadedInArray('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('id'));
            $select->from('fb_comments');
            $select->where(array(
                'fb_comments.id' => $ids,
                'fb_comments.is_readed' => 1,
                'fb_comments.website_id' => $this->getWebsiteId()
            ));
            $select->order('fb_comments.id desc');
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

    public function getCommentIsImportantInArray($ids)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($ids);
        $key = md5($this->getNamspaceCached().':FBCommentTable:getCommentIsImportantInArray('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('id'));
            $select->from('fb_comments');
            $select->where(array(
                'fb_comments.id' => $ids,
                'fb_comments.is_important' => 1,
                'fb_comments.website_id' => $this->getWebsiteId()
            ));
            $select->order('fb_comments.id desc');
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

    public function insertOrUpdate( $insertData, $updateData)
    {
        try{
            if( !empty($insertData) 
                && !empty($updateData) ){
                $sqlStringTemplate = 'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s';
                $adapter = $this->adapter;
                $driver = $adapter->getDriver();
                $platform = $adapter->getPlatform();

                $tableName = $platform->quoteIdentifier('fb_comments');
                $parameterContainer = new ParameterContainer();
                $statementContainer = $adapter->createStatement();
                $statementContainer->setParameterContainer($parameterContainer);

                /* Preparation insert data */
                $insertQuotedValue = [];
                $insertQuotedColumns = [];
                foreach ($insertData as $column => $value) {
                    $insertQuotedValue[] = $driver->formatParameterName($column);
                    $insertQuotedColumns[] = $platform->quoteIdentifier($column);
                    $parameterContainer->offsetSet($column, $value);
                }

                /* Preparation update data */
                $updateQuotedValue = [];
                foreach ($updateData as $column => $value) {
                    $updateQuotedValue[] = $platform->quoteIdentifier($column) . '=' . $driver->formatParameterName( $column);
                    $parameterContainer->offsetSet( $column, $value);
                }

                $query = sprintf(
                    $sqlStringTemplate,
                    $tableName,
                    implode(',', $insertQuotedColumns),
                    implode(',', array_values($insertQuotedValue)),
                    implode(',', $updateQuotedValue)
                );
                $statementContainer->setSql($query);
                return $statementContainer->execute();
            }
        }catch(\Exception $e ){}
    }

    public function getToggleReaded( $id ){
        $cm = $this->getComment($id);
        if ( !empty($cm) ) {
            $is_readed = 0;
            if( empty($cm->is_readed) ){
                $is_readed = 1;
            }
            $this->tableGateway->update( array('is_readed' => $is_readed) , array('id' => $id));
            if( !empty($cm->id_parent)
                && !empty($is_readed) ){
                $this->tableGateway->update( array('is_readed' => $is_readed) , array('id' => $cm->id_parent));
            }else if( empty($cm->id_parent)
                && empty($is_readed) ){
                $this->tableGateway->update( array('is_readed' => $is_readed) , array('id_parent' => $cm->id_parent));
            }

            $cache = $this->getServiceLocator()->get('cache');
            $temp = $this->createKeyCacheFromArray($id);
            $key = md5($this->getNamspaceCached().':FBCommentTable:getComment('.$temp.')');
            $cache->setItem($key, '');
            return $this->getComment($id);
        }
        return FALSE;
    }

    public function getToggleImportant( $id ){
        $cm = $this->getComment($id);
        if ( !empty($cm) ) {
            $is_important = 0;
            if( empty($cm->is_important) ){
                $is_important = 1;
            }
            $this->tableGateway->update( array('is_important' => $is_important) , array('id' => $id));
            if( !empty($cm->id_parent)
                && !empty($is_important) ){
                $this->tableGateway->update( array('is_important' => $is_important) , array('id' => $cm->id_parent));
            }else if( empty($cm->id_parent)
                && empty($is_important) ){
                $this->tableGateway->update( array('is_important' => $is_important) , array('id_parent' => $cm->id_parent));
            }
            $cache = $this->getServiceLocator()->get('cache');
            $temp = $this->createKeyCacheFromArray($id);
            $key = md5($this->getNamspaceCached().':FBCommentTable:getComment('.$temp.')');
            $cache->setItem($key, '');
            return $this->getComment($id);
        }
        return FALSE;
    }

    
}