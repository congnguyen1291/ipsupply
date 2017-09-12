<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 9:22 AM
 */

namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class BannersTable extends AppTable
{
    
    public function fetchAll( $params = array() )
    {
        try{
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('banners');
            $select->join('banners_position', 'banners.position_id=banners_position.position_id', array('position_name', 'position_alias'));
            $select->where(array('banners.website_id' => $this->getWebsiteId()));

            if(isset($params['banners_title'])){
                $select->where->like('banners_title', "%{$params['banners_title']}%");
            }

            if( $this->hasPaging($params) ){
                $select->offset($this->getOffsetPaging($params['page'], $params['limit']));
                $select->limit($params['limit']);
            }
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch(\Exception $ex){}
        return array();
    }

    public function countAll(  $params = array()  ){
        try{
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->columns(array('total' => new Expression("COUNT(banners.banners_id)")));
            $select->from('banners');
            $select->join('banners_position', 'banners.position_id=banners_position.position_id', array('position_name', 'position_alias'));
            $select->where(array('banners.website_id' => $this->getWebsiteId()));
            if(isset($params['banners_title'])){
                $select->where->like('banners_title', "%{$params['banners_title']}%");
            }
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch(\Exception $ex){}
        return 0;
    }

    public function getBanner($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('banners_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveBanner(Banners $banner,  $picture_id = '')
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'banners_title' => $banner->banners_title,
                'banners_description' => $banner->banners_description,
                'website_id' => $this->getWebsiteId(),
                'position_id' => $banner->position_id,
                'type_banners' => $banner->type_banners,
                'size_id' => $banner->size_id,
                'code' => $banner->code,
                'status' => $banner->status,
                'date_show' => $banner->date_show,
                'date_hide' => $banner->date_hide,
                'link' => $banner->link,
                'is_published' => $banner->is_published,
                'background' => $banner->background,
            );
            if(!empty($picture_id)){
                $picture = $this->getModelTable('PictureTable')->getPicture($picture_id);
                if(!empty($picture)){
                    $data['file'] = $picture->folder.'/'.$picture->name.'.'.$picture->type;
                }
            }
            if(strtotime($banner->date_show) > strtotime($banner->date_hide)){
                throw new \Exception('Ngày bắt đầu phải trước ngày kết thuc');
            }
            $id = (int)$banner->banners_id;
            if ($id == 0) {
                $this->tableGateway->insert($data);
            } else {
                if ($this->getBanner($id)) {
                    $this->tableGateway->update($data, array('banners_id' => $id));
                } else {
                    throw new \Exception('Banner id does not exist');
                }
            }

            $adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }

    }

    public function getPositions(){
        try{
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('banners_position');
            $select->where(array('website_id' => $this->getWebsiteId()));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getTypeBanners(){
        try{
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('banners_type');
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getSizeBanners(){
        try{
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('banners_size');
            $select->where(array('website_id' => $this->getWebsiteId()));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function removeBannerOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function deleteBanners($ids)
    {
        $this->tableGateway->delete(array('banners_id' => $ids));
    }

    public function updateBanners($ids, $data)
    {
        $this->tableGateway->update($data, array('banners_id' => $ids));
    }

    public function getIdCol()
    {
        return 'banners_id';
    }
} 