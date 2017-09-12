<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 3:18 PM
 */

namespace Application\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\ResultSet;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class CountryTable extends AppTable {

    public function getOne($country_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CountryTable:getOne('.$country_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('country');
            $select->where(array(
                'id' => $country_id,
                'status' => 1
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
    
    public function getContries(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CountryTable:getContries');
        $results = $cache->getItem($key);
        if(!$results){
            $sql = "SELECT *
                    FROM `country`
                    WHERE status=1 ORDER BY ordering ";
            try{
                $adapter = $this->tableGateway->getAdapter();
                $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
	public function getContriesLimit($where){
        $cache = $this->getServiceLocator()->get('cache');
		if(!empty($where) && count($where)>0){
			$key = md5($this->getNamspaceCached().':CountryTable:getContriesLimit'.implode(",",$where).$this->getWebsiteId());
		}else{
			$key = md5($this->getNamspaceCached().':CountryTable:getContriesLimit'.$where.$this->getWebsiteId());
		}
        
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('country');
			if(!empty($where)){
            $select->where($where);
			}
            try{
				$selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
} 
