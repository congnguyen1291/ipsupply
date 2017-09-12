<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 11:54 AM
 */
namespace Cms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;

use Cms\Model\AppTable;

class ShippingTable extends AppTable{

    public function getShipping($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('shipping_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
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
            $updateOrdering = "UPDATE `shipping` SET `ordering` = `ordering`+1 WHERE `website_id` = '{$this->getWebsiteId()}' ";
            $adapter->query($updateOrdering,$adapter::QUERY_MODE_EXECUTE);

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

    public function saveWithDistricts( Shipping $ship, $districts = array())
    {
        $website_id = $this->getWebsiteId();
        $data = array(
            'website_id' => $website_id,
            'group_regions_id' => $ship->group_regions_id,
            'shipping_title' => $ship->shipping_title,
            'shipping_type' => $ship->shipping_type,
            'shipping_from' => str_replace(',','',$ship->shipping_from),
            'shipping_to' => str_replace(',','',$ship->shipping_to),
            'shipping_value' => str_replace(',','',$ship->shipping_value),
            'shipping_fast_value' => str_replace(',','',$ship->shipping_fast_value),
            'shipping_time' => $ship->shipping_time,
        );
        $id = 0;
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            if ( empty($ship->shipping_id) ) {
                $updateOrdering = "UPDATE `shipping` SET `ordering` = `ordering`+1 WHERE `website_id` = '{$this->getWebsiteId()}' ";
                $adapter->query($updateOrdering,$adapter::QUERY_MODE_EXECUTE);

                $this->tableGateway->insert($data);
                $id = $this->tableGateway->getLastInsertValue();
            } else {
                $id = (int) $ship->shipping_id;
                if ($this->getShipping($id)) {
                    $this->tableGateway->update($data, array('shipping_id' => $id));
                } else {
                    throw new \Exception('id does not exist');
                }
            }
            if( !empty($id) ){
                $adapter = $this->tableGateway->getAdapter();
                $del = "DELETE FROM shipping_districts WHERE shipping_id={$id}";
                $adapter->query($del, $adapter::QUERY_MODE_EXECUTE);
                if( !empty($districts) ){
                    $val = array();
                    foreach ($districts as $districts_id => $district) {
                        $shipping_fixed_value = $district['shipping_fixed_value'];
                        if( empty($shipping_fixed_value) ){
                            $shipping_fixed_value = 0;
                        }
                        $shipping_fixed_value = str_replace(',','',$shipping_fixed_value);
                        $shipping_fast_fixed_value = $district['shipping_fast_fixed_value'];
                        if( empty($shipping_fast_fixed_value) ){
                            $shipping_fast_fixed_value = 0;
                        }
                        $shipping_fast_fixed_value = str_replace(',','',$shipping_fast_fixed_value);
                        $shipping_fixed_time = $district['shipping_fixed_time'];
                        if( empty($shipping_fixed_time) ){
                            $shipping_fixed_time = '';
                        }
                        $no_shipping = $district['no_shipping'];
                        if( empty($no_shipping) ){
                            $no_shipping = 0;
                        }
                        if( (!empty($shipping_fixed_value)
                            || !empty($shipping_fast_fixed_value)
                            || !empty($shipping_fixed_time)
                            || !empty($no_shipping) )
                            && !empty($website_id)
                            && !empty($districts_id) ){
                            $val[] = "({$website_id}, {$districts_id}, {$id}, {$shipping_fixed_value}, {$shipping_fast_fixed_value}, '{$shipping_fixed_time}', {$no_shipping})";
                        }
                    }
                    if( !empty($val) ){
                        $val = implode(',', $val);
                        $insert = "INSERT INTO shipping_districts(website_id,districts_id,shipping_id,shipping_fixed_value,shipping_fast_fixed_value,shipping_fixed_time,no_shipping) VALUES {$val}";
                        $adapter->query($insert, $adapter::QUERY_MODE_EXECUTE);
                    }
                }
            }
            $adapter->getDriver()->getConnection()->commit();
            return $id;
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            //echo $ex->getMessage();die();
            throw new \Exception($ex->getMessage());
        }
    }

    public function getLastestId(){
        return $this->tableGateway->getLastInsertValue();
    }

    protected function getIdCol(){
        return 'shipping_id';
    }

    public function delete($where)
    {
        $this->tableGateway->delete($where);
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
            if( isset($params['shipping_title']) ){
                $select->where->like('shipping_title', "%{$params['shipping_title']}%");
            }
            $select->group('shipping.shipping_id');
            
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

            if( isset($params['shipping_title']) ){
                $select->where->like('shipping_title', "%{$params['shipping_title']}%");
            }

			$select->group('shipping.shipping_id');

            if( $this->hasPaging($params) ){
                $select->offset($this->getOffsetPaging($params['page'], $params['limit']));
                $select->limit($params['limit']);
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
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)') , array('cities_id','cities_title','country_id'), 'left');
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
            $select->join('cities', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)') , array('cities_id','cities_title','country_id'), 'left');
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

    public function getShippingOfCity(   $cities_id  = ''   )
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
                'cities.cities_id' => $cities_id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function updateOrder($data){
        $sql = "INSERT INTO shipping(shipping_id, ordering) VALUES ";
        $val = array();
        foreach($data as $id => $order){
            $val[] = "({$id}, {$order})";
        }
        $val = implode(',', $val);
        $sql .= $val;
        $sql .= " ON DUPLICATE KEY UPDATE ordering=VALUES(ordering)";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function updateShipping($ids, $data)
    {
        $this->tableGateway->update($data, array('shipping_id' => $ids));
    }

}