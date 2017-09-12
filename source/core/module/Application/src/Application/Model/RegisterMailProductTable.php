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

class RegisterMailProductTable  extends AppTable{

	public function fetchAll() {
		$cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':RegisterMailProductTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
        	try{
				$results = $this->tableGateway->select (array(
	                'website_id'=>$this->getWebsiteId(),
	            ));
	            $cache->setItem($key,$results);
			}catch(\Exception $ex){
                $results = null;
            }
        }
        return $results;
	}
	public function insertEmail(RegisterMailProduct $em) {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $data = array (
        		'website_id'=>$this->getWebsiteId(),
                'products_id' => $em->products_id,
                'name' => $em->name,
                'phone' => $em->phone,
                'email' => $em->email,
                'date_create' => $em->date_create
            );
            $this->tableGateway->insert ( $data );
            $lastId = $this->getLastestId ();
            $adapter->getDriver()->getConnection()->commit();
            return $lastId;
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
			return $ex;
			//throw new \Exception($ex);
        }
	}

}