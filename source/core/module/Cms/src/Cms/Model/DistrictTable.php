<?php

namespace Cms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

use Cms\Model\AppTable;

class DistrictTable extends AppTable 
{

    
    public function find($str)
    {
        $code = $str;
        $str = $this->toAlias($str);
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('districts');
        $select->columns(array(
            '*',
            'data' => 'districts_id',
            'value' => 'districts_title',
        ));
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0
        ));
        $select->where("(LCASE(districts_title) LIKE '%{$str}%')");
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getAll($city_id = '') {
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
            return $results;
        }catch(\Exception $ex){
            return array();
        }
    }
	
	public function getRow($id) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('districts');
        $select->where(array(
            'is_delete' => 0,
            'districts_id' => $id
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results;
        }catch(\Exception $ex){
            return array();
        }
    }

    public function getDistrictByIds($ids) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select = $sql->select()->columns(array('*','id' => 'districts_id', 'text' => 'districts_title'));
        $select->from('districts');
        $select->where(array(
            'is_delete' => 0,
            'districts_id' => $ids
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch(\Exception $ex){
            return array();
        }
    }

}