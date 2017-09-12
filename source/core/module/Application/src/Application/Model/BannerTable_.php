<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BannerTable extends AbstractTableGateway implements ServiceLocatorAwareInterface{
    protected $table   = 'banners';
    protected $primary = 'banners_id';
    protected $tableGateway;
    protected $adapter = null;
    
	public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->adapter = $this->tableGateway->getAdapter();
    }

    private $sm = NULL;
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->sm;
    }

    public function getRows() {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-BannerTable-getRows-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results){
            $select = $this->sql->select();
            $select->where(array(
                'status' => 1,
                'website_id'=>$_SESSION['website_id'],
            ));
            $select->order('date_show ASC');
            try{
                $results = $this->fetchAll($select);
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
    public function getAll($positionid) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-BannerTable-getAll-'.$positionid.'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results){
            $select = $this->sql->select();
            $select->where(array(
                'status' => 1,
                'position_id' => $positionid,
                'website_id'=>$_SESSION['website_id'],
            ));
            $select->order('banners_id ASC');
            try{
                $results = $this->fetchAll($select);

                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = $resultSet->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getBannerWithPositionAlias($position){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-BannerTable-getBannerWithPositionAlias-'.$position.'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('banners');
            $select->join('banners_size', 'banners_size.id=banners.size_id', array('width','height'),'left');
            $select->join('banners_position', 'banners_position.id=banners.position_id', array('position_name'),'left');
            $select->join('banners_type', 'banners_type.id=banners.type_banners', array('code'),'left');
            $select->where(array(
                'banners_position.position_id' => $position,
                'banners.is_published' => 1,
                'banners.website_id'=>$_SESSION['website_id'],
            ));
            $select->where->like('banners_position.position_alias', $position);
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);echo $selectString;die();
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

	public function getBanner($position, $size, $type){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-BannerTable-getBanner-'.$position.'-'.$size.'-'.$type.'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results){
    		$select = $this->sql->select();
    		$select->join('banners_size', 'banners_size.id=banners.size_id', array('width','height'),'left');
    		//$select->join('banners_position', 'banners_position.id=banners.position_id', array('position_name'),'left');
    		//$select->join('banners_type', 'banners_type.id=banners.type_banners', array('code'),'left');
    		$select->where(array(
    			'banners.size_id' => $size,
    			'banners.position_id' => $position,
    			'banners.type_banners' => $type,
    			'banners.is_published' => 1,
                'banners.website_id'=>$_SESSION['website_id'],
    		));
            try{
        		$results = $this->fetchAll($select);
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

	public function getBannerHomepage($idbanner){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('Application-BannerTable-getBannerHomepage-'.$idbanner.'-Website-'.$_SESSION['domain']);
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $rows = $this->getAll($idbanner);
                $results = array();
                if(count($rows)){
                    foreach($rows as $bannershowlist){
                        $item_banner = array();
                        $item_banner['banners_title'] =$bannershowlist["banners_title"];
                        $item_banner['banners'] =$bannershowlist["file"];
                        $item_banner['link'] =$bannershowlist["link"];
                        $results[]=$item_banner;
                    }
                    $cache->setItem($key, $results);
                }
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

}