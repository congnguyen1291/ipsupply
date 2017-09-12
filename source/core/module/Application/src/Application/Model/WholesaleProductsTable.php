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

class WholesaleProductsTable  extends AppTable {
    
    public function insertWholesaleProducts($row)
    {
        try {
            $this->tableGateway->insert($row);
            $wholesale_products_id = $this->getLastestId();
            return $wholesale_products_id;
        } catch (\Exception $ex) {
            die($ex->getMessage());
            return 0;
        }
    }
    
}