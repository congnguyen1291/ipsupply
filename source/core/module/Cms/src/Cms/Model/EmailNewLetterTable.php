<?php

namespace Cms\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Cms\Model\AppTable;

class EmailNewLetterTable extends AppTable {
	
	public function fetchAll( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('email_new_letter');
        $select->where(array(
            'email_new_letter.website_id' => $this->getWebsiteId(),
        ));

        $select->order(array(
            'email_new_letter.date_create' => 'DESC',
        ));

        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch (\Exception $ex){}
        return array();
    }

    public function countAll( $where = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(email_new_letter.email_id)")));
        $select->from('email_new_letter');
        $select->where(array(
            'email_new_letter.website_id' => $this->getWebsiteId(),
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function deleteEmail($ids)
    {
        $this->tableGateway->delete(array('email_id' => $ids));
    }

}