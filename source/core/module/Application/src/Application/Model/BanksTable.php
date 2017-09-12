<?php

namespace Application\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class BanksTable extends AppTable
{

    public function getAllBanks()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':BanksTable:getAllBanks');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('banks_id', 'banks_title','banks_description','thumb_image'));
            $select->from('banks');
            $select->where(array(
                'banks.is_published' => 1,
                'website_id'=>$this->getWebsiteId(),
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
	
	public function getAllRate(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':BanksTable:getAllRate');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('banks_title', 'is_published','banks_id'));
            $select->from('banks');
            $select->join('banks_config','banks_config.banks_id=banks.banks_id', array('percent_interest_rate','percent_must_pay','total_month','banks_config_id'));
            $select->where(array(
                'banks.is_published' => 1,
                'website_id'=>$this->getWebsiteId(),
            ));
            $select->order(array(
                'total_month' => 'ASC',
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
	
	public function getAllRateSort()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':BanksTable:getAllRateSort');
        $results = $cache->getItem($key);
        if(!$results){
            $rows = $this->getAllRate();
    		$results = array();
    		if(COUNT($rows)>0){
    			foreach ($rows as $item ) {
    				$idMonth = $item['total_month'];
    				$idBank = $item['banks_id'];
    				if (isset($results[$idMonth]) && !empty($results[$idMonth]) ) {
    					if(!isset($results[$idMonth][$idBank])){
    						$results[$idMonth][$idBank] = $item;
    					}
    				} else {
    					$results[$idMonth] = array($idBank=>$item);
    				}
    			}
    		}
        }
        return $results;
    }
	
	public function getAllFooterSocial(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':BanksTable:getAllFooterSocial');
        $results = $cache->getItem($key);
        if(!$results){
    		$adapter = $this->tableGateway->getAdapter();
    		$sql = new Sql($adapter);
    		$select = $sql->select();
    		$select->from('socials_networks');
    		$select->where(array(
    			'is_published' => 1,
                'website_id'=>$this->getWebsiteId(),
    		));
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

}