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

class ArticlesLanguagesTable extends AppTable{

    public function removeAllArticlesLanguagesWithArticlesId($articles_id)
    {
        $this->tableGateway->delete(array('articles_id' => $articles_id));
    }

    public function getRow($articles_id, $languages_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':ArticlesLanguagesTable:getRow('.(is_array($articles_id) ? implode('-',$articles_id) : $articles_id).';'.(is_array($languages_id) ? implode('-',$languages_id) : $languages_id).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('articles_languages');
                $select->where(array(
                    'articles_id' => $articles_id,
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

    public function updateArticles($data, $where)
    {
        $this->tableGateway->update($data, $where);
    }

    public function insert($data)
    {   try{
            $this->tableGateway->insert($data);
        }catch(\Exception $e){
            die($e->getMessage());
        }
        return $this->getLastestId();
    }
}