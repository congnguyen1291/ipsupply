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

class TrafficTable extends AppTable
{

    public function getTraffic( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('traffic');
        if( !empty($where['traffic_id']) ){
            $select->where(array(
                'traffic.traffic_id'=>    $where['traffic_id']
            ));
        }
        if( !empty($where['session_id']) ){
            $select->where(array(
                'traffic.session_id'=>    $where['session_id']
            ));
        }
        $select->group( 'traffic.traffic_id' );
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->current();
            return $result;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function register(Traffic $traffic){
        $row = array(
            'session_id'         => $traffic->session_id,
            'users_id'  => $traffic->users_id,
            'email'  => $traffic->email,
            'date_create'  => $traffic->date_create,
        );
        $this->tableGateway->insert($row);
        return $this->getLastestId();
    }

    public function updateTraffic( $row, $traffic_id ){
        $this->tableGateway->update($row, array('traffic_id' => $traffic_id) );
        return $traffic_id;
    }

}