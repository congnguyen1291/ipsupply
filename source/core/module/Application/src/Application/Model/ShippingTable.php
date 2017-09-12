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

use Application\Model\AppTable;

class ShippingTable extends AppTable{

    public function getShipping($shipping_id)
    {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('shipping');
        $select->join('shipping_districts', new Expression(' (shipping.shipping_id = shipping_districts.shipping_id ) '), array('shipping_fixed_value','shipping_fixed_time','shipping_fast_fixed_value','no_shipping'), 'left');
        $select->join('group_regions', 'shipping.group_regions_id = group_regions.group_regions_id', array('is_rest_world', 'group_regions_name', 'regions', 'group_regions_type'));
        $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)') , array('cities_id','cities_title','country_id'), 'left');
        $select->where(array(
            'shipping.website_id' => $this->getWebsiteId(),
            'shipping.shipping_id' => $shipping_id,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
        }catch(\Exception $ex){
            $results = array();
        }
        return $results;
    }

    public function getShippingOfCity($shipping_id, $cities_id)
    {
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('shipping');
        $select->join('shipping_districts', new Expression(' (shipping.shipping_id = shipping_districts.shipping_id ) '), array('shipping_fixed_value','shipping_fixed_time','shipping_fast_fixed_value','no_shipping'), 'left');
        $select->join('group_regions', 'shipping.group_regions_id = group_regions.group_regions_id', array('is_rest_world', 'group_regions_name', 'regions', 'group_regions_type'));
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)') , array('cities_id','cities_title','country_id'));
        $select->where(array(
            'shipping.website_id' => $this->getWebsiteId(),
            'shipping.shipping_id' => $shipping_id,
            'cities.cities_id' => $cities_id,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
        }catch(\Exception $ex){
            $results = array();
        }
        return $results;
    }

