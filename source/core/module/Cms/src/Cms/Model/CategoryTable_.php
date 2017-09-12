<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class CategoryTable extends MultiLevelTable
{
//    protected $tableGateway;
    protected $_breadCrumb = array();
    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway);
    }

    public function getListCategory($where = array(), $order = array(), $intPage, $intPageSize)
    {
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }

        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('categories');
        $select->where(array(
            'categories.website_id' => $_SESSION['CMSMEMBER']['website_id'],
        ));
        if(isset($where['categories_title'])){
            $categories_title = $this->toAlias($where['categories_title']);
            $select->where->like('categories_alias', "%{$categories_title}%");
        }
        
        $select->group('categories.categories_id');
        $select->limit($intPageSize);
        $select->offset($intPage);
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function countListCategory($where = array())
    {

        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression('count(categories.categories_id)')));
        $select->from('categories');
        $select->where(array(
            'categories.website_id' => $_SESSION['CMSMEMBER']['website_id'],
        ));
        if(isset($where['categories_title'])){
            $categories_title = $this->toAlias($where['categories_title']);
            $select->where->like('categories_alias', "%{$categories_title}%");
        }

        $select->group('categories.categories_id');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        }catch (\Exception $ex){
            return array();
        }
    }


    public function getCategory($id)
    {
        return $this->getById($id);
    }

    public function getAllFeatureChecked($id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('categories_feature');
        $select->where(array(
            'categories_id' => $id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function saveCategory(Category $cat, $file = array(), $old_file = '')
    {
        $data = array(
            'parent_id'         => $cat->parent_id,
            'website_id'         => $_SESSION['CMSMEMBER']['website_id'],
            'categories_title'  => $cat->categories_title,
            'categories_alias'  => $cat->categories_alias,
            'seo_keywords' => $cat->seo_keywords,
            'seo_description' => $cat->seo_description,
            'seo_title' => $cat->seo_title,
            'is_published'      => $cat->is_published,
            'is_delete'         => $cat->is_delete,
            'date_create'       => $cat->date_create,
            'date_update'       => $cat->date_update,
            'ordering'          => $cat->ordering,
        );
        $id = (int) $cat->categories_id;
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            if($file['name']){
                $upload_dir = DS.'custom'.DS.'domain_1'.DS.'categories_icons';
                if(!is_dir(PATH_BASE_ROOT.$upload_dir)){
                    mkdir(PATH_BASE_ROOT.$upload_dir, 0777);
                }

                $domain=$_SESSION['website']['website_domain'];
                $folderCategoriesdomain = $upload_dir. DS .$domain;
                if (!is_dir(PATH_BASE_ROOT.$folderCategoriesdomain)) {
                    mkdir(PATH_BASE_ROOT.$folderCategoriesdomain, 0777);
                }

                $year=date("Y");
                $month=date("m");
                $day=date("d");
                $ten_thu_muc_year = $folderCategoriesdomain.DS.$year;
                if(!is_dir(PATH_BASE_ROOT.$ten_thu_muc_year)) {
                    @mkdir(PATH_BASE_ROOT.$ten_thu_muc_year);
                }
                $ten_thu_muc_month=$ten_thu_muc_year."/".$month;
                if(!is_dir(PATH_BASE_ROOT.$ten_thu_muc_month)) {
                    @mkdir(PATH_BASE_ROOT.$ten_thu_muc_month);
                }
                $ten_thu_muc_day=$ten_thu_muc_month."/".$day;
                if(!is_dir(PATH_BASE_ROOT.$ten_thu_muc_day)) {
                    @mkdir(PATH_BASE_ROOT.$ten_thu_muc_day);
                }



                $filename = $file['name'];
                $ext = explode('.', $filename);
                $ext = end($ext);
                if(!in_array($ext, array('png','jpg','jpeg'))){
                    throw new \Exception('Định dạng hình không được hỗ trợ');
                }
                $name = $this->toAlias($data['categories_title']).'-'.time().'.'.$ext;

                @move_uploaded_file($file['tmp_name'], PATH_BASE_ROOT.DS.$ten_thu_muc_day.DS.$name);
                if($old_file){
                    $filename = explode('/',$old_file);
                    $filename = end($filename);
                    @unlink($upload_dir.DS.$filename);
                }

                $src = $ten_thu_muc_day.DS.$name;
                $src = '/image/'.$year.'/'.$month.'/'.$day.'/categories_icons-'$name;
                $data['icon'] = $src;
            }
            $this->saveData($data, $id);

            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
    }

    public function addRecommendCat($id, $cats){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $del = "DELETE FROM categories_recommend WHERE from_categories_id={$id}";
            $adapter->query($del,$adapter::QUERY_MODE_EXECUTE);
            if(count($cats) > 0){
                $val = array();
                foreach($cats as $cat){
                    $val[] = "({$id}, {$cat})";
                }
                $val = implode(',', $val);
                $insert = "INSERT INTO categories_recommend(from_categories_id,to_categories_id) VALUES {$val}";
                $adapter->query($insert,$adapter::QUERY_MODE_EXECUTE);
            }
            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function getRecommendCat($id){
        try{
            $sql = "SELECT to_categories_id as categories_id
                    FROM categories_recommend
                    WHERE from_categories_id={$id}";
            $adapter = $this->tableGateway->getAdapter();
            $results = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function deleteCategory($id)
    {
        $this->tableGateway->delete(array('categories_id' => (int) $id));
    }

    public function updateFeatureData($catid = -1,$olddata = array(),$newdata = array()){
        if(count(array_diff($olddata, $newdata)) == 0 && count(array_diff($newdata, $olddata)) == 0){
            return TRUE;
        }
        $delsql = "DELETE FROM `categories_feature` WHERE `categories_id`={$catid}";
        $sql = "INSERT INTO categories_feature(`categories_id`,`feature_id`) VALUES ";
        $val = array();
        foreach($newdata as $feat){
            $val[] = "({$catid}, {$feat})";
        }
        $val = implode(',', $val);
        $sql .= $val;
        try{
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($delsql,$adapter::QUERY_MODE_EXECUTE);
            $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function getLastestId(){
        return $this->tableGateway->getLastInsertValue();
    }

    public function filter($data){
        $where = array();
		$where[] = 'is_delete=0';
        if(isset($data['categories_title']) && trim($data['categories_title']) != '' ){
			$data['categories_title'] = $this->toAlias($data['categories_title']);
            $where[] = "LCASE(categories_alias) LIKE '%{$data['categories_title']}%'";
        }
        if(isset($data['is_published'])){
            $where[] = "is_published=1";
        }
        $where = implode(' AND ', $where);
        $sql = "SELECT *
                FROM `categories`
                WHERE is_delete=0 {$where} AND website_id = {$_SESSION['CMSMEMBER']['website_id']}";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
            return $result;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function getBanners($params = array(), $intPage, $intPageSize){
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('categories_banners');
        $select->join('categories', 'categories.categories_id=categories_banners.categories_id',array());
        $select->where(array(
            'website_id' => $_SESSION['CMSMEMBER']['website_id'],
        ));
        if(isset($params['cat_id'])){
            $select->where(array(
                'categories_id' => $params['cat_id'],
            ));
        }
        $select->limit($intPageSize);
        $select->offset($intPage);
        $select->order(array(
            'categories_id' => 'DESC',
            'box_num' => 'ASC',
            'ordering' => 'ASC',
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            return $result;
        }catch(\Exception $ex){
            throw new \Exception($ex);
        }
    }

    public function filterBanners($params = array()){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('categories_banners');
        $select->join('categories', 'categories.categories_id=categories_banners.categories_id',array());
        $select->where(array(
            'website_id' => $_SESSION['CMSMEMBER']['website_id'],
        ));
        if(isset($params['categories_id']) && $params['categories_id']){
            $select->where(array(
                'categories_id' => $params['categories_id'],
            ));
        }
        if(isset($params['box_num']) && $params['box_num']){
            $select->where(array(
                'box_num' => $params['box_num'],
            ));
        }

        if($params['categories_banners_title']){
            $select->where->like('categories_banners_title', "%{$params['categories_banners_title']}%");
        }

        $select->order(array(
            'categories_id' => 'DESC',
            'box_num' => 'ASC',
            'ordering' => 'ASC',
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            return $result;
        }catch(\Exception $ex){
            throw new \Exception($ex);
        }
    }

    public function getBanner($id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('categories_banners');
        $select->where(array(
            'categories_banners_id' => $id,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            return $result->current();
        }catch(\Exception $ex){
            throw new \Exception($ex);
        }
    }

    public function countAllBanners($params){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('categories_banners')->columns(array('total' => new Expression('count(categories_banners_id)')));
        $select->join('categories', 'categories.categories_id=categories_banners.categories_id',array());
        $select->where(array(
            'website_id' => $_SESSION['CMSMEMBER']['website_id'],
        ));
        if(isset($params['cat_id'])){
            $select->where(array(
                'categories_id' => $params['cat_id'],
            ));
        }

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        }catch(\Exception $ex){
            throw new \Exception($ex);
        }
    }

    public function addBanner($data){
        $type = $data['banner_type'];
        unset($data['banner_type']);
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $sql = new Sql($adapter);

            if(isset($temp_name) && isset($name) && $type == 1){
                $upload_dir = DS.'custom'.DS.'domain_1'.DS.'categories_banners';
                if(!is_dir(PATH_BASE_ROOT.$upload_dir)){
                    mkdir(PATH_BASE_ROOT.$upload_dir, 0777);
                }

                $domain=$_SESSION['website']['website_domain'];
                $folderCategoriesdomain = $upload_dir. DS .$domain;
                if (!is_dir(PATH_BASE_ROOT.$folderCategoriesdomain)) {
                    mkdir(PATH_BASE_ROOT.$folderCategoriesdomain, 0777);
                }

                $year=date("Y");
                $month=date("m");
                $day=date("d");
                $ten_thu_muc_year = $folderCategoriesdomain.DS.$year;
                if(!is_dir(PATH_BASE_ROOT.$ten_thu_muc_year)) {
                    @mkdir(PATH_BASE_ROOT.$ten_thu_muc_year);
                }
                $ten_thu_muc_month=$ten_thu_muc_year."/".$month;
                if(!is_dir(PATH_BASE_ROOT.$ten_thu_muc_month)) {
                    @mkdir(PATH_BASE_ROOT.$ten_thu_muc_month);
                }
                $ten_thu_muc_day=$ten_thu_muc_month."/".$day;
                if(!is_dir(PATH_BASE_ROOT.$ten_thu_muc_day)) {
                    @mkdir(PATH_BASE_ROOT.$ten_thu_muc_day);
                }



                $filename = $data['file']['name'];
                $ext = explode('.', $filename);
                $ext = end($ext);
                $temp_name = $data['file']['tmp_name'];
                if(!in_array($ext, array('png','jpg','jpeg'))){
                    throw new \Exception('Định dạng hình không được hỗ trợ');
                }
                $name = $this->toAlias($data['categories_banners_title']).'-'.time().'.'.$ext;

                @move_uploaded_file($temp_name, PATH_BASE_ROOT.DS.$ten_thu_muc_day.DS.$name);

                $src = $ten_thu_muc_day.DS.$name;
                $src = '/image/'.$year.'/'.$month.'/'.$day.'/categories_banners-'$name;
                $$data['file'] = $src;
            }

            $insert = $sql->insert();
            $insert->into('categories_banners');
            $insert->columns(array('categories_banners_title','link','file','code','is_published','is_product','categories_id','box_num','categories_banners_description','price'));
			$data = $data->toArray();
			$data['price'] = str_replace(',','', $data['price']);
            $insert->values($data);
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString,$adapter::QUERY_MODE_EXECUTE);

            if(isset($temp_name) && isset($name)){
                $upload_dir = PATH_BASE_ROOT.DS.'custom'.DS.'domain_1'.DS.'categories_banners';
                if(!is_dir($upload_dir)){
                    mkdir($upload_dir, 0777);
                }
                @move_uploaded_file($temp_name, $upload_dir.DS.$name);
            }

            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function editBanner($id, $data, $old_file = ''){
        $type = $data['banner_type'];
        unset($data['banner_type']);
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $sql = new Sql($adapter);
            $upload_dir = PATH_BASE_ROOT.DS.'custom'.DS.'domain_1'.DS.'categories_banners';
            if($type == 1){
                $data['code'] = '';
                $name = $data['file']['name'];
                if($name){
                    $ext = explode('.', $name);
                    $ext = end($ext);
                    $name = $this->toAlias($data['categories_banners_title']).'-'.time().'.'.$ext;
                    $temp_name = $data['file']['tmp_name'];
                    $data['file'] = '/custom/domain_1/categories_banners/'.$name;
                    if($old_file){
                        $image_name = explode('/', $old_file);
                        $image_name = end($image_name);
                        @unlink($upload_dir.DS.$image_name);
                    }
                }else{
                    unset($data['file']);
                }
            }else{
                $data['file'] = '';
                if($old_file){
                    $image_name = explode('/', $old_file);
                    $image_name = end($image_name);
                    @unlink($upload_dir.DS.$image_name);
                }
            }
            $update = $sql->update('categories_banners');
			$data = $data->toArray();
			$data['price'] = str_replace(',', '', $data['price']);
            $update->set($data);
            $update->where(array(
                'categories_banners_id' => $id,
            ));
            $updateString = $sql->getSqlStringForSqlObject($update);
            $adapter->query($updateString,$adapter::QUERY_MODE_EXECUTE);
            if(isset($temp_name) && isset($name)){
                if(!is_dir($upload_dir)){
                    mkdir($upload_dir, 0777);
                }
                @move_uploaded_file($temp_name, $upload_dir.DS.$name);
            }
            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function deleteBanner($ids = array()){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $sql = new Sql($adapter);
            $delete = $sql->delete('categories_banners');
            $select = $sql->select('categories_banners')->columns(array('file'));
            $select->where(array(
                'categories_banners_id' => $ids,
            ));
            $select->where->notLike('file', '');
            $delete->where(array(
                'categories_banners_id' => $ids,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $deleteString = $sql->getSqlStringForSqlObject($delete);
            $results = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            $adapter->query($deleteString,$adapter::QUERY_MODE_EXECUTE);
            if(count($results) > 0){
                foreach($results as $file){
                    $name = $file['file'];
                    $name = explode('/', $name);
                    $name = end($name);
                    $upload_dir = PATH_BASE_ROOT.DS.'custom'.DS.'domain_1'.DS.'categories_banners';
                    @unlink($upload_dir.DS.$name);
                }
            }
            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
    }

    public function updateorderBannerData($data){
        $sql = "INSERT INTO categories_banners(categories_banners_id, ordering) VALUES ";
        $val = array();
        foreach($data as $id => $order){
            $val[] = "({$id}, {$order})";
        }
        $val = implode(',', $val);
        $sql .= $val;
        $sql .= " ON DUPLICATE KEY UPDATE ordering=VALUES(ordering)";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function removeCategoryOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    protected function getIdCol(){
        return 'categories_id';
    }
    protected function getOrderCol(){
        return 'ordering';
    }
}