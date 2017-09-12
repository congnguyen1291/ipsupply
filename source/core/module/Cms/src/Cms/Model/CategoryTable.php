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

use Cms\Model\AppTable;

class CategoryTable extends AppTable
{
    public function getAllCategoriesSort( $params = array() )
    {
        $results = array();
        try{
            $rows = $this->fetchAll( $params );
            $listCategory = array();
            $map = array();
            if(COUNT($rows)>0){
                foreach ($rows as $item ) {
                    $idParentCategory = $item['parent_id'];
                    if( $item['categories_id'] == $item['parent_id']){
                        $idParentCategory = 0;
                    }
                    if( isset($map['map'][$idParentCategory]) ){
                        $map['map'][$item['categories_id']] =  $map['map'][$idParentCategory];
                        $map['map'][$item['categories_id']][] =  $idParentCategory;
                    }else{
                        $map['map'][$item['categories_id']] =  array($idParentCategory);
                    }
                    $item['map'] = $map['map'][$item['categories_id']];
                    
                    if (isset($listCategory[$idParentCategory]) && !empty($listCategory[$idParentCategory]) ) {
                        $listCategory[$idParentCategory][] = $item;
                    } else {
                        $listCategory[$idParentCategory] = array($item);
                    }
                }
            }
            $results = $listCategory;
        }catch(\Exception $ex){}
        return $results;
    }

