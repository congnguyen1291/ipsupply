<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 11:54 AM
 */
namespace Cms\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class TransportationTable extends AppTable{
    
    public function fetchAll($where = '', $order = '', $intPage, $intPageSize)
    {
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }
        $where1 = "WHERE is_delete = 0 AND website_id={$this->getWebsiteId()}";
        if ($where) {
            $where1 .= " AND {$where}";
        }

        $sql = "SELECT *
                FROM {$this->tableGateway->table}
                {$where1}
                {$order}
                LIMIT {$intPage}, {$intPageSize}";
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        return $result;
    }

    public function getTransportation($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('transportation_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveTransportation(Transportation $trans)
    {
        $data = array(
            'website_id' => $this->getWebsiteId(),
            'shipping_class' => $trans->shipping_class,
            'transportation_type' => $trans->transportation_type,
            'transportation_title' => $trans->transportation_title,
            'transportation_description' => $trans->transportation_description,
            'is_published' => $trans->is_published,
            'is_delete' => $trans->is_delete,
        );
        $id = (int) $trans->transportation_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getTransportation($id)) {
                $this->tableGateway->update($data, array('transportation_id' => $id));
            } else {
                throw new \Exception('id does not exist');
            }
        }
    }

    public function deleteCategory($id)
    {
        $this->tableGateway->delete(array('categories_id' => (int) $id));
    }
    public function getLastestId(){
        return $this->tableGateway->getLastInsertValue();
    }

    public function filter($data){
        $where = array();
        $where[] = 'is_delete=0 AND website_id = '.$this->getWebsiteId();
        if(isset($data['categories_title']) && trim($data['categories_title']) != '' ){
            $data['categories_title'] = mb_strtolower(trim($data['categories_title']));
            $where[] = "LCASE(categories_title) LIKE '%{$data['categories_title']}%'";
        }
        if(isset($data['is_published'])){
            $where[] = "is_published=1";
        }
        $where = implode(' AND ', $where);
        $sql = "SELECT *
                FROM `categories`
                WHERE is_delete=0{$where}";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
            return $result;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    protected function getIdCol(){
        return 'transportation_id';
    }

    public function removeTransportationOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function all($where = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('transportation');
            if(!empty($where) && is_array($where) ){
                $where = array_merge($where, array('website_id' => $this->getWebsiteId()));
                $select->where($where);
            }
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function find($str)
    {
        $code = $str;
        $str = $this->toAlias($str);
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array(
            '*',
            'data' => 'transportation_id',
            'value' => 'transportation_title',
        ));
        $select->from('transportation');
        $select->where(array(
            'transportation.shipping_class' => 1,
            'transportation.transportation_type' => 0
        ));
        $select->where("(LCASE(transportation_title) LIKE '%{$str}%')");
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getTransportationByIds($ids) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select = $sql->select()->columns(array('*','id' => 'transportation_id', 'text' => 'transportation_title'));
        $select->from('transportation');
        $select->where(array(
            'transportation.transportation_id' => $ids,
            'transportation.shipping_class' => 1,
            'transportation.transportation_type' => 0
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