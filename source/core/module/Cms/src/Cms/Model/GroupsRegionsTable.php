<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 9:22 AM
 */

namespace Cms\Model;


use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class GroupsRegionsTable extends AppTable
{
    public function getGroupRestWorld()
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('group_regions');
            $select->where(array(
                'is_rest_world' => 1,
                'website_id' => $this->getWebsiteId(),
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->current();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function addGroupRestWorld()
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'website_id' => $this->getWebsiteId(),
                'is_rest_world' => 1,
                'group_regions_name' => 'Rest of the word',
                'is_published' => 1
            );
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
            $adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }

    }

    public function fetchAll( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('group_regions');
            $select->where(array(
                'website_id' => $this->getWebsiteId(),
            ));

            if( isset($where['group_regions_name']) ){
                $select->where->like('group_regions_name', "%{$where['group_regions_name']}%");
            }

            if( $this->hasPaging($where) ){
                $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
                $select->limit($where['limit']);
            }

            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function countAll( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(group_regions.group_regions_id)")));
        $select->from('group_regions');
        $select->where(array(
            'website_id' => $this->getWebsiteId(),
        ));

        if( isset($where['group_regions_name']) ){
            $select->where->like('group_regions_name', "%{$where['group_regions_name']}%");
        }

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function one($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('group_regions_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function save(GroupsRegions $g)
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'website_id' => $this->getWebsiteId(),
                //'is_rest_world' => 0,
                'country_id' => $g->country_id,
                'group_regions_name' => $g->group_regions_name,
                'regions' => $g->regions,
                'group_regions_type' => $g->group_regions_type,
                'is_published' => $g->is_published
            );
            $id = (int)$g->group_regions_id;
            if ($id == 0) {
                $this->tableGateway->insert($data);
                $id = $this->tableGateway->getLastInsertValue();
            } else {
                if ($this->one($id)) {
                    $this->tableGateway->update($data, array('group_regions_id' => $id));
                } else {
                    throw new \Exception('group_regions_id does not exist');
                }
            }
            $adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }

    }

    public function saveSyncShipZone(GroupsRegions $g, $ship_zone=array())
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'website_id' => $this->getWebsiteId(),
                //'is_rest_world' => 0,
                'country_id' => $g->country_id,
                'group_regions_name' => $g->group_regions_name,
                'regions' => $g->regions,
                'group_regions_type' => $g->group_regions_type,
                'is_published' => $g->is_published
            );
            $id = (int)$g->group_regions_id;
            if ($id == 0) {
                $this->tableGateway->insert($data);
                $id = $this->tableGateway->getLastInsertValue();
            } else {
                if ($this->one($id)) {
                    $this->tableGateway->update($data, array('group_regions_id' => $id));
                } else {
                    throw new \Exception('group_regions_id does not exist');
                }
            }

            /*save $ship_zone*/
            $this->getModelTable('ShippingTable')->delete(array('group_regions_id'=>$id, 'website_id' => $this->getWebsiteId()));
            
            if(!empty($ship_zone)){
                if(!empty($ship_zone[0])){
                    foreach ($ship_zone[0] as $key => $ship) {
                        if( !empty($ship['shipping_title'])
                            && !empty($ship['is_published'])
                            && ( ($ship['price_type'] == 0 && !empty($ship['price0'])) 
                                || ($ship['price_type'] == 1 && !empty($ship['price1'])) 
                                || ($ship['price_type'] == 2 && !empty($ship['fee_percent']) && !empty($ship['min_fee'])) ) ){
                            $row = array(
                                'transportation_id' => $ship['transportation_id'],
                                'group_regions_id' => $id,
                                'shipping_title' => $ship['shipping_title'],
                                'price_type' => $ship['price_type'],
                                'price' => 0,
                                'fee_percent' => 0,
                                'min_fee' => 0,
                                'conditions' => '',
                                'is_published' => $ship['is_published']);
                            if($ship['price_type'] == 0){
                                $row['price'] = $ship['price0'];
                            }else if($ship['price_type'] == 1){
                                $row['price'] = $ship['price1'];
                            }else if($ship['price_type'] == 2){
                                $row['fee_percent'] = $ship['fee_percent'];
                                $row['min_fee'] = $ship['min_fee'];
                            }
                            $shipping_id = $this->getModelTable('ShippingTable')->saveWithArray($row);
                        }
                    }
                }

                if( !empty($ship_zone[1]) ){
                    foreach ($ship_zone[1] as $key => $ship) {
                        if( !empty($ship['shipping_title'])
                            && !empty($ship['is_published'])
                            && !empty($ship['conditions']) ){
                            $row = array(
                                'transportation_id' => $ship['transportation_id'],
                                'group_regions_id' => $id,
                                'shipping_title' => $ship['shipping_title'],
                                'price_type' => 0,
                                'price' => 0,
                                'fee_percent' => 0,
                                'min_fee' => 0,
                                'conditions' => json_encode($ship['conditions']),
                                'is_published' => $ship['is_published']);
                            $shipping_id = $this->getModelTable('ShippingTable')->saveWithArray($row);
                        }
                    }
                }
            }

            $adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }

    }

    public function getIdCol()
    {
        return 'group_regions_id';
    }

    public function delete($ids)
    {
        $this->tableGateway->delete(array('group_regions_id' => $ids, 'website_id' => $this->getWebsiteId()));
        return TRUE;
    }

    public function getGroupsRegions(   $params = array()   )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('group_regions');
            $select->where(array(
                'group_regions.website_id' => $this->getWebsiteId(),
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getDistrictsInGroup(   $group_regions_id  = ''   )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('districts');
            $select->join('cities', 'cities.cities_id = districts.cities_id' , array('cities_title'));
            $select->join('group_regions', new Expression('FIND_IN_SET(cities.cities_id, group_regions.regions)'), array());
            $select->where(array(
                'group_regions.website_id' => $this->getWebsiteId(),
                'group_regions.group_regions_id' => $group_regions_id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getCities(   $group_regions_id  = ''   )
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('cities');
            $select->join('group_regions', new Expression("(group_regions.country_id LIKE cities.country_id OR FIND_IN_SET(cities.country_id,group_regions.country_id)>0 )") , array());
            $select->where(array(
                'group_regions.website_id' => $this->getWebsiteId(),
                'group_regions.group_regions_id' => $group_regions_id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getCountries()
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('country');
            $select->join('group_regions', new Expression("(group_regions.country_id LIKE country.id OR FIND_IN_SET(country.id,group_regions.country_id)>0 )") , array());
            $select->where(array(
                'group_regions.website_id' => $this->getWebsiteId()
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getRegions()
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('cities');
            $select->join('group_regions', new Expression("(group_regions.regions LIKE cities.cities_id OR FIND_IN_SET(cities.cities_id,group_regions.regions)>0 )") , array());
            $select->where(array(
                'group_regions.website_id' => $this->getWebsiteId()
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

    public function updateGroupRegions($ids, $data)
    {
        $this->tableGateway->update($data, array('group_regions_id' => $ids));
    }
} 