    public function fetchAll( $params = array() )
    {
        $languages_id = $this->getLanguagesId();
        if( !empty($params['languages_id']) ){
            $languages_id = $params['languages_id'];
        }
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('categories_id', 'website_id', 'parent_id', 'is_static', 'icon', 'is_published', 'is_delete', 'date_create', 'date_update', 'ordering', 'parent_name', 'addfeature', 'template_id', 'number_childrens_active'=> new Expression('(SELECT COUNT(*)
            FROM `categories` AS `t1`
            LEFT JOIN `categories` AS `t2` ON `t2`.`parent_id` = `t1`.`categories_id`
            LEFT JOIN `categories` AS `t3` ON `t3`.`parent_id` = `t2`.`categories_id`
            LEFT JOIN `categories` AS `t4` ON `t4`.`parent_id` = `t3`.`categories_id`
            WHERE `t1`.`parent_id` = `categories`.`categories_id` 
            AND (`t1`.`is_published` = 1 OR `t2`.`is_published` = 1 OR `t3`.`is_published` = 1 OR `t4`.`is_published` = 1))'), 'total_childrens_active'=> new Expression('(SELECT COUNT(*)
            FROM `categories` AS `t1`
            LEFT JOIN `categories` AS `t2` ON `t2`.`parent_id` = `t1`.`categories_id`
            LEFT JOIN `categories` AS `t3` ON `t3`.`parent_id` = `t2`.`categories_id`
            LEFT JOIN `categories` AS `t4` ON `t4`.`parent_id` = `t3`.`categories_id`
            WHERE `t1`.`parent_id` = `categories`.`categories_id`)')));
        $select->from('categories');
        $select->join('categories_translate', new Expression("categories.categories_id=categories_translate.categories_id AND categories_translate.language = '{$languages_id}'"), array('categories_title' => new Expression("IFNULL(`categories_translate`.`categories_title`, `categories`.`categories_title`)"),'has_language' => new Expression("IFNULL(`categories_translate`.`categories_title`, '')"), 'categories_description', 'categories_alias', 'seo_keywords', 'seo_description','seo_title','language'),'left');
        if( isset($params['parent_id']) ){
            $select->where(array(
                'categories.parent_id' => $params['parent_id']
            ));
        }
        if(isset($params['categories_title'])){
            $categories_title = $this->toAlias($params['categories_title']);
            $select->where->like('categories_translate.categories_alias', "%{$categories_title}%");
        }
        $select->order('categories.ordering ASC');
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

    public function countAll(  $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(categories.categories_id)")));
        $select->from('categories');
        if( isset($params['parent_id']) ){
            $select->where(array(
                'categories.parent_id' => $params['parent_id']
            ));
        }
        if(isset($params['categories_title'])){
            $categories_title = $this->toAlias($params['categories_title']);
            $select->where->like('categories_alias', "%{$categories_title}%");
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }


    public function getById($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('categories_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return array();
        }
        return $row;
    }

    public function getListCategory($params = array())
    {
        $languages_id = $this->getLanguagesId();
        if( !empty($params['languages_id']) ){
            $languages_id = $params['languages_id'];
        }
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('categories_id', 'website_id', 'parent_id', 'is_static', 'icon', 'is_published', 'is_delete', 'date_create', 'date_update', 'ordering', 'parent_name', 'addfeature', 'template_id', 'number_childrens_active'=> new Expression('(SELECT COUNT(*)
            FROM `categories` AS `t1`
            LEFT JOIN `categories` AS `t2` ON `t2`.`parent_id` = `t1`.`categories_id`
            LEFT JOIN `categories` AS `t3` ON `t3`.`parent_id` = `t2`.`categories_id`
            LEFT JOIN `categories` AS `t4` ON `t4`.`parent_id` = `t3`.`categories_id`
            WHERE `t1`.`parent_id` = `categories`.`categories_id` 
            AND (`t1`.`is_published` = 1 OR `t2`.`is_published` = 1 OR `t3`.`is_published` = 1 OR `t4`.`is_published` = 1))'), 'total_childrens_active'=> new Expression('(SELECT COUNT(*)
            FROM `categories` AS `t1`
            LEFT JOIN `categories` AS `t2` ON `t2`.`parent_id` = `t1`.`categories_id`
            LEFT JOIN `categories` AS `t3` ON `t3`.`parent_id` = `t2`.`categories_id`
            LEFT JOIN `categories` AS `t4` ON `t4`.`parent_id` = `t3`.`categories_id`
            WHERE `t1`.`parent_id` = `categories`.`categories_id`)')));
        $select->from('categories');
        $select->join('categories_translate', new Expression("categories.categories_id=categories_translate.categories_id AND categories_translate.language = '{$languages_id}'"), array('categories_title' => new Expression("IFNULL(`categories_translate`.`categories_title`, `categories`.`categories_title`)"),'has_language' => new Expression("IFNULL(`categories_translate`.`categories_title`, '')"), 'categories_description', 'categories_alias', 'seo_keywords', 'seo_description','seo_title','language'),'left');
        if( isset($params['parent_id']) ){
            $select->where(array(
                'categories.parent_id' => $params['parent_id']
            ));
        }
        if(isset($params['categories_title'])){
            $categories_title = $this->toAlias($params['categories_title']);
            $select->where->like('categories_translate.categories_alias', "%{$categories_title}%");
        }
        $select->order('categories.ordering ASC');
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
	public function getCategoryLanguage($id, $language=1)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('categories_id', 'website_id', 'parent_id', 'is_static', 'icon', 'is_published', 'is_delete', 'date_create', 'date_update', 'ordering', 'parent_name', 'addfeature', 'template_id'));
        $select->from('categories');
		$select->join('categories_translate', new Expression("categories.categories_id=categories_translate.categories_id AND categories_translate.language = '{$language}'"), array('categories_title', 'categories_title_root' => new Expression("IFNULL(`categories_translate`.`categories_title`, `categories`.`categories_title`)"), 'categories_description', 'categories_alias', 'seo_keywords', 'seo_description','seo_title','language'),'left');
        $select->where(array(
            'categories.categories_id' => $id,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
			$row = $result->current();
            return $row;
        }catch (\Exception $ex){}
        return array();
    }
    public function countListCategory($where = array())
    {

        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression('count(categories.categories_id)')));
        $select->from('categories');
        $select->where(array(
            'categories.website_id' => $this->getWebsiteId(),
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

    public function saveCategory(Category $cat, $picture_id = '', $old_file = '')
    {
        $data = array(
            'parent_id'         => $cat->parent_id,
            'website_id'         => $this->getWebsiteId(),
            'categories_title'  => $cat->categories_title,
            'categories_alias'  => $cat->categories_alias,
            'seo_keywords' => $cat->seo_keywords,
            'seo_description' => $cat->seo_description,
            'seo_title' => $cat->seo_title,
            'is_published'      => $cat->is_published,
            'is_delete'         => $cat->is_delete,
            'date_create'       => $cat->date_create,
			'categories_description' => $cat->categories_description,
            'date_update'       => $cat->date_update,
            'ordering'          => $cat->ordering,
            'template_id'          => $cat->template_id,
			'is_static'          => $cat->is_static,
        );
        if(!empty($picture_id)){
            $picture = $this->getModelTable('PictureTable')->getPicture($picture_id);
            if(!empty($picture)){
                $data['icon'] = $picture->folder.'/'.$picture->name.'.'.$picture->type;
            }
        }
        $id = (int) $cat->categories_id;
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            if( empty($id) ){
                $updateOrdering = "UPDATE `categories` SET `ordering` = `ordering`+1 WHERE `parent_id` = '{$cat->parent_id}' AND `website_id` = '{$this->getWebsiteId()}' ";
                $adapter->query($updateOrdering,$adapter::QUERY_MODE_EXECUTE);
            }
			$this->saveData($data, $id);
            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
    }
	public function saveCategoryTranslate(Category $cat, $categories_id = '')
    {
        $data = array(
			'categories_id'  => $categories_id,
            'categories_title'  => $cat->categories_title,
			'categories_description'=>$cat->categories_description,
            'categories_alias'  => $cat->categories_alias,
            'seo_keywords' => $cat->seo_keywords,
            'seo_description' => $cat->seo_description,
            'seo_title' => $cat->seo_title,
			'language' => $cat->language,
        );
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
			$sql = new Sql($adapter);
            $insert = $sql->insert();
            $insert->into('categories_translate');
            $insert->columns(array('categories_id','categories_title', 'categories_description','categories_alias','seo_keywords','seo_description','seo_title','language'));
            $insert->values($data);
			$insertString = $sql->getSqlStringForSqlObject($insert);
			$delsql = "DELETE FROM `categories_translate` WHERE `categories_id`={$categories_id} and language={$cat->language}";
            $adapter->query($delsql,$adapter::QUERY_MODE_EXECUTE);
            $adapter->query($insertString,$adapter::QUERY_MODE_EXECUTE);
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

    public function filter($params){
        $languages_id = $this->getLanguagesId();
        if( !empty($params['languages_id']) ){
            $languages_id = $params['languages_id'];
        }
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('categories_id', 'website_id', 'parent_id', 'is_static', 'icon', 'is_published', 'is_delete', 'date_create', 'date_update', 'ordering', 'parent_name', 'addfeature', 'template_id', 'number_childrens_active'=> new Expression('(SELECT COUNT(*)
            FROM `categories` AS `t1`
            LEFT JOIN `categories` AS `t2` ON `t2`.`parent_id` = `t1`.`categories_id`
            LEFT JOIN `categories` AS `t3` ON `t3`.`parent_id` = `t2`.`categories_id`
            LEFT JOIN `categories` AS `t4` ON `t4`.`parent_id` = `t3`.`categories_id`
            WHERE `t1`.`parent_id` = `categories`.`categories_id` 
            AND (`t1`.`is_published` = 1 OR `t2`.`is_published` = 1 OR `t3`.`is_published` = 1 OR `t4`.`is_published` = 1))'), 'total_childrens_active'=> new Expression('(SELECT COUNT(*)
            FROM `categories` AS `t1`
            LEFT JOIN `categories` AS `t2` ON `t2`.`parent_id` = `t1`.`categories_id`
            LEFT JOIN `categories` AS `t3` ON `t3`.`parent_id` = `t2`.`categories_id`
            LEFT JOIN `categories` AS `t4` ON `t4`.`parent_id` = `t3`.`categories_id`
            WHERE `t1`.`parent_id` = `categories`.`categories_id`)')));
        $select->from('categories');
        $select->join('categories_translate', new Expression("categories.categories_id=categories_translate.categories_id AND categories_translate.language = '{$languages_id}'"), array('categories_title' => new Expression("IFNULL(`categories_translate`.`categories_title`, `categories`.`categories_title`)"),'has_language' => new Expression("IFNULL(`categories_translate`.`categories_title`, '')"), 'categories_description', 'categories_alias', 'seo_keywords', 'seo_description','seo_title','language'),'left');
        if( isset($params['parent_id']) ){
            $select->where(array(
                'categories.parent_id' => $params['parent_id']
            ));
        }
        if(isset($params['categories_title'])){
            $categories_title = $this->toAlias($params['categories_title']);
            $select->where->like('categories_translate.categories_alias', "%{$categories_title}%");
        }
        $select->order('categories.ordering ASC');
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

    public function getBanners( $params = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('categories_banners');
        $select->join('categories', 'categories.categories_id=categories_banners.categories_id',array());
        $select->where(array(
            'website_id' => $this->getWebsiteId(),
        ));
        if(isset($params['cat_id'])){
            $select->where(array(
                'categories_id' => $params['cat_id'],
            ));
        }
        if( $this->hasPaging($params) ){
            $select->offset($this->getOffsetPaging($params['page'], $params['limit']));
            $select->limit($params['limit']);
        }
        $select->order(array(
            'categories_id' => 'DESC',
            'box_num' => 'ASC',
            'ordering' => 'ASC',
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch(\Exception $ex){}
        return array();
    }

    public function filterBanners($params = array()){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('categories_banners');
        $select->join('categories', 'categories.categories_id=categories_banners.categories_id',array());
        $select->where(array(
            'website_id' => $this->getWebsiteId(),
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

    public function countAllBanners($params = array()){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('categories_banners')->columns(array('total' => new Expression('count(categories_banners_id)')));
        $select->join('categories', 'categories.categories_id=categories_banners.categories_id',array());
        $select->where(array(
            'website_id' => $this->getWebsiteId(),
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
        }catch(\Exception $ex){}
        return 0;
    }

    public function addBanner($data){
        $type = $data['banner_type'];
        unset($data['banner_type']);
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $sql = new Sql($adapter);
            $insert = $sql->insert();
            $insert->into('categories_banners');
            $insert->columns(array('categories_banners_title','link','file','code','is_published','is_product','categories_id','box_num','categories_banners_description','price'));
            $data = $data->toArray();
            $data['price'] = str_replace(',','', $data['price']);
            $insert->values($data);
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString,$adapter::QUERY_MODE_EXECUTE);
            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function editBanner($id, $data){
        $type = $data['banner_type'];
        unset($data['banner_type']);
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $sql = new Sql($adapter);
            $update = $sql->update('categories_banners');
            $data = $data->toArray();
            $data['price'] = str_replace(',', '', $data['price']);
            $update->set($data);
            $update->where(array(
                'categories_banners_id' => $id,
            ));
            $updateString = $sql->getSqlStringForSqlObject($update);
            $adapter->query($updateString,$adapter::QUERY_MODE_EXECUTE);
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

    public function updateOrder($data){
        $sql = "INSERT INTO categories(categories_id, ordering) VALUES ";
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

    public function deleteCategories($ids)
    {
        $this->tableGateway->delete(array('categories_id' => $ids));
    }

    public function updateCategories($ids, $data)
    {
        $this->tableGateway->update($data, array('categories_id' => $ids));
    }

    protected function getIdCol(){
        return 'categories_id';
    }
    protected function getOrderCol(){
        return 'ordering';
    }
}