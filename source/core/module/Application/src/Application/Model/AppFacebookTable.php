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

class AppFacebookTable  extends AppTable {

    public function getApp($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($id);
        $key = md5($this->getNamspaceCached().':AppFacebookTable:getApps('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('app_facebook');
            $select->where(array(
                'app_facebook.app_facebook_id' => $id,
                'app_facebook.website_id' => $this->getWebsiteId()
            ));
            $select->order('app_facebook.app_facebook_id desc');
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

    public function getAppWithAppIDAndSecret( $app_id, $app_secret )
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':AppFacebookTable:getAppWithAppIDAndSecret('.$app_id.','.$app_secret.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('app_facebook');
            $select->where(array(
                'app_facebook.app_id' => $app_id,
                'app_facebook.app_secret' => $app_secret,
                'app_facebook.website_id' => $this->getWebsiteId()
            ));
            $select->order('app_facebook.app_facebook_id desc');
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