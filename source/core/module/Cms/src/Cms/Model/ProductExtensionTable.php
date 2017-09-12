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

class ProductExtensionTable extends AppTable
{
    public function getProductExtension( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_extensions');
        if( !empty($params['id']) ){
            $select->where(array(
                'id' => $params['id']
            ));
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->current();
        } catch (\Exception $e) {}
        return array();
    }

    public function saveProductExtension( $type )
    {
        $data = array(
            'ext_id' => $type['ext_id'],
            'products_id' => $type['products_id'],
            'ext_name' => $type['ext_name'],
            'price' => $type['price'],
            'ext_description' => $type['ext_description'],
            'quantity' => $type['quantity'],
            'is_always' => $type['is_always'],
            'type' => $type['type'],
            'refer_product_id' => $type['refer_product_id'],
            'icons' => !empty($type['icons']) ? $type['icons'] : '',
        );
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        try {
            $id = (int)$type['id'];
            if ( empty($id) ) {
                $this->tableGateway->insert($data);
                $id = $this->getLastestId();
                $type['id'] = $id;
            } else {
                if ( $this->getProductExtension( array('id' =>$id) ) ) {
                    $this->tableGateway->update($data, array('id' => $id));
                } else {
                    throw new \Exception('Product id does not exist');
                }
            }
            if( !empty($type['id']) && !empty($type['language']) ){
                $this->saveProductExtensionTranslate($type);
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function saveProductExtensionTranslate( $type )
    {
        $row = array(
            'id'  => $type['id'],
            'ext_name'  => $type['ext_name'],
            'ext_description'  => $type['ext_description'],
            'language' => $type['language'],
        );
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert();
        $insert->into('products_extensions_translate');
        $insert->columns(array('id','ext_name', 'ext_description', 'language'));
        $insert->values($row);
        $insertString = $sql->getSqlStringForSqlObject($insert);
        $delsql = "DELETE FROM `products_extensions_translate` WHERE `id`={$type['id']} and language={$type['language']}";
        $adapter->query($delsql,$adapter::QUERY_MODE_EXECUTE);
        $adapter->query($insertString,$adapter::QUERY_MODE_EXECUTE);
    }

}