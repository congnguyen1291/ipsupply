<?php
namespace Application\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Filter\File\LowerCase;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\GreaterThan;

use Application\Model\AppTable;

class MenusTable extends AppTable {
	
    public function removeAllMenusOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }
    
    public function fetchAll()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':MenusTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('menus');
                $select->where(array(
                    'website_id'=>$this->getWebsiteId(),
                    'is_published'=>1,
                ));
                $select->order('ordering');
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
	
    public function getMenusById($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':MenusTable:getMenusById('.(is_array($id)? implode('-',$id) : $id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try {
                $id = (int)$id;
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('menus');
                $select->where(array(
                    'menus_id' => $id , 
                    'website_id'=>$this->getWebsiteId(),
                    'is_published'=>1
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = $resultSet->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllAndSort()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':MenusTable:getAllAndSort');
        $results = $cache->getItem($key);
        if(!$results){
            try {
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
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getMenuWithAlias($alias)
    {
        $menus = $this->getAllAndSort();
        $result = array();
        foreach ($menus as $key => $list_menu) {
            foreach ($list_menu as $key => $item_menu) {
                if($alias == $item_menu['menus_alias']){
                    $result = $item_menu;
                    return $result;
                    break 2;
                }
            }
        }
        return $result;
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
    
}