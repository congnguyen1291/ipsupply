<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class PermissionsTable  extends AppTable {

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
        $select->from('permissions');
        if($_SESSION['CMSMEMBER']['website_id'] != ID_MASTERPAGE){
            $select ->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression("permissions.permissions_id IN (SELECT permissions.permissions_id 
            FROM websites 
            INNER JOIN module_websites ON module_websites.website_id = websites.website_id
            INNER JOIN module ON module.module_id = module_websites.module_id
            INNER JOIN module_permissions ON module_permissions.module_id = module.module_id
            INNER JOIN permissions ON permissions.permissions_id = module_permissions.permissions_id
            WHERE websites.website_id = {$this->getWebsiteId()} )"));
        }
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
        $select = $sql->select()->columns(array('total' => new Expression("count(permissions_id)")));
        $select->from('permissions');
        if($this->getWebsiteId() != ID_MASTERPAGE){
            $select ->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression("permissions.permissions_id IN (SELECT permissions.permissions_id 
            FROM websites 
            INNER JOIN module_websites ON module_websites.website_id = websites.website_id
            INNER JOIN module ON module.module_id = module_websites.module_id
            INNER JOIN module_permissions ON module_permissions.module_id = module.module_id
            INNER JOIN permissions ON permissions.permissions_id = module_permissions.permissions_id
            WHERE websites.website_id = {$this->getWebsiteId()} )"));
        }
        if ($where) {
            $select->where($where);
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
    }

    public function getPermission($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('permissions_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function fetchAllGroups($where = 0, $order = '', $intPage, $intPageSize)
    {
        $adapter = $this->tableGateway->getAdapter();
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('groups');
        $select->where(array('website_id'=>$this->getWebsiteId()));
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

    public function countAllGroups($where = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("count(groups_id)")));
        $select->from('groups');
        $select->where(array('website_id'=>$this->getWebsiteId()));
        if ($where) {
            $select->where($where);
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
    }

    public function getGroup($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('groups');
        $select->where(array(
            'groups_id' => $id,
            'website_id'=>$this->getWebsiteId()
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        if (!$row) {
            throw new \Exception('Row does not exists');
        }
        $g = new Groups();
        $g->exchangeArray($row);
        return $g;
    }

    public function getGroupPermissions($groups_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('groups_permission');
        $select->join('groups','groups_permission.groups_id = groups.groups_id',array());
        $select->where(array(
            'groups_permission.groups_id' => $groups_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $ex) {
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
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function getGroupPermissionsWebsiteForUser($groups_id, $website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array());
        $select->from('groups_permission');
        $select->join('permissions', 'groups_permission.permissions_id=permissions.permissions_id', array('module', 'controller', 'action'));
        
        $select->where(array(
            'groups_id' => $groups_id,
        ));
        $select ->where->addPredicate(new \Zend\Db\Sql\Predicate\Expression("permissions.permissions_id IN (SELECT permissions.permissions_id 
            FROM websites 
            INNER JOIN module_websites ON module_websites.website_id = websites.website_id
            INNER JOIN module ON module.module_id = module_websites.module_id
            INNER JOIN module_permissions ON module_permissions.module_id = module.module_id
            INNER JOIN permissions ON permissions.permissions_id = module_permissions.permissions_id
            WHERE websites.website_id = {$website_id} )"));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function getGroupPermissionsWebsiteForAdmin($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array());
        $select->from('websites');
        $select->join('module_websites', 'module_websites.website_id = websites.website_id', array());
        $select->join('module', 'module.module_id = module_websites.module_id', array());
        $select->join('module_permissions', 'module_permissions.module_id = module.module_id', array());
        $select->join('permissions', 'permissions.permissions_id = module_permissions.permissions_id', array('module', 'controller', 'action'));
        $select->where(array(
            'websites.website_id' => $website_id,
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