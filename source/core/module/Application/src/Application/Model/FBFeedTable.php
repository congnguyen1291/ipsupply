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

class FBFeedTable  extends AppTable {

    public function getFeed($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($id);
        $key = md5($this->getNamspaceCached().':FBFeedTable:getFeed('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('fb_feed');
            $select->where(array(
                'fb_feed.id' => $id,
                'fb_feed.website_id' => $this->getWebsiteId()
            ));
            $select->order('fb_feed.id desc');
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

    public function getFeeds( $params = array() )
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($params);
        $key = md5($this->getNamspaceCached().':FBFeedTable:getFeeds('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('fb_feed');
            $select->where(array(
                'fb_feed.website_id' => $this->getWebsiteId()
            ));

            if( !empty($params['page_id']) ){
                $select->where(array(
                    'fb_feed.page_id' => $params['page_id']
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

            $select->order('fb_feed.date desc');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $feeds = array();
                if( !empty($results) ){
                    foreach ($results as $key => $result) {
                        $params = array();
                        $params['page_id'] = $result['page_id'];
                        $from = $this->getModelTable('FBUserTable')->getUser( $result['user_id'] );
                        $result['from'] = $from;

                        $params['post_id'] = $result['id'];
                        $comments = $this->getModelTable('FBCommentTable')->getComments( $params );
                        $result['comments'] = $comments;
                        $feeds[] = $result;
                    }
                }
                $results = $feeds;
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function countTotalFeeds( $params = array() )
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($params);
        $key = md5($this->getNamspaceCached().':FBFeedTable:countTotalFeeds('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('fb_feed');
            $select->columns(array('total' => new Expression("count(DISTINCT fb_feed.id)")));
            $select->where(array(
                'fb_feed.website_id' => $this->getWebsiteId()
            ));

            if( !empty($params['page_id']) ){
                $select->where(array(
                    'fb_feed.page_id' => $params['page_id']
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

    public function insertOrUpdate( $insertData, $updateData)
    {
        try{
            if( !empty($insertData) 
                && !empty($updateData) ){
                $sqlStringTemplate = 'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s';
                $adapter = $this->adapter;
                $driver = $adapter->getDriver();
                $platform = $adapter->getPlatform();

                $tableName = $platform->quoteIdentifier('fb_feed');
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
        }catch(\Exception $e ){
            //echo $e->getMessage();die();
        }
    }

}