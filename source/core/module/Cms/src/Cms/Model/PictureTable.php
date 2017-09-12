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

class PictureTable extends AppTable
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
        $select->from('picture');
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

    public function getPictures($offset = 0, $limit= 10)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('picture');
        $select->where(array('website_id' => $this->getWebsiteId()));
        $select->order('picture_id DESC');
        $select->limit($limit);
        $select->offset($offset);
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $ex) {
            //throw new \Exception($ex->getMessage());
            return array();
        }
    }

    public function deletePicture($id)
    {
        $id = (int)$id;
        $this->tableGateway->delete(array('picture_id' => $id, 'website_id' => $this->getWebsiteId()));
        return TRUE;
    }

    public function getPictureInIds($picture_id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('picture');
        $select->where(array('picture_id'=>$picture_id));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $ex) {
            return array();
        }
    }

    public function getPicture($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('picture_id' => $id, 'website_id' => $this->getWebsiteId()));
        $row = $rowset->current();
        return $row;
    }

    public function savePicture($row)
    {
        $this->tableGateway->insert($row);
        $picture_id = $this->getLastestId();
        $picture = $this->getPicture($picture_id);
        return $picture;
    }

    public function getLastestId()
    {
        return $this->tableGateway->getLastInsertValue();
    }

}