<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/16/14
 * Time: 2:12 PM
 */
namespace Application\Model;


use Zend\Db\Sql\Predicate\Predicate;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class BannersTable extends AppTable{

    public function removeAllBannersOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function getBannerWithPositionAlias($position, $size = ''){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':BannersTable:getBannerWithPositionAlias('.$position.';'.$size.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('banners');
            $select->join('banners_size', 'banners_size.id=banners.size_id', array('width','height'),'left');
            $select->join('banners_position', 'banners_position.position_id=banners.position_id', array('position_name'),'left');
            $select->join('banners_type', 'banners_type.id=banners.type_banners', array('code'),'left');
            $select->where(array(
                'banners.is_published' => 1,
                'banners.website_id'=>$this->getWebsiteId(),
                'banners_position.website_id'=>$this->getWebsiteId()
            ));
            $select->where->like('banners_position.position_alias', $position);
            if(!empty($size)){
                $select->where->like('banners_size.size', $size);
            }
            //$select->where("CURDATE() BETWEEN banners.date_show AND banners.date_hide");
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

    public function getBanners($position, $width, $height){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':BannersTable:getBanners('.$position.';'.$width.';'.$height.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('banners');
            $select->join('banners_position', 'banners.position_id=banners_position.position_id',array('pcode' => 'code'));
            $select->join('banners_size', 'banners.size_id=banners_size.id', array('width','height'));
            $select->join('banners_type', 'banners.type_banners=banners_type.id', array('tcode' => 'code'));
            $select->where(array(
                'banners_size.width' => $width,
                'banners_size.height' => $height,
                'banners.website_id'=>$this->getWebsiteId(),
                'banners_position.website_id'=>$this->getWebsiteId(),
            ));
            $select->where->like('banners_position.position_alias', $position);
            //$select->where("CURDATE() BETWEEN date_show AND date_hide");
            $selectString = $sql->getSqlStringForSqlObject($select);
            try{
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getBannersOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('banners');
        $select->where(array(
            'website_id' => $website_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function insertBanners($data){
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }

    public function getLastestId(){
        return $this->tableGateway->getLastInsertValue();
    }
}