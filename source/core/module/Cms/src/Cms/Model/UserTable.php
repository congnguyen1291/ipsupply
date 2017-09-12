<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/23/14
 * Time: 10:14 AM
 */
namespace Cms\Model;

use Cms\Controller\UsersLevelController;
use Cms\Model\User;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

use Cms\Model\AppTable;

class UserTable extends AppTable
{

    public function getUsers( $datas = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array(
            'website_id','users_id','user_name', 'full_name','type','birthday','phone','address','cities_id','districts_id', 'is_published','is_delete','date_create','type','date_update'
        ));
        $select->from('users');
        
        $where['website_id'] = $this->getWebsiteId();
        if( !empty($datas['type']) ){
            $select->where(array(
                    'type' => $datas['type']
                ));
        }
        
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            return array();
        }
        return array();
    }

    public function getUserById($id)
    {
        try {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('users');
            $select->where(array(
                'users_id' => $id
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $results->current();
            if(!$row){
                throw new \Exception('User not found');
            }
            return $row;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getUserByAlias( $alias )
    {
        try {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('users');
            $select->where(array(
                'users_alias' => $alias
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $results->toArray();
            return $row;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getUserByUsername($username)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array(
            'website_id','users_id','user_name', 'full_name','type','birthday', 'cities_id', 'districts_id','phone','address','is_administrator','groups_id',
        ));
        $select->from('users');
        $select->where(array(
            'user_name' => $username,
            'website_id' => $this->getWebsiteId()
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function getUser($username, $password)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array(
            'website_id','users_id','user_name', 'full_name','type','birthday', 'cities_id', 'districts_id','phone','address','is_administrator','groups_id',
        ));
        $select->from('users');
        $select->where(array(
            'user_name' => $username,
            'password' => $this->passwordEncript($password)
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->current();
        return $results;
    }

    public function login(Login $login)
    {
        try {
            $user = $this->getUser($login->username, $login->password);
            if( !empty($user) ){
                $user = $user;
                //chi dang nhap bang adminstrator
                if($user['is_administrator'] == 1){
                    $_SESSION['CMSMEMBER'] = $user;
                   // if ($login->rememberme) {
                        setcookie("CMSMEMBER", json_encode($_SESSION['CMSMEMBER']), time() + 3600 * 24 * 30);
                   // }
                    return TRUE;
                }
            }
            return FALSE;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function logout()
    {
        if(isset($_SESSION['CMSMEMBER'])){
            unset($_SESSION['CMSMEMBER']);
            if (isset($_COOKIE['CMSMEMBER'])) {
                unset($_COOKIE['CMSMEMBER']);
                setcookie('CMSMEMBER', null, -1);
            }
        }
    }

    public function fetchAll( $where = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('*', 'step' => new Expression('(SELECT group_concat(`clog1`.`step_sign`  SEPARATOR \'-\' )
            FROM `cart_log` AS `clog1`
            WHERE `clog1`.`users_id` = `users`.`users_id`
            AND `clog1`.`session_id` = (SELECT `clog2`.`session_id`
            FROM `cart_log` AS `clog2`
            WHERE `clog2`.`users_id` = `users`.`users_id` ORDER BY `clog2`.`log_id` DESC LIMIT 0, 1 )
            ORDER BY `clog1`.`log_id` ASC)')));
        $select->from('users');
        $select->where(array(
            'website_id' => $this->getWebsiteId()
        ));

        if(isset($where['full_name'])){
            $select->where->like('full_name', "%{$where['full_name']}%");
        }

        if(isset($where['address'])){
            $select->where->like('address', "%{$where['address']}%");
        }

        if(isset($where['phone'])){
            $select->where->like('phone', "%{$where['phone']}%");
        }

        if(isset($where['user_name'])){
            $select->where->like('user_name', "%{$where['user_name']}%");
        }

        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $e) {}
        return array();
    }

    public function countAll($where = array()){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(users.users_id)")));
        $select->from('users');
        $select->where(array(
            'website_id' => $this->getWebsiteId()
        ));

        if(isset($where['full_name'])){
            $select->where->like('full_name', "%{$where['full_name']}%");
        }

        if(isset($where['address'])){
            $select->where->like('address', "%{$where['address']}%");
        }

        if(isset($where['phone'])){
            $select->where->like('phone', "%{$where['phone']}%");
        }

        if(isset($where['user_name'])){
            $select->where->like('user_name', "%{$where['user_name']}%");
        }
        
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $results->current();
            return $row['total'];
        } catch (\Exception $e) {}
        return array();
    }

    public function loadDistrict($city){
        $sql = "SELECT districts_id,districts_title
                FROM `districts`
                WHERE cities_id={$city} AND is_published=1 AND is_delete=0
                ORDER BY ordering";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
            $results = $result->toArray();
            if(count($results) > 0){
                return $results;
            }
            return FALSE;
        }catch(\Exception $e){
            throw new \Exception($e);
        }
    }

    public function loadCities($idcountry=241){
        $sql = "SELECT cities_id,cities_title
                FROM `cities`
                WHERE is_published=1 AND is_delete=0 and country_id='".$idcountry."'
                ORDER BY ordering";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
            $results = $result->toArray();
            if(count($results) > 0){
                return $results;
            }
            return FALSE;
        }catch(\Exception $e){
            throw new \Exception($e);
        }
    }
	public function loadCountry($where=""){
        $sql = "SELECT *
                FROM country
                WHERE status=1
				{$where}
                ORDER BY title";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
            $results = $result->toArray();
            if(count($results) > 0){
                return $results;
            }
            return FALSE;
        }catch(\Exception $e){
            throw new \Exception($e);
        }
    }
    public function loadCitiesByArea($area_id=0){
        $sql = "SELECT cities_id,cities_title
                FROM `cities`
                WHERE is_published=1 AND is_delete=0 AND area_id = {$area_id}
                ORDER BY ordering";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
            $results = $result->toArray();
            return $results;
        }catch(\Exception $e){
            return array();
        }
    }

    public function loadWard($dist){
        $sql = "SELECT wards_id,wards_title
                FROM `wards`
                WHERE districts_id={$dist} AND is_published=1 AND is_delete=0
                ORDER BY ordering";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
            $results = $result->toArray();
            if(count($results) > 0){
                return $results;
            }
            return FALSE;
        }catch(\Exception $e){
            throw new \Exception($e);
        }
    }

    public function saveUser(User $user){
        $data = array(
            'website_id'  => $this->getWebsiteId(),
            'parent_id'  => $user->parent_id,
            'create_by'  => $user->create_by,
            'user_name'  => $user->user_name,
            'first_name'      => $user->first_name,
            'last_name'      => $user->last_name,
            'full_name'      => $user->full_name,
            'users_alias'         => $user->users_alias,
            'birthday'       => $user->birthday,
            'phone'       => $user->phone,
            'address'         => $user->address,
            'address_full'          => $user->address_full,
            'address01'       => $user->address01,
            'zipcode'          => $user->zipcode,
            'longitude'             => $user->longitude,
            'latitude'           => $user->latitude,
            'country_id'         => $user->country_id,
            'city'      => $user->city,
            'state'              => $user->state,
            'suburb'              => $user->suburb,
            'region'         => $user->region,
            'province'         => $user->province,
            'cities_id'         => $user->cities_id,
            'districts_id'         => $user->districts_id,
            'wards_id'         => $user->wards_id,
            'is_published'         => $user->is_published,
            'is_delete'         => $user->is_delete,
            'date_update'         => $user->date_update,
            'type'         => $user->type,
            'is_administrator'         => $user->is_administrator,
            'groups_id'         => $user->groups_id,
            'merchant_id'         => $user->merchant_id,
        );
        if( !empty($user->password) ){
            $data['password']  = $this->passwordEncript($user->password);
        }
        $id = (int) $user->users_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->getLastestId();
        } else {
            if ($this->getUserById($id)) {
                if( empty($user->password) ){
                    unset($data['password']);
                }
                $this->tableGateway->update($data, array('users_id' => $id));
            } else {
                throw new \Exception('Row does not exist');
            }
        }
        return $id;
    }

    public function editUser($row, $where){
        $this->tableGateway->update($row, $where);
    }

    public function passwordEncript($password){
        return md5($password);
    }

    public function getUsersLevel($id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('users_level')
               ->where(array(
                    'users_level_id' => $id,
                ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
        $result = $result->current();
        if(!$result){
            throw new \Exception('Could not found row '.$id);
        }
        $user = new UsersLevel();
        $user->exchangeArray($result);
        return $user;
    }

    public function getUsersLevels( $where = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('*', 'total_user'=> new Expression('(SELECT COUNT(*)
            FROM `users` WHERE `users_level`.`users_level_id` = `users`.`users_level_id`)')));
        $select->from('users_level');
        $select->where(array(
            'website_id' => $this->getWebsiteId()
        ));
        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            $results = $result->toArray();
            return $results;
        }catch (\Exception $ex){}
        return array();
    }

    public function countUsersLevels( $where = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(users_level.users_level_id)")));
        $select->from('users_level');
        $select->where(array(
            'website_id' => $this->getWebsiteId()
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        }catch (\Exception $ex){}
        return 0;
    }

    public function saveUsersLevel(UsersLevel $user){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {

            $id = (int)$user->users_level_id;
            $data = array(
                'website_id' => $this->getWebsiteId(),
                'users_level_name' => $user->users_level_name,
                'users_level_decrease' => $user->users_level_decrease,
                'users_level_longdescription' => htmlentities($user->users_level_longdescription,ENT_QUOTES, 'UTF-8'),
                'min_buy' => $user->min_buy,
                'is_published' => $user->is_published,
                'date_create' => $user->date_create,
            );
            $sql = new Sql($adapter);
            if ($id == 0) {
                $insert = $sql->insert();
                $insert->into('users_level');
                $insert->columns(array('users_level_name','users_level_decrease','users_level_longdescription','min_buy','is_published','date_create'));
                $insert->values($data);
                $insertString = $sql->getSqlStringForSqlObject($insert);
                $adapter->query($insertString,$adapter::QUERY_MODE_EXECUTE);
            } else {
                if ($this->getUsersLevel($id)) {
                    $update = $sql->update('users_level');
                    $update->where(array('users_level_id' => $id));
                    $update->set($data);
                    $updateString = $sql->getSqlStringForSqlObject($update);
                    $adapter->query($updateString,$adapter::QUERY_MODE_EXECUTE);
                } else {
                    throw new \Exception('Row id does not exist');
                }
            }
            $adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function filter($params){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('users');
        $select->columns(array(
            'website_id','users_id','user_name', 'full_name','type','birthday','phone','address','cities_id','districts_id', 'is_published','is_delete','date_create','type','date_update'
        ));
        $select->join('groups','users.groups_id=groups.groups_id', array(),'left');
        if(isset($params['user_name']) && $params['user_name']){
            $select->where->like('user_name',"%{$params['user_name']}%");
        }
        $select->where(array(
            'users.groups_id' => $params['groups_id'],
            'users.website_id' => $this->getWebsiteId(),
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            if(!$results){
                return FALSE;
            }
            $results = $results->toArray();
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getCity($id){
        try {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('cities');
            $select->where(array(
                'cities_id' => $id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $row = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $row->current();
            if (!$row) {
                throw new \Exception('Row not found');
            }
            return (array)$row;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getDistrict($id){
        try {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('districts');
            $select->where(array(
                'districts_id' => $id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $row = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $row->current();
            if (!$row) {
                throw new \Exception('Row not found');
            }
            return (array)$row;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getWard($id){
        try {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('wards');
            $select->where(array(
                'wards_id' => $id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $row = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $row->current();
            if (!$row) {
                throw new \Exception('Row not found');
            }
            return (array)$row;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function removeUserOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function deleteUsers($ids)
    {
        $this->tableGateway->delete(array('users_id' => $ids));
    }

    public function updateUsers($ids, $data)
    {
        $this->tableGateway->update($data, array('users_id' => $ids));
    }

    public function deleteUsersLevel($ids)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->delete('users_level');
        $select->where(array(
            'users_level_id' => $ids
        ));
        $deleteString = $sql->getSqlStringForSqlObject($select);
        $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
    }

    public function updateUsersLevel($ids, $data)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $update = $sql->update();
        $update->table( 'users_level' );
        $update->set( $data );
        $update->where(array(
            'users_level_id' => $ids
        ));
        $updateString = $sql->getSqlStringForSqlObject($update);
        $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
    }

    public function getLogUser( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('cart_log');
        $select->join('users', new Expression('(users.users_id = cart_log.users_id) OR (users.user_name = cart_log.email) '), array());
        if( !empty($where['session_id']) ){
            $select->where(array(
                'cart_log.session_id' => $where['session_id'],
            ));
        }
        $select->order(array(
                'cart_log.date_create' => 'ASC',
            ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){}
        return array();
    }

    public function fetchLogUser( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('*', 'step' => new Expression('(SELECT group_concat(`clog1`.`step_name`  SEPARATOR \'-\' )
            FROM `cart_log` AS `clog1`
            WHERE `clog1`.`session_id` = `cart_log`.`session_id` )')));
        $select->from('cart_log');
        $select->join('users', new Expression('(users.users_id = cart_log.users_id) OR (users.user_name = cart_log.email) '), array());
        if( !empty($where['log_id']) ){
            $select->where(array(
                'cart_log.log_id' => $where['log_id'],
            ));
        }
        if( !empty($where['session_id']) ){
            $select->where(array(
                'cart_log.session_id' => $where['session_id'],
            ));
        }
        if( !empty($where['users_id']) ){
            if( is_array($where['users_id']) )
                $select->where( ' (cart_log.users_id  IN ('.implode(',', $where['users_id']).') OR users.users_id  IN ('.implode(',', $where['users_id']).') )');
            else
                $select->where( " (cart_log.users_id  = '".$where['users_id']."' OR users.users_id  = '".$where['users_id']."') ");
        }
        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }
        $select->group('cart_log.session_id');
        $select->order(array(
                'cart_log.date_create' => 'DESC',
            ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){}
        return array();
    }

    public function countLogUser( $where = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(cart_log.log_id)")));
        $select->from('cart_log');
        $select->join('users', new Expression('(users.users_id = cart_log.users_id) OR (users.user_name = cart_log.email) '), array());
        if( !empty($where['log_id']) ){
            $select->where(array(
                'cart_log.log_id' => $where['log_id'],
            ));
        }
        if( !empty($where['session_id']) ){
            $select->where(array(
                'cart_log.session_id' => $where['session_id'],
            ));
        }
        if( !empty($where['users_id']) ){
            if( is_array($where['users_id']) )
                $select->where( ' (cart_log.users_id  IN ('.implode(',', $where['users_id']).') OR users.users_id  IN ('.implode(',', $where['users_id']).') )');
            else
                $select->where( " (cart_log.users_id  = '".$where['users_id']."' OR users.users_id  = '".$where['users_id']."') ");
        }
        $select->group('cart_log.session_id');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            if( !empty($results) )
                return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function deleteLog( $params )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->delete('cart_log');
        if( !empty($params['session_id']) ){
            $select->where(array(
                'cart_log.session_id' => $params['session_id'],
            ));
        }
        $deleteString = $sql->getSqlStringForSqlObject($select);
        $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
    }

    public function getLogsByDay($from_date, $to_date, $params = array()){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression('COUNT(DISTINCT cart_log.session_id)'),'date_simple' => new Expression('DATE_FORMAT(date_create, "%Y-%m-%d")'), 'date_create'));
        $select->from('cart_log');
        if( isset($params['step_sign']) ){
            $select->where(array(
                    'step_sign' => $params['step_sign']
                ));
        }
        $select->where('(cart_log.date_create BETWEEN "'.$from_date.'" AND "'.$to_date.'" )');
        $select->group('date_simple');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->toArray();
    }

}