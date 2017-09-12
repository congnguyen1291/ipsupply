<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class PermissionsTable extends AppTable
{
    public function fetchAll( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
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

        if( !empty($params['order']) ){
            $select->order($params['order']);
        }

        if( isset($params['permissions_name']) ){
            $select->where->like('permissions_name', "%{$params['permissions_name']}%");
        }

        if( $this->hasPaging($params) ){
            $select->offset($this->getOffsetPaging($params['page'], $params['limit']));
            $select->limit($params['limit']);
        }

        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $ex) {}
        return array();
    }

    public function countAll(  $params = array()  )
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

        if( isset($params['permissions_name']) ){
            $select->where->like('permissions_name', "%{$params['permissions_name']}%");
        }

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        }catch (\Exception $ex){}
        return 0;
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

    public function savePermission(Permissions $permissions)
    {
        $data = array(
            'permissions_name' => $permissions->permissions_name,
            'module' => strtolower($permissions->module),
            'controller' => strtolower($permissions->controller),
            'action' => strtolower($permissions->action),
            'description' => $permissions->description,
            'is_published' => $permissions->is_published,
        );

        $id = (int)$permissions->permissions_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPermission($id)) {
                $this->tableGateway->update($data, array('permissions_id' => $id));
            } else {
                throw new \Exception('Permissions id does not exist');
            }
        }
    }

    public function fetchAllGroups( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('*', 'total'=> new Expression('(SELECT COUNT(`us`.`users_id`)
            FROM `users` AS `us` WHERE `us`.`groups_id` = `groups`.`groups_id` )')));
        $select->from('groups');
        $select->where(array('website_id'=>$this->getWebsiteId()));
        if( isset($params['groups_name']) ){
            $select->where->like('groups.groups_name', "%{$params['groups_name']}%");
        }
        if( $this->hasPaging($params) ){
            $select->offset($this->getOffsetPaging($params['page'], $params['limit']));
            $select->limit($params['limit']);
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $ex) {}
        return array();
    }

    public function countAllGroups( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("count(groups_id)")));
        $select->from('groups');
        $select->where(array('website_id'=>$this->getWebsiteId()));
        if( isset($params['groups_name']) ){
            $select->where->like('groups.groups_name', "%{$params['groups_name']}%");
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        }catch (\Exception $ex){}
        return 0;
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

    public function saveGroup(Groups $g, $permissions = array())
    {

        $data = array(
            'website_id' => $this->getWebsiteId(),
            'groups_name' => $g->groups_name,
            'groups_description' => $g->groups_description,
            'is_published' => $g->is_published,
            'date_create' => $g->date_create,
            'date_update' => $g->date_update,
        );

        $id = (int)$g->groups_id;
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        $sql = new Sql($adapter);
        if ($id == 0) {
            $query = $sql->insert('groups');
            $query->columns(array('website_id','groups_name', 'groups_description', 'is_published', 'date_create', 'date_update'));
            $query->values(array(
                'website_id' => $this->getWebsiteId(),
                'groups_name' => $data['groups_name'],
                'groups_description' => $data['groups_description'],
                'is_published' => $data['is_published'],
                'date_create' => $data['date_create'],
                'date_update' => $data['date_update'],
            ));
        } else {
            if ($this->getGroup($id)) {
                $query = $sql->update('groups');
                $query->where(array(
                    'groups_id' => $id,
                ));
                $query->set(array(
                    'groups_name' => $data['groups_name'],
                    'groups_description' => $data['groups_description'],
                    'is_published' => $data['is_published'],
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
                $sql = "DELETE FROM groups_permission WHERE groups_id={$id}";
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                if (count($permissions) > 0) {
                    $value = array();
                    foreach ($permissions as $permit) {
                        $value[] = "({$id}, {$permit})";
                    }
                    $value = implode(',', $value);
                    $insertSql = "INSERT INTO groups_permission(groups_id,permissions_id) VALUES {$value}";
                    $adapter->query($insertSql, $adapter::QUERY_MODE_EXECUTE);
                }
            }
            $adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }

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

    public function getGroupPermissionsForUser($groups_id){
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
            WHERE websites.website_id = {$this->getWebsiteId()} )"));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function getGroupPermissionsForAdmin(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array());
        $select->from('websites');
        $select->join('module_websites', 'module_websites.website_id = websites.website_id', array());
        $select->join('module', 'module.module_id = module_websites.module_id', array());
        $select->join('module_permissions', 'module_permissions.module_id = module.module_id', array());
        $select->join('permissions', 'permissions.permissions_id = module_permissions.permissions_id', array('module', 'controller', 'action'));
        $select->where(array(
            'websites.website_id' => $this->getWebsiteId(),
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function updateGroups($ids, $data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $update = $sql->update('groups');
        $update->set($data);
        $update->where(array(
            'groups_id' => $ids,
        ));
        $updateString = $sql->getSqlStringForSqlObject($update);
        $adapter->query($updateString,$adapter::QUERY_MODE_EXECUTE);
    }

    public function deleteGroups($ids){
        $adapter = $this->tableGateway->getAdapter();
        $delete = $sql->delete('groups');
        $delete->where(array(
            'groups_id' => $ids
        ));
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $results = $adapter->query($deleteString,$adapter::QUERY_MODE_EXECUTE);

        $delete2 = $sql->delete('groups_permission');
        $delete2->where(array(
            'groups_id' => $ids
        ));
        $deleteString2 = $sql->getSqlStringForSqlObject($delete2);
        $results = $adapter->query($deleteString2,$adapter::QUERY_MODE_EXECUTE);
    }

    public function deletePermissions($ids)
    {
        $this->tableGateway->delete(array('permissions_id' => $ids));
    }

    public function updatePermissions($ids, $data)
    {
        $this->tableGateway->update($data, array('permissions_id' => $ids));
    }

}