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

class InvoiceTable extends GeneralTable
{
    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway);
    }

    public function fetchAll($where = array(), $order = array(), $intPage, $intPageSize)
    {
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }

        /**
         * @var $adapter \Zend\Db\Adapter\Adapter
         */
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('*','total_products' => new Expression('count(products_invoice.products_id)')));
        $select->from('invoice');
        $select->join('products_invoice', 'products_invoice.invoice_id = invoice.invoice_id', array());
        $select->where(array(
            'website_id' => $_SESSION['CMSMEMBER']['website_id'],
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
        $select->where("invoice.payment!='cancel'");
        if(!count($order)){
            $select->order(array(
                'invoice.date_create' => 'ASC',
            ));
        }else{
            $select->order($order);
        }
        $select->group('invoice.invoice_id');
        $select->limit($intPageSize);
        $select->offset($intPage);
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
        /*
        $where1 = "WHERE invoice.is_delete = 0";
        if ($where) {
            $where1 .= " AND {$where}";
        }
        $sql = "SELECT invoice.*,CASE WHEN count(products_invoice.products_id) IS NULL THEN 0 ELSE count(products_invoice.products_id) END as total_products
                FROM {$this->tableGateway->table}
                LEFT JOIN products_invoice ON products_invoice.invoice_id = {$this->tableGateway->table}.invoice_id
                {$where1}
                GROUP BY invoice.invoice_id
                {$order}
                LIMIT {$intPage}, {$intPageSize}";
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        return $result;
        */
    }

    public function countAll($where = array()){
        /*
        if($where){
            $where = "WHERE {$where}";
        }
        $sql = "SELECT count(invoice.invoice_id) as total
                FROM {$this->tableGateway->table}
                {$where}";
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
        */
        /**
         * @var $adapter \Zend\Db\Adapter\Adapter
         */
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('invoice_id'));
        $select->from('invoice');
        $select->join('products_invoice', 'products_invoice.invoice_id = invoice.invoice_id', array());
        $select->where(array(
            'website_id' => $_SESSION['CMSMEMBER']['website_id'],
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
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return count($result);
        }catch (\Exception $ex){
            return array();
        }
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
            $select->join('cities', 'invoice.cities_id = cities.cities_id', array('cities_title'), 'left');
            $select->join('districts', 'invoice.districts_id = districts.districts_id', array('districts_title'), 'left');
            $select->join('wards', 'invoice.wards_id = wards.wards_id', array('wards_title'), 'left');
            $select->where(array(
                'invoice_id' => $id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            if(!$results){
                throw new \Exception('Row not found');
            }
            $update = "UPDATE invoice SET is_view=1 WHERE invoice_id={$id}";
            $adapter->query($update, $adapter::QUERY_MODE_EXECUTE);
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
                'website_id' => $_SESSION['CMSMEMBER']['website_id'],
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
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('products_invoice');
            $select->join('products', 'products_invoice.products_id=products.products_id', array('products_title','products_alias','products_code'));
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_invoice`.`products_type_id` = `products_type`.`products_type_id`'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->where(array(
                'invoice_id' => $invoiceid
            ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->toArray();
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
            $results = $results->toArray();
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
            $sql = "SELECT * FROM users WHERE website_id = {$_SESSION['CMSMEMBER']['website_id']}";
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
        $where[] = "invoice.is_delete=0 AND website_id = {$_SESSION['CMSMEMBER']['website_id']}";
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
            'website_id' => $_SESSION['CMSMEMBER']['website_id'],
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
        $select->where("coupons_code LIKE '{$coupon_code}' AND NOW() BETWEEN start_date AND expire_date AND website_id = {$_SESSION['CMSMEMBER']['website_id']}");
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
}