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

class MerchanTable extends AppTable
{
    public function getMerchans( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('merchan');
        $select->where(array(
                'website_id' => $this->getWebsiteId()
            ));
        if( !empty($where['merchan_alias']) ){
            $select->where(array(
                        'merchan_alias' => $where['merchan_alias']
                    ));
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

    public function getTotalMerchans( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("COUNT(merchan.merchan_id)")));
        $select->from('merchan');
        $select->where(array(
                'website_id' => $this->getWebsiteId()
            ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getMerchan( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('merchan');
        if( !empty($where['merchan_id']) ){
            $select->where(array(
                        'merchan_id' => $where['merchan_id']
                    ));
        }

        if( !empty($where['merchan_alias']) ){
            $select->where(array(
                        'merchan_alias' => $where['merchan_alias']
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

    public function createMerchan( Merchan $merchan )
    {
        $data = array(
            'website_id'  => $this->getWebsiteId(),
            'merchan_type'  => $merchan->merchan_type,
            'merchan_name'  => $merchan->merchan_name,
            'merchan_alias'  => $merchan->merchan_alias,
            'merchan_phone'  => $merchan->merchan_phone,
            'merchan_email'  => $merchan->merchan_email,
            'merchan_fax'  => $merchan->merchan_fax,
            'merchan_avatar'  => $merchan->merchan_avatar,
            'merchan_note'  => $merchan->merchan_note,
            'address'  => $merchan->address,
            'address01'  => $merchan->address01,
            'zipcode'  => $merchan->zipcode,
            'longitude'  => $merchan->longitude,
            'latitude'  => $merchan->latitude,
            'country_id'  => $merchan->country_id,
            'city'  => $merchan->city,
            'state'  => $merchan->state,
            'suburb'  => $merchan->suburb,
            'region'  => $merchan->region,
            'province'  => $merchan->province,
            'cities_id'  => $merchan->cities_id,
            'districts_id'  => $merchan->districts_id,
            'wards_id'  => $merchan->wards_id,
            'is_published'  => $merchan->is_published,
        );
        $id = (int) $merchan->merchan_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->getLastestId();
        } else {
            if ( $this->getMerchan( array('merchan_id' => $id) )) {
                $this->tableGateway->update($data, array('merchan_id' => $id));
            } else {
                throw new \Exception('Row does not exist');
            }
        }
        return $id;
    }


    public function deleteMerchan($ids)
    {
        $this->tableGateway->delete(array('merchan_id' => $ids));
    }

    public function updateMerchan($ids, $data)
    {
        $this->tableGateway->update($data, array('merchan_id' => $ids));
    }

    public function updateOrder($ids, $data)
    {
    }

}