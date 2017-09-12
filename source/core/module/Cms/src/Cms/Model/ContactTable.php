<?php

namespace Cms\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;

use Cms\Model\AppTable;

class ContactTable extends AppTable {

    public function getContacts() {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('website_contact');
        $select->join('products', 'website_contact.product_id = products.products_id', array('*'),'left');
        $select->join('website_replay_contact', 'website_contact.id=website_replay_contact.contact_id',array('total_relay' => new Expression('count(website_replay_contact.replay_id)')), 'left');
        $select->where(array(
            'website_contact.website_id' => $this->getWebsiteId()
        ));
        $select->order('website_contact.id ASC');
        $select->group('website_contact.id');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch(\Exception $ex){
            return array();
        }
    }

    public function getReplays( $id ) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('website_replay_contact');
        $select->join('website_contact', 'website_replay_contact.contact_id = website_contact.id', array());
        $select->where(array(
            'id' => $id,
            'website_id' => $this->getWebsiteId()
        ));
        $select->order('website_replay_contact.replay_id ASC');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch(\Exception $ex){
            return array();
        }
    }

    public function getTotalContact() {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression('count(website_contact.id)')));
        $select->from('website_contact');
        $select->join('products', 'website_contact.product_id = products.products_id', array('*'),'left');
        $select->where(array(
            'website_contact.website_id' => $this->getWebsiteId()
        ));
        $select->order('website_contact.id ASC');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $results->current();
            $total = 0;
            if( !empty($row['total']) ){
                $total = $row['total'];
            }
            return $total;
        }catch(\Exception $ex){
            return array();
        }
    }
	
	public function getContact($id) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('website_contact');
        $select->where(array(
            'id' => $id,
            'website_id' => $this->getWebsiteId()
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results;
        }catch(\Exception $ex){
            return array();
        }
    }

    public function replay( $row ) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert();
        $insert->columns(array(
            'users_id','contact_id','date_create','content',
        ));
        $insert->into('website_replay_contact');
        $insert->values(array(
            'users_id' => $row['users_id'],
            'contact_id' => $row['id'],
            'date_create' => date('Y-m-d H:i:s'),
            'content' => $row['content'],
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
            return TRUE;
        }catch (\Exception $ex){
            return FALSE;
        }
    }

    public function update( $row, $where ) {
        $this->tableGateway->update($row, $where);
    }

    public function deleteContact($ids){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $ids = implode(',', $ids);
            $deleteString = "DELETE FROM website_replay_contact WHERE contact_id IN ({$ids})";
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
            $deleteString = "DELETE FROM website_contact WHERE id IN ({$ids})";
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
            $adapter->getDriver()->getConnection()->commit();
        }catch (\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

}