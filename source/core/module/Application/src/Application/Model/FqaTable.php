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

class FqaTable extends AppTable {

	public function fetchAll() {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':FqaTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('fqa');
            $select->join ( 'products', 'products.products_id=fqa.products_id', array() );
            $select->where(array(
                'website_id'=>$this->getWebsiteId(),
            ));
    		try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = new ResultSet();
                $results->initialize($results);
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = null;
            }
        }
        return $results;
	}
	public function insertFqa(Fqa $fqa) {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $data = array (
                    'products_id' => $fqa->products_id,
                    'id_parent' => $fqa->id_parent,
                    'email' => $fqa->email,
                    'users_id' => $fqa->users_id,
                    'tieu_de' => $fqa->tieu_de,
                    'noi_dung' => $fqa->noi_dung,
                    'date_create' => $fqa->date_create,
                    'is_published' => $fqa->is_published
            );
            $this->tableGateway->insert ( $data );
            $lastId = $this->getLastestId ();
            $adapter->getDriver()->getConnection()->commit();
            return $lastId;
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
	}

}