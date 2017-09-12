<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 3:18 PM
 */

namespace Application\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\ResultSet;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class UserTable extends AppTable {

    public function totalAssignMember($users_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:totalAssignMember('.(is_array($users_id)? implode('-',$users_id) : $users_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression("count(assign.assign_id)")));
            $select->from('assign');
            $select->join('invoice', 'invoice.invoice_id = assign.invoice_id', array());
            $select->join('assign_merchant', 'assign_merchant.assign_id = assign.assign_id', array());
            $select->join('merchant', 'assign_merchant.merchant_id = merchant.merchant_id', array());
            $select->join('users', 'users.merchant_id = merchant.merchant_id', array());
            $select->where(array(  
                'users.users_id'=>$users_id,
                'users.website_id'=>$this->getWebsiteId()
            ));
            $select->group('assign.assign_id');
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $result = $results->current();
                $results = 0;
                if ( !empty($result) ) {
                    $results = $result->total;
                }
                $cache->setITem($key, $results);
            }catch (\Exception $ex){
                $results = 0;
            }
        }
        return $results;
    }

    public function getAssignMember($users_id, $offset = 0, $limit= 5){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:getAssignMember('.(is_array($users_id)? implode('-',$users_id) : $users_id).','.$offset.','.$limit.')');
        $results = $cache->getItem($key);
        //if(!$results){
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
            $select->join('invoice', 'invoice.invoice_id = assign.invoice_id', array('invoice_title', 'total', 'total_old', 'total_orig', 'total_tax', 'total_old_tax', 'total_orig_tax', 'value_ship','delivery'));
            $select->join('invoice_shipping', 'invoice_shipping.invoice_id = invoice.invoice_id', array('shipping_fee' => new Expression("IFNULL(`invoice_shipping`.`shipping_fee`, 0)")), 'left');
            $select->join('assign_merchant', 'assign_merchant.assign_id = assign.assign_id', array('assign_merchant_status', 'is_read', 'is_important', 'assign_merchant_date_send', 'assign_merchant_date_relay'));
            $select->join('merchant', 'assign_merchant.merchant_id = merchant.merchant_id', array('merchant_name', 'merchant_alias', 'merchant_phone', 'merchant_email', 'merchant_fax', 'merchant_note', 'merchant_type'));
            $select->join('users', 'users.merchant_id = merchant.merchant_id', array());
            $select->where(array(  
                'users.users_id'=>$users_id,
                'users.website_id'=>$this->getWebsiteId()
            ));
            $select->group('assign.assign_id');
            $select->order(array(
                'assign.assign_date' => 'DESC'
            ));
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
        //}
        return $results;
    }

    public function register(User $user){
        $date = explode('/', $user->birthday);
        if(!empty($date[2])){
            $date = $date[2]."-".$date[1]."-".$date[0]." 00:00:00";
        }else{
            $date  = '';
        }

        $data = array(
            'website_id'         => $this->getWebsiteId(),
            'full_name'         => $user->full_name,
            'user_name'  => $user->user_name,
            'password'      => $this->passwordEncript($user->password),
            'users_alias'      => $user->users_alias,
            'birthday'      => $date,
            'phone'      => $user->phone,
            'country_id'      => $user->country_id,
            'address'      => $user->address,
            'address01'      => $user->address01,
            'city'      => $user->city,
            'state'      => $user->state,
            'suburb'      => $user->suburb,
            'region'      => $user->region,
            'zipcode'      => $user->zipcode,
            'province'      => $user->province,
            'cities_id'      => $user->cities_id,
            'districts_id'      => $user->districts_id,
        	'wards_id'      => $user->wards_id,
            'is_published'      => $user->is_published,
            'is_delete'      => $user->is_delete,
            'date_create'      => $user->date_create,
            'date_update'      => $user->date_update,
            'type'      => $user->type,
        );

        $id = (int) $user->users_id;
        if ($id == 0 && !$this->getUserByUserame($user->user_name)) {
            $this->tableGateway->insert($data);
            $_SESSION['MEMBER'] = $this->getUserByUserame($user->user_name);
            
        } else {
            if ($this->getUser($id)) {
                $this->tableGateway->update($data, array('users_id' => $id));
                $_SESSION['MEMBER'] = $this->getUserByUserame($user->user_name);
            } else {

                throw new \Exception('User id does not exist');
            }
        }

        return TRUE;
    }

    public function createUser($data){
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }

    public function getLastestId(){
        return $this->tableGateway->getLastInsertValue();
    }
    
    public function edit(User $user){
        $date = explode('/', $user->birthday);
        $date = $date[2]."-".$date[1]."-".$date[0]." 00:00:00";
        $data = array(
                'full_name'         => $user->full_name,
                'users_alias'      => $user->users_alias,
                'birthday'      => $date,
                'phone'      => $user->phone,
                'address_full'      => $user->address_full,
                'country_id'      => $user->country_id,
                'address'      => $user->address,
                'address01'      => $user->address01,
                'city'      => $user->city,
                'state'      => $user->state,
                'suburb'      => $user->suburb,
                'region'      => $user->region,
                'zipcode'      => $user->zipcode,
                'province'      => $user->province,
                'cities_id'      => $user->cities_id,
                'districts_id'      => $user->districts_id,
                'wards_id'      => $user->wards_id,
                'is_published'      => $user->is_published,
                'is_delete'      => $user->is_delete,
                'date_create'      => $user->date_create,
                'date_update'      => $user->date_update,
                'type'      => $user->type,
                
        );
        $id = (int) $user->users_id;        
        if ( !empty($id) ) {
        if ($this->getUser($id)) {
            //var_dump($data);die;
                $this->tableGateway->update($data, array('users_id' => $id, 'website_id'=>$this->getWebsiteId()));
                $_SESSION['MEMBER'] = $this->getUserById($id);
                //echo var_dump($_SESSION['MEMBER']) ;die;
            } else {
                throw new \Exception('User id does not exist');
            }
            //$_SESSION['MEMBER'] = $this->getUserByUserame($user->user_name);
        }
    
        return TRUE;
    }

    public function editPassword($password, $users_id){
        if ( $this->getUser($users_id) ) {
            $this->tableGateway->update(array('password'=>md5($password)), array('users_id' => $users_id, 'website_id'=>$this->getWebsiteId()));
        } else {
            return FALSE;
        }
        return TRUE;
    }

    public function editUserByArray($row, $id){
    	if ( $this->getUser($id) ) {
			$this->tableGateway->update($row, array('users_id' => $id, 'website_id'=>$this->getWebsiteId()));
			$_SESSION['MEMBER'] = $this->getUserById($id);
		} else {
			return FALSE;
		}
    	return TRUE;
    }

    public function getUser($id){
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('users_id' => $id, 'website_id'=>$this->getWebsiteId()));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getUserByUserame($username){
        $rowset = $this->tableGateway->select(array('user_name' => $username, 'website_id'=>$this->getWebsiteId()));
        $row = $rowset->current();
        if (!$row) {
            return FALSE;
        }
        return $row;
    }
    
    public function getUserById($id){   	
    	$sql = "SELECT * FROM `users` WHERE `users_id`='{$id}' AND website_id={$this->getWebsiteId()} ";
    	try{
    		$adapter = $this->tableGateway->getAdapter();
    		$result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
    		$result = $result->toArray();
    		if(count($result) > 0){
    			return $result[0];
    		}
    		return FALSE;
    	}catch (\Exception $e){
    		throw new \Exception($e->getMessage());
    	}
    }

    public function getOneAdminOfWebsite($website_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:getOneAdmin('.$website_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('users');
            $select->where(array(
                'website_id' => $website_id,
                'is_published'      => 1,
                'is_delete'      => 0,
                'is_administrator'      => 1,
                'type'      => 'admin'
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }
    

    public function getAllConfig(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:getAllConfig');
        $results = $cache->getItem($key);
        if(!$results){
            $sql = "SELECT settings.id,settings.name,settings.value,settings.description
                    FROM settings";
            try{
                $adapter = $this->tableGateway->getAdapter();
                $results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                $config = array();
                foreach($results as $result){
                    $config[$result->name] = $result->value;
                }
                $results =  $config; 
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function login($username, $password){
        $password = $this->passwordEncript($password);
        $sql = "SELECT *, (SELECT count(`merchant`.`merchant_id`) FROM `merchant` WHERE `users`.`merchant_id` = `merchant`.`merchant_id` ) AS is_merchant
                FROM    `users` 
                WHERE   `user_name`='{$username}' 
                        AND `password`='{$password}' 
                        AND website_id = {$this->getWebsiteId()} ";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            if(count($result) > 0){
                return $result[0];
            }
            return FALSE;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function loginWithWebsite($username, $password, $website_id){
        $password = $this->passwordEncript($password);
        $sql = "SELECT *, (SELECT count(`merchant`.`merchant_id`) FROM `merchant` WHERE `users`.`merchant_id` = `merchant`.`merchant_id` ) AS is_merchant FROM `users` WHERE `user_name`='{$username}' AND `password`='{$password}' AND website_id = {$website_id} ";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            if(count($result) > 0){
                return $result[0];
            }
            return array();
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function facebook_login($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('users');
        //$select->columns(array('users_id', 'avatar','facebookId', 'user_name', 'full_name', 'users_alias', 'birthday', 'phone', 'cities_id', 'districts_id', 'is_published', 'is_delete', 'date_update', 'date_create', 'type'));
        $select->where(array('user_name' => $data['user_name'], 'website_id'=>$this->getWebsiteId()));

        $selectString = $sql->getSqlStringForSqlObject($select);
        $row = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
		 $row = $row->toArray();
        try{			
            if(count($row)>0){	             
				if( !empty($row[0]['facebookId']) || $row[0]['facebookId'] != $data['facebookId']){
					$this->tableGateway->update(array('facebookId' => $data['facebookId']),array( 'users_id' => $row[0]['users_id'], 'website_id'=>$this->getWebsiteId()));
				}
                return $row[0];
            }else{				
                $data['password'] = $this->passwordEncript($data['password']);
                $this->tableGateway->insert($data);
                $row = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $row = $row->toArray();
                return $row[0];
            }
        }catch (\Exception $ex){
            //throw new \Exception($ex);
			return $ex;
        }
    }

    public function passwordEncript($password){
        return md5($password);
    }

    public function loadDistrict( $city = array() ){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:loadDistrict('.(is_array($city)? implode('-',$city) : $city).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('districts');
            $select->where(array(
                'is_published'      => 1,
                'is_delete'      => 0,
                'cities_id'      => $city,
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }

    public function loadCities( $country_id= array() ){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:loadCities('.(is_array($country_id)? implode('-',$country_id) : $country_id).')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('cities');
            $select->where(array(
                'is_published'      => 1,
                'is_delete'      => 0,
                'country_id'      => $country_id,
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }

    public function loadCitiesByArea( $area_id=array() ){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:loadCitiesByArea('.(is_array($area_id)? implode('-',$area_id) : $area_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('cities');
            $select->where(array(
                'is_published'      => 1,
                'is_delete'      => 0,
                'area_id'      => $area_id,
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }

    public function loadWard( $dist = array() ){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:loadWard('.(is_array($dist)? implode('-',$dist) : $dist).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('wards');
            $select->where(array(
                'is_published'      => 1,
                'is_delete'      => 0,
                'districts_id'      => $dist,
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }

    public function loadTransportations(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:loadTransportations('.$_SESSION['domain'].')');
        $results = $cache->getItem($key);
        if(!$results){
            $sql = "SELECT transportation_id,transportation_title
                    FROM `transportation`
                    WHERE is_published=1 AND is_delete=0 AND website_id={$this->getWebsiteId()} 
                            AND (({$_SESSION['website']['type_buy']} = 0 ) 
                                OR ({$_SESSION['website']['type_buy']} = 1 AND transportation_type = 0) 
                                OR ({$_SESSION['website']['type_buy']} = 2 AND transportation_type = 1))";
            try{
                $adapter = $this->tableGateway->getAdapter();
                $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getTransportationsById($transportation_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:getTransportationsById('.$transportation_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $sql = "SELECT *
                    FROM `transportation`
                    WHERE is_published=1 AND is_delete=0 AND website_id={$this->getWebsiteId()} 
                            AND transportation_id = {$transportation_id}";
            try{
                $adapter = $this->tableGateway->getAdapter();
                $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
                $results = $result->current();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }
    
	public function getTotalPoint($users_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:getTotalPoint('.(is_array($users_id)? implode('-',$users_id) : $users_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('point' => new Expression("SUM(users_point.point)")));
            $select->from('users_point');
            $select->where(array(  
                            'users_point.users_id'=>$users_id,
                            'users_point.website_id'=>$this->getWebsiteId()
                        ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $result = $results->toArray();
                $results = 0;
                if ( count($result) > 0) {
                    $results = $result[0]['point'];
                }
                $cache->setITem($key, $results);
            }catch (\Exception $ex){
                $results = 0;
            }
        }
        return $results;  
    }
	
	public function getPointUser($users_id, $offset){
	    $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:getPointUser('.(is_array($users_id)? implode('-',$users_id) : $users_id).';'.$offset.')');
        $results = $cache->getItem($key);
        if(!$results){
    		if ($offset <= 1) {
    			$offset = 0;
    		} else {
    			$offset = ($offset - 1) * PAGE_LIST_COUNT;
    		}
    		 
    		 
    		$adapter = $this->tableGateway->getAdapter();
    		$sql = new Sql($adapter);
    		$select = $sql->select();
    		$select->from('users_point');
            $select->join ( 'users', 'users.users_id=users_point.users_id', array());
    		$select->where(array('users_id' => $users_id, 'website_id'=>$this->getWebsiteId()));
    		$select->order('users_point.date_create DESC');
    	
    		$select->limit(PAGE_LIST_COUNT);
    		$select->offset($offset);
            try{
        		$selectString = $sql->getSqlStringForSqlObject($select);
        		$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
                $cache->setItem($key,$results);
    		}catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
	}

    public function getPaymentMethod(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:getPaymentMethod');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select('payment_method');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'website_id'=>$this->getWebsiteId()
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = $resultSet->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getLanguages(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:getLanguages');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('languages');
            $select->where(array(
                'is_published' => 1,
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;

    }

    public function getLanguageByCode($code){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':UserTable:getLanguageByCode');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('languages');
            $select->where(array(
                'is_published' => 1,
                'languages_file' => $code,
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function doSendDay()
    {
        /**
         * @var $adapter \Zend\Db\Adapter\Adapter
         */
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $selectString = "SELECT template_email.id,template_email.first_send,template_email.title,template_email.template,template_email.news_letter_groups_id
                             FROM template_email
                             INNER JOIN news_letter_groups ON template_email.news_letter_groups_id=news_letter_groups.news_letter_groups_id
                             WHERE cron_type=1 AND
                                   time_send=HOUR(CURTIME()) AND
                                   (
                                        first_send IS NULL OR
                                        (
                                          DATEDIFF(NOW(),first_send) !=0 AND
                                          DATEDIFF(NOW(),first_send) >= countEveryday
                                        )
                                   )";
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            foreach($results as $row){
                $email_list = $this->getEmailListByGroup($row['news_letter_groups_id']);
                if(count($email_list)){
                    $email_list = array_map(function($r){
                        return $r['email'];
                    }, $email_list);
                    $this->sendMail($email_list, 'Deal for today', html_entity_decode($row['template'],ENT_QUOTES, 'UTF-8'));
                }
                $updateSET = "countEveryday=countEveryday+1";
                if(!$row['first_send']){
                    $updateSET .= ',first_send=NOW()';
                }
                $updateString = "UPDATE template_email SET {$updateSET} WHERE id={$row['id']}";
                $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
            }
            $adapter->getDriver()->getConnection()->commit();
        }catch (\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function doSendWeek()
    {
        /**
         * @var $adapter \Zend\Db\Adapter\Adapter
         */
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $selectString = "SELECT template_email.id,template_email.first_send,template_email.title,template_email.template,template_email.news_letter_groups_id
                             FROM template_email
                             INNER JOIN news_letter_groups ON template_email.news_letter_groups_id=news_letter_groups.news_letter_groups_id
                             WHERE cron_type=2 AND
                                   time_send=HOUR(CURTIME()) AND
                                   day_of_week=DAYOFWEEK(NOW()) AND
                                   (
                                        first_send IS NULL OR
                                        (
                                          DATEDIFF(NOW(),first_send) !=0 AND
                                          DATEDIFF(NOW(),first_send) MOD 7 = 0 AND
                                          DATEDIFF(NOW(),first_send) MOD 7 < countEveryweek
                                        )
                                   )";
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            foreach($results as $row){
                $email_list = $this->getEmailListByGroup($row['news_letter_groups_id']);
                if(count($email_list)){
                    $email_list = array_map(function($r){
                        return $r['email'];
                    }, $email_list);
                    $this->sendMail($email_list, 'Deal for this week', html_entity_decode($row['template'],ENT_QUOTES, 'UTF-8'));
                }
                $updateSET = "countEveryweek=countEveryweek+1";
                if(!$row['first_send']){
                    $updateSET .= ',first_send=NOW()';
                }
                $updateString = "UPDATE template_email SET {$updateSET} WHERE id={$row['id']}";
                $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
            }
            $adapter->getDriver()->getConnection()->commit();
        }catch (\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function doSendMonth()
    {
        /**
         * @var $adapter \Zend\Db\Adapter\Adapter
         */
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $selectString = "SELECT template_email.id,template_email.first_send,template_email.title,template_email.template,template_email.news_letter_groups_id
                             FROM template_email
                             INNER JOIN news_letter_groups ON template_email.news_letter_groups_id=news_letter_groups.news_letter_groups_id
                             WHERE cron_type=3 AND
                                   time_send=HOUR(CURTIME()) AND
                                   day_of_month=DAY(NOW()) AND
                                   (
                                        first_send IS NULL OR
                                        (first_send IS NOT NULL AND MONTH(NOW()) != MONTH(first_send))
                                   )";
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            foreach($results as $row){
                $email_list = $this->getEmailListByGroup($row['news_letter_groups_id']);
                if(count($email_list)){
                    $email_list = array_map(function($r){
                        return $r['email'];
                    }, $email_list);
                    $this->sendMail($email_list, 'Deal for this month', html_entity_decode($row['template'],ENT_QUOTES, 'UTF-8'));
                }
                $updateSET = "countEverymonth=countEverymonth+1";
                $updateSET .= ',first_send=NOW()';
                $updateString = "UPDATE template_email SET {$updateSET} WHERE id={$row['id']}";
                $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
            }
            $adapter->getDriver()->getConnection()->commit();
        }catch (\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function getEmailListByGroup($gid){
        /**
         * @var $adapter \Zend\Db\Adapter\Adapter
         */
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('news_letters');
        $select->where(array(
            'news_letter_groups_id' => $gid,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function sendMail($to, $subject, $html){
        $html = new MimePart($html);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));

        $message = new Message();
        $message->addTo($to)
            ->addFrom(EMAIL_ADMIN_RECEIVE)
            ->setSubject($subject)
            ->setBody($body)
            ->setEncoding("UTF-8");

        // Setup SMTP transport using LOGIN authentication
        $transport = new SmtpTransport();
        $options = new SmtpOptions(array(
            'name' => HOST_MAIL,
            'host' => HOST_MAIL,
            'connection_class' => 'login',
            'connection_config' => array(
                'username' => USERNAME_HOST_MAIL,
                'password' => PASSWORD_HOST_MAIL,
            ),
        ));

        $transport->setOptions($options);
        $transport->send($message);
    }

    public function getTotalFreAlias($alias){
        $adapter = $this->tableGateway->getAdapter();
        $query = "SELECT SUM(users_id) as total
        FROM users
        WHERE users_alias LIKE '{$alias}%'";
        $results = $adapter->query($query, $adapter::QUERY_MODE_EXECUTE);
        return $results->current();

    }

    /*
     CREATE TABLE `users_point` (
     		`id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
     		`users_id` int NOT NULL,
     		`point_rule_id` int NOT NULL,
     		`date_create` datetime NOT NULL,
     		`point` int NOT NULL
     ) COMMENT='' ENGINE='MyISAM' COLLATE 'utf8_unicode_ci';
    */
} 
