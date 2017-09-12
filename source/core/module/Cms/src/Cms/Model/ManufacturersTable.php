<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/2/14
 * Time: 4:48 PM
 */

namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class ManufacturersTable extends AppTable
{

    public function fetchAll( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('*', 'total'=> new Expression('(SELECT COUNT(products.products_id) FROM products WHERE products.manufacturers_id  = manufacturers.manufacturers_id )')));
        $select->from('manufacturers');
        $select->where(array(
            'manufacturers.website_id' => $this->getWebsiteId()
        ));
        if(isset($params['manufacturers_name'])){
            $manufacturers_name = $this->toAlias($params['manufacturers_name']);
            $select->where->like('manufacturers_name', "%{$params['manufacturers_name']}%");
        }
        if( $this->hasPaging($params) ){
            $select->offset($this->getOffsetPaging($params['page'], $params['limit']));
            $select->limit($params['limit']);
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){}
        return array();
    }

    public function countAll(  $params = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(manufacturers.manufacturers_id)")));
        $select->from('manufacturers');
        $select->where(array(
            'manufacturers.website_id' => $this->getWebsiteId()
        ));
        if(isset($params['manufacturers_name'])){
            $manufacturers_name = $this->toAlias($params['manufacturers_name']);
            $select->where->like('manufacturers_name', "%{$params['manufacturers_name']}%");
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function getManufacture($id){
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('manufacturers_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveManufacture(Manufacturers $m, $picture_id = '', $old_image = '')
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        $data = array(
            'manufacturers_id' => $m->manufacturers_id,
            'website_id' => $this->getWebsiteId(),
            'manufacturers_name' => $m->manufacturers_name,
            'description' => $m->description,
            'warranty_description' => htmlentities($m->warranty_description,ENT_QUOTES, 'UTF-8'),
            'is_published' => $m->is_published,
            'is_delete' => $m->is_delete,
            'date_create' => $m->date_create,
            'date_update' => $m->date_update,
            'ordering' => $m->ordering,
        );
        if(!empty($picture_id)){
            $picture = $this->getModelTable('PictureTable')->getPicture($picture_id);
            if(!empty($picture)){
                $data['thumb_image'] = $picture->folder.'/'.$picture->name.'.'.$picture->type;
            }
        }
        $id = (int)$m->manufacturers_id;
        try{
            if ($id == 0) {
                $this->tableGateway->insert($data);
            } else {
                if ($this->getManufacture($id)) {
                    $this->tableGateway->update($data, array('manufacturers_id' => $id));
                } else {
                    throw new \Exception('Manufacture id does not exist');
                }
            }
            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
    }

    public function filter($params = array()){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('manufacturers');
        $is_condition = FALSE;
        if(isset($params['manufacturers_name']) && $params['manufacturers_name']){
            $select->where->like('manufacturers_name', "%{$params['manufacturers_name']}%");
            $is_condition = TRUE;
        }
        $select->where(array(
            'is_delete' => 0,
            'website_id' => $this->getWebsiteId(),
        ));
        if(!$is_condition){
            $select->limit(10);
            $select->offset(0);
        }
        $select->order(array(
            'ordering' => 'ASC',
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try{
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex);
        }
    }

    public function getAllPromotions($where = array(), $order = array(), $intPage, $intPageSize){
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }
        /**
         * @var $adapter \Zend\Db\Adapter\Adapter
         */
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('promotions');
        $select->join('manufacturers','promotions.manufacturers_id=manufacturers.manufacturers_id', array('manufacturers_name'));
        $select->where(array(
            'manufacturers.website_id' => $this->getWebsiteId(),
        ));
        if(count($order)) {
            $select->order(array(
                'date_create' => 'DESC',
            ));
        }
        $select->limit($intPageSize);
        $select->offset($intPage);
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function savePromotion($promotion = array(), $categories = array()){
        try {
            if (!$promotion['title']) {
                throw new \Exception('Vui lòng nhập tiêu đề');
            }
            if (!$promotion['date_start']) {
                throw new \Exception('Vui lòng chọn ngày bắt đầu');
            }
            if (!$promotion['date_end']) {
                throw new \Exception('Vui lòng chọn ngày kết thúc');
            }
            $data_start_date = $promotion['date_start'] .' 00:00:00';
            $data_end_date = $promotion['date_end'] .' 00:00:00';
            $start_date = strtotime($data_start_date);
            $end_date = strtotime($data_end_date);
            if($start_date > $end_date){
                throw new \Exception('Ngày bắt đầu phải nhỏ hơn ngày kết thúc');
            }

            /**
             * @var $adapter \Zend\Db\Adapter\Adapter
             */
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            $sql = new Sql($adapter);
            if(!isset($promotion['promotions_id'])){
                $insert = $sql->insert('promotions');
                $insert->columns(array('manufacturers_id','title','description','seo_keywords','seo_description','min_price','date_start','date_end','is_published','date_create','date_update'));
                $insert->values(array(
                    'manufacturers_id' => $promotion['manufacturers_id'],
                    'title' => $promotion['title'],
                    'description' => htmlentities($promotion['description'],ENT_QUOTES, 'UTF-8'),
                    'seo_keywords' => $promotion['seo_keywords'],
                    'seo_description' => $promotion['seo_description'],
                    'min_price' => $promotion['min_price'],
                    'date_start' => $data_start_date,
                    'date_end' => $data_end_date,
                    'is_published' => $promotion['is_published'],
                    'date_create' => date('Y-m-d H:i:s'),
                    'date_update' => date('Y-m-d H:i:s'),
                ));
                $insertString = $sql->getSqlStringForSqlObject($insert);
                $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
                $promotions_id = $adapter->getDriver()->getConnection()->getLastGeneratedValue();
            }else{
                $update = $sql->update('promotions');
                $update->set(array(
                    'manufacturers_id' => $promotion['manufacturers_id'],
                    'title' => $promotion['title'],
                    'description' => $promotion['description'],
                    'seo_keywords' => $promotion['seo_keywords'],
                    'seo_description' => $promotion['seo_description'],
                    'min_price' => $promotion['min_price'],
                    'date_start' => $data_start_date,
                    'date_end' => $data_end_date,
                    'is_published' => $promotion['is_published'],
                    'date_update' => date('Y-m-d H:i:s'),
                ));
                $update->where(array(
                    'promotions_id' => $promotion['promotions_id'],
                ));
                $updateString = $sql->getSqlStringForSqlObject($update);
                $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
                $sql = "DELETE FROM `promotions_detail` WHERE promotions_id={$promotion['promotions_id']}";
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                $promotions_id = $promotion['promotions_id'];
            }
            $insert = $sql->insert('promotions_detail');
            $insert->columns(array('promotions_id','categories_id'));
            foreach($categories as $cat) {
                $insert->values(array(
                    'promotions_id' => $promotions_id,
                    'categories_id' => $cat,
                ));
                $insertString = $sql->getSqlStringForSqlObject($insert);
                $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
            }
            $adapter->getDriver()->getConnection()->commit();
        }catch (\Exception $ex){
            if(isset($adapter)){
                $adapter->getDriver()->getConnection()->rollback();
            }
            throw new \Exception($ex->getMessage());
        }
    }

    public function getPromotionById($id){
        try{
            /**
             * @var $adapter \Zend\Db\Adapter\Adapter
             */
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('promotions');
            $select->where(array(
                'promotions_id' => $id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->current();
            if(!$result){
                throw new \Exception('Row not found');
            }
            return (array)$result;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getCategoriesByPromotionId($id){
        try{
            /**
             * @var $adapter \Zend\Db\Adapter\Adapter
             */
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('categories_id'));
            $select->from('promotions_detail');
            $select->where(array(
                'promotions_id' => $id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function updatePromotion($ids = array(), $data = array()){
        try {
            if(!is_array($ids) || !count($ids) || !count($data)){
                throw new \Exception('Data not valid');
            }
            /**
             * @var $adapter \Zend\Db\Adapter\Adapter
             */
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $update = $sql->update('promotions');
            $update->where(array(
                'promotions_id' => $ids,
            ));
            $update->set($data);
            $updateString = $sql->getSqlStringForSqlObject($update);
            $adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function deletePromotion($ids = array()){
        try{
            if(!is_array($ids) || !count($ids)){
                throw new \Exception('ID not valid');
            }
            /**
             * @var $adapter \Zend\Db\Adapter\Adapter
             */
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            $sql = new Sql($adapter);
            $delete = $sql->delete('promotions_detail');
            $delete->where(array(
                'promotions_id' => $ids,
            ));
            $deleteString = $sql->getSqlStringForSqlObject($delete);
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
            $delete = $sql->delete('promotions');
            $delete->where(array(
                'promotions_id' => $ids,
            ));
            $deleteString = $sql->getSqlStringForSqlObject($delete);
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
            $adapter->getDriver()->getConnection()->commit();
        }catch (\Exception $ex){
            if(isset($adapter)){
                $adapter->getDriver()->getConnection()->rollback();
            }
            throw new \Exception($ex->getMessage());
        }
    }

    public function removeManufacturersOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    protected function getIdCol()
    {
        return 'manufacturers_id';
    }

    protected function getOrderCol()
    {
        return 'ordering';
    }

    public function deleteManufacturers($ids)
    {
        $this->tableGateway->delete(array('manufacturers_id' => $ids));
    }

    public function updateManufacturers($ids, $data)
    {
        $this->tableGateway->update($data, array('manufacturers_id' => $ids));
    }

} 