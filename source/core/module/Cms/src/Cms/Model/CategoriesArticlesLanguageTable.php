<?php
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Filter\File\LowerCase;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\GreaterThan;

use Cms\Model\AppTable;

class CategoriesArticlesLanguageTable extends AppTable
{

    public function getCategories($categories_articles_id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('categories_articles_languages');
        $select->where(array(
            'categories_articles_id' => $categories_articles_id
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function getRow($categories_articles_id, $languages_id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('categories_articles_languages');
        $select->where(array(
            'categories_articles_id' => $categories_articles_id,
            'languages_id' => $languages_id
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function insert($data)
    {
        $this->tableGateway->insert($data);
        $id = $this->getLastestId();
    }

    public function delete($where)
    {
        $this->tableGateway->delete($where);
    }

    public function update($data, $where)
    {
        $this->tableGateway->update($data, $where);
    }
}