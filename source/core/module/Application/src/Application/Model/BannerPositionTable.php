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
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class BannerPositionTable extends AppTable{

    public function removeAllBannersPositionOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function getBannersPositionOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('banners_position');
        $select->where(array(
            'website_id' => $website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function insertBannersPosition($data){
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }

    public function getLastestId(){
        return $this->tableGateway->getLastInsertValue();
    }

}