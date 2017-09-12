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
use Zend\View\Model\ViewModel;

use Application\Model\AppTable;

class PaymentTable extends AppTable {

    public function getPayment($payment_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':PaymentTable:getPayment('.$payment_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('payment_method');
            $select->where(array(
                'website_id' => $this->getWebsiteId(),
                'payment_id' => $payment_id
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            try {
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function getPaymentNoJoinWebsite($payment_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':PaymentTable:getPaymentNoJoinWebsite('.$payment_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('payment_method');
            $select->where(array(
                'payment_id' => $payment_id
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            try {
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }

    public function getOneByCode($code){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':PaymentTable:getOneByCode('.$code.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('payment_method');
            $select->where(array(
                'website_id' => $this->getWebsiteId(),
                'code' => $code
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            try {
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            } catch (\Exception $ex) {
                $results = array();
            }
        }
        return $results;
    }
    
    public function removeAllPaymentsOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }
    
    public function getPaymentsOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('payment_method');
        $select->where(array(
            'website_id' => $website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function insertPayments($data){
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    } 

}