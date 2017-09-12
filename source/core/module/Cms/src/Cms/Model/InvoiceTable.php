<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 2:53 PM
 */

namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class InvoiceTable extends AppTable
{
    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway);
    }

    public function getProductsInvoice( $invoice_id, $products_id, $products_type_id )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_invoice');
        $select->where(array(
            'invoice_id' => $invoice_id,
            'products_id' => $products_id,
            'products_type_id' => $products_type_id,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->current();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getLatLongInvoice( $invoice_id )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('invoice');
        $select->join('users', new Expression('users.users_id = invoice.users_id') , array('longitude', 'latitude'), 'left');
        $select->where(array(
            'invoice.website_id' => $this->getWebsiteId(),
            'invoice.invoice_id' => $invoice_id
        ));
        $select->group('invoice.invoice_id');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->current();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function fetchAll( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('*','total_products' => new Expression('count(products_invoice.products_id)')));
        $select->from('invoice');
        $select->join('products_invoice', 'products_invoice.invoice_id = invoice.invoice_id', array());
        $select->join('assign',new Expression('`assign`.`invoice_id`=`invoice`.`invoice_id`'),array('assign_status'), 'left');
        $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee' => new Expression("IFNULL(`invoice_shipping`.`shipping_fee`, 0)")), 'left');
        $select->where(array(
            'invoice.website_id' => $this->getWebsiteId(),
        ));
        if(isset($where['products_id'])){
            $select->where(array(
                'products_invoice.products_id' => $where['products_id'],
            ));
        }

        if(isset($where['invoice_title'])){
            $invoice_title = $where['invoice_title'];
            $select->where->like('invoice_title', "%{$invoice_title}%");
        }

        if(isset($where['full_name'])){
            $select->where->like('full_name', "%{$where['full_name']}%");
        }

        if(isset($where['date_create'])){
            $date_create = $where['date_create'];
            $between = explode(' _to_ ', $date_create);
            if(count($between) == 2){
                $select->where("invoice.date_create BETWEEN '{$between[0]}' AND '{$between[1]}'");
            }
        }
        if(isset($where['payment'])){
            $select->where(array(
                'invoice.payment' => $where['payment'],
            ));
        }
        if(isset($where['delivery'])){
            $select->where(array(
                'invoice.delivery' => $where['delivery'],
            ));
        }
        $select->where(array(
            'invoice.is_delete' => 0,
        ));
        if( empty($where['order']) ){
            $select->order(array(
                'invoice.date_create' => 'DESC',
            ));
        }else{
            $select->order($where['order']);
        }
        $select->group('invoice.invoice_id');
        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){}
        return array();
    }

    public function countAll( $where = array() ){
        /**
         * @var $adapter \Zend\Db\Adapter\Adapter
         */
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(invoice.invoice_id)")));
        $select->from('invoice');
        $select->join('products_invoice', 'products_invoice.invoice_id = invoice.invoice_id', array());
        $select->where(array(
            'website_id' => $this->getWebsiteId(),
        ));
        if(isset($where['products_id'])){
            $select->where(array(
                'products_invoice.products_id' => $where['products_id'],
            ));
        }

        if(isset($where['invoice_title'])){
            $invoice_title = $where['invoice_title'];
            $select->where->like('invoice_title', "%{$invoice_title}%");
        }

        if(isset($where['full_name'])){
            $select->where->like('full_name', "%{$where['full_name']}%");
        }

        if(isset($where['date_create'])){
            $date_create = $where['date_create'];
            $between = explode(' _to_ ', $date_create);
            if(count($between) == 2){
                $select->where("invoice.date_create BETWEEN '{$between[0]}' AND '{$between[1]}'");
            }
        }
        if(isset($where['payment'])){
            $select->where(array(
                'invoice.payment' => $where['payment'],
            ));
        }
        if(isset($where['delivery'])){
            $select->where(array(
                'invoice.delivery' => $where['delivery'],
            ));
        }
        $select->where(array(
            'invoice.is_delete' => 0,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            if( !empty($results) )
                return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function getInvoice($id)
    {

        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('invoice_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getCouponByInvoice($id){
        $id = (int)$id;
        try{
            /**
             * @var $adapter \Zend\Db\Adapter\Adapter
             */
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('invoice_coupon');
            $select->where(array(
                'invoice_id' => $id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            if(!$results){
                throw new \Exception('Nothing here');
            }
            $results = $results->toArray();
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getDetailInvoice($id){
        try {
            $id = (int)$id;
            /**
             * @var $adapter \Zend\Db\Adapter\Adapter
             */
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('invoice');
            $select->join('assign', 'invoice.invoice_id = assign.invoice_id ' ,array('assign_id'), 'left');
            $select->join('cities', 'invoice.cities_id = cities.cities_id', array('cities_title'), 'left');
            $select->join('districts', 'invoice.districts_id = districts.districts_id', array('districts_title'), 'left');
            $select->join('wards', 'invoice.wards_id = wards.wards_id', array('wards_title'), 'left');
            $select->where(array(
                'invoice.invoice_id' => $id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            if(!$results){
                throw new \Exception('Row not found');
            }
            if( empty($results->is_view)){
                $update = "UPDATE invoice SET is_view=1 WHERE invoice_id={$id}";
                $adapter->query($update, $adapter::QUERY_MODE_EXECUTE);
            }
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * @param Invoice $m
     * @param \Zend\Http\Request $request
     * @return bool
     */
    public function updateInvoice(Invoice $m, $request = NULL)
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'website_id' => $this->getWebsiteId(),
                'invoice_title' => $m->invoice_title,
                'promotion' => $m->promotion,
                'users_id' => $m->users_id,
                'payment' => $m->payment,
                'delivery' => $m->delivery,
                'invoice_description' => $m->invoice_description,
                'is_published' => $m->is_published,
                'company_name' => $m->company_name,
                'company_tax_code' => $m->company_tax_code,
                'company_address' => $m->company_address
            );

            $id = (int)$m->invoice_id;
            if ($this->getInvoice($id)) {
                $this->tableGateway->update($data, array('invoice_id' => $id));
            } else {
                throw new \Exception('id does not exist');
            }
            $adapter->getDriver()->getConnection()->commit();
            return TRUE;
        } catch (\Exception $e) {
            $adapter->getDriver()->getConnection()->rollback();
            $_SESSION['error_message'] = $e->getMessage();
            return FALSE;
        }
    }

    public function insertCoupon($invoice_id, $coupon){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('invoice_coupon');
        $insert->columns(array('invoice_id','coupon_id','coupon_code','date_create','coupon_price','price_used'));
        $insert->values(array(
            'invoice_id' => $invoice_id,
            'coupon_id' => $coupon['coupons_id'],
            'coupon_code' => $coupon['coupons_code'],
            'coupon_price' => $coupon['coupon_price'],
            'date_create' => date('Y-m-d H:i:s'),
            'price_used' => $coupon['price_used'],
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getLastestId()
    {
        return $this->tableGateway->getLastInsertValue();
    }

    public function insertProducts($invoiceid, $products = array())
    {
        $delete_sql = "DELETE FROM products_invoice WHERE invoice_id={$invoiceid}";
        $sql = "INSERT INTO products_invoice(invoice_id,is_delete,is_published,products_id,quantity,total) VALUES ";
        $val = array();
        foreach($products as $product){
            $val[] = "({$invoiceid},0,1, {$product['products_id']}, {$product['quantity']}, {$product['total']})";
        }
        $val = implode(',', $val);
        $sql .= $val;
        try{
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($delete_sql, $adapter::QUERY_MODE_EXECUTE);
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function getProducts($invoiceid){
        /*$sql = "SELECT products_invoice.*,products.price, products.products_code, products.products_title,products.products_alias,
                        products_type.type_name, products_type.price, products_type.price_sale, products_type.is_default
                FROM products_invoice
                INNER JOIN products ON products_invoice.products_id=products.products_id
                LEFT JOIN products_type ON products.products_id=products_type.products_id AND products_invoice.products_type_id=products_type.products_type_id
                WHERE invoice_id={$invoiceid}";*/

        $sql = "SELECT products_invoice.*,products.price, products.products_code, products_title,products_alias
                    FROM products_invoice
                    INNER JOIN products ON products_invoice.products_id=products.products_id
                    LEFT JOIN products_type ON products.products_id=products_type.products_id AND products_invoice.products_type_id=products_type.products_type_id
                    WHERE invoice_id={$invoiceid} ";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            return $results;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function getProductsCart($invoiceid){
        try{
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns( array('id','products_id', 'products_type_id', 'invoice_id', 'promotion', 'price', 'price_sale','quantity','total_price_extention', 'vat', 'total', 'assign_id','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 'id_detect_group' => new Expression("CONCAT(`products_invoice`.`products_id`, '_', `products_invoice`.`products_type_id`)") ) );
            $select->from('products_invoice');
            $select->join('products', 'products_invoice.products_id=products.products_id', array('products_code' => new Expression("IFNULL(`products`.`products_code`, `products_invoice`.`products_code`)"), 'type_view'), 'left');
            $select->join('products_translate', new Expression("products.products_id=products_translate.products_id AND products_translate.language = '{$this->getLanguagesId()}'"), array('products_title' => new Expression("IFNULL(`products_translate`.`products_title`, `products_invoice`.`products_title`)"), 'products_alias'),'left');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_invoice`.`products_type_id` = `products_type`.`products_type_id`'),array(), 'left');
            $select->join('products_type_translate', new Expression("products_type_translate.products_type_id=products_type.products_type_id AND products_type_translate.language={$this->getLanguagesId()}" ), array('type_name' => new Expression("IFNULL(`products_type_translate`.`type_name`, `products_invoice`.`type_name`)"), 'thumb_image' => new Expression("IFNULL(`products`.`thumb_image`, IFNULL(`products_type`.`thumb_image`, `products_invoice`.`thumb_image`))")),'left');
            $select->where(array(
                'invoice_id' => $invoiceid
            ));
            $select->group( 'id_detect_group' );
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $e){}
        return array();
    }

    public function getInvoiceExtensions($invoice_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('invoice_products_extension');
            $select->join ( 'invoice', 'invoice.invoice_id=invoice_products_extension.invoice_id', array() );
            $select->where(array(
                'invoice.invoice_id' => $invoice_id
            ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getShipping($invoice_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('invoice_shipping');
        $select->join ( 'invoice', 'invoice.invoice_id=invoice_shipping.invoice_id', array() );
        $select->where(array(
            'invoice.invoice_id' => $invoice_id
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
        }catch(\Exception $ex){
            $results = array();
        }
        return $results;
    }

    public function getExtRequire($invoice_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('invoice_products_ext_require');
        $select->join ( 'invoice', 'invoice.invoice_id=invoice_products_ext_require.invoice_id', array() );
        $select->where(array(
            'invoice.invoice_id' => $invoice_id
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getCouponUsed($invoice_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('invoice_coupon');
        $select->where(array(
            'invoice_id' => $invoice_id,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            if(!$results){
                return FALSE;
            }
            return (array)$results;
        }catch(\Exception $ex){
            return FALSE;
        }
    }

    public function getAllUsers()
    {
        try {
            $sql = "SELECT users_id, user_name, first_name, last_name FROM users WHERE website_id = {$this->getWebsiteId()}";
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateContent($id, $data)
    {
        $set = array();
        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $pre = "";
            } else {
                $pre = "'";
            }
            $set[] = "{$key}={$pre}{$value}{$pre}";
        }
        $set = implode(',', $set);
        $sql = "UPDATE invoice SET {$set} WHERE invoice_id={$id}";
        try {
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            if(array_key_exists('payment', $data)){
                if($data['payment'] == 'paid'){
                    $update = "UPDATE products SET quantity=quantity-1 WHERE products_id=(SELECT products_id FROM products_invoice WHERE invoice_id={$id})";
                    $adapter->query($update, $adapter::QUERY_MODE_EXECUTE);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function filter($data){
        $where = array();
        $select = "";
        $where[] = "invoice.is_delete=0 AND website_id = {$this->getWebsiteId()}";
        if (isset($data['invoice_title']) && trim($data['invoice_title']) != '') {
            $data['invoice_title'] = mb_strtolower(trim($data['invoice_title']));
            $where[] = "LCASE(invoice_title) LIKE '%{$data['invoice_title']}%'";
        }
        if (isset($data['products_id']) && $data['products_id'] != 0) {
            $where[] = "products_invoice.products_id = {$data['products_id']}";
            $select .= ",products_invoice.quantity as total_products";
        }
        if (isset($data['date_create']) && trim($data['date_create']) != '') {
            $range = explode('-', $data['date_create']);
            if (count($range) == 2) {
                $start = explode('/', trim($range[0]));
                if (count($start) == 3) {
                    $temp[0] = $start[2];
                    $temp[1] = $start[0];
                    $temp[2] = $start[1];
                    $start = implode('-', $temp) . ' 00:00:00';
                    $end = explode('/', trim($range[1]));
                    if (count($end) == 3) {
                        $temp[0] = $end[2];
                        $temp[1] = $end[0];
                        $temp[2] = $end[1];
                        $end = implode('-', $temp) . ' 00:00:00';
                        $where[] = "invoice.date_create BETWEEN '{$start}' AND '{$end}'";
                    }
                }
            }
        }
        if($data['payment']){
            $where[] = "invoice.payment='{$data['payment']}'";
        }
        if($data['delivery']){
            $where[] = "invoice.delivery='{$data['delivery']}'";
        }
        $where = implode(' AND ', $where);
        $sql = "SELECT DISTINCT `invoice`.* {$select}
                FROM {$this->tableGateway->table}
                INNER JOIN products_invoice ON products_invoice.invoice_id = {$this->tableGateway->table}.invoice_id
                WHERE {$where}";
                //die($sql);
        try {
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function countNewInvoice(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total_new' => new Expression('count(invoice.invoice_id)')));
        $select->from('invoice');
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0,
            'is_view' => 0,
            'website_id' => $this->getWebsiteId(),
        ));

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->current();
            if(!$result){
                return 0;
            }
            return $result['total_new'];
        }catch (\Exception $ex){
            return 0;
        }
    }


    /**
     * @param $coupon_code
     * @throws \Exception
     * @return bool|array
     */
    public function getCouponByCode($coupon_code){
        /**
         * @var $adapter \Zend\Db\Adapter\Adapter
         */
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('coupons');
        $select->where("coupons_code LIKE '{$coupon_code}' AND NOW() BETWEEN start_date AND expire_date AND website_id = {$this->getWebsiteId()}");
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            if($results){
                $results = (array)$results;
                $selectString = "SELECT * FROM invoice_coupon WHERE coupon_id={$results['coupons_id']}";
                $rows = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $rows = $rows->toArray();
                if(count($rows)){
                    if($results['coupons_type']){
                        $total_used = array_map(function($r){return $r['price_used'];}, $rows);
                        $total_used = array_sum($total_used);
                        if($total_used >= $results['coupon_price']){
                            return FALSE;
                        }
                        $results['coupon_price'] -= $total_used;
                        return $results;
                    }
                    return FALSE;
                }
            }
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex);
        }
    }

    public function deleteInvoice($ids = array()){

    }

    public function deleteProducts(){

    }

    protected function getIdCol()
    {
        return 'invoice_id';
    }

    public function getSubTotalInvoiceByDay($from_date, $to_date){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('invoice_id', 'total', 'date_create'));
        $select->from('invoice');
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0,
            'website_id' => $this->getWebsiteId(),
        ));
        $select->where('(invoice.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->toArray();
    }

    public function sumSubTotalInvoiceByDay($from_date, $to_date){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression('SUM(invoice.total)')));
        $select->from('invoice');
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0,
            'website_id' => $this->getWebsiteId(),
        ));
        $select->where('(invoice.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $results->current();
            return $row['total'];
        }catch (\Exception $ex){
            return 0;
        }
    }

    public function countSubTotalInvoiceByDay($from_date, $to_date){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression('count(invoice.invoice_id)')));
        $select->from('invoice');
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0,
            'website_id' => $this->getWebsiteId(),
        ));
        $select->where('(invoice.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $results->current();
            return $row['total'];
        }catch (\Exception $ex){
            return 0;
        }
    }

    public function getProductOnInvoiceByDay($from_date, $to_date, $intPage = 0, $intPageSize = 5){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array());
        $select->from('invoice');
        $select->join('products_invoice', 'invoice.invoice_id = products_invoice.invoice_id', array());
        $select->join('products', 'products_invoice.products_id = products.products_id', array('*'));
        $select->where(array(
            'invoice.is_published' => 1,
            'invoice.is_delete' => 0,
            'invoice.website_id' => $this->getWebsiteId(),
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.website_id' => $this->getWebsiteId(),
        ));
        $select->where('(invoice.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
        $select->group('products.products_id');
        $select->offset($intPage);
        $select->limit($intPageSize);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->toArray();
    }

    public function getProductOnInvoiceByInvoiceIdAndProductsId($invoice_id, $products_id, $products_type_id = 0){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('invoice_id'));
        $select->from('invoice');
        $select->join('products_invoice', 'invoice.invoice_id = products_invoice.invoice_id', array('products_invoice_id' => 'id', 'products_type_id', 'price', 'price_sale', 'quantity', 'total_price_extention', 'vat', 'total','promotion'));
        $select->join('products', 'products_invoice.products_id = products.products_id', array('products_id', 'products_code','categories_id','manufacturers_id','users_id','website_id','transportation_id','users_fullname','is_published', 'is_delete', 'is_new', 'is_hot', 'is_available', 'is_goingon', 'is_sellonline', 'tra_gop', 'date_create', 'date_update', 'hide_price', 'wholesale', 'ordering', 'thumb_image', 'list_thumb_image', 'number_views', 'vat', 'rating', 'number_like', 'total_sale', 'convert_search', 'is_viewed', 'position_view', 'youtube_video', 'price_total', 'url_crawl', 'convert_sitemap', 'convert_images', 'tags', 'type_view', 'publisher_id'));
        $select->join('products_translate', 'products_translate.products_id=products.products_id',array('products_title','products_alias','products_description','products_longdescription_2','products_longdescription','bao_hanh', 'products_more', 'seo_keywords', 'seo_description', 'seo_title', 'promotion_description', 'promotion1_description', 'promotion2_description', 'promotion3_description', 'tags', 'language'));
        $select->where(array(
            'products_invoice.products_id' => $products_id,
            'products_invoice.invoice_id' => $invoice_id,
            'products_invoice.products_type_id' => $products_type_id,
            'invoice.is_published' => 1,
            'invoice.is_delete' => 0,
            'invoice.website_id' => $this->getWebsiteId(),
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.website_id' => $this->getWebsiteId(),
            'products_translate.language'=>$this->getLanguagesId()
        ));
        $select->group('products.products_id');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->current();
    }

    public function deleteCategories($ids)
    {
        $this->tableGateway->delete(array('categories_id' => $ids));
    }

    public function updateInvoices($ids, $data)
    {
        $this->tableGateway->update($data, array('invoice_id' => $ids));
    }

    public function updateLog( $row ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('invoice_log');
        $insert->columns(array('invoice_id','users_id','user_name','has_feedback','invoice_status','comment','date_create'));
        $insert->values(array(
            'invoice_id' => $row['invoice_id'],
            'users_id' => $row['users_id'],
            'user_name' => $row['user_name'],
            'has_feedback' => $row['has_feedback'],
            'invoice_status' => $row['invoice_status'],
            'comment' => $row['comment'],
            'date_create' => date('Y-m-d H:i:s'),
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getLogs($invoice_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('invoice_log');
        $select->join('users', 'users.users_id = invoice_log.users_id', array('first_name', 'last_name'));
        $select->where(array(
            'invoice_log.invoice_id' => $invoice_id
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->toArray();
    }

    public function getReport( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('*','total_products' => new Expression('count(products_invoice.products_id)')));
        $select->from('invoice');
        $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
        $select->join('products_invoice', 'products_invoice.invoice_id = invoice.invoice_id', array());
        $select->join('assign',new Expression('`assign`.`invoice_id`=`invoice`.`invoice_id`'),array('assign_date','assign_status', 'assign_shipping', 'assign_total'=> new Expression('(SELECT SUM(`ap1`.`price_sale`*`pi1`.`quantity`)
            FROM `assign_products` AS ap1
            INNER JOIN `products_invoice` AS pi1 ON `ap1`.`products_invoice_id` = `pi1`.`id`
            WHERE `ap1`.`assign_id` =  `assign`.`assign_id`)')), 'left');
        $select->join('assign_merchant',new Expression("`assign`.`assign_id`=`assign_merchant`.`assign_id` AND (`assign_merchant`.`assign_merchant_status` = 'accept' OR `assign_merchant`.`assign_merchant_status` = 'finish' )"),array(), 'left');
        $select->join('merchant',new Expression("`merchant`.`merchant_id`=`assign_merchant`.`merchant_id`"),array('merchant_type', 'merchant_name', 'merchant_alias', 'merchant_phone', 'merchant_email', 'merchant_fax', 'merchant_avatar', 'merchant_note', 'merchant_address' => 'address', 'merchant_address01' => 'address01', 'merchant_zipcode' => 'zipcode', 'merchant_longitude' => 'longitude', 'merchant_latitude' => 'latitude', 'merchant_country_id' => 'country_id', 'merchant_city' => 'city', 'merchant_state' => 'state', 'merchant_suburb' => 'suburb', 'merchant_region' => 'region', 'merchant_province' => 'province', 'merchant_cities_id' => 'cities_id', 'merchant_districts_id' => 'districts_id', 'merchant_wards_id' => 'wards_id'), 'left');
        $select->join('users',new Expression("`assign`.`users_id`=`users`.`users_id`"),array('assign_first_name' => 'first_name', 'assign_last_name' => 'last_name'), 'left');
        $select->where(array(
            'invoice.website_id' => $this->getWebsiteId(),
        ));
        if(isset($where['products_id'])){
            $select->where(array(
                'products_invoice.products_id' => $where['products_id'],
            ));
        }

        if(isset($where['invoice_title'])){
            $invoice_title = $where['invoice_title'];
            $select->where->like('invoice_title', "%{$invoice_title}%");
        }

        if(isset($where['full_name'])){
            $select->where->like('full_name', "%{$where['full_name']}%");
        }

        if(isset($where['date_create'])){
            $date_create = $where['date_create'];
            $between = explode(' _to_ ', $date_create);
            if(count($between) == 2){
                $select->where("invoice.date_create BETWEEN '{$between[0]}' AND '{$between[1]}'");
            }
        }
        if(isset($where['payment'])){
            $select->where(array(
                'invoice.payment' => $where['payment'],
            ));
        }
        if(isset($where['delivery'])){
            $select->where(array(
                'invoice.delivery' => $where['delivery'],
            ));
        }
        $select->where(array(
            'invoice.is_delete' => 0,
        ));
        if( empty($where['order']) ){
            $select->order(array(
                'invoice.date_create' => 'DESC',
            ));
        }else{
            $select->order($where['order']);
        }
        $select->group('invoice.invoice_id');
        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){}
        return array();
    }

    public function countReport( $where = array() ){
        /**
         * @var $adapter \Zend\Db\Adapter\Adapter
         */
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(invoice.invoice_id)")));
        $select->from('invoice');
        $select->join('products_invoice', 'products_invoice.invoice_id = invoice.invoice_id', array());
        $select->where(array(
            'website_id' => $this->getWebsiteId(),
        ));
        if(isset($where['products_id'])){
            $select->where(array(
                'products_invoice.products_id' => $where['products_id'],
            ));
        }

        if(isset($where['invoice_title'])){
            $invoice_title = $where['invoice_title'];
            $select->where->like('invoice_title', "%{$invoice_title}%");
        }

        if(isset($where['full_name'])){
            $select->where->like('full_name', "%{$where['full_name']}%");
        }

        if(isset($where['date_create'])){
            $date_create = $where['date_create'];
            $between = explode(' _to_ ', $date_create);
            if(count($between) == 2){
                $select->where("invoice.date_create BETWEEN '{$between[0]}' AND '{$between[1]}'");
            }
        }
        if(isset($where['payment'])){
            $select->where(array(
                'invoice.payment' => $where['payment'],
            ));
        }
        if(isset($where['delivery'])){
            $select->where(array(
                'invoice.delivery' => $where['delivery'],
            ));
        }
        $select->where(array(
            'invoice.is_delete' => 0,
        ));
        $select->group('invoice.invoice_id');

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            if( !empty($results) )
                return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }


    public function getTotalRevenue( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression('SUM(`invoice`.`total` + IFNULL(`invoice_shipping`.`shipping_fee`, 0))')));
        $select->from('invoice');
        $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array(), 'left');
        $select->where(array(
            'invoice.website_id' => $this->getWebsiteId(),
        ));
        $select->where(" invoice.delivery != 'cancel' ");

        if(isset($where['invoice_title'])){
            $invoice_title = $where['invoice_title'];
            $select->where->like('invoice_title', "%{$invoice_title}%");
        }

        if(isset($where['full_name'])){
            $select->where->like('full_name', "%{$where['full_name']}%");
        }

        if(isset($where['date_create'])){
            $date_create = $where['date_create'];
            $between = explode(' _to_ ', $date_create);
            if(count($between) == 2){
                $select->where("invoice.date_create BETWEEN '{$between[0]}' AND '{$between[1]}'");
            }
        }

        if(isset($where['payment'])){
            $select->where(array(
                'invoice.payment' => $where['payment'],
            ));
        }

        if(isset($where['delivery'])){
            $select->where(array(
                'invoice.delivery' => $where['delivery'],
            ));
        }

        $select->where(array(
            'invoice.is_delete' => 0,
        ));

        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);//echo $selectString;die();
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            if( !empty($results) )
                return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function getTotalCost( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array());
        $select->from('invoice');
        $select->join('products_invoice', 'products_invoice.invoice_id = invoice.invoice_id', array());
        $select->join('assign',new Expression('`assign`.`invoice_id`=`invoice`.`invoice_id`'),array());
        $select->join('assign_products', 'assign_products.products_invoice_id = products_invoice.id', array('total' => new Expression('(SUM(`assign_products`.`price_sale`*`products_invoice`.`quantity`) + `assign`.`assign_shipping`)')));
        $select->where(array(
            'invoice.website_id' => $this->getWebsiteId(),
        ));
        $select->where(" (invoice.delivery != 'cancel' AND assign.assign_status IN('accept', 'finish')) ");

        if(isset($where['products_id'])){
            $select->where(array(
                'products_invoice.products_id' => $where['products_id'],
            ));
        }

        if(isset($where['invoice_title'])){
            $invoice_title = $where['invoice_title'];
            $select->where->like('invoice_title', "%{$invoice_title}%");
        }

        if(isset($where['full_name'])){
            $select->where->like('full_name', "%{$where['full_name']}%");
        }

        if(isset($where['date_create'])){
            $date_create = $where['date_create'];
            $between = explode(' _to_ ', $date_create);
            if(count($between) == 2){
                $select->where("invoice.date_create BETWEEN '{$between[0]}' AND '{$between[1]}'");
            }
        }

        if(isset($where['payment'])){
            $select->where(array(
                'invoice.payment' => $where['payment'],
            ));
        }

        if(isset($where['delivery'])){
            $select->where(array(
                'invoice.delivery' => $where['delivery'],
            ));
        }

        $select->where(array(
            'invoice.is_delete' => 0,
        ));

        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            if( !empty($results) )
                return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function getTotalCommission( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression('SUM((`invoice`.`total` + IFNULL(`invoice_shipping`.`shipping_fee`, 0))*(IFNULL(`invoice`.`commission`, 0)/100)')));
        $select->from('invoice');
        $select->join('products_invoice', 'products_invoice.invoice_id = invoice.invoice_id', array());
        $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array(), 'left');
        $select->where(array(
            'invoice.website_id' => $this->getWebsiteId(),
        ));
        $select->where(" invoice.delivery != 'cancel' ");
        if(isset($where['products_id'])){
            $select->where(array(
                'products_invoice.products_id' => $where['products_id'],
            ));
        }

        if(isset($where['invoice_title'])){
            $invoice_title = $where['invoice_title'];
            $select->where->like('invoice_title', "%{$invoice_title}%");
        }

        if(isset($where['full_name'])){
            $select->where->like('full_name', "%{$where['full_name']}%");
        }

        if(isset($where['date_create'])){
            $date_create = $where['date_create'];
            $between = explode(' _to_ ', $date_create);
            if(count($between) == 2){
                $select->where("invoice.date_create BETWEEN '{$between[0]}' AND '{$between[1]}'");
            }
        }

        if(isset($where['payment'])){
            $select->where(array(
                'invoice.payment' => $where['payment'],
            ));
        }

        if(isset($where['delivery'])){
            $select->where(array(
                'invoice.delivery' => $where['delivery'],
            ));
        }

        $select->where(array(
            'invoice.is_delete' => 0,
        ));

        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            if( !empty($results) )
                return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }


}