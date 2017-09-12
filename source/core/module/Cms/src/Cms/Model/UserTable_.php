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

class UserTable extends GeneralTable
{
    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway);
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
            'password' => $this->passwordEncript($password),
            'website_id' => $_SESSION['website_id']
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function login(Login $login)
    {
        try {
            $user = $this->getUser($login->username, $login->password);
            $user = $user->toArray();
            if(count($user) > 0){
                $user = $user[0];
                //chi dang nhap bang adminstrator
                if($user['is_administrator'] == 1){
                    $_SESSION['CMSMEMBER'] = $user;
                    if ($login->rememberme) {
                        setcookie("CMSMEMBER", json_encode($_SESSION['CMSMEMBER']), time() + 3600 * 24 * 30);
                    }
                    return TRUE;
                }
            }
            return FALSE;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function fetchAll($intPage, $intPageSize, $order_by = '', $where = array()){
        if ($intPage <= 1){
            $intPage= 0;
        }else {
            $intPage = ($intPage-1) * $intPageSize;
        }
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array(
            'website_id','users_id','user_name', 'full_name','type','birthday','phone','address','cities_id','districts_id', 'is_published','is_delete','date_create','type','date_update'
        ));
        $select->from('users');
        $where['website_id'] = $_SESSION['CMSMEMBER']['website_id'];
        $select->where($where);
        if($order_by){
            $select->order($order_by);
        }
        $select->limit($intPageSize);
        $select->offset($intPage);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
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
            'website_id'  => $user->website_id,
            'full_name'  => $user->full_name,
            'is_published'      => $user->is_published,
            'is_delete'         => $user->is_delete,
            'date_create'       => $user->date_create,
            'date_update'       => $user->date_update,
            'user_name'         => $user->user_name,
            'password'          => $this->passwordEncript($user->password),
            'users_alias'       => $user->users_alias,
            'birthday'          => $user->birthday,
            'phone'             => $user->phone,
            'address'           => $user->address,
            'cities_id'         => $user->cities_id,
            'districts_id'      => $user->districts_id,
            'type'              => $user->type,
            'is_administrator'              => $user->is_administrator,
            'groups_id'         => $user->groups_id,


        );
        $id = (int) $user->users_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUserById($id)) {
                if(!$user->password){
                    unset($data['password']);
                }
                $this->tableGateway->update($data, array('users_id' => $id));
            } else {
                throw new \Exception('Row does not exist');
            }
        }
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

    public function getUsersLevels($intPage, $intPageSize, $where = array()){
        if ($intPage <= 1){
            $intPage= 0;
        }else {
            $intPage = ($intPage-1) * $intPageSize;
        }
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('users_level');
        $select->where(array('website_id'=>$_SESSION['CMSMEMBER']['website_id']));
        if(count($where)){
            $select->where($where);
        }
        $select->limit($intPageSize);
        $select->offset($intPage);
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            if($result){
                $results = $result->toArray();
                return $results;
            }
            return FALSE;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function saveUsersLevel(UsersLevel $user){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {

            $id = (int)$user->users_level_id;
            if($user->users_level_icon['name'] != ''){
                try{
                    $file = $user->users_level_icon;
                    $upload_dir = PATH_BASE_ROOT.DS.'custom'.DS.'domain_1'.DS.'user_levels';
                    $filename = $file['tmp_name'];
                    $old_name = $file['name'];
                    $tmp = @explode('.', $old_name);
                    $ext = end($tmp);
                    $name = 'level-user-'.$this->toAlias($user->users_level_name).'-'.time().'.'.$ext;
                    $src = "/custom/domain_1/user_levels/".$name;
                    $user->users_level_icon = $src;
                }catch(\Exception $ex){

                }
            }
            $data = array(
                'website_id' => $_SESSION['CMSMEMBER']['website_id'],
                'users_level_name' => $user->users_level_name,
                'users_level_decrease' => $user->users_level_decrease,
                'users_level_longdescription' => htmlentities($user->users_level_longdescription,ENT_QUOTES, 'UTF-8'),
                'min_buy' => $user->min_buy,
                'date_create' => $user->date_create,
            );
            if(isset($src)){
                $data['users_level_icon'] = $user->users_level_icon;
            }
            $sql = new Sql($adapter);
            if ($id == 0) {
                $insert = $sql->insert();
                $insert->into('users_level');
                $insert->columns(array('users_level_name','users_level_decrease','users_level_longdescription','min_buy','date_create'));
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
            if(isset($src)){
                if(isset($filename) && isset($upload_dir) && isset($name)){
                    if(!is_dir($upload_dir)){
                        @mkdir($upload_dir,0777);
                    }
                    @move_uploaded_file($filename, $upload_dir.DS.$name);
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
            'users.website_id' => $_SESSION['CMSMEMBER']['website_id'],
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

}