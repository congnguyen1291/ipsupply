<?php

namespace Cms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;


use Cms\Model\AppTable;

class CityTable extends AppTable {

    
    public function getTotalCities( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression('COUNT(cities.cities_id)')));
        $select->from('cities');
        if( !empty($params['country_id']) ){
            $select->where(array(
                    'country_id' => $params['country_id']
                ));
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $results->current();
            $results = 0;
            if( !empty($row) && !empty($row['total']) ){
                $results = $row['total'];
            }
        }catch(\Exception $ex){
            $results = 0;
        }
        return $results;
    }

    public function getCities( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('cities');

        if( !empty($params['country_id']) ){
            $select->where(array(
                    'country_id' => $params['country_id']
                ));
        }

        if( isset($params['page']) 
            && !empty($params['itemsPerPage']) ){
            $limit = $params['itemsPerPage'];
            $offset = $this->getOffsetPaging($params['page'], $limit);
            $select->offset($offset);
            $select->limit($limit);
        }
        
        $select->order('cities.cities_id desc');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
        }catch(\Exception $ex){
            $results = array();
        }
        return $results;
    }

    public function getCity( $cities_id )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('cities');
        $select->where(array(
                    'cities_id' => $cities_id
                ));
        $select->order('cities.cities_id desc');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
        }catch(\Exception $ex){
            $results = array();
        }
        return $results;
    }

    public function find($params = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('cities');
        $select->columns(array(
            '*',
            'data' => 'cities_id',
            'value' => 'cities_title',
        ));
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0
        ));

        if( !empty($params['cities_title']) ){
            $str = $params['cities_title'];
            $str = $this->toAlias($str);
            $select->where("(LCASE(cities_title) LIKE '%{$str}%')");
        }

        if( !empty($params['country_id']) ){
            $select->where(array('country_id' => $params['country_id']));
        }
        
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
	
	public function getRow($id) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('cities');
        $select->where(array(
            'is_delete' => 0,
            'cities_id' => $id
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

    public function getCitiesByIds($ids) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select = $sql->select()->columns(array('*','id' => 'cities_id', 'text' => 'cities_title'));
        $select->from('cities');
        $select->where(array(
            'is_delete' => 0,
            'cities_id' => $ids
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