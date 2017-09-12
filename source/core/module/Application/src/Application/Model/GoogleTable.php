<?php
namespace Application\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Filter\File\LowerCase;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\GreaterThan;

use Application\Model\AppTable;

class GoogleTable  extends AppTable {

    public function saveGoogle($data)
    {
        try{
            $google = $this->getGoogleByFacebookId($data['google_id']);
            $users_id = 0;
            $user = array();
            if( !empty($google) ){
                $user = $this->getModelTable('UserTable')->getUser( $google->users_id );
            }

            if( empty($user) ){
                $alias = $this->toAlias( $data['name'], '.' );
                $total = $this->getModelTable('UserTable')->getTotalFreAlias($alias);
                $row = array(
                        'website_id'        => $this->getWebsiteId(),
                        'first_name'        => $data['first_name'],
                        'last_name'         => $data['last_name'],
                        'full_name'         => $data['name'],
                        'user_name'         => $data['email'],
                        'password'          => md5($this->randText(8)),
                        'users_alias'       => !empty($total['total']) ? $alias.'.'.$total['total'] : $alias,
                        'birthday'          => '',
                        'phone'             => '',
                        'address'           => '',
                        'cities_id'         => 0,
                        'districts_id'      => 0,
                        'wards_id'          => 0,
                        'is_published'      => 1,
                        'is_delete'         => 0,
                        'date_create'       => date('Y-m-d H:i:s'),
                        'date_update'       => date('Y-m-d H:i:s'),
                        'type'              => 'user',
                    );
                $users_id = $this->getModelTable('UserTable')->createUser( $row );
            }else{
                $users_id = $google->users_id;
            }

            $row = array(
                    'website_id'         => $this->getWebsiteId(),
                    'google_id'          => $data['google_id'],
                    'users_id'           => $users_id,
                    'email'              => $data['email'],
                    'first_name'         => $data['first_name'],
                    'last_name'          => $data['last_name'],
                    'name'               => $data['name'],
                    'cover'              => $data['cover'],
                    'date_create'        => date('Y-m-d H:i:s'),
                    'date_update'        => date('Y-m-d H:i:s')
                );

            if( empty($google) ){
                $this->tableGateway->insert($row);
                $id = $this->getLastestId();
            } else {
                $id = $google->google_id_a;
                $this->tableGateway->update($row, array('google_id_a' => $id));
            }
            return $id;
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function getGoogleByFacebookId($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($id);
        $key = md5($this->getNamspaceCached().':GoogleTable:getGoogleByFacebookId('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('google');
            $select->where(array(
                'google.google_id' => $id,
                'google.website_id' => $this->getWebsiteId()
            ));
            $select->order('google.google_id_a desc');
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

    public function getGoogle($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($id);
        $key = md5($this->getNamspaceCached().':GoogleTable:getGoogle('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('google');
            $select->where(array(
                'google.google_id_a' => $id,
                'google.website_id' => $this->getWebsiteId()
            ));
            $select->order('google.google_id_a desc');
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

    public function login( $id, $email )
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':GoogleTable:login('.$id.','.$email.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns( array(    'google_id', 'users_id', 'email', 
                                        'first_name', 'last_name', 'name', 'cover' ));
            $select->from('google');
            $select->join('users', 'google.users_id = users.users_id', array('user_name', 'full_name', 'avatar', 'users_alias', 'birthday', 'phone', 'address', 'address_full', 'country_id', 'cities_id', 'districts_id', 'wards_id', 'city', 'state', 'suburb', 'region', 'province', 'zipcode'));
            $select->where(array(
                'google.google_id' => $id,
                'google.email' => $email,
                'google.website_id' => $this->getWebsiteId(),
                'users.website_id' => $this->getWebsiteId(),
                'users.is_published'      => 1,
                'users.is_delete'      => 0
            ));
            $select->order('google.google_id_a desc');
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
    
}