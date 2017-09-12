<?php

namespace Application\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class KeywordsTable extends AppTable {

    public function getKeywords($keyword)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':KeywordsTable:getKeywords('.$keyword.')');
        $results = $cache->getItem($key);
        if(!$results){
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select()->columns(array('keyword_id', 'url_friendly','keyword','number_search'));
                $select->from('keywords');
                $select->where(array('website_id'=>$this->getWebsiteId()));
                if(!empty($keyword)){
                    $keyword_url = $this->toAlias($keyword);
                    $select->where->like('url_friendly', '%'.$keyword_url.'%');
                }
                
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getTopKeywords()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':KeywordsTable:getTopKeywords');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('keyword_id', 'url_friendly','keyword','number_search'));
            $select->from('keywords');
            $select->where(array('website_id'=>$this->getWebsiteId()));
            $select->order('number_search DESC');
            $select->limit(5);
            $select->offset(0);
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $keywords = $result->toArray();
                $results = $keywords;
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function updateKeyword($keyword)
    {
        if(!empty($keyword)){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('keyword_id', 'url_friendly','keyword','number_search'));
            $select->from('keywords');
            $select->where(array('website_id'=>$this->getWebsiteId()));
            $keyword_url = $this->toAlias($keyword);
            $select->where->like('url_friendly', $keyword_url);
            
            $selectString = $sql->getSqlStringForSqlObject($select);
            $keywords = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $keywords = $keywords->toArray();
            
            if(!empty($keywords)){
                $row = array('number_search' => ($keywords['0']['number_search']+1));
                $this->tableGateway->update ( $row , array('keyword_id'=>$keywords['0']['keyword_id'], 'website_id'=>$this->getWebsiteId() ));
            }else{
                $row = array('website_id'=>$this->getWebsiteId(), 'url_friendly' => $keyword_url, 'keyword' => $keyword , 'number_search' => 0);
                $this->tableGateway->insert ( $row );
            }
            
        }
    }
	
}