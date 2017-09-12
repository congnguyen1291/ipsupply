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

class WardsTable extends AppTable {
    
    public function getAll($districts_id = null) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':WardsTable:fetchAll('.(is_array($districts_id)? implode('-',$districts_id) : $districts_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
            	$select = $this->sql->select();
            	if(empty($districts_id)){
            		$select->where(array(
        				'is_published' => 1,
        				'is_delete' => 0
            		));
            	}else{
            		$select->where(array(
        				'is_published' => 1,
        				'is_delete' => 0,
        				'districts_id' => $districts_id
            		));
            	}
            	$select->order('wards_title ASC');
            	$results = $this->fetchAll($select);
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
	
	public function getRow($id) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':WardsTable:getRow('.$id. ')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
				$sql = new Sql($adapter);
				$select = $sql->select();
				$select->from('wards');
                $select->where(array(
                        'is_published' => 1,
                        'is_delete' => 0,
        				'wards_id' => $id
                        
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

}