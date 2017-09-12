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
use Zend\View\Model\ViewModel;

use Application\Model\AppTable;

class TransportationTable extends AppTable {

    public function getTransportationLocal()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':TransportationTable:getTransportationLocal');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('transportation');
            $select->where(array(
                'transportation.is_published' => 1,
                'transportation.is_delete' => 0,
                'transportation.transportation_type' => 2,
                'transportation.website_id'=>$this->getWebsiteId()
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

    public function getFreeTransportationForCityAndTransportation($cities_id, $transportation_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':TransportationTable:getFreeTransportationForCityTransportation('.$cities_id.','.$transportation_id.')');
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
                'transportation.transportation_id' => $transportation_id,
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

    public function getFreeTransportationForTransportation($transportation_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':TransportationTable:getFreeTransportationForTransportation('.$transportation_id.')');
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

    public function removeAllTransportationOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function getTransportationOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('transportation');
        $select->where(array(
            'website_id' => $website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function getTransportationById($transportation_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('transportation');
        $select->where(array(
            'transportation_id' => $transportation_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function insertTransportation($data){
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    } 

    public function getLastestId(){
        return $this->tableGateway->getLastInsertValue();
    }    
}