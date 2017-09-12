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
use Zend\Db\TableGateway\AbstractTableGateway;
use Cms\Model\AppTable;

class BannerSizeTable extends AppTable
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
        $select->from('banners_size');
        $select->where(array(
            'website_id'=>$this->getWebsiteId(),
        ));
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
        $select = $sql->select()->columns(array('total' => new Expression("count(id)")));
        $select->from('banners_size');
        $select->where(array(
            'website_id'=>$this->getWebsiteId(),
        ));
        if ($where) {
            $select->where($where);
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
    }

    public function getBannerSize($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id, 'website_id'=>$this->getWebsiteId()));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveBannerSize(BannerSize $size)
    {
        $data = array(
            'website_id'=>$this->getWebsiteId(),
            'size' => $size->size,
            'width' => $size->width,
            'height' => $size->height,
        );

        $id = (int)$size->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getBannerSize($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('size id does not exist');
            }
        }
    }

    public function removeBannersSizeOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

}