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

class ProductTypeTable extends AppTable
{
    public function getProductTypes( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_type');

        if( !empty($params['products_id']) ){
            $select->where(array(
                'products_id' => $params['products_id']
            ));
        }

        if( !empty($params['products_type_id']) ){
            $select->where(array(
                'products_type_id' => $params['products_type_id']
            ));
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->toArray();
        } catch (\Exception $e) {}
        return array();
    }

    public function getProductType( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_type');
        if( !empty($params['products_type_id']) ){
            $select->where(array(
                'products_type_id' => $params['products_type_id']
            ));
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->current();
        } catch (\Exception $e) {}
        return array();
    }

    public function deleteProductType( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_type');
        if( !empty($params['products_id']) ){
            $select->where(array(
                'products_id' => $params['products_id']
            ));
        }
        if( !empty($params['products_type_id']) ){
            $select->where(array(
                'products_type_id' => $params['products_type_id']
            ));
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            $ids = array();
            foreach ($results as $key => $result) {
                $ids[] = $result['products_type_id'];
            }
            if( !empty($ids) ){
                $deleteType = $sql->delete('products_type')
                    ->where( array(
                            'products_type_id' => $ids
                        ));
                $deleteTypeString = $sql->getSqlStringForSqlObject($deleteType);
                $deleteTran = $sql->delete('products_type_translate')
                    ->where( array(
                            'products_type_id' => $ids
                        ));
                $deleteTranString = $sql->getSqlStringForSqlObject($deleteTran);
                $adapter->query($deleteTypeString, $adapter::QUERY_MODE_EXECUTE);
                $adapter->query($deleteTranString, $adapter::QUERY_MODE_EXECUTE);
            }

        } catch (\Exception $e) {}
        return array();
    }

    public function saveProductType( $type )
    {
        $data = array(
            'products_id' => $type['products_id'],
            'type_name' => $type['type_name'],
            'price' => $type['price'],
            'price_sale' => $type['price_sale'],
            'quantity' => $type['quantity'],
            'is_available' => $type['is_available'],
            'thumb_image' => $type['thumb_image'],
            'is_default' => $type['is_default'],
        );
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $products_type_id = (int)$type['products_type_id'];
        try {
            if ( empty($products_type_id) ) {
                $this->tableGateway->insert($data);
                $products_type_id = $this->getLastestId();
                $type['products_type_id'] = $products_type_id;
            } else {
                if ( $this->getProductType( array('products_type_id' =>$products_type_id) ) ) {
                    $this->tableGateway->update($data, array('products_type_id' => $products_type_id));
                } else {
                    throw new \Exception('Product id does not exist');
                }
            }
            if( !empty($type['products_type_id']) && !empty($type['language']) ){
                $this->saveProductTypeTranslate($type);
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
        return $products_type_id;
    }

    public function saveProductTypeTranslate( $type )
    {
        $row = array(
            'products_type_id'  => $type['products_type_id'],
            'type_name'  => $type['type_name'],
            'language' => $type['language'],
        );
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert();
        $insert->into('products_type_translate');
        $insert->columns(array('products_type_id','type_name', 'language'));
        $insert->values($row);
        $insertString = $sql->getSqlStringForSqlObject($insert);
        $delsql = "DELETE FROM `products_type_translate` WHERE `products_type_id`={$type['products_type_id']} and language={$type['language']}";
        $adapter->query($delsql,$adapter::QUERY_MODE_EXECUTE);
        $adapter->query($insertString,$adapter::QUERY_MODE_EXECUTE);
    }

}