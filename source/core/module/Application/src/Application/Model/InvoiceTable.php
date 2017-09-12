<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 2:53 PM
 */

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class InvoiceTable extends AppTable {

    public function getInvoiceByDayOfMember($users_id ,$from_date, $to_date){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getInvoiceByDayOfMember('.(is_array($users_id)? implode('-',$users_id) : $users_id).','.$from_date.','.$to_date.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('invoice');
            $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
            $select->where(array(
                'invoice.is_published' => 1,
                'invoice.is_delete' => 0,
                'invoice.website_id' => $this->getWebsiteId(),
                'invoice.users_id'=>    $users_id,
            ));
            if( !empty($from_date) && !empty($to_date) ){
                $select->where('(STR_TO_DATE(invoice.date_create, \'%Y-%m-%d\') >= "'.$from_date.'" AND STR_TO_DATE(invoice.date_create, \'%Y-%m-%d\') <= "'.$to_date.'" )');
            }
            $select->group('invoice.invoice_id');
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

    public function fetchAll($where = 0, $order = '', $intPage, $intPageSize)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:fetchAll('.(is_array($where)? implode('-',$where) : $where).'-'.$order.'-'.$intPage.'-'.$intPageSize.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                if ($intPage <= 1) {
                    $intPage = 0;
                } else {
                    $intPage = ($intPage - 1) * $intPageSize;
                }
                $where1 = "WHERE invoice.is_delete = 0 AND invoice.website_id = {$this->getWebsiteId()} ";
                if ($where) {
                    $where1 .= " AND {$where}";
                }
                $sql = "SELECT invoice.*
                        FROM {$this->tableGateway->table}
                        {$where1}
                        {$order}
                        LIMIT {$intPage}, {$intPageSize}";
                $adapter = $this->tableGateway->getAdapter();
                $results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getInvoice($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getInvoice('.(is_array($id)? implode('-',$id) : $id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('invoice');
                $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
                $select->join('cities', 'invoice.cities_id=cities.cities_id', array('cities_title'),'left');
                $select->join('districts', 'invoice.districts_id=districts.districts_id', array('districts_title'),'left');
                $select->join('wards', 'invoice.wards_id=wards.wards_id', array('wards_title'),'left');

                $select->join(array('ships_cities'=>'cities'), 'invoice.ships_cities_id=ships_cities.cities_id', array('ships_cities_title' => 'cities_title'),'left');
                $select->join(array('ships_districts'=>'districts'), 'invoice.ships_districts_id=ships_districts.districts_id', array('ships_districts_title' => 'districts_title'),'left');
                $select->join(array('ships_wards'=>'wards'), 'invoice.ships_wards_id=ships_wards.wards_id', array('ships_wards_title' => 'wards_title'),'left');

                $select->where(array(
                    'invoice.invoice_id' => $id,
                    'invoice.website_id'=>$this->getWebsiteId()
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getInvoiceNoJoinWebsite($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getInvoiceNoJoinWebsite('.(is_array($id)? implode('-',$id) : $id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('invoice');
                $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
                $select->join('cities', 'invoice.cities_id=cities.cities_id', array('cities_title'),'left');
                $select->join('districts', 'invoice.districts_id=districts.districts_id', array('districts_title'),'left');
                $select->join('wards', 'invoice.wards_id=wards.wards_id', array('wards_title'),'left');

                $select->join(array('ships_cities'=>'cities'), 'invoice.ships_cities_id=ships_cities.cities_id', array('ships_cities_title' => 'cities_title'),'left');
                $select->join(array('ships_districts'=>'districts'), 'invoice.ships_districts_id=ships_districts.districts_id', array('ships_districts_title' => 'districts_title'),'left');
                $select->join(array('ships_wards'=>'wards'), 'invoice.ships_wards_id=ships_wards.wards_id', array('ships_wards_title' => 'wards_title'),'left');

                $select->where(array(
                    'invoice.invoice_id' => $id
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getInvoiceByTitle($title){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getInvoiceByTitle('.(is_array($title)? implode('-',$title) : $title).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('invoice');
                $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
                $select->join('cities', 'invoice.cities_id=cities.cities_id', array('cities_title'),'left');
                $select->join('districts', 'invoice.districts_id=districts.districts_id', array('districts_title'),'left');
                $select->join('wards', 'invoice.wards_id=wards.wards_id', array('wards_title'),'left');

                $select->join(array('ships_cities'=>'cities'), 'invoice.ships_cities_id=ships_cities.cities_id', array('ships_cities_title' => 'cities_title'),'left');
                $select->join(array('ships_districts'=>'districts'), 'invoice.ships_districts_id=ships_districts.districts_id', array('ships_districts_title' => 'districts_title'),'left');
                $select->join(array('ships_wards'=>'wards'), 'invoice.ships_wards_id=ships_wards.wards_id', array('ships_wards_title' => 'wards_title'),'left');

                $select->where(array(
                    'invoice.invoice_title' => $title,
                    'invoice.website_id'=>$this->getWebsiteId()
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getInvoiceByCodeSha($id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getInvoiceByCodeSha('.(is_array($id)? implode('-',$id) : $id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('invoice');
                $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
                $select->join('cities', 'invoice.cities_id=cities.cities_id', array('cities_title'),'left');
                $select->join('districts', 'invoice.districts_id=districts.districts_id', array('districts_title'),'left');
                $select->join('wards', 'invoice.wards_id=wards.wards_id', array('wards_title'),'left');
                $select->where(array(
                    'invoice.website_id'=>$this->getWebsiteId()
                ));
                $select->where("SHA1(invoice.invoice_title) LIKE '$id'");
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getInvoiceByTitleNotJoinAddress($title){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getInvoiceByTitleNotJoinAddress('.(is_array($title)? implode('-',$title) : $title).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('invoice');
                $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
                $select->join('cities', 'invoice.cities_id=cities.cities_id', array('cities_title'),'left');
                $select->join('districts', 'invoice.districts_id=districts.districts_id', array('districts_title'),'left');
                $select->join('wards', 'invoice.wards_id=wards.wards_id', array('wards_title'),'left');
                $select->where(array(
                    'invoice.invoice_title' => $title,
                    'invoice.website_id'=>$this->getWebsiteId()
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function saveInvoice($dataSave, $dataCart, $shipping = array(), $dataExtension = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
			if(!empty($dataCart['coupon'])){
                $coupon = $dataCart['coupon'];
                unset($dataCart['coupon']);
            }
            if(!empty($dataCart['shipping'])){
                unset($dataCart['shipping']);
			}
            $dataSave['website_id']=$this->getWebsiteId();
            $this->tableGateway->insert($dataSave);
            $id = $this->getLastestId();
            $this->insertProducts($id, $dataCart);
            $this->updateStock($id, $dataCart);
            $this->insertExtension($id, $dataExtension);
			$this->insertShipping($id, $shipping);
			if(isset($coupon)){
				$this->insertCoupon($id, $coupon);
			}
            $adapter->getDriver()->getConnection()->commit();
            return $dataSave['invoice_title'];
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            unset($_SESSION['error_message']);
			// throw new \Exception($ex->getMessage());
			//die($ex->getMessage());
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
			// throw new \Exception($ex->getMessage());
			die();
		}
	}

	public function getCouponUsed($invoice_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getCouponUsed('.(is_array($invoice_id)? implode('-',$invoice_id) : $invoice_id).')');
        $results = $cache->getItem($key);
        if(!$results){
    		$adapter = $this->tableGateway->getAdapter();
    		$sql = new Sql($adapter);
    		$select = $sql->select();
    		$select->from('invoice_coupon');
            $select->join ( 'invoice', 'invoice.invoice_id=invoice_coupon.invoice_id', array() );
    		$select->where(array(
    			'invoice.invoice_id' => $invoice_id,
                'invoice.website_id'=>$this->getWebsiteId()
    		));
    		try{
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    			$results = $results->current();
                $cache->setItem($key, $results);
    		}catch(\Exception $ex){
    			$results = array();
    		}
        }
        return $results;
	}
	
    public function insertExtension($invoice_id, $dataExtension = array()){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('invoice_products_extension');
        $insert->columns(array('invoice_id','products_id','products_type_id','ext_name','price','quantity','is_available','is_always','type','refer_product_id'));
        foreach($dataExtension as $products_id => $products){
            foreach($products as $products_type_id => $products_type){
                foreach($products_type as $ext_id => $ext){
                    $insert->values(array(
                        'invoice_id' => $invoice_id,
                        'products_id' => $products_id,
                        'products_type_id' => $products_type_id,
                        'ext_name' => $ext['ext_name'],
                        'price' => $ext['price'],
                        'quantity' => $ext['quantity'],
                        'is_available' => $ext['is_available'],
                        'is_always' => $ext['is_always'],
                        'type' => $ext['type'],
                        'refer_product_id' => $ext['refer_product_id']
                    ));
                    $insertString = $sql->getSqlStringForSqlObject($insert);
                    $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
                }
            }
        }
    }
	
	public function insertShipping_bk($invoice_id, $shippings){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('invoice_shipping');
        $insert->columns(array('invoice_id','products_id','website_id','shipping_class','transportation_type','shipping_title','price_type','price','fee_percent','min_fee','cities_id','description'));
        try{
            if(!empty($shippings['public'])){
                $ship = $shippings['public'];
                $insert->values(array(
                    'invoice_id' => $invoice_id,
                    'products_id' => 0,
                    'website_id' => $this->getWebsiteId(),
                    'shipping_class' => $ship['shipping_class'],
                    'transportation_type' => $ship['transportation_type'],
                    'shipping_title' => $ship['shipping_title'],
                    'price_type' => $ship['price_type'],
                    'price' => $ship['price'],
                    'fee_percent' => $ship['fee_percent'],
                    'min_fee' => $ship['min_fee'],
                    'cities_id' => $ship['cities_id'],
                ));
                $insertString = $sql->getSqlStringForSqlObject($insert);
                $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
            }
            if(!empty($shippings['product'])){
                foreach($shippings['product'] as $products_id=>$ship){
                    $insert->values(array(
                        'invoice_id' => $invoice_id,
                        'products_id' => $products_id,
                        'website_id' => $this->getWebsiteId(),
                        'shipping_class' => $ship['shipping_class'],
                        'transportation_type' => $ship['transportation_type'],
                        'shipping_title' => $ship['shipping_title'],
                        'price_type' => $ship['price_type'],
                        'price' => $ship['price'],
                        'fee_percent' => $ship['fee_percent'],
                        'min_fee' => $ship['min_fee'],
                        'cities_id' => $ship['cities_id'],
                    ));
                    $insertString = $sql->getSqlStringForSqlObject($insert);
                    $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
                }
            }
        }catch(\Exception $ex){
            // throw new \Exception($ex->getMessage()); 
			die();
        }
    }

    public function insertShipping($invoice_id, $ship){
        if( !empty($ship) ){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $insert = $sql->insert('invoice_shipping');
            $insert->columns(array('invoice_id','website_id','shipping_title','shipping_type','shipping_from','shipping_to','shipping_value','shipping_fast_value','shipping_time','shipping_fee'));
            try{
                $insert->values(array(
                    'invoice_id' => $invoice_id,
                    'website_id' => $this->getWebsiteId(),
                    'shipping_title' => $ship['shipping_title'],
                    'shipping_type' => $ship['shipping_type'],
                    'shipping_from' => $ship['shipping_from'],
                    'shipping_to' => $ship['shipping_to'],
                    'shipping_value' => $ship['shipping_value'],
                    'shipping_fast_value' => $ship['shipping_fast_value'],
                    'shipping_time' => $ship['shipping_time'],
                    'shipping_fee' => $ship['shipping_fee'],
                ));
                $insertString = $sql->getSqlStringForSqlObject($insert);
                $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
            }catch(\Exception $ex){
                // throw new \Exception($ex->getMessage()); 
				die();
            }
        }
    }

    public function insertExtTransportation($invoice_id, $dataExtension){
		$adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('invoice_products_ext_require');
        $insert->columns(array('invoice_id','products_id','ext_require_type','ext_require_name','ext_require_price','ext_require_description','ext_require_cities'));
        try{
			foreach($dataExtension as $products_id => $exts){
                foreach ($exts as $index => $ext) {
                    if(empty($ext)){
                        continue;
                    }
                    $insert->values(array(
                        'invoice_id' => $invoice_id,
                        'products_id' => $ext['products_id'],
                        'ext_require_type' => $ext['transportation_type'],
                        'ext_require_name' => $ext['transportation_name'],
                        'ext_require_price' => $ext['transportation_price'],
                        'ext_require_description' => $ext['transportation_description'],
                        'ext_require_cities' => $ext['transportation_cities']
                    ));
                    $insertString = $sql->getSqlStringForSqlObject($insert);
                    $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
                }
			}
		}catch(\Exception $ex){
			// throw new \Exception($ex->getMessage()); 
			die();
		}
	}
	
	public function getExtRequire($invoice_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getExtRequire('.(is_array($invoice_id)? implode('-',$invoice_id) : $invoice_id).')');
        $results = $cache->getItem($key);
        if(!$results){
    		$adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('invoice_products_ext_require');
            $select->join ( 'invoice', 'invoice.invoice_id=invoice_products_ext_require.invoice_id', array() );
            $select->where(array(
                'invoice.invoice_id' => $invoice_id,
                'invoice.website_id'=>$this->getWebsiteId()
            ));
    		try{
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    			$results = $results->toArray();
    			$cache->setItem($key, $results);
    		}catch(\Exception $ex){
    			$results = array();
    		}
        }
        return $results;
	}

    public function getInvoiceExtensions($invoice_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getInvoiceExtensions('.(is_array($invoice_id)? implode('-',$invoice_id) : $invoice_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('invoice_products_extension');
            $select->join ( 'invoice', 'invoice.invoice_id=invoice_products_extension.invoice_id', array() );
            $select->where(array(
                'invoice.invoice_id' => $invoice_id,
                'invoice.website_id'=>$this->getWebsiteId()
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getShipping($invoice_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getShipping('.(is_array($invoice_id)? implode('-',$invoice_id) : $invoice_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('invoice_shipping');
            $select->join ( 'invoice', 'invoice.invoice_id=invoice_shipping.invoice_id', array() );
            $select->where(array(
                'invoice.invoice_id' => $invoice_id,
                'invoice.website_id'=>$this->getWebsiteId()
            ));
    		try{
    			$selectString = $sql->getSqlStringForSqlObject($select);
    			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    			$results = $results->current();
    			$cache->setItem($key, $results);
    		}catch(\Exception $ex){
    			$results = array();
    		}
        }
        return $results;
    }

    public function getCouponByInvoice($id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getCouponByInvoice('.(is_array($id)? implode('-',$id) : $id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('invoice_coupon');
                $select->where(array(
                    'invoice_id' => $id,
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch (\Exception $ex){}
        }
        return $results;
    }

    public function insertProducts($invoiceid, $products = array())
    {
        $productsHelper = $this->getServiceLocator()->get('viewhelpermanager')->get('Products');
        $sql = "INSERT INTO products_invoice(invoice_id,is_delete,is_published,products_id,products_type_id,price,price_sale,quantity,total_price_extention,vat,total, products_title, type_name, thumb_image, products_code) VALUES ";
        $val = array();
        foreach($products as $product){
            foreach($product['product_type'] as $product_type){
                //$total = $product['quantity'] * $product['price'];
                //$total += $total * $product['vat'] / 100;
                $price = $productsHelper->getPriceSimple($product_type);
                $price_sale = $productsHelper->getPriceSaleSimple($product_type);
                $total_price_extention = 0;
                if( !empty($product['total_price_extention']) ){
                    $total_price_extention = $product['total_price_extention'];
                }
                $products_type_id = 0;
                $thumb_image = $product_type['thumb_image'];
                if( !empty($product_type['products_type_id']) ){
                    $products_type_id = $product_type['products_type_id'];
                    $thumb_image = $product_type['t_thumb_image'];
                }
                $val[] = "({$invoiceid},0,1, {$product['id']},{$products_type_id}, {$price}, {$price_sale}, {$product_type['quantity']}, {$total_price_extention}, {$product['vat']}, {$product_type['price_total']}, '{$product_type['products_title']}', '{$product_type['type_name']}', '{$thumb_image}', '{$product_type['products_code']}')";
            }
        }
        $val = implode(',', $val);
        $sql .= $val;
        try{
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function updateStock($invoiceid, $products = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        foreach($products as $product){
            foreach($product['product_type'] as $product_type){
                $strUpdateStock = "UPDATE `products` SET `products`.`quantity` = (`products`.`quantity`-{$product_type['quantity']}) WHERE `products_id`='{$product['id']}'";
                $strUpdateMAXStock = "UPDATE `products` SET `products`.`quantity` = 0 WHERE `products_id`='{$product['id']}' AND `products`.`quantity` < 0";
                if( !empty($product_type['products_type_id']) ){
                    $strUpdateStock = "UPDATE `products_type` SET `products_type`.`quantity` = (`products_type`.`quantity`- {$product_type['quantity']}) WHERE `products_type_id`='{$product_type['products_type_id']}' AND `products_id`='{$product['id']}'";
                    $strUpdateMAXStock = "UPDATE `products_type` SET `products_type`.`quantity` = 0 WHERE `products_type_id`='{$product_type['products_type_id']}' AND `products_id`='{$product['id']}'  AND `products_type`.`quantity` < 0";
                }
                $adapter->query($strUpdateStock, $adapter::QUERY_MODE_EXECUTE);
                $adapter->query($strUpdateMAXStock, $adapter::QUERY_MODE_EXECUTE);
            }
        }
    }
	

    public function getProducts($invoiceid){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getProducts('.(is_array($invoiceid)? implode('-',$invoiceid) : $invoiceid).')');
        $results = $cache->getItem($key);
        if(!$results){
            $sql = "SELECT products_invoice.*,products.price, products.products_code, products_title,products_alias
                    FROM products_invoice
                    INNER JOIN products ON products_invoice.products_id=products.products_id
                    WHERE invoice_id={$invoiceid} AND products.website_id = {$this->getWebsiteId()} ";
            try{
                $adapter = $this->tableGateway->getAdapter();
                $results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
    			$results = $results->toArray();
                $cache->setItem($key, $results);
            }catch (\Exception $e){
                $results = array();
            }
        }
        return $results;
    }

    public function getProductsCart($invoiceid){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getProductsCart('.(is_array($invoiceid)? implode('-',$invoiceid) : $invoiceid).')');
        $results = $cache->getItem($key);
        if(!$results){
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
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch (\Exception $e){
                $results = array();
            }
        }
        return $results;
    }

    public function updateData($data, $invoice){
        if($this->getInvoice($invoice)){
            $this->tableGateway->update($data, array('invoice_id' => $invoice, 'website_id'=>$this->getWebsiteId()));
        }else{
            throw new \Exception('Invoice not exists');
        }
    }

    public function updateDataNoJoinWebsite($data, $invoice){
        if($this->getInvoiceNoJoinWebsite($invoice)){
            $this->tableGateway->update($data, array('invoice_id' => $invoice));
        }else{
            throw new \Exception('Invoice not exists');
        }
    }

    public function countProductsOfUser($users_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:countProductsOfUser('.(is_array($users_id)? implode('-',$users_id) : $users_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT products.products_id)")));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('products_invoice', 'products.products_id = products_invoice.products_id', array());
            $select->join('invoice', 'invoice.invoice_id = products_invoice.invoice_id', array());
            $select->where(array(  'invoice.users_id'=>$users_id,
                            'products.website_id'=>$this->getWebsiteId()
                        ));
            $select->group('products.products_id');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $result = $results->toArray();
                $results = 0;
                if ( count($result) > 0) {
                    $results = $result[0]['total'];
                }
                $cache->setITem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getProductsOfUser($users_id, $offset = 0, $limit= 5){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getProductsOfUser('.(is_array($users_id)? implode('-',$users_id) : $users_id).','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns( array('products_id', 'products_type_id','invoice_id', 'price', 'price_sale', 'quantity', 'total_price_extention', 'vat', 'total','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 'id_detect_group' => new Expression("CONCAT(`products_invoice`.`products_id`, '_', `products_invoice`.`products_type_id`)"), 'total_quantity' => new Expression("SUM(`products_invoice`.`quantity`)"), 'total_pay' => new Expression("SUM(`products_invoice`.`total`)") ) );
            $select->from('products_invoice');
            $select->join('products', 'products_invoice.products_id=products.products_id', array('products_code' => new Expression("IFNULL(`products`.`products_code`, `products_invoice`.`products_code`)"),
                    'categories_id',
                    'url_crawl',
                    'manufacturers_id',
                    'users_id',
                    'users_fullname',
                    'is_published',
                    'is_delete',
                    'is_new',
                    'is_hot',
                    'is_available',
                    'is_goingon',
                    'is_viewed',
                    'position_view',
                    'date_create',
                    'date_update',
                    'ordering',
                    'number_views',
                    'rating',
                    'number_like',
                    'total_sale',
                    'wholesale',
                    'type_view',
                    'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'), 'type_view'), 'left');
            $select->join('products_translate', new Expression("products.products_id=products_translate.products_id AND products_translate.language = '{$this->getLanguagesId()}'"), array('products_title' => new Expression("IFNULL(`products_translate`.`products_title`, `products_invoice`.`products_title`)"), 'products_alias', 'products_description'),'left');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_invoice`.`products_type_id` = `products_type`.`products_type_id`'),array('t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('products_type_translate', new Expression("products_type_translate.products_type_id=products_type.products_type_id AND products_type_translate.language={$this->getLanguagesId()}" ), array('type_name' => new Expression("IFNULL(`products_type_translate`.`type_name`, `products_invoice`.`type_name`)"), 'thumb_image' => new Expression("IFNULL(`products`.`thumb_image`, IFNULL(`products_type`.`thumb_image`, `products_invoice`.`thumb_image`))")),'left');
            $select->join('invoice', 'invoice.invoice_id = products_invoice.invoice_id', array());
            $select->where(array(  
                'invoice.users_id'=>$users_id
            ));
            $select->group( 'id_detect_group' );
            $select->offset($offset);
            $select->limit($limit);

            /*$select->columns(array(
                    'products_id',
                    'products_code',
                    'categories_id',
                    'url_crawl',
                    'manufacturers_id',
                    'users_id',
                    'users_fullname',
                    'products_title',
                    'products_alias',
                    'products_description',
                    'is_published',
                    'is_delete',
                    'is_new',
                    'is_hot',
                    'is_available',
                    'is_goingon',
                    'is_viewed',
                    'position_view',
                    'date_create',
                    'date_update',
                    'ordering',
                    'thumb_image',
                    'number_views',
                    'rating',
                    'number_like',
                    'total_sale',
                    'wholesale',
                    'type_view',
                    'title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),
                    'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')
                ));
            $select->from('products');
            $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
            $select->join('products_invoice', 'products.products_id = products_invoice.products_id', array('price', 'price_sale', 'quantity', 'total_price_extention', 'vat', 'total'));
            $select->join('invoice', 'invoice.invoice_id = products_invoice.invoice_id', array());
            $select->where(array(  'invoice.users_id'=>$users_id,
                            'products.website_id'=>$this->getWebsiteId()
                        ));
            $select->group('products.products_id');
            $select->offset($offset);
            $select->limit($limit);*/
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

    public function countInvoiceMember($users_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:countInvoiceMember('.(is_array($users_id)? implode('-',$users_id) : $users_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression("count(DISTINCT invoice.invoice_id)")));
            $select->from('invoice');
            $select->where(array(  'users_id'=>$users_id,
                            'website_id'=>$this->getWebsiteId()
                        ));

            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $result = $results->toArray();
                $results = 0;
                if ( count($result) > 0) {
                    $results = $result[0]['total'];
                }
                $cache->setITem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getInvoiceMember($users_id, $offset = 0, $limit= 5){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getInvoiceMember('.(is_array($users_id)? implode('-',$users_id) : $users_id).','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('invoice');
            $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
            $select->where(array(  
                'invoice.users_id'=>$users_id,
                'invoice.website_id'=>$this->getWebsiteId()
            ));
            $select->order('invoice.date_update DESC, invoice.invoice_id DESC');
            $select->offset($offset);
            $select->limit($limit);
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
	
	public function getCoupon($coupon_code){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getCoupon('.(is_array($coupon_code)? implode('-',$coupon_code) : $coupon_code).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('coupons');
            $select->where("coupons_code LIKE '{$coupon_code}' AND NOW() BETWEEN start_date AND expire_date AND coupons.website_id = {$this->getWebsiteId()}");
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
    			if($results){
    				$results = (array)$results;
    				$selectString = "SELECT * FROM invoice_coupon WHERE coupon_id={$results['coupons_id']}";
    				$rows = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    				$rows = $rows->toArray();
                    $max_use_coupon = 1;
                    if( !empty($results['coupons_type']) ){
                        $max_use_coupon = $results['coupons_max_use'];
                    }
    				if( $max_use_coupon < count($rows) ){
                        $results = array();
    					/*if( !empty($results['coupons_type']) ){
    						$total_used = array_map(function($r){
                                return $r['price_used'];
                            }, $rows);
    						$total_used = array_sum($total_used);
    						if($total_used >= $results['coupon_price']){
    							return FALSE;
    						}
    						$results['coupon_price'] -= $total_used;
    					}else{
                            $results = array();
                        }*/
    				}
    			}
    			$cache->setItem($key, $results);
            }catch (\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getSubTotalInvoiceByDay($from_date, $to_date){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getSubTotalInvoiceByDay('.$from_date.','.$to_date.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('invoice_id', 'total', 'date_create'));
            $select->from('invoice');
            $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
            $select->where(array(
                'invoice.is_published' => 1,
                'invoice.is_delete' => 0,
                'invoice.website_id' => $this->getWebsiteId(),
            ));
            $select->where('(invoice.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
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

    public function sumSubTotalInvoiceByDay($from_date, $to_date){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:sumSubTotalInvoiceByDay('.$from_date.','.$to_date.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression('SUM(`invoice`.`total` + IFNULL(`invoice_shipping`.`shipping_fee`, 0))')));
            $select->from('invoice');
            $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array(), 'left');
            $select->where(array(
                'invoice.is_published' => 1,
                'invoice.is_delete' => 0,
                'invoice.website_id' => $this->getWebsiteId()
            ));
            $select->where('(invoice.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $row = $results->current();
                $results = $row['total'];
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                return 0;
            }
        }
        return $results;
    }

    public function countSubTotalInvoiceByDay($from_date, $to_date){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:countSubTotalInvoiceByDay('.$from_date.','.$to_date.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression('SUM(`invoice`.`total` + IFNULL(`invoice_shipping`.`shipping_fee`, 0))')));
            $select->from('invoice');
            $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array(), 'left');
            $select->where(array(
                'invoice.is_published' => 1,
                'invoice.is_delete' => 0,
                'invoice.website_id' => $this->getWebsiteId()
            ));
            $select->where('(invoice.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $row = $results->current();
                $results = $row['total'];
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                return 0;
            }
        }
        return $results;
    }

    public function getProductOnInvoiceByDay($from_date, $to_date, $intPage = 0, $intPageSize = 5){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getProductOnInvoiceByDay('.$from_date.','.$to_date.','.$intPage.','.$intPageSize.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array());
            $select->from('invoice');
            $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
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

    public function getSubTotalInvoiceByDayOfMember($users_id ,$from_date , $to_date){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getSubTotalInvoiceByDayOfMember('.(is_array($users_id)? implode('-',$users_id) : $users_id).','.$from_date.','.$to_date.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('invoice_id', 'total', 'date_create'));
            $select->from('invoice');
            $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
            $select->where(array(
                'invoice.is_published' => 1,
                'invoice.is_delete' => 0,
                'invoice.website_id' => $this->getWebsiteId(),
                'invoice.users_id'=>    $users_id,
            ));
            $select->where('(invoice.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
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

    public function sumSubTotalInvoiceByDayOfMember($users_id ,$from_date , $to_date){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:sumSubTotalInvoiceByDayOfMember('.(is_array($users_id)? implode('-',$users_id) : $users_id).','.$from_date.','.$to_date.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression('SUM(`invoice`.`total` + IFNULL(`invoice_shipping`.`shipping_fee`, 0))')));
            $select->from('invoice');
            $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array(), 'left');
            $select->where(array(
                'invoice.is_published' => 1,
                'invoice.is_delete' => 0,
                'invoice.website_id' => $this->getWebsiteId(),
                'invoice.users_id'=>    $users_id,
            ));
            $select->where('(invoice.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $row = $results->current();
                $results = $row['total'];
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                return 0;
            }
        }
        return $results;
    }

    public function countSubTotalInvoiceByDayOfMember($users_id ,$from_date , $to_date){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:countSubTotalInvoiceByDayOfMember('.(is_array($users_id)? implode('-',$users_id) : $users_id).','.$from_date.','.$to_date.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression('count(invoice.invoice_id)')));
            $select->from('invoice');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'website_id' => $this->getWebsiteId(),
                'users_id'=>    $users_id,
            ));
            $select->where('(invoice.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $row = $results->current();
                $results = $row['total'];
                $cache->setItem($key, $results);
            }catch (\Exception $ex){
                return 0;
            }
        }
        return $results;
    }

    public function getProductOnInvoiceByDayOfMember($users_id ,$from_date, $to_date, $intPage = 0, $intPageSize = 5){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':InvoiceTable:getProductOnInvoiceByDayOfMember('.(is_array($users_id)? implode('-',$users_id) : $users_id).','.$from_date.','.$to_date.','.$intPage.','.$intPageSize.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array());
            $select->from('invoice');
            $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
            $select->join('products_invoice', 'invoice.invoice_id = products_invoice.invoice_id', array());
            $select->join('products', 'products_invoice.products_id = products.products_id', array('*'));
            $select->where(array(
                'invoice.is_published' => 1,
                'invoice.is_delete' => 0,
                'invoice.website_id' => $this->getWebsiteId(),
                'products.is_published' => 1,
                'products.is_delete' => 0,
                'products.website_id' => $this->getWebsiteId(),
                'invoice.users_id'=>    $users_id,
            ));
            $select->where('(invoice.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
            $select->group('products.products_id');
            $select->offset($intPage);
            $select->limit($intPageSize);
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

    public function getAllInvoiceOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('invoice');
        $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee'), 'left');
        $select->where(array(
            'invoice.website_id'=>$website_id,
            'invoice.is_delete'=>0,
            'invoice.is_published'=>1
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function getInvoiceProductsOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_invoice');
        $select->join('products', 'products_invoice.products_id = products.products_id', array());
        $select->join('invoice', 'products_invoice.invoice_id = invoice.invoice_id', array());
        $select->where(array(
            'products_invoice.is_published' => 1,
            'products_invoice.is_delete' => 0,
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.website_id' => $website_id,
            'invoice.website_id' => $website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertInvoiceProducts($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('products_invoice');
        $insert->columns(array('products_id','products_type_id','invoice_id','is_published','is_delete','promotion','price','price_sale','quantity','total_price_extention','vat','total'));
        $insert->values(array(
            'products_id' => $data['products_id'],
            'products_type_id' => $data['products_type_id'],
            'invoice_id' => $data['invoice_id'],
            'is_published' => $data['is_published'],
            'is_delete' => $data['is_delete'],
            'promotion' => $data['promotion'],
            'price' => $data['price'],
            'price_sale' => $data['price_sale'],
            'quantity' => $data['quantity'],
            'total_price_extention' => $data['total_price_extention'],
            'vat' => $data['vat'],
            'total' => $data['total'],
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            // throw new \Exception($ex->getMessage());
			die();
        }
    }

    public function getInvoiceProductsExtensionOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('invoice_products_extension');
        $select->join('products', 'invoice_products_extension.products_id = products.products_id', array());
        $select->join('invoice', 'invoice_products_extension.invoice_id = invoice.invoice_id', array());
        $select->where(array(
            'invoice.is_published' => 1,
            'invoice.is_delete' => 0,
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.website_id' => $website_id,
            'invoice.website_id' => $website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertInvoiceProductsExtension($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('invoice_products_extension');
        $insert->columns(array('invoice_id','products_id','ext_name','price','is_available','is_always','type','refer_product_id'));
        $insert->values(array(
            'invoice_id' => $data['invoice_id'],
            'products_id' => $data['products_id'],
            'ext_name' => $data['ext_name'],
            'price' => $data['price'],
            'is_available' => $data['is_available'],
            'is_always' => $data['is_always'],
            'type' => $data['type'],
            'refer_product_id' => $data['refer_product_id']
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            // throw new \Exception($ex->getMessage());
			die();
        }
    }

    public function getInvoiceShippingOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('invoice_shipping');
        $select->where(array(
            'invoice_shipping.website_id' => $website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertInvoiceShipping($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('invoice_shipping');
        $insert->columns(array('invoice_id','website_id','shipping_title','shipping_type','shipping_from','shipping_to','shipping_value','shipping_fast_value','shipping_time','shipping_fee'));
        $insert->values(array(
            'invoice_id' => $data['invoice_id'],
            'website_id' => $data['website_id'],
            'shipping_title' => $data['shipping_title'],
            'shipping_type' => $data['shipping_type'],
            'shipping_from' => $data['shipping_from'],
            'shipping_to' => $data['shipping_to'],
            'shipping_value' => $data['shipping_value'],
            'shipping_fast_value' => $data['shipping_fast_value'],
            'shipping_time' => $data['shipping_time'],
            'shipping_fee' => $data['shipping_fee'],
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            // throw new \Exception($ex->getMessage());
			die();
        }
    }

    public function getInvoiceCouponsOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('invoice_coupon');
        $select->join('coupons', 'invoice_coupon.coupon_id = coupons.coupons_id', array());
        $select->join('invoice', 'invoice_coupon.invoice_id = invoice.invoice_id', array());
        $select->where(array(
            'coupons.is_published' => 1,
            'coupons.website_id' => $website_id,
            'invoice.website_id' => $website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insertInvoiceCoupons($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('invoice_coupon');
        $insert->columns(array('invoice_id','coupon_id','coupon_code','date_create','price_used','coupon_price'));
        $insert->values(array(
            'invoice_id' => $data['invoice_id'],
            'coupon_id' => $data['coupon_id'],
            'coupon_code' => $data['coupon_code'],
            'date_create' => $data['date_create'],
            'price_used' => $data['price_used'],
            'coupon_price' => $data['coupon_price']
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            // throw new \Exception($ex->getMessage());
			die();
        }
    }

    public function insertInvoices($data)
    {
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }

    public function removeAllInvoicesOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function accept( $row )
    {
        if( !empty($row['invoice_id']) ){
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            try {
                $updateAu = "UPDATE invoice SET delivery = 'accept' WHERE invoice_id={$row['invoice_id']}";
                $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);

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
        if( !empty($row['invoice_id']) ){
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            try {
                $updateAu = "UPDATE invoice SET delivery = 'finish' WHERE invoice_id={$row['invoice_id']}";
                $adapter->query($updateAu, $adapter::QUERY_MODE_EXECUTE);

                $adapter->getDriver()->getConnection()->commit();
                return TRUE;
            } catch (\Exception $e) {
                $adapter->getDriver()->getConnection()->rollback();
            }
        }
        return FALSE;
    }

    public function saveLog( $row ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('cart_log');
        $insert->columns(array('session_id','users_id','email','step_sign','step_name','step_content'));
        $insert->values(array(
            'session_id' => $row['session_id'],
            'users_id' => $row['users_id'],
            'email' => $row['email'],
            'step_sign' => $row['step_sign'],
            'step_name' => $row['step_name'],
            'step_content' => $row['step_content'],
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){}
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
	

}