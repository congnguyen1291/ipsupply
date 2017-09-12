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

class MerchantTable extends AppTable
{
    public function getMerchants( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('merchant');
        $select->where(array(
            'website_id' => $this->getWebsiteId()
        ));

        if( !empty($where['merchant_alias']) ){
            $select->where(array(
                'merchant_alias' => $where['merchant_alias']
            ));
        }

        if(isset($where['merchant_name'])){
            $merchant_name = $this->toAlias($where['merchant_name']);
            $select->where->like('merchant_name', "%{$merchant_name}%");
        }

        if(isset($where['address'])){
            $select->where->like('address', "%{$where['address']}%");
        }

        if(isset($where['merchant_phone'])){
            $select->where->like('merchant_phone', "%{$where['merchant_phone']}%");
        }

        if(isset($where['merchant_fax'])){
            $select->where->like('merchant_fax', "%{$where['merchant_fax']}%");
        }

        if(isset($where['merchant_email'])){
            $select->where->like('merchant_email', "%{$where['merchant_email']}%");
        }

        if( !empty($where['notIn']) ){
            if( !empty($where['notIn']['merchant_id']) ){
                $select->where->notIn('merchant_id', $where['notIn']['merchant_id']);
            }
        }
        
        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function getTotalMerchants( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("COUNT(merchant.merchant_id)")));
        $select->from('merchant');
        $select->where(array(
            'website_id' => $this->getWebsiteId()
        ));

        if(isset($where['merchant_name'])){
            $merchant_name = $this->toAlias($where['merchant_name']);
            $select->where->like('merchant_name', "%{$merchant_name}%");
        }

        if(isset($where['address'])){
            $select->where->like('address', "%{$where['address']}%");
        }

        if(isset($where['merchant_phone'])){
            $select->where->like('merchant_phone', "%{$where['merchant_phone']}%");
        }

        if(isset($where['merchant_fax'])){
            $select->where->like('merchant_fax', "%{$where['merchant_fax']}%");
        }

