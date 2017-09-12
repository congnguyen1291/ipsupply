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

class BannerPositionTable extends AppTable
{
    public function fetchAll( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('*', 'total_banner' => new Expression('(SELECT COUNT(`banners`.`banners_id`) FROM `banners` WHERE `banners`.`position_id` = `banners_position`.`position_id` )')));
        $select->from('banners_position');
        $select->where(array(
            'website_id'=>$this->getWebsiteId(),
        ));

        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }

        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $ex) {}
        return array();
    }

    public function countAll( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("count(position_id)")));
        $select->from('banners_position');
        $select->where(array(
            'website_id'=>$this->getWebsiteId(),
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function getBannerPosition($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('position_id' => $id, 'website_id'=>$this->getWebsiteId()));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function countBannerPositionWithAlias($alias)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('banners_position');
        $select->where(array(
            'website_id'=>$this->getWebsiteId(),
        ));
        $select->where->like('position_alias', $alias);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->toArray();
        return $row;
    }

    public function saveBannerPosition(BannerPosition $position)
    {
        $data = array(
            'website_id'=>$this->getWebsiteId(),
            'position_name' => $position->position_name,
            'position_alias' => $position->position_alias,
            'date_create' => $position->date_create,
        );

        if( !empty($position->image_preview) ){
            $data['image_preview'] = $position->image_preview;
        }

        $id = (int)$position->position_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getBannerPosition($id)) {
                $this->tableGateway->update($data, array('position_id' => $id));
            } else {
                throw new \Exception('position id does not exist');
            }
        }
    }

    public function removeBannersPositionOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function deletePosition($ids)
    {
        $this->tableGateway->delete(array('position_id' => $ids));
    }

    public function updatePosition($ids, $data)
    {
        $this->tableGateway->update($data, array('position_id' => $ids));
    }

}