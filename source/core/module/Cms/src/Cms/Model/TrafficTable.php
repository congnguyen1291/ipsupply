<?php

namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Cms\Model\AppTable;

class TrafficTable extends AppTable
{

    public function getTrafficByDay($from_date, $to_date){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression('COUNT(traffic.traffic_id)'),'date_simple' => new Expression('DATE_FORMAT(date_create, "%Y-%m-%d")'), 'date_create'));
        $select->from('traffic');
        $select->where('(traffic.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
        $select->group('date_simple');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->toArray();
    }

}