<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class DistrictsTable extends AppTable {

    public function getAll($city_id = '') {  
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':DistrictsTable:getAll('.$city_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('districts');
            if(empty($city_id)){
                $select->where(array(
                        'is_published' => 1,
                        'is_delete' => 0
                ));
            }else{
                $select->where(array(
                        'is_published' => 1,
                        'is_delete' => 0,
                        'cities_id' => $city_id
                ));
            }
            $select->order('districts_title ASC');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }
	
	public function getRow($id) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':DistrictsTable:getRow('.$id.')');
        $results = $cache->getItem($key);
        if(!$results){ 
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('districts');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'districts_id' => $id
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