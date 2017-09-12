<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;


class BranchesTable  extends AppTable{

    public function getRows($branches_id) { 
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':BranchesTable:getRows('.$branches_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('branches');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'website_id'=>$this->getWebsiteId(),
                'branches_id'=>$branches_id,
            ));
            $select->order('date_create ASC');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllBranches() { 
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':BranchesTable:getAllBranches');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('branches');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'website_id'=>$this->getWebsiteId(),
            ));
            $select->order('date_create ASC');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $e){
                $results = array();
            }
        }
        return $results;
    }

    public function getMainBranches() { 
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':BranchesTable:getMainBranches');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('branches');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'is_default' => 1,
    			'website_id'=>$this->getWebsiteId(),
            ));
            $select->order('date_create ASC');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                if( !empty($results) ){
                    $results = $results[0];
                }
                $cache->setItem($key,$results);
            }catch(\Exception $e){
                $results = array();
            }
        }
        return $results;
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array('branches_id' => $id, 'website_id'=>$this->getWebsiteId()));
    }

    public function getBranchesOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('branches');
        $select->where(array(
            'branches.website_id' => $website_id,
            'branches.is_published' => 1,
            'branches.is_delete' => 0
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertBranches($data)
    {
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }
}