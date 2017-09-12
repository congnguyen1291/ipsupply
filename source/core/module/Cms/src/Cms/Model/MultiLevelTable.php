<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Cms\Model;

use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\TableGateway\TableGateway;
//use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class MultiLevelTable extends GeneralTable
{
    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway);
    }

    public function fetchAll($exp_id = 0)
    {
//        $results = $this->cache->getItem('topcare_' . $this->tableGateway->table . '_fetchall_' . $exp_id);
//        if (!$results) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select($this->tableGateway->table);
        $select->order(array(
            $this->getOrderCol() => 'ASC',
            'date_create' => 'DESC',
        ));
        if (!empty($exp_id) && !is_array($exp_id)) {
            $select->where("{$this->getIdCol()} != {$exp_id} AND parent_id != {$exp_id}");
        }elseif (!empty($exp_id)) {
            $select->where($exp_id);
        }
        $select->where("is_delete=0");
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//        }
        return $results;
    }

    public function countAll($exp_id = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select($this->tableGateway->table)->columns(array('total' => new Expression("count({$this->tableGateway->table}.{$this->tableGateway->table}_id)")));
        $select->order(array(
            $this->getOrderCol() => 'ASC',
            'date_create' => 'DESC',
        ));
        if ($exp_id) {
            $select->where("{$this->getIdCol()} != {$exp_id} AND parent_id != {$exp_id}");
        }
        $select->where("is_delete=0 AND website_id={$_SESSION['CMSMEMBER']['website_id']}");
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->current();
        return $results->total;
    }


    public function getById($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array($this->getIdCol() => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    protected function saveData($data, $id)
    {
        try {
            if (!$id) {
                $sql = 'SELECT max(ordering) as current_order FROM ' . $this->tableGateway->table . ' WHERE parent_id=' . $data['parent_id'];
                $adapter = $this->tableGateway->getAdapter();
                $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                $row = $result->current();
                if ($row) {
                    $data['ordering'] = $row->current_order + 1;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->getLastestId();
        } else {
            if ($this->getById($id)) {
                $this->tableGateway->update($data, array($this->getIdCol() => $id));
            } else {
                throw new \Exception('id does not exist');
            }
        }
        return $id;
    }

    public function getLastestId()
    {
        return $this->tableGateway->getLastInsertValue();
    }

    public function getOffsetPaging($page, $itemsPerPage)
    {
        $offset = 0;
        if ( $page > 1 ) {
            $offset = ($page - 1) * $itemsPerPage;
        }
        return $offset;
    }

}