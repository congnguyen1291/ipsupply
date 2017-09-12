<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/4/14
 * Time: 1:44 PM
 */
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class PaymentTable extends AppTable
{
    public function getAll( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('payment_method');
        $select->where(array(
            'website_id' => $this->getWebsiteId()
        ));

        if( isset($params['payment_name']) ){
            $select->where->like('payment_name', "%{$params['payment_name']}%");
        }

        if( $this->hasPaging($params) ){
            $select->offset($this->getOffsetPaging($params['page'], $params['limit']));
            $select->limit($params['limit']);
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $rows = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $rows;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function countAll(  $params = array()  ){
        try{
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('total' => new Expression("COUNT(payment_method.payment_id)")));
            $select->from('payment_method');
            $select->where(array(
                'website_id' => $this->getWebsiteId()
            ));
            if( isset($params['payment_name']) ){
                $select->where->like('payment_name', "%{$params['payment_name']}%");
            }
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch(\Exception $ex){}
        return 0;
    }

    /**
     * @param $payment Payment
     * @throws \Exception
     */
    public function savePayment($payment, $picture_id = '')
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'website_id' => $this->getWebsiteId(),
                'payment_name' => $payment->payment_name,
                'code' => $payment->code,
                'payment_description' => $payment->payment_description,

                'sale_account' => $payment->sale_account,
                'api_username' => $payment->api_username,
                'api_password' => $payment->api_password,
                'api_signature' => $payment->api_signature,

                'vpc_merchant' => $payment->vpc_merchant,
                'vpc_accesscode' => $payment->vpc_accesscode,
                'vpc_hashcode' => $payment->vpc_hashcode,

                'vnp_merchant' => $payment->vnp_merchant,
                'vnp_tmncode' => $payment->vnp_tmncode,
                'vnp_hashsecret' => $payment->vnp_hashsecret,
                'ordering' => $payment->ordering,
                
                'is_local' => $payment->is_local,
                'is_sandbox' => $payment->is_sandbox,

                'is_published' => $payment->is_published,
                'is_delete' => $payment->is_delete
            );
            if(!empty($picture_id)){
                $picture = $this->getModelTable('PictureTable')->getPicture($picture_id);
                if(!empty($picture)){
                    $data['image'] = $picture->folder.'/'.$picture->name.'.'.$picture->type;
                }
            }
            $id = $payment->payment_id;
            if (!$id) {
                $updateOrdering = "UPDATE `payment_method` SET `ordering` = `ordering`+1 WHERE `website_id` = '{$this->getWebsiteId()}' ";
                $adapter->query($updateOrdering,$adapter::QUERY_MODE_EXECUTE);

                $data['date_create'] = $payment->date_create;
                $this->tableGateway->insert($data);
            } else {
                if ($this->getPaymentMethodById($id)) {
                    $this->tableGateway->update($data, array('payment_id' => $id));
                } else {
                    throw new \Exception('Rows does not exists');
                }
            }
            $adapter->getDriver()->getConnection()->commit();
            return $id;
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function getPaymentMethodById($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('payment_method');
        $select->where(array(
            'payment_id' => $id,
        ));

        $selectString = $sql->getSqlStringForSqlObject($select);

        $rows = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $rows->current();
        if (!$row) {
            throw new \Exception('Rows does not exists');
        }
        return $row;
    }

    public function removePaymentOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }
	protected function getIdCol()
    {
        return 'payment_id';
    }
	 protected function getOrderCol()
    {
        return 'ordering';
    }

    public function deletePayments($ids)
    {
        $this->tableGateway->delete(array('payment_id' => $ids));
    }

    public function updatePayments($ids, $data)
    {
        $this->tableGateway->update($data, array('payment_id' => $ids));
    }

    public function updateOrder($data){
        $sql = "INSERT INTO payment_method(payment_id, ordering) VALUES ";
        $val = array();
        foreach($data as $id => $order){
            $val[] = "({$id}, {$order})";
        }
        $val = implode(',', $val);
        $sql .= $val;
        $sql .= " ON DUPLICATE KEY UPDATE ordering=VALUES(ordering)";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
    
}