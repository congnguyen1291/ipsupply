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

class CouponsTable extends AppTable {
    
    public function delete($id)
    {
        $this->tableGateway->delete(array('coupons_id' => $id, 'website_id'=>$this->getWebsiteId()));
    }

    public function getCouponsOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('coupons');
        $select->where(array(
            'coupons.website_id' => $website_id,
            'coupons.is_published' => 1,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertCoupons($data)
    {
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }
    
}