<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Application\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

use Application\Model\AppTable;

class CommissionTable extends AppTable
{
    public function getCommissionOfUser( $users_id )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('merchant_commission');
        $select->join('merchant', 'merchant_commission.merchant_id = merchant.merchant_id', array());
        $select->join('users', 'users.merchant_id = merchant_commission.merchant_id', array());
        $select->where(array(
            'users.users_id' => $users_id,
            'merchant_commission.is_published' => 1
        ));
        $select->where(' merchant_commission.rate >0 AND NOW() >= merchant_commission.start_date AND NOW() <= merchant_commission.end_date');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->current();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getCommission( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('merchant_commission');
        if( !empty($where['commission_id']) ){
            $select->where(array(
                'merchant_commission.commission_id' => $where['commission_id']
            ));
        }
        $select->group( 'merchant_commission.commission_id' );
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->current();
            return $result;
        } catch (\Exception $e) {
            return array();
        }
    }

}