    public function saveShipping($row)
    {
        $data = array(
            'website_id' => $this->getWebsiteId(),
            'transportation_id' => $row['transportation_id'],
            'group_regions_id' => $row['group_regions_id'],
            'shipping_title' => $row['shipping_title'],
            'price_type' => $row['price_type'],
            'price' => $row['price'],
            'fee_percent' => $row['fee_percent'],
            'min_fee' => $row['min_fee'],
            'conditions' => $row['conditions'],
            'is_published' => $row['is_published'],
        );
        $id = 0;
        if (empty($row['shipping_id'])) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $id = (int) $row['shipping_id'];
            if ($this->one($id)) {
                $this->tableGateway->update($data, array('shipping_id' => $id));
            } else {
                throw new \Exception('id does not exist');
            }
        }
        return $id;
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array('shipping_id' => $id, 'website_id'=>$this->getWebsiteId()));
    }

    public function getTotalShippings(   $params = array()   )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('total' => new Expression('COUNT(shipping.shipping_id)')));
            $select->from('shipping');
            $select->join('group_regions', 'shipping.group_regions_id = group_regions.group_regions_id', array('is_rest_world', 'group_regions_name', 'regions', 'group_regions_type'));
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)') , array('cities_id','cities_title','country_id'), 'left');
            $select->where(array(
                'shipping.website_id' => $this->getWebsiteId(),
            ));
            if( !empty($params['group_regions_id']) ){
                $select->where(array(
                    'shipping.group_regions_id' => $params['group_regions_id'],
                ));
            }
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $results->current();
            $results = 0;
            if( !empty($row) && !empty($row['total']) ){
                $results = $row['total'];
            }
            return $results;
        }catch (\Exception $ex){
            return 0;
        }
    }

    public function getShippings(   $params = array()   )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('group_regions', 'shipping.group_regions_id = group_regions.group_regions_id', array('is_rest_world', 'group_regions_name', 'regions', 'group_regions_type'));
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)') , array('cities_id','cities_title','country_id'), 'left');
            $select->where(array(
                'shipping.website_id' => $this->getWebsiteId(),
            ));
            if( !empty($params['group_regions_id']) ){
                $select->where(array(
                    'shipping.group_regions_id' => $params['group_regions_id'],
                ));
            }
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getDistrictsShippings( $shipping_id = '' )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping_districts');
            $select->join('shipping', 'shipping.shipping_id = shipping_districts.shipping_id', array());
            $select->join('group_regions', 'shipping.group_regions_id = group_regions.group_regions_id', array('is_rest_world', 'group_regions_name', 'regions', 'group_regions_type'));
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)') , array('cities_id','cities_title','country_id'));
            $select->where(array(
                'shipping.website_id' => $this->getWebsiteId(),
                'shipping_districts.shipping_id' => $shipping_id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getTotalShippingOfCity(   $cities_id  = ''   )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('total' => new Expression('COUNT(shipping.shipping_id)')));
            $select->from('shipping');
            $select->join('group_regions', 'shipping.group_regions_id = group_regions.group_regions_id', array('is_rest_world', 'group_regions_name', 'regions', 'group_regions_type'));
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)') , array('cities_id','cities_title','country_id'));
            $select->where(array(
                'shipping.website_id' => $this->getWebsiteId(),
                'cities.cities_id' => $cities_id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $results->current();
            $results = 0;
            if( !empty($row) && !empty($row['total']) ){
                $results = $row['total'];
            }
            return $results;
        }catch (\Exception $ex){
            return 0;
        }
    }

    public function getShippingsOfCity(   $cities_id  = ''   )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('group_regions', 'shipping.group_regions_id = group_regions.group_regions_id', array('is_rest_world', 'group_regions_name', 'regions', 'group_regions_type'));
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)') , array('cities_id','cities_title','country_id'));
            $select->where(array(
                'shipping.website_id' => $this->getWebsiteId(),
                'cities.cities_id' => $cities_id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getShippingOfCityAndDistricts(   $cities_id  = 0, $districts_id  = 0   )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('shipping_districts', new Expression(' (shipping.shipping_id = shipping_districts.shipping_id AND shipping_districts.districts_id = '.$districts_id.') '), array('districts_id','shipping_fixed_value','shipping_fixed_time','shipping_fast_fixed_value','no_shipping'), 'left');
            $select->join('group_regions', 'shipping.group_regions_id = group_regions.group_regions_id', array('is_rest_world', 'group_regions_name', 'regions', 'group_regions_type'));
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)') , array('cities_id','cities_title','country_id'));
            $select->where(array(
                'shipping.website_id' => $this->getWebsiteId(),
                'cities.cities_id' => $cities_id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getShippingOfCountry(   $country_id  = 0 )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('group_regions', 'shipping.group_regions_id = group_regions.group_regions_id', array('is_rest_world', 'group_regions_name', 'regions', 'group_regions_type'));
            $select->join('country', new Expression("(group_regions.country_id LIKE country.id OR FIND_IN_SET(country.id,group_regions.country_id)>0 )") , array());
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions) > 0 AND (country.id = cities.country_id) ') , array('cities_id','cities_title','country_id'), 'left');
            $select->join('districts', new Expression(' districts.cities_id = cities.cities_id ') , array(), 'left');
            $select->join('shipping_districts', new Expression(' (shipping.shipping_id = shipping_districts.shipping_id) AND (districts.districts_id = shipping_districts.districts_id) '), array('districts_id','shipping_fixed_value','shipping_fixed_time','shipping_fast_fixed_value','no_shipping'), 'left');
            $select->where(array(
                'shipping.website_id' => $this->getWebsiteId(),
                'country.id' => $country_id
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getShippingWithCountry(   $shipping_id  = 0, $country_id  = 0 )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('group_regions', 'shipping.group_regions_id = group_regions.group_regions_id', array('is_rest_world', 'group_regions_name', 'regions', 'group_regions_type'));
            $select->join('country', new Expression("(group_regions.country_id LIKE country.id OR FIND_IN_SET(country.id,group_regions.country_id)>0 )") , array());
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions) > 0 AND (country.id = cities.country_id)') , array('cities_id','cities_title','country_id'), 'left');
            $select->join('districts', new Expression(' districts.cities_id = cities.cities_id ') , array(), 'left');
            $select->join('shipping_districts', new Expression(' (shipping.shipping_id = shipping_districts.shipping_id)  AND (districts.districts_id = shipping_districts.districts_id) '), array('districts_id','shipping_fixed_value','shipping_fixed_time','shipping_fast_fixed_value','no_shipping'), 'left');
            
            $select->where(array(
                'shipping.website_id' => $this->getWebsiteId(),
                'shipping.shipping_id' => $shipping_id,
                'country.id' => $country_id
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->current();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getShippingWithCityAndDistricts(   $shipping_id  = 0, $cities_id  = 0, $districts_id  = 0   )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('shipping_districts', new Expression(' (shipping.shipping_id = shipping_districts.shipping_id AND shipping_districts.districts_id = '.$districts_id.') '), array('districts_id','shipping_fixed_value','shipping_fixed_time','shipping_fast_fixed_value','no_shipping'), 'left');
            $select->join('group_regions', 'shipping.group_regions_id = group_regions.group_regions_id', array('is_rest_world', 'group_regions_name', 'regions', 'group_regions_type'));
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)') , array('cities_id','cities_title','country_id'));
            $select->where(array(
                'shipping.website_id' => $this->getWebsiteId(),
                'cities.cities_id' => $cities_id,
                'shipping.shipping_id' => $shipping_id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->current();
        }catch (\Exception $ex){
            return array();
        }
    }

}