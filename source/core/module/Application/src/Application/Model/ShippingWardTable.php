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

use Application\Model\AppTable;

class ShippingWardTable extends AppTable{

    public function getShippingWard($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('shipping_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveShippingWard( $datas )
    {
        $data = array(
            'website_id' => $this->getWebsiteId(),
            'ward_id' => $datas['ward_id'],
            'shipping_id' => $datas['shipping_id'],
            'shipping_fixed_value' => $datas['shipping_fixed_value'],
            'no_shipping' => $datas['no_shipping'],
        );
        $id = (int) $datas['shipping_ward_id'];
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->one($id)) {
                $this->tableGateway->update($data, array('shipping_ward_id' => $id));
            } else {
                throw new \Exception('id does not exist');
            }
        }
        return $id;
    }
    
    public function deleteShippingWard($where)
    {
        $this->tableGateway->delete($where);
    }

    public function getShippingWards($where = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('shipping');
            $select->join('transportation', 'shipping.transportation_id = transportation.transportation_id', array('shipping_class', 'transportation_type', 'transportation_title', 'transportation_description'));
            if(!empty($where) && is_array($where) ){
                $where = array_merge($where, array('shipping.website_id' => $this->getWebsiteId(), 'transportation.website_id' => $this->getWebsiteId()));
                $select->where($where);
            }
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
    }

}