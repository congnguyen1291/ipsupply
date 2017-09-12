<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Application\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

use Application\Model\AppTable;

class AssignUserTable extends AppTable
{
    public function fetchAll($where = 0, $order = '', $intPage, $intPageSize)
    {
        $adapter = $this->tableGateway->getAdapter();
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('area');
        if ($where) {
            $select->where($where);
        }
        if ($order) {
            $select->order($order);
        }
        $select->limit($intPageSize);
        $select->offset($intPage);
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $e) {
            echo $e->getMessage();die();
            throw new \Exception($e->getMessage());
        }
    }

    public function countAll($where = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("count(area_id)")));
        $select->from('area');
        if ($where) {
            $select->where($where);
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
    }

    public function getAreaById($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('area_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveArea($area)
    {
        $data = array(
            'country_id' => $area['country_id'],
            'area_title' => $area['area_title'],
            'area_alias' => $area['area_alias'],
            'is_published' => $area['is_published']
        );
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        try {
            $this->tableGateway->insert($data);
            $id = $adapter->getDriver()->getConnection()->getLastGeneratedValue();
            return $id;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

}