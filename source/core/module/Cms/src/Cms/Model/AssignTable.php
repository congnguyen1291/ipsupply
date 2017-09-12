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

class AssignTable extends AppTable
{

    public function checkAssignsMerchant( $assign_id, $merchant_id )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('assign_merchant');
        $select->where(array(
            'assign_merchant.assign_id' => $assign_id,
            'assign_merchant.merchant_id' => $merchant_id
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            if( !empty($result) ){
                return TRUE;
            }
        }catch (\Exception $ex){}
        return FALSE;
    }

    public function checkProductAssignsPublisher( $users_id, $invoice_id, $products_id, $products_type_id )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('invoice_id','products_type_id','price', 'price_sale', 'quantity', 'total_price_extention', 'vat', 'total','promotion', 'hash_name' => new Expression('CONCAT(products_invoice.invoice_id, \'_\', products_invoice.products_id, \'_\', products_invoice.products_type_id)')) );
        $select->from('products_invoice');
        $select->join('assign_products', 'assign_products.products_invoice_id=products_invoice.id', array());
        $select->join('assign_user', 'assign_user.assign_id=assign_products.assign_id', array());
        $select->where(array(
            'assign_user.users_id' => $users_id,
            'products_invoice.invoice_id' => $invoice_id,
            'products_invoice.products_id' => $products_id,
            'products_invoice.products_type_id' => $products_type_id,
        ));
        $select->group( 'hash_name');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            if( !empty($result) ){
                return TRUE;
            }
        }catch (\Exception $ex){}
        return FALSE;
    }

    public function getProductAssignsPublisher( $users_id )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('invoice_id','products_type_id','price', 'price_sale', 'quantity', 'total_price_extention', 'vat', 'total','promotion', 'hash_name' => new Expression('CONCAT(products_invoice.invoice_id, \'_\', products_invoice.products_id, \'_\', products_invoice.products_type_id)')) );
        $select->from('products_invoice');
        $select->join('assign_products', 'assign_products.products_invoice_id=products_invoice.id', array());
        $select->join('assign_user', 'assign_user.assign_id=assign_products.assign_id', array());
        $select->where(array(
            'assign_user.users_id' => $users_id
        ));
        $select->group( 'hash_name');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getProductsOfAssign( $assign_id )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('invoice_id','products_type_id','price', 'price_sale', 'quantity', 'total_price_extention', 'vat', 'total','promotion', 'hash_name' => new Expression('CONCAT(products_invoice.invoice_id, \'_\', products_invoice.products_id, \'_\', products_invoice.products_type_id)')) );
        $select->from('products_invoice');
        $select->join('assign_products', 'assign_products.products_invoice_id=products_invoice.id', array());
        $select->join('products', 'products_invoice.products_id = products.products_id', array('products_id', 'products_code','categories_id','manufacturers_id','users_id','website_id','transportation_id','users_fullname','is_published', 'is_delete', 'is_new', 'is_hot', 'is_available', 'is_goingon', 'is_sellonline', 'tra_gop', 'date_create', 'date_update', 'hide_price', 'wholesale', 'ordering', 'thumb_image', 'list_thumb_image', 'number_views', 'vat', 'rating', 'number_like', 'total_sale', 'convert_search', 'is_viewed', 'position_view', 'youtube_video', 'price_total', 'url_crawl', 'convert_sitemap', 'convert_images', 'tags', 'type_view', 'publisher_id'));
        $select->join('products_translate', 'products_translate.products_id=products.products_id',array('products_title','products_alias','products_description','products_longdescription_2','products_longdescription','bao_hanh', 'products_more', 'seo_keywords', 'seo_description', 'seo_title', 'promotion_description', 'promotion1_description', 'promotion2_description', 'promotion3_description', 'tags', 'language'));
        $select->where(array(
            'assign_products.assign_id' => $assign_id
        ));
        $select->group( 'hash_name');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getAssignMerchants( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('assign_merchant');
        $select->join('merchant',new Expression("`merchant`.`merchant_id`=`assign_merchant`.`merchant_id`"),array('merchant_id', 'merchant_type', 'merchant_name', 'merchant_alias', 'merchant_phone', 'merchant_email', 'merchant_fax', 'merchant_avatar', 'merchant_note', 'address', 'address01', 'zipcode', 'longitude', 'latitude', 'country_id', 'city', 'state', 'suburb', 'region', 'province', 'cities_id', 'districts_id', 'wards_id'));
        if( !empty($where['assign_id']) ){
            $select->where(array(
                'assign_merchant.assign_id' => $where['assign_id']
            ));
        }
        $select->group('merchant.merchant_id');
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $e) {}
        return array();
    }

    public function getAssigns( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('assign_id', 'assign_code', 'assign_name', 'assign_shipping', 'assign_status', 'assign_read', 'assign_date','invoice_id', 'assign_total'=> new Expression('(SELECT SUM(((`assign_products`.`price_sale`+`products_invoice`.`total_price_extention`)*`products_invoice`.`quantity`))
            FROM `assign_products` 
            INNER JOIN `products_invoice` ON `assign_products`.`products_invoice_id` = `products_invoice`.`id`
            WHERE `assign_products`.`assign_id` =  `assign`.`assign_id`)'), 'assign_extention_total'=> new Expression('(SELECT SUM(`invoice_products_extension`.`price`*`invoice_products_extension`.`quantity`)
            FROM `invoice_products_extension`
            WHERE `invoice_products_extension`.`invoice_id` =  `assign`.`invoice_id` AND `invoice_products_extension`.`is_always` = 0)'), 'assign_total_tax'=> new Expression('(SELECT SUM(((`assign_products`.`price_sale`+`products_invoice`.`total_price_extention`)*`products_invoice`.`quantity` + (`assign_products`.`price_sale`+`products_invoice`.`total_price_extention`)*`products_invoice`.`quantity`*`products_invoice`.`vat`/100))
            FROM `assign_products` 
            INNER JOIN `products_invoice` ON `assign_products`.`products_invoice_id` = `products_invoice`.`id`
            WHERE `assign_products`.`assign_id` =  `assign`.`assign_id`)'), 'assign_extention_total_tax'=> new Expression('(SELECT SUM(`invoice_products_extension`.`price`*`invoice_products_extension`.`quantity` + `invoice_products_extension`.`price`*`invoice_products_extension`.`quantity`*`products_invoice`.`vat`/100)
            FROM `invoice_products_extension`
            INNER JOIN `products_invoice` ON `invoice_products_extension`.`products_id` = `products_invoice`.`products_id` AND `invoice_products_extension`.`products_type_id` = `products_invoice`.`products_type_id`
            WHERE `products_invoice`.`invoice_id` =  `assign`.`invoice_id` AND `invoice_products_extension`.`invoice_id` =  `assign`.`invoice_id` AND `invoice_products_extension`.`is_always` = 0)')));
        $select->from('assign');
        $select->join('invoice', 'invoice.invoice_id = assign.invoice_id ' ,array('invoice_title', 'total', 'total_old', 'total_orig', 'total_tax', 'total_old_tax', 'total_orig_tax', 'value_ship'), 'left');
        $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee' => new Expression("IFNULL(`invoice_shipping`.`shipping_fee`, 0)")), 'left');
        if(isset($where['assign_name'])){
            $select->where->like('assign.assign_name', "%{$where['assign_name']}%");
        }

        if(isset($where['assign_code'])){
            $select->where->like('assign.assign_code', "%{$where['assign_code']}%");
        }

        if(isset($where['invoice_title'])){
            $invoice_title = $where['invoice_title'];
            $select->where->like('invoice.invoice_title', "%{$invoice_title}%");
        }

        $select->group('assign.assign_id');

        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }

        $select->order(array(
            'assign.assign_read' => 'DESC',
            'assign.assign_date' => 'DESC',
        ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function getTotalAssigns( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("COUNT(assign.assign_id)")));
        $select->from('assign');
        $select->join('invoice', 'invoice.invoice_id = assign.invoice_id ' ,array(), 'left');

        if(isset($where['assign_name'])){
            $select->where->like('assign.assign_name', "%{$where['assign_name']}%");
        }

        if(isset($where['assign_code'])){
            $select->where->like('assign.assign_code', "%{$where['assign_code']}%");
        }

        if(isset($where['invoice_title'])){
            $invoice_title = $where['invoice_title'];
            $select->where->like('invoice.invoice_title', "%{$invoice_title}%");
        }

        $select->group('assign.assign_id');
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getAssign( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('assign');
        $select->join('invoice', 'invoice.invoice_id = assign.invoice_id ' ,array('invoice_title', 'total', 'total_old', 'total_orig', 'total_tax', 'total_old_tax', 'total_orig_tax', 'value_ship'), 'left');
        $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
        $select->join('assign_merchant',new Expression("`assign`.`assign_id`=`assign_merchant`.`assign_id` AND (`assign_merchant`.`assign_merchant_status` = 'accept' OR `assign_merchant`.`assign_merchant_status` = 'finish' )"),array(), 'left');
        $select->join('merchant',new Expression("`merchant`.`merchant_id`=`assign_merchant`.`merchant_id`"),array('merchant_id', 'merchant_type', 'merchant_name', 'merchant_alias', 'merchant_phone', 'merchant_email', 'merchant_fax', 'merchant_avatar', 'merchant_note', 'address', 'address01', 'zipcode', 'longitude', 'latitude', 'country_id', 'city', 'state', 'suburb', 'region', 'province', 'cities_id', 'districts_id', 'wards_id'), 'left');
        if( !empty($where['assign_id']) ){
            $select->where(array(
                'assign.assign_id' => $where['assign_id']
            ));
        }
        if( !empty($where['invoice_id']) ){
            $select->where(array(
                'assign.invoice_id' => $where['invoice_id']
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

    public function getAssignProducts( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('assign_products');
        $select->join('assign', 'assign.assign_id = assign_products.assign_id ' ,array());
        if( !empty($where['assign_id']) ){
            $select->where(array(
                'assign.assign_id' => $where['assign_id']
            ));
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function createAssign( $row )
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'users_id' => $row['users_id'],
                'invoice_id' => $row['invoice_id'],
                'assign_code' => $row['assign_code'],
                'assign_name' => $row['assign_name'],
            );
            if( !empty($row['merchant_id']) ){
                $data['assign_status'] = 'pending';
            }
            if( !empty($row['shipping']) ){
                $shipping  = str_replace(',', '', $row['shipping']);
                $data['assign_shipping'] = $shipping;
            }
            $assign_id = (int)$row['assign_id'];
            if( empty($assign_id) ){
                $this->tableGateway->insert($data);
                $assign_id = $this->getLastestId();
            }else{
                if ( $this->getAssign( array('assign_id' => $assign_id) ) ) {
                    $this->tableGateway->update($data, array('assign_id' => $assign_id));
                } else {
                    return 0;
                }
            }

            if( !empty($row['price']) ){
                foreach ( $row['price'] as $id => $price ) {
                    $price  = str_replace(',', '', $price);
                    $insert = "INSERT INTO assign_products(assign_id, products_invoice_id, price_sale) VALUES ({$assign_id}, {$id}, '{$price}')";
                    $adapter->query($insert, $adapter::QUERY_MODE_EXECUTE);
                }
            }

            if( !empty($row['merchant_id']) ){
                foreach ( $row['merchant_id'] as $key => $merchant_id ) {
                    $insert = "INSERT INTO assign_merchant(assign_id, merchant_id, assign_merchant_status) VALUES ({$assign_id}, {$merchant_id}, 'pending')";
                    $adapter->query($insert, $adapter::QUERY_MODE_EXECUTE);
                }
                $updateIv = "UPDATE invoice SET delivery='processing' WHERE invoice_id={$row['invoice_id']} AND delivery='pending'";
                $adapter->query($updateIv, $adapter::QUERY_MODE_EXECUTE);
            }

            $adapter->getDriver()->getConnection()->commit();
            return $assign_id;
        } catch (\Exception $e) {
            $adapter->getDriver()->getConnection()->rollback();
            echo $e->getMessage();die();
            return 0;
        }
        return 0;
    }

}