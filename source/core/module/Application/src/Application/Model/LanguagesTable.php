<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/9/14
 * Time: 11:01 AM
 */
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class LanguagesTable extends AppTable {
    
    public function getLanguages( $filter = array() )
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':LanguagesTable:getLanguages()');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('languages');
            $selectString = $sql->getSqlStringForSqlObject($select);
            try {
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                return $results;
            } catch (\Exception $e) {
                return  array();
            }
        }
        return $results;
    }

    public function getTotalLanguages( $filter = array() )
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':LanguagesTable:getTotalLanguages()');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('total' => new Expression("COUNT(languages_id)")));
            $select->from('languages');
            $selectString = $sql->getSqlStringForSqlObject($select);
            try {
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                return $results['total'];
            } catch (\Exception $e) {
                return  0;
            }
        }
        return $results;
    }

    public function getLanguage($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':LanguagesTable:getLanguage('.$id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('languages');
            $select->where(array(
                'languages_id' => $id
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }

    public function getLanguageByCodeMd5( $id )
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':LanguagesTable:getLanguageByCodeMd5('.$id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('languages');
            $select->where( "MD5(languages_file) = '{$id}' " );
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }

    public function getByCode( $code )
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':LanguagesTable:getByCode('.$code.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('languages');
            $select->where(array(
                'languages_file' => $code
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }
}