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
use Zend\Db\Sql\Expression;

use Cms\Model\AppTable;

class ModulesTable  extends AppTable
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
        $select->from('module');
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
        $select = $sql->select()->columns(array('total' => new Expression("count(module_id)")));
        $select->from('module');
        if ($where) {
            $select->where($where);
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
    }

    public function getModule($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('module');
        $select->where(array(
            'module_id' => $id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        if (!$row) {
            throw new \Exception('Row does not exists');
        }
        $g = new Modules();
        $g->exchangeArray($row);
        return $g;
    }

    public function saveModule(Modules $g, $permissions = array(), $apis = array())
    {

        $data = array(
            'module_name' => $g->module_name,
            'module_description' => $g->module_description,
            'price' => $g->price,
            'is_default' => $g->is_default,
            'date_create' => $g->date_create,
            'date_update' => $g->date_update,
        );

        $id = (int)$g->module_id;
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        $sql = new Sql($adapter);
        if ($id == 0) {
            //$this->tableGateway->insert($data);
            $query = $sql->insert('module');
            $query->columns(array('module_name', 'module_description', 'price', 'is_default', 'date_create', 'date_update'));
            $query->values(array(
                'module_name' => $data['module_name'],
                'module_description' => $data['module_description'],
                'price' => $data['price'],
                'is_default' => $data['is_default'],
                'date_create' => $data['date_create'],
                'date_update' => $data['date_update'],
            ));
        } else {
            if ($this->getModule($id)) {
                $query = $sql->update('module');
                $query->where(array(
                    'module_id' => $id,
                ));
                $query->set(array(
                    'module_name' => $data['module_name'],
                    'module_description' => $data['module_description'],
                    'price' => $data['price'],
                    'is_default' => $data['is_default'],
                ));
            } else {
                throw new \Exception('Permissions id does not exist');
            }
        }
        try {
            if (isset($query)) {
                $queryString = $sql->getSqlStringForSqlObject($query);
                $adapter->query($queryString, $adapter::QUERY_MODE_EXECUTE);
                if (!$id) {
                    $id = $adapter->getDriver()->getConnection()->getLastGeneratedValue();
                }
                $sql = "DELETE FROM module_permissions WHERE module_id={$id}";
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                if (count($permissions) > 0) {
                    $value = array();
                    foreach ($permissions as $permit) {
                        $value[] = "({$id}, {$permit})";
                    }
                    $value = implode(',', $value);
                    $insertSql = "INSERT INTO module_permissions(module_id,permissions_id) VALUES {$value}";
                    $adapter->query($insertSql, $adapter::QUERY_MODE_EXECUTE);
                }
                $sql = "DELETE FROM module_api WHERE module_id={$id}";
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                if (count($apis) > 0) {
                    $value = array();
                    foreach ($apis as $api) {
                        $value[] = "({$id}, {$api})";
                    }
                    $value = implode(',', $value);
                    $insertSql = "INSERT INTO module_api(module_id,api_id) VALUES {$value}";
                    $adapter->query($insertSql, $adapter::QUERY_MODE_EXECUTE);
                }
            }
            $adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }

    }

    public function getModulePermissions($module_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('module_permissions');
        $select->where(array(
            'module_id' => $module_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function getModuleApis($module_id){
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
            $result = $result->toArray();
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function getPackModule($pack_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('pack_module');
        $select->where(array(
            'pack_id' => $pack_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function getWebsiteModule($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('module_websites');
        $select->where(array(
            'website_id' => $website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function removeModulesOfWebsite($website_id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = "DELETE FROM module_websites WHERE website_id={$website_id}";
        $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
    }
    
}