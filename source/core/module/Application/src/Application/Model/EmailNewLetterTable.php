<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class EmailNewLetterTable extends AppTable {
	
	public function fetchAll() {
		$cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':EmailNewLetterTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
        	try{
				$results = $this->tableGateway->select ();
				$cache->setItem($key,$results);
			}catch(\Exception $ex){
                $results = null;
            }
        }
        return $results;
	}

	public function insertEmail(EmailNewLetter $em) {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $data = array (
            	'website_id'=>$this->getWebsiteId(),
                'email' => $em->email,
                'date_create' => $em->date_create
            );
            $this->tableGateway->insert ( $data );
            $lastId = $this->getLastestId ();
            $adapter->getDriver()->getConnection()->commit();
            return $lastId;
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
			return '';
        }
	}

}