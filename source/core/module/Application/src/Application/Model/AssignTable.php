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

use Application\Model\AppTable;

class AssignTable extends AppTable
{
    public function getAssignByDayOfMember($users_id ,$from_date, $to_date){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':AssignTable:getAssignByDayOfMember('.(is_array($users_id)? implode('-',$users_id) : $users_id).','.$from_date.','.$to_date.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('assign_id', 'assign_code', 'assign_name', 'total' => new Expression('(SELECT SUM(`assign_products`.`price_sale`*`products_invoice`.`quantity`)
            FROM `assign_products` 
            INNER JOIN `products_invoice` ON `assign_products`.`products_invoice_id` = `products_invoice`.`id`
            WHERE `assign_products`.`assign_id` =  `assign`.`assign_id`)'), 'assign_unit', 'assign_status', 'assign_read', 'assign_date', 'ratio_usd'));
            $select->from('assign');
            $select->join('assign_merchant', new Expression("`assign`.`assign_id`=`assign_merchant`.`assign_id` AND (`assign_merchant`.`assign_merchant_status` = 'accept' OR `assign_merchant`.`assign_merchant_status` = 'finish' )"), array('assign_merchant_status', 'date_create' => 'assign_merchant_date_send'));
            $select->join('merchant', 'assign_merchant.merchant_id = merchant.merchant_id', array('merchant_name', 'merchant_alias', 'merchant_phone', 'merchant_email', 'merchant_fax', 'merchant_note', 'merchant_type'));
            $select->join('users', 'users.merchant_id = merchant.merchant_id', array());
            $select->join('invoice', 'assign.invoice_id=invoice.invoice_id', array('invoice_id', 'website_id', 'first_name', 'last_name', 'full_name', 'phone', 'email', 'type_address_delivery', 'country_id', 'address', 'address01', 'city', 'state', 'suburb', 'region', 'province', 'zipcode', 'cities_id', 'districts_id', 'wards_id', 'users_id', 'transportation_id', 'shipping_id', 'is_free_shipping', 'transport_type', 'invoice_title', 'invoice_description', 'ship_to_different_address', 'ships_first_name', 'ships_last_name', 'ships_full_name', 'ships_email', 'ships_phone', 'ships_country_id', 'ships_address', 'ships_address01', 'ships_city', 'ships_state','ships_suburb', 'ships_region', 'ships_province', 'ships_zipcode', 'ships_cities_id', 'ships_districts_id', 'ships_wards_id', 'ships_fee', 'from_currency', 'to_currency', 'rate_exchange', 'total_converter', 'is_published', 'is_delete', 'payment', 'delivery', 'date_update', 'promotion', 'total_old', 'total_orig', 'total_tax', 'total_old_tax', 'total_orig_tax', 'content', 'company_name', 'company_tax_code', 'company_address', 'email_subscription', 'payment_id', 'payment_code', 'pay_date', 'transactionid', 'ip_addr', 'user_agent', 'vnp_pay_date', 'vnp_transaction_no', 'vpc_transaction_no', 'is_view', 'is_wholesale', 'commission'));
            $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
            $select->where(array(
                'users.website_id' => $this->getWebsiteId(),
                'users.users_id'=>    $users_id,
            ));
            if( !empty($from_date) && !empty($to_date) ){
                $select->where('(STR_TO_DATE(assign_merchant.assign_merchant_date_send, \'%Y-%m-%d\') >= "'.$from_date.'" AND STR_TO_DATE(assign_merchant.assign_merchant_date_send, \'%Y-%m-%d\') <= "'.$to_date.'" )');
            }
            $select->group('assign.assign_id');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
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

    public function getAssigns( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('assign');
        $select->join('invoice', 'invoice.invoice_id = assign.invoice_id', array('invoice_title', 'total', 'total_old', 'total_orig', 'total_tax', 'total_old_tax', 'total_orig_tax', 'value_ship','delivery'));
        $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
        $select->join('assign_merchant', 'assign_merchant.assign_id = assign.assign_id', array('assign_merchant_status', 'is_read', 'is_important', 'assign_merchant_date_send', 'assign_merchant_date_relay'));
        $select->join('merchant', 'assign_merchant.merchant_id = merchant.merchant_id', array('merchant_name', 'merchant_alias', 'merchant_phone', 'merchant_email', 'merchant_fax', 'merchant_note', 'merchant_type'));
        $select->join('users', 'users.merchant_id = merchant.merchant_id', array('users_id', 'merchant_id'));
        $select->where(array(
            'users.website_id'=>$this->getWebsiteId()
        ));
        if( !empty($where['assign_id']) ){
            $select->where(array(
                'assign.assign_id' => $where['assign_id']
            ));
        }
        if( !empty($where['users_id']) ){
            $select->where(array(
                'users.users_id' => $where['users_id']
            ));
        }
        if( !empty($where['assign_merchant_status']) ){
            $select->where(array(
                'assign_merchant.assign_merchant_status' => $where['assign_merchant_status']
            ));
        }
        if( isset($where['is_read']) ){
            $select->where(array(
                'assign_merchant.is_read' => $where['is_read']
            ));
        }
        if( isset($where['is_important']) ){
            $select->where(array(
                'assign_merchant.is_important' => $where['is_important']
            ));
        }
        $select->order(array(
            'assign.assign_date' => 'DESC'
        ));
        $select->group('assign.assign_id');
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
        $select = $sql->select();
        $select->from('assign');
        $select->join('invoice', 'invoice.invoice_id = assign.invoice_id', array());
        $select->join('assign_merchant', 'assign_merchant.assign_id = assign.assign_id', array());
        $select->join('merchant', 'assign_merchant.merchant_id = merchant.merchant_id', array());
        $select->join('users', 'users.merchant_id = merchant.merchant_id', array());
        $select->where(array(
            'users.website_id'=>$this->getWebsiteId()
        ));
        if( !empty($where['assign_id']) ){
            $select->where(array(
                'assign.assign_id' => $where['assign_id']
            ));
        }
        if( !empty($where['users_id']) ){
            $select->where(array(
                'users.users_id' => $where['users_id']
            ));
        }
        if( !empty($where['assign_merchant_status']) ){
            $select->where(array(
                'assign_merchant.assign_merchant_status' => $where['assign_merchant_status']
            ));
        }
        if( isset($where['is_read']) ){
            $select->where(array(
                'assign_merchant.is_read' => $where['is_read']
            ));
        }
        if( isset($where['is_important']) ){
            $select->where(array(
                'assign_merchant.is_important' => $where['is_important']
            ));
        }
        $select->group('assign.assign_id');
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return COUNT($results);
        } catch (\Exception $e) {}
        return 0;
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

    public function getAssign( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('assign_id', 'assign_code', 'assign_name', 'assign_shipping', 'assign_status', 'assign_read', 'assign_date','invoice_id', 'assign_total'=> new Expression('(SELECT SUM(`assign_products`.`price_sale`*`products_invoice`.`quantity`)
            FROM `assign_products` 
            INNER JOIN `products_invoice` ON `assign_products`.`products_invoice_id` = `products_invoice`.`id`
            WHERE `assign_products`.`assign_id` =  `assign`.`assign_id`)')));
        $select->from('assign');
        $select->join('invoice', 'invoice.invoice_id = assign.invoice_id', array('invoice_title', 'total', 'total_old', 'total_orig', 'total_tax', 'total_old_tax', 'total_orig_tax', 'value_ship','delivery'));
        $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
        $select->join('assign_merchant', 'assign_merchant.assign_id = assign.assign_id', array('assign_merchant_status', 'is_read', 'is_important', 'assign_merchant_date_send', 'assign_merchant_date_relay'));
        $select->join('merchant', 'assign_merchant.merchant_id = merchant.merchant_id', array('merchant_name', 'merchant_alias', 'merchant_phone', 'merchant_email', 'merchant_fax', 'merchant_note', 'merchant_type'));
        $select->join('users', 'users.merchant_id = merchant.merchant_id', array('users_id', 'merchant_id'));
        if( !empty($where['assign_id']) ){
            $select->where(array(
                'assign.assign_id' => $where['assign_id']
            ));
        }
        if( !empty($where['users_id']) ){
            $select->where(array(
                'users.users_id'=>    $where['users_id']
            ));
        }
        $select->group( 'assign.assign_id' );
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->current();
            return $result;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function createAssign( $products, $row )
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'assign_code' => $row['assign_code'],
                'assign_name' => $row['assign_name'],
                'total_money' => $row['total_money'],
                'assign_unit' => $row['assign_unit'],
                'assign_status' => 'pending'
            );
            $assign_id = (int)$row['assign_id'];
            if( empty($assign_id) ){
                $this->tableGateway->insert($data);
                $assign_id = $this->getLastestId();

                $insert = "INSERT INTO assign_user(assign_id,users_id,assign_user_status,assign_user_date_send) VALUES ({$assign_id}, {$row['publisher_id']}, 'pending', '".date('Y-m-d H:m:s')."')";
                $adapter->query($insert, $adapter::QUERY_MODE_EXECUTE);

                foreach ($products as $products_id => $product) {
                    foreach ($product as $products_type_id => $type ) {
                        $insert = "INSERT INTO assign_products(assign_id,products_invoice_id) VALUES ({$assign_id}, {$type['products_invoice_id']})";
                        $adapter->query($insert, $adapter::QUERY_MODE_EXECUTE);
                    }
                }

            }else{
                if ( $this->getAssign( array('assign_id' => $assign_id) ) ) {
                    $this->tableGateway->update($data, array('assign_id' => $assign_id));

                    $selectString = "SELECT * FROM assign_user WHERE assign_id = {$assign_id} AND users_id = {$publisher_id}";
                    $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                    $result = $result->current();
                    if( empty($result) ){
                        $insert = "INSERT INTO assign_user(assign_id,users_id,assign_user_status,assign_user_date_send) VALUES ({$assign_id}, {$row['publisher_id']}, 'pending', '".date('Y-m-d H:m:s')."')";
                        $adapter->query($insert, $adapter::QUERY_MODE_EXECUTE);
                    }

                    $updateAu = "UPDATE assign_user SET assign_user_status='pending', assign_user_date_send='".date('Y-m-d H:m:s')."' WHERE assign_id={$assign_id}";
                    $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);


                } else {
                    return 0;
                }
            }

            $updateIv = "UPDATE invoice SET delivery='processing' WHERE invoice_id={$row['invoice_id']} AND delivery='pending'";
            $adapter->query($updateIv, $adapter::QUERY_MODE_EXECUTE);

            $adapter->getDriver()->getConnection()->commit();
            return $assign_id;
        } catch (\Exception $e) {
            $adapter->getDriver()->getConnection()->rollback();
            return 0;
        }
        return 0;
    }