        if(isset($where['merchant_email'])){
            $select->where->like('merchant_email', "%{$where['merchant_email']}%");
        }
        
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        } catch (\Exception $e) {}
        return 0;
    }

    public function getTotalCommissions( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("COUNT(merchant_commission.id)")));
        $select->from('merchant_commission');
        $select->join('merchant', 'merchant_commission.merchant_id=merchant.merchant_id', array());
        if( !empty($where['merchant_id']) ){
            $select->where(array(
                'merchant_commission.merchant_id' => $where['merchant_id']
            ));
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getTotalUsers( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("COUNT(merchant_user.id)")));
        $select->from('users');
        $select->join('merchant', 'merchant.merchant_id=users.merchant_id', array());
        if( !empty($where['merchant_id']) ){
            $select->where(array(
                'merchant.merchant_id' => $where['merchant_id']
            ));
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getUsers( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('users');
        $select->join('merchant', 'merchant.merchant_id=users.merchant_id', array('merchant_id', 'merchant_name'));
        if( !empty($where['merchant_id']) ){
            $select->where(array(
                'merchant.merchant_id' => $where['merchant_id']
            ));
        }
        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function getUser( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('id','is_published') );
        $select->from('merchant_user');
        $select->join('merchant', 'merchant_user.merchant_id=merchant.merchant_id', array('merchant_id', 'merchant_name'));
        $select->join('users', 'merchant_user.users_id=users.users_id', array('users_id', 'parent_id', 'website_id', 'create_by', 'facebookId', 'url_twitter', 'url_google_plus', 'url_facebook', 'yahooId', 'googleId', 'user_name', 'first_name', 'last_name', 'full_name', 'avatar', 'cover', 'users_alias', 'birthday', 'gender', 'phone', 'address', 'address_full', 'address01', 'zipcode', 'longitude', 'latitude', 'country_id', 'city', 'state', 'suburb', 'region', 'province', 'cities_id', 'districts_id', 'wards_id'));
        if( !empty($where['id']) ){
            $select->where(array(
                'merchant_user.id' => $where['id']
            ));
        }
        if( !empty($where['merchant_id']) ){
            $select->where(array(
                'merchant_user.merchant_id' => $where['merchant_id']
            ));
        }
        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function getCommissions( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('merchant_commission');
        $select->join('merchant', 'merchant_commission.merchant_id=merchant.merchant_id', array('merchant_name'));
        if( !empty($where['commission_id']) ){
            $select->where(array(
                'merchant_commission.commission_id' => $where['commission_id']
            ));
        }
        if( !empty($where['merchant_id']) ){
            $select->where(array(
                'merchant_commission.merchant_id' => $where['merchant_id']
            ));
        }
        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function getCommission( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('merchant_commission');
        $select->join('merchant', 'merchant_commission.merchant_id=merchant.merchant_id', array('merchant_name'));
        if( !empty($where['commission_id']) ){
            $select->where(array(
                'merchant_commission.commission_id' => $where['commission_id']
            ));
        }
        if( !empty($where['merchant_id']) ){
            $select->where(array(
                'merchant_commission.merchant_id' => $where['merchant_id']
            ));
        }
        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function getMerchant( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('merchant');
        if( !empty($where['merchant_id']) ){
            $select->where(array(
                        'merchant_id' => $where['merchant_id']
                    ));
        }

        if( !empty($where['merchant_alias']) ){
            $select->where(array(
                        'merchant_alias' => $where['merchant_alias']
                    ));
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->current();
            return $result;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function createMerchant( Merchant $merchant )
    {
        $data = array(
            'website_id'  => $this->getWebsiteId(),
            'merchant_type'  => $merchant->merchant_type,
            'merchant_name'  => $merchant->merchant_name,
            'merchant_alias'  => $merchant->merchant_alias,
            'merchant_phone'  => $merchant->merchant_phone,
            'merchant_email'  => $merchant->merchant_email,
            'merchant_fax'  => $merchant->merchant_fax,
            'merchant_avatar'  => $merchant->merchant_avatar,
            'merchant_note'  => $merchant->merchant_note,
            'address'  => $merchant->address,
            'address01'  => $merchant->address01,
            'zipcode'  => $merchant->zipcode,
            'longitude'  => $merchant->longitude,
            'latitude'  => $merchant->latitude,
            'country_id'  => $merchant->country_id,
            'city'  => $merchant->city,
            'state'  => $merchant->state,
            'suburb'  => $merchant->suburb,
            'region'  => $merchant->region,
            'province'  => $merchant->province,
            'cities_id'  => $merchant->cities_id,
            'districts_id'  => $merchant->districts_id,
            'wards_id'  => $merchant->wards_id,
            'is_published'  => $merchant->is_published,
        );
        $id = (int) $merchant->merchant_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->getLastestId();
        } else {
            if ( $this->getMerchant( array('merchant_id' => $id) )) {
                $this->tableGateway->update($data, array('merchant_id' => $id));
            } else {
                throw new \Exception('Row does not exist');
            }
        }
        return $id;
    }


    public function deleteMerchant($ids)
    {
        $this->tableGateway->delete(array('merchant_id' => $ids));
    }

    public function updateMerchant($ids, $data)
    {
        $this->tableGateway->update($data, array('merchant_id' => $ids));
    }

    public function updateOrder($ids, $data)
    {
    }

    public function deleteUsers($ids)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->delete('merchant_user');
        $select->where(array(
                'id' => $ids
            ));
        $deleteString = $sql->getSqlStringForSqlObject($select);
        $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
    }

    public function updateUsers($ids, $data)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $update = $sql->update();
        $update->table( 'merchant_user' );
        $update->set( $data );
        $update->where(array(
            'id' => $ids
        ));
        $updateString = $sql->getSqlStringForSqlObject($update);
        $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
    }

    public function deleteCommissions($ids)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->delete('merchant_commission');
        $select->where(array(
            'commission_id' => $ids
        ));
        $deleteString = $sql->getSqlStringForSqlObject($select);
        $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
    }

    public function updateCommissions($ids, $data)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $update = $sql->update();
        $update->table( 'merchant_commission' );
        $update->set( $data );
        $update->where(array(
            'commission_id' => $ids
        ));
        $updateString = $sql->getSqlStringForSqlObject($update);
        $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
    }

}