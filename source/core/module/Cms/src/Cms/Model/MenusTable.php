<?php
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Filter\File\LowerCase;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\GreaterThan;

use Cms\Model\AppTable;

class MenusTable extends AppTable
{
    public function fetchAll()
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('*', 'number_childrens'=> new Expression('(SELECT COUNT(*)
            FROM `menus` AS `t1`
            LEFT JOIN `menus` AS `t2` ON `t2`.`parent_id` = `t1`.`menus_id`
            LEFT JOIN `menus` AS `t3` ON `t3`.`parent_id` = `t2`.`menus_id`
            LEFT JOIN `menus` AS `t4` ON `t4`.`parent_id` = `t3`.`menus_id`
            WHERE `t1`.`parent_id` = `menus`.`menus_id`)')));
        $select->from('menus');
        $select->where(array(
            'website_id' => $this->getWebsiteId()
        ));
        $select->order('ordering');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function getAllAndSort()
    {
        $menus = $this->fetchAll();
        $results = array();
        foreach ($menus as $key => $menu) {
            $parent_id = $menu['parent_id'];
            if(isset($results[$parent_id])){
                $results[$parent_id][] = $menu;
            }else{
                $results[$parent_id] = array($menu);
            }
        }
        return $results;
    }
	
    public function getMenusById($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('menus_id' => $id , 'website_id'=>$this->getWebsiteId()));
        $results = $rowset->current();
        return $results;
    }

	
    public function delete($id)
    {
        $this->tableGateway->delete(array('menus_id' => $id, 'website_id'=>$this->getWebsiteId()));
    }

    public function getMenusOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('menus');
        $select->where(array(
            'website_id' => $website_id
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertMenus($data)
    {
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }
    public function editMenus($data ,$where)
    {
        $this->tableGateway->update($data, $where);
        return $this->getLastestId();
    }

    public function getLastestId(){
        return $this->tableGateway->getLastInsertValue();
    }

    public function getMenu($id){
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('menus_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getMenuByAlias($alias){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('menus');
        $select->where(array(
            'website_id' => $this->getWebsiteId(),
        ));
        $select->where->like('menus_alias', $alias);
        $selectString = $sql->getSqlStringForSqlObject($select);
        try{
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch (\Exception $ex){echo $e->getMessage();die();
            throw new \Exception($ex);
        }
    }

    public function removeMenusOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }
}