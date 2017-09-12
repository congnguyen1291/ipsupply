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

class WholesaleTable  extends AppTable {

    public function insertWholesale($row, $cart)
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            if(!empty($row) && !empty($cart) ){
                $row['website_id']=$this->getWebsiteId();
                $this->tableGateway->insert($row);
                $wholesale_id = $this->getLastestId();
                foreach ($cart['products'] as $key => $product) {
                    $total_price_extention = 0;
                    if(!empty($product['total_price_extention'])){
                        $total_price_extention = $product['total_price_extention'];
                    }
                    $products_type_id = 0;
                    if(!empty($product['products_type_id'])){
                        $products_type_id = $product['products_type_id'];
                    }
                    $row = array();
                    $row['products_id']=$product['products_id'];
                    $row['products_type_id']=$products_type_id;
                    $row['wholesale_id']=$wholesale_id;
                    $row['is_published']= 1;
                    $row['is_delete']= 0;
                    $row['promotion']= '';
                    $row['price']= $product['price'];
                    $row['price_sale']= $product['price_sale'];
                    $row['quantity']= $cart['quality'];
                    $row['total_price_extention']= $total_price_extention;
                    $row['vat']= empty($product['vat']) ? 0 : (int)$product['vat'];
                    $row['total']= $cart['total'];

                    $wholesale_products_id = $this->getModelTable('WholesaleProductsTable')->insertWholesaleProducts($row);
                }

                $adapter->getDriver()->getConnection()->commit();
                return $wholesale_id;
            }
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            return 0;
        }
    }

    public function getOneWholesale($wholesale_id)
    {
        $wholesale_id = (int)$wholesale_id;
        $rowset = $this->tableGateway->select(array('wholesale_id' => $wholesale_id, 'website_id' => $this->getWebsiteId()));
        $row = $rowset->current();
        return $row;
    }
    
}