    public function accept( $row )
    {
        if( !empty($row['users_id']) && !empty($row['merchant_id']) 
            && !empty($row['assign_id']) && !empty($row['invoice_id']) ){
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            try {
                $updateAu = "UPDATE assign_merchant SET assign_merchant_status='accept', assign_merchant_date_relay='".date('Y-m-d H:m:s')."' WHERE assign_id={$row['assign_id']} AND merchant_id={$row['merchant_id']}";
                $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);

                $updateAs = "UPDATE assign SET assign_status='accept', users_id={$row['users_id']} WHERE assign_id={$row['assign_id']}";
                $adapter->query($updateAs, $adapter::QUERY_MODE_EXECUTE);

                $updateAu = "UPDATE invoice SET delivery='accept' WHERE invoice_id={$row['invoice_id']}";
                $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);

                $insertLog = "INSERT INTO user_logs(users_id,action,type,user_logs_date,reference_id) VALUES ({$row['users_id']}, 'accept', 'assign', '".date('Y-m-d H:m:s')."', {$row['assign_id']})";
                $adapter->query($insertLog, $adapter::QUERY_MODE_EXECUTE);

                $adapter->getDriver()->getConnection()->commit();
                return TRUE;
            } catch (\Exception $e) {
                $adapter->getDriver()->getConnection()->rollback();
            }
        }
        return FALSE;
    }

    public function cancel( $row )
    {
        if( !empty($row['users_id']) && !empty($row['merchant_id']) && !empty($row['assign_id']) ){
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            try {
                $updateAu = "UPDATE assign_merchant SET assign_merchant_status='cancel', assign_merchant_date_relay='".date('Y-m-d H:m:s')."' WHERE assign_id={$row['assign_id']} AND merchant_id={$row['merchant_id']}";
                $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);

                $insertLog = "INSERT INTO user_logs(users_id,action,type,user_logs_date,reference_id) VALUES ({$row['users_id']}, 'cancel', 'assign', '".date('Y-m-d H:m:s')."', {$row['assign_id']})";
                $adapter->query($insertLog, $adapter::QUERY_MODE_EXECUTE);

                $adapter->getDriver()->getConnection()->commit();
                return TRUE;
            } catch (\Exception $e) {
                $adapter->getDriver()->getConnection()->rollback();
            }
        }
        return FALSE;
    }

    public function finish( $row )
    {
        if( !empty($row['users_id']) && !empty($row['merchant_id']) 
            && !empty($row['assign_id']) && !empty($row['invoice_id']) ){
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            try {
                $updateAu = "UPDATE assign_merchant SET assign_merchant_status='finish', assign_merchant_date_relay='".date('Y-m-d H:m:s')."' WHERE assign_id={$row['assign_id']} AND merchant_id={$row['merchant_id']}";
                $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);

                $updateAs = "UPDATE assign SET assign_status='finish', users_id={$row['users_id']} WHERE assign_id={$row['assign_id']}";
                $adapter->query($updateAs, $adapter::QUERY_MODE_EXECUTE);

                $updateAu = "UPDATE invoice SET delivery='finish' WHERE invoice_id={$row['invoice_id']}";
                $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);

                $insertLog = "INSERT INTO user_logs(users_id,action,type,user_logs_date,reference_id) VALUES ({$row['users_id']}, 'finish', 'assign', '".date('Y-m-d H:m:s')."', {$row['assign_id']})";
                $adapter->query($insertLog, $adapter::QUERY_MODE_EXECUTE);

                $adapter->getDriver()->getConnection()->commit();
                return TRUE;
            } catch (\Exception $e) {
                $adapter->getDriver()->getConnection()->rollback();
            }
        }
        return FALSE;
    }

    public function read( $row )
    {
        if( !empty($row['merchant_id']) && !empty($row['assign_id']) ){
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            try {
                $updateAu = "UPDATE assign_merchant SET is_read = 1 WHERE assign_id={$row['assign_id']} AND merchant_id={$row['merchant_id']}";
                $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);

                $adapter->getDriver()->getConnection()->commit();
                return TRUE;
            } catch (\Exception $e) {
                $adapter->getDriver()->getConnection()->rollback();
            }
        }
        return FALSE;
    }

    public function unread( $row )
    {
        if( !empty($row['merchant_id']) && !empty($row['assign_id']) ){
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            try {
                $updateAu = "UPDATE assign_merchant SET is_read = 0 WHERE assign_id={$row['assign_id']} AND merchant_id={$row['merchant_id']}";
                $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);

                $adapter->getDriver()->getConnection()->commit();
                return TRUE;
            } catch (\Exception $e) {
                $adapter->getDriver()->getConnection()->rollback();
            }
        }
        return FALSE;
    }

    public function important( $row )
    {
        if( !empty($row['merchant_id']) && !empty($row['assign_id']) ){
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            try {
                $updateAu = "UPDATE assign_merchant SET is_important = 1 WHERE assign_id={$row['assign_id']} AND merchant_id={$row['merchant_id']}";
                $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);

                $adapter->getDriver()->getConnection()->commit();
                return TRUE;
            } catch (\Exception $e) {
                $adapter->getDriver()->getConnection()->rollback();
            }
        }
        return FALSE;
    }

    public function unimportant( $row )
    {
        if( !empty($row['merchant_id']) && !empty($row['assign_id']) ){
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            try {
                $updateAu = "UPDATE assign_merchant SET is_important = 0 WHERE assign_id={$row['assign_id']} AND merchant_id={$row['merchant_id']}";
                $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);

                $adapter->getDriver()->getConnection()->commit();
                return TRUE;
            } catch (\Exception $e) {
                $adapter->getDriver()->getConnection()->rollback();
            }
        }
        return FALSE;
    }

}