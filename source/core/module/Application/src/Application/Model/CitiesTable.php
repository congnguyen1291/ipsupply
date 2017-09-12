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

class CitiesTable extends AppTable {

    public function getAll() {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getAll');
        $results = $cache->getItem($key);
        if(!$results){
            try{        
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('cities');
                $select->where(array(
                    'is_published' => 1,
                    'is_delete' => 0
                ));
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
	
	public function getRow($id) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getRow('.$id.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('cities');
                $select->where(array(
                    'is_published' => 1,
                    'is_delete' => 0,
                    'cities_id' => $id
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key,$results);
            }catch(\Exception $e){
                $results = array();
            }
        }
        return $results;
    }

    public function getCitiesFromStringId($str, $op = ',')
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getCitiesFromStringId('.$str.';'.$op.')');
        $results = $cache->getItem($key);
        if(!$results){
            $id = explode($op, $str);
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('cities');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0
            ));
            $select->where->in('cities_id', $id);
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

    public function getTransportationForCity($cities_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getTransportationForCity('.$cities_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array('shipping_class', 'transportation_type', 'transportation_title', 'transportation_description'));
            $select->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)'), array());
            $select->where(array(
                'shipping.is_published' => 1,
                'transportation.is_published' => 1,
                'transportation.is_delete' => 0,
                'group_regions.is_rest_world' => 0,
                'cities.cities_id' => $cities_id,
                'transportation.website_id'=>$this->getWebsiteId(),
                'group_regions.website_id'=>$this->getWebsiteId(),
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getFreeTransportationForCity($cities_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getFreeTransportationForCity('.$cities_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array('shipping_class', 'transportation_type', 'transportation_title', 'transportation_description'));
            $select->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)'), array());
            $select->where(array(
                'transportation.shipping_class' => 0,
                'transportation.transportation_type' => 1,
                'shipping.is_published' => 1,
                'transportation.is_published' => 1,
                'transportation.is_delete' => 0,
                'group_regions.is_rest_world' => 0,
                'cities.cities_id' => $cities_id,
                'transportation.website_id'=>$this->getWebsiteId(),
                'group_regions.website_id'=>$this->getWebsiteId(),
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                if(!empty($results)){
                    $results = $results[0];
                }
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getFlatRateTransportationNormalForCity($cities_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getFlatRateTransportationNormalForCity('.$cities_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array('shipping_class', 'transportation_type', 'transportation_title', 'transportation_description'));
            $select->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)'), array());
            $select->where(array(
                'transportation.shipping_class' => 0,
                'transportation.transportation_type' => 0,
                'shipping.is_published' => 1,
                'transportation.is_published' => 1,
                'transportation.is_delete' => 0,
                'group_regions.is_rest_world' => 0,
                'cities.cities_id' => $cities_id,
                'transportation.website_id'=>$this->getWebsiteId(),
                'group_regions.website_id'=>$this->getWebsiteId(),
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getFlatRateTransportationSpeacialForCity($cities_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getFlatRateTransportationSpeacialForCity('.$cities_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array('shipping_class', 'transportation_type', 'transportation_title', 'transportation_description'));
            $select->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)'), array());
            $select->where(array(
                'transportation.shipping_class' => 1,
                'transportation.transportation_type' => 0,
                'shipping.is_published' => 1,
                'transportation.is_published' => 1,
                'transportation.is_delete' => 0,
                'group_regions.is_rest_world' => 0,
                'cities.cities_id' => $cities_id,
                'transportation.website_id'=>$this->getWebsiteId(),
                'group_regions.website_id'=>$this->getWebsiteId(),
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getOneFlatRateTransportationSpeacialWithTransportation($transportation_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getOneFlatRateTransportationSpeacialWithTransportation('.$transportation_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array('shipping_class', 'transportation_type', 'transportation_title', 'transportation_description'));
            $select->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)'), array());
            $select->where(array(
                'transportation.shipping_class' => 1,
                'transportation.transportation_type' => 0,
                'shipping.is_published' => 1,
                'transportation.is_published' => 1,
                'transportation.is_delete' => 0,
                'group_regions.is_rest_world' => 0,
                'transportation.transportation_id' => $transportation_id,
                'transportation.website_id'=>$this->getWebsiteId(),
                'group_regions.website_id'=>$this->getWebsiteId(),
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                if(!empty($results)){
                    $results = $results[0];
                }
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getTransportationRestWorld()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getTransportationRestWorld');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array('shipping_class', 'transportation_type', 'transportation_title', 'transportation_description'));
            $select->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
            $select->where(array(
                'shipping.is_published' => 1,
                'transportation.is_published' => 1,
                'transportation.is_delete' => 0,
                'group_regions.is_rest_world' => 1,
                'transportation.website_id'=>$this->getWebsiteId(),
                'group_regions.website_id'=>$this->getWebsiteId(),
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getFreeTransportationRestWorld()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getFreeTransportationRestWorld');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array('shipping_class', 'transportation_type', 'transportation_title', 'transportation_description'));
            $select->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
            $select->where(array(
                'transportation.shipping_class' => 0,
                'transportation.transportation_type' => 1,
                'shipping.is_published' => 1,
                'transportation.is_published' => 1,
                'transportation.is_delete' => 0,
                'group_regions.is_rest_world' => 1,
                'transportation.website_id'=>$this->getWebsiteId(),
                'group_regions.website_id'=>$this->getWebsiteId(),
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                if(!empty($results)){
                    $results = $results[0];
                }
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getFlatRateTransportationRestWorldNormal()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getFlatRateTransportationRestWorldNormal');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array('shipping_class', 'transportation_type', 'transportation_title', 'transportation_description'));
            $select->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
            $select->where(array(
                'transportation.shipping_class' => 0,
                'transportation.transportation_type' => 0,
                'shipping.is_published' => 1,
                'transportation.is_published' => 1,
                'transportation.is_delete' => 0,
                'group_regions.is_rest_world' => 1,
                'transportation.website_id'=>$this->getWebsiteId(),
                'group_regions.website_id'=>$this->getWebsiteId(),
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getFlatRateTransportationRestWorldSpeacial()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getFlatRateTransportationRestWorldSpeacial');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array('shipping_class', 'transportation_type', 'transportation_title', 'transportation_description'));
            $select->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
            $select->where(array(
                'transportation.shipping_class' => 1,
                'transportation.transportation_type' => 0,
                'shipping.is_published' => 1,
                'transportation.is_published' => 1,
                'transportation.is_delete' => 0,
                'group_regions.is_rest_world' => 1,
                'transportation.website_id'=>$this->getWebsiteId(),
                'group_regions.website_id'=>$this->getWebsiteId(),
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getOneFlatRateTransportationRestWorldSpeacial($transportation_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getOneFlatRateTransportationRestWorldSpeacial('.$transportation_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array('shipping_class', 'transportation_type', 'transportation_title', 'transportation_description'));
            $select->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
            $select->where(array(
                'transportation.shipping_class' => 1,
                'transportation.transportation_type' => 0,
                'shipping.is_published' => 1,
                'transportation.is_published' => 1,
                'transportation.is_delete' => 0,
                'group_regions.is_rest_world' => 1,
                'transportation.transportation_id' => $transportation_id,
                'transportation.website_id'=>$this->getWebsiteId(),
                'group_regions.website_id'=>$this->getWebsiteId(),
            ));
            try {
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                if(!empty($results)){
                    $results = $results[0];
                }
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getGroupRegionsOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('group_regions');
        $select->where(array(
            'group_regions.website_id'=>$website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertGroupRegions($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('group_regions');
        $insert->columns(array('website_id','is_rest_world','group_regions_name','regions','group_regions_type','is_published'));
        $insert->values(array(
            'website_id' => $data['website_id'],
            'is_rest_world' => $data['is_rest_world'],
            'group_regions_name' => $data['group_regions_name'],
            'regions' => $data['regions'],
            'group_regions_type' => $data['group_regions_type'],
            'is_published' => $data['is_published']
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function deleteGroupRegionsOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $delete  = $sql->delete('group_regions');
        $delete->where(array(
            'website_id' => $website_id
        ));
        try{
            $deleteString = $sql->getSqlStringForSqlObject($delete);
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getShippingOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('shipping');
        $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array());
        $select->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
        $select->where(array(
            'transportation.shipping_class' => 0,
            'transportation.transportation_type' => 1,
            'shipping.is_published' => 1,
            'transportation.is_published' => 1,
            'transportation.is_delete' => 0,
            'group_regions.is_rest_world' => 0,
            'transportation.website_id'=>$website_id,
            'group_regions.website_id'=>$website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertShipping($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('shipping');
        $insert->columns(array('website_id','transportation_id','group_regions_id','shipping_title','price_type','price','fee_percent','min_fee','conditions','is_published'));
        $insert->values(array(
            'website_id' => $data['website_id'],
            'transportation_id' => $data['transportation_id'],
            'group_regions_id' => $data['group_regions_id'],
            'shipping_title' => $data['shipping_title'],
            'price_type' => $data['price_type'],
            'price' => $data['price'],
            'fee_percent' => $data['fee_percent'],
            'min_fee' => $data['min_fee'],
            'conditions' => $data['conditions'],
            'is_published' => $data['is_published'],
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function deleteShippingOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $delete  = $sql->delete('shipping');
        $delete->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array());
        $delete->join('group_regions', 'group_regions.group_regions_id = shipping.group_regions_id', array());
        $delete->where(array(
            'transportation.website_id'=>$website_id,
            'group_regions.website_id'=>$website_id,
        ));
        try{
            $deleteString = $sql->getSqlStringForSqlObject($delete);
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getCityOfCountry($id, $country_id) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getCityOfCountry('.$id.','.$country_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('cities');
                $select->where(array(
                    'is_published' => 1,
                    'is_delete' => 0,
                    'cities_id' => $id,
                    'country_id' => $country_id,
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key,$results);
            }catch(\Exception $e){
                $results = array();
            }
        }
        return $results;
    }

    public function getCitiesOfCountry($country_id) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CitiesTable:getCitiesOfCountry('.$country_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('cities');
                $select->where(array(
                    'is_published' => 1,
                    'is_delete' => 0,
                    'country_id' => $country_id,
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $e){
                $results = array();
            }
        }
        return $results;
    }

}