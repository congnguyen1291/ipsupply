<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

use Cms\Model\AppTable;

class TagsTable extends AppTable
{
    public function fetchAll($where = 0, $order = '', $intPage, $intPageSize)
    {
        $adapter = $this->tableGateway->getAdapter();
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('tags');
        if ($where) {
            $select->where($where);
        }
        if ($order) {
            $select->order($order);
        }
        $select->limit($intPageSize);
        $select->offset($intPage);
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function countAll($where = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("count(tags_id)")));
        $select->from('tags');
        if ($where) {
            $select->where($where);
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
    }

    public function getTagById($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('tags_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getTagByName($tags_name)
    {
        $code = $tags_name;
        $str = $this->toAlias($tags_name);
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('tags');
        $select->where(array(
            'website_id' => $this->getWebsiteId(),
        ));
        $select->where("(LCASE(tags_name) LIKE '{$code}' OR LCASE(tags_alias) LIKE '{$str}')");
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getTagsOfProduct($str_tags)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('tags');
        $select->where(array(
            'website_id' => $this->getWebsiteId()
        ));
        $select->where("FIND_IN_SET(tags.tags_id, '{$str_tags}')>0");
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function saveTag($tag)
    {
        $data = array(
            'website_id' => $tag['website_id'],
            'tags_name' => $tag['tags_name'],
            'tags_alias' => $tag['tags_alias'],
        );
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        try {
            $this->tableGateway->insert($data);
            $id = $adapter->getDriver()->getConnection()->getLastGeneratedValue();
            return $id;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function findTags($str)
    {
        $code = $str;
        $str = $this->toAlias($str);
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('tags');
        $select->columns(array(
            '*',
            'data' => 'tags_id',
            'value' => 'tags_name',
        ));
        $select->where(array(
            'website_id' => $this->getWebsiteId()
        ));
        $select->where("(LCASE(tags_name) LIKE '%{$code}%' OR LCASE(tags_alias) LIKE '%{$str}%')");
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

}