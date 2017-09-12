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

class BannerSizeTable extends AppTable{

    public function removeAllBannersSizeOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }
    
    public function getBannersSizeOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('banners_size');
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

    public function insertBannersSize($data){
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }

    public function getLastestId(){
        return $this->tableGateway->getLastInsertValue();
    }

}