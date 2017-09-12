<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BannerTable extends DbTable implements ServiceLocatorAwareInterface{
    protected $table   = 'banners';
    protected $primary = 'banners_id';
    
	public function __construct(Adapter $adapter) {
        parent::__construct($adapter);
    }
    public function getRows() {
        $select = $this->sql->select();
        $select->where(array(
                'status' => 1
        ));
        $select->order('date_show ASC');
        $results = $this->fetchAll($select);
        $resultSet = new ResultSet();
        $resultSet->initialize($results);
        return $resultSet->toArray();
    }
    public function getAll($positionid) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('get-all-'.(is_array($positionid) ? implode('-',$positionid) : $positionid));
        $data = $cache->getItem($key);
        if(!$data){
            $select = $this->sql->select();
            $select->where(array(
                'status' => 1,
                'position_id' => $positionid,
            ));
            $select->order('banners_id ASC');
            $results = $this->fetchAll($select);
            //$keywordcache='getAll'.$positionid;
            //if(!$this->getServiceLocator()->get('memcached')->getItem($keywordcache)){
                //$this->getServiceLocator()->get('memcached')->setItem($keywordcache, $results);
            //}
            //$results=$this->getServiceLocator()->get('memcached')->getItem($keywordcache);

            $resultSet = new ResultSet();
            $resultSet->initialize($results);
            $data = $resultSet->toArray();
            $cache->setItem($key, $data);
        }
        return $data;
    }
	public function getBanner($position, $size, $type){
		$select = $this->sql->select();
		$select->join('banners_size', 'banners_size.id=banners.size_id', array('width','height'),'left');
		//$select->join('banners_position', 'banners_position.id=banners.position_id', array('position_name'),'left');
		//$select->join('banners_type', 'banners_type.id=banners.type_banners', array('code'),'left');
		$select->where(array(
			'banners.size_id' => $size,
			'banners.position_id' => $position,
			'banners.type_banners' => $type,
			'banners.is_published' => 1,
		));
		$results = $this->fetchAll($select);
		$resultSet = new ResultSet();
		$resultSet->initialize($results);
		return $resultSet->toArray();
	}

	public function getBannerHomepage($idbanner){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('get-banner-homepage-'.(is_array($idbanner) ? implode('-',$idbanner) : $idbanner));
        $data = $cache->getItem($key);
        if(!$data){
            $rows = $this->getAll($idbanner);
            $data = array();
            if(count($rows)){
                foreach($rows as $bannershowlist){
                    $item_banner = array();
                    $item_banner['banners_title'] =$bannershowlist["banners_title"];
                    $item_banner['banners'] =$bannershowlist["file"];
                    $item_banner['link'] =$bannershowlist["link"];
                    $data[]=$item_banner;
                }
                $cache->setItem($key, $data);
            }
        }
        return $data;
    }

    private $sm = NULL;
    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        // TODO: Implement setServiceLocator() method.
        $this->sm = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        // TODO: Implement getServiceLocator() method.
        return $this->sm;
    }
}