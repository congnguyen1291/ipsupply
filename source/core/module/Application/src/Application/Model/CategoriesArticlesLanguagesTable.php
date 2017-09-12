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
use Zend\Db\TableGateway\TableGateway;

use Application\Model\AppTable;

class CategoriesArticlesLanguagesTable extends AppTable{

    public function removeAllCategoriesArticlesLanguagesWithCategoriesArticlesId($ids)
    {
        $this->tableGateway->delete(array('categories_articles_id' => $ids));
    }

	public function getRow($categories_articles_id, $languages_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':CategoriesArticlesLanguagesTable:getRow('.(is_array($categories_articles_id) ? implode('-',$categories_articles_id) : $categories_articles_id).';'.(is_array($languages_id) ? implode('-',$languages_id) : $languages_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('categories_articles_languages');
                $select->where(array(
                    'categories_articles_id' => $categories_articles_id,
                    'languages_id' => $languages_id,
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

    public function insert($data)
    {
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }

    public function updateCategory($data, $where)
    {
        $this->tableGateway->update($data, $where);
    }
    
}