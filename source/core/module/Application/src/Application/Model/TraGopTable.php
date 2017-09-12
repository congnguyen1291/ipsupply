<?php

namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Application\Model\AppTable;

class TraGopTable extends AppTable{

	public function fetchAll() {
		$cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':TraGopTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('tra_gop');
            $select->join ( 'products', 'products.products_id=tra_gop.products_id', array('title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')) );
            $select->where(array(
                'website_id'=>$this->getWebsiteId(),
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = new ResultSet();
                $results->initialize($results);
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = null;
            }
        }
        return $results;
	}
    
	public function insertTraGop(TraGop $tg) {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $data = array (
                    'products_id' => $tg->products_id,
                    'cities_id' => $tg->cities_id,
                    'total_month' => $tg->total_month,
                    'banks_id' => $tg->banks_id,
                    'full_name' => $tg->full_name,
                    'phone' => $tg->phone,
                    'users_id' => $tg->users_id,
                    'date_create' => $tg->date_create,
                    'total_pay' => $tg->total_pay,
                    'month_pay' => $tg->month_pay,
                    'first_pay' => $tg->first_pay,
            );
            $this->tableGateway->insert ( $data );
            $lastId = $this->getLastestId ();
            $adapter->getDriver()->getConnection()->commit();
            return $lastId;
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
	}

}