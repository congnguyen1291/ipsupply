<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

use Cms\Model\AppTable;

class ApiTable extends AppTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

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
        $select->from('api');
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
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function countAll($where = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("count(api_id)")));
        $select->from('api');
        if ($where) {
            $select->where($where);
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
    }

    public function getApi($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('api_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveApi(Api $api)
    {
        $data = array(
            'api_module' => strtolower($api->api_module),
            'api_class' => $api->api_class,
            'api_function' => $api->api_function,
            'is_published' => $api->is_published,
        );

        $id = (int)$api->api_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getApi($id)) {
                $this->tableGateway->update($data, array('api_id' => $id));
            } else {
                throw new \Exception('Api id does not exist');
            }
        }
    }

    public function getModuleApi($module_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('module_api');
        $select->where(array(
            'module_id' => $module_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

}