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
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class DomainTable extends AppTable {

    public function getList($domain){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().'DomainTable:getList('.$domain.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('domain');
            $select->where(array('is_published' => 1));
            if(!empty($domain)){
                $select->where(array('domain_name' => $domain));
            }
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $result->toArray();
            $cache->setItem($key,$results);
        }
        return $results;
    }

} 
