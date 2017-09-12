<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

use Cms\Model\AppTable;

class CommissionTable extends AppTable
{
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

    public function saveCommission( Commission $commission )
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'merchant_id' => $commission->merchant_id,
                'rate' => $commission->rate,
                'start_date' => $commission->start_date,
                'end_date' => $commission->end_date,
                'is_published' => $commission->is_published,
            );

            $commission_id = (int)$commission->commission_id;
            if( empty($commission_id) ){
                $this->tableGateway->insert($data);
                $commission_id = $this->getLastestId();
            }else{
                if ( $this->getCommission( array('commission_id' => $commission_id) ) ) {
                    $this->tableGateway->update($data, array('commission_id' => $commission_id));
                } else {
                    return 0;
                }
            }

            $adapter->getDriver()->getConnection()->commit();
            return $commission_id;
        } catch (\Exception $e) {
            $adapter->getDriver()->getConnection()->rollback();
            return 0;
        }
        return 0;
    }

}