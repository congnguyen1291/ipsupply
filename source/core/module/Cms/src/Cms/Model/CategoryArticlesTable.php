<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 10:19 AM
 */

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

class CategoryArticlesTable extends AppTable
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
                    if( $item['categories_articles_id'] == $item['parent_id']){
                        $idParentCategory = 0;
                    }
                    if( isset($map['map'][$idParentCategory]) ){
                        $map['map'][$item['categories_articles_id']] =  $map['map'][$idParentCategory];
                        $map['map'][$item['categories_articles_id']][] =  $idParentCategory;
                    }else{
                        $map['map'][$item['categories_articles_id']] =  array($idParentCategory);
                    }
                    $item['map'] = $map['map'][$item['categories_articles_id']];
                    
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
        $select->columns(array('categories_articles_id', 'website_id', 'parent_id', 'is_published', 'is_delete', 'is_technical_category', 'date_create', 'date_update', 'ordering', 'is_faq', 'is_static', 'number_childrens_active'=> new Expression('(SELECT COUNT(*)
            FROM `categories_articles` AS `t1`
            LEFT JOIN `categories_articles` AS `t2` ON `t2`.`parent_id` = `t1`.`categories_articles_id`
            LEFT JOIN `categories_articles` AS `t3` ON `t3`.`parent_id` = `t2`.`categories_articles_id`
            LEFT JOIN `categories_articles` AS `t4` ON `t4`.`parent_id` = `t3`.`categories_articles_id`
            WHERE `t1`.`parent_id` = `categories_articles`.`categories_articles_id` 
            AND (`t1`.`is_published` = 1 OR `t2`.`is_published` = 1 OR `t3`.`is_published` = 1 OR `t4`.`is_published` = 1))'), 'total_childrens_active'=> new Expression('(SELECT COUNT(*)
            FROM `categories_articles` AS `t1`
            LEFT JOIN `categories_articles` AS `t2` ON `t2`.`parent_id` = `t1`.`categories_articles_id`
            LEFT JOIN `categories_articles` AS `t3` ON `t3`.`parent_id` = `t2`.`categories_articles_id`
            LEFT JOIN `categories_articles` AS `t4` ON `t4`.`parent_id` = `t3`.`categories_articles_id`
            WHERE `t1`.`parent_id` = `categories_articles`.`categories_articles_id`)')));
        $select->from('categories_articles');
        $select->join('categories_articles_translate', new Expression("categories_articles_translate.categories_articles_id=categories_articles.categories_articles_id AND categories_articles_translate.language = '{$languages_id}'"), array('categories_articles_title' => new Expression("IFNULL(`categories_articles_translate`.`categories_articles_title`, `categories_articles`.`categories_articles_title`)"), 'has_language' => new Expression("IFNULL(`categories_articles_translate`.`categories_articles_title`, '')"), 'categories_articles_alias', 'seo_keywords', 'seo_description', 'seo_title','language'),'left');
        if( isset($params['parent_id']) ){
            $select->where(array(
                'categories_articles.parent_id' => $params['parent_id']
            ));
        }

        if(isset($params['categories_articles_title'])){
            $categories_articles_title = $this->toAlias($params['categories_articles_title']);
            $select->where->like('categories_articles_translate.categories_articles_alias', "%{$categories_articles_title}%");
        }

        $select->order('categories_articles.ordering ASC');
        
        if( $this->hasPaging($params) ){
            $select->offset($this->getOffsetPaging($params['page'], $params['limit']));
            $select->limit($params['limit']);
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function countAll( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(categories_articles.categories_articles_id)")));
        $select->from('categories_articles');
        if( isset($params['parent_id']) ){
            $select->where(array(
                'categories_articles.parent_id' => $params['parent_id']
            ));
        }

        if(isset($params['categories_articles_title'])){
            $categories_articles_title = $this->toAlias($params['categories_articles_title']);
            $select->where->like('categories_articles_alias', "%{$categories_articles_title}%");
        }

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function getCategoryLanguage($id, $language = 1){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('categories_articles_id', 'website_id', 'parent_id', 'is_published', 'is_delete', 'is_technical_category', 'date_create', 'date_update', 'ordering', 'is_faq', 'is_static'));
        $select->from('categories_articles');
        $select->join('categories_articles_translate', new Expression("categories_articles_translate.categories_articles_id=categories_articles.categories_articles_id AND categories_articles_translate.language = '{$language}'"), array('categories_articles_title', 'categories_articles_title_root' => new Expression("IFNULL(`categories_articles_translate`.`categories_articles_title`, `categories_articles`.`categories_articles_title`)"), 'categories_articles_alias', 'seo_keywords', 'seo_description', 'seo_title','language'),'left');
        $select->where(array(
            'categories_articles.categories_articles_id' =>  $id,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results;
        }catch(\Exception $ex){
            return array();
        }
        return array();
    }

    public function getCategory($id)
    {
        return $this->getById($id);
    }

    public function getById($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('categories_articles_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return array();
        }
        return $row;
    }

    public function saveCategory( CategoryArticles $data )
    {
        $row = array(
            'categories_articles_id' => $data->categories_articles_id,
            'website_id' => $this->getWebsiteId(),
            'parent_id' => $data->parent_id,
            'categories_articles_title' => $data->categories_articles_title,
            'categories_articles_alias' => $data->categories_articles_alias,
            'is_published' => $data->is_published,
            'seo_title' => $data->seo_title,
            'seo_keywords' => $data->seo_keywords,
            'seo_description' => $data->seo_description,
            'ordering' => $data->ordering,
			'is_static' => $data->is_static,
        );
        $id = (int)$data->categories_articles_id;
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            if ( empty($id) ) {
                $updateOrdering = "UPDATE `categories_articles` SET `ordering` = `ordering`+1 WHERE `parent_id` = '{$cat->parent_id}' AND `website_id` = '{$this->getWebsiteId()}' ";
                $adapter->query($updateOrdering,$adapter::QUERY_MODE_EXECUTE);

                $this->tableGateway->insert($row);
                $id = $this->getLastestId();
            } else {
                if ($this->getById($id)) {
                    $this->tableGateway->update($row, array('categories_articles_id' => $id));
                } else {
                    throw new \Exception('Category id does not exist');
                }
            }
            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
        return $id;
    }

    public function saveCategoryTranslate( CategoryArticles $data , $categories_articles_id = '')
    {
        $row = array(
                    'categories_articles_id' => $categories_articles_id,
                    'categories_articles_title' => $data->categories_articles_title,
                    'categories_articles_alias' => $data->categories_articles_alias,
                    'seo_title' => $data->seo_title,
                    'seo_keywords' => $data->seo_keywords,
                    'seo_description' => $data->seo_description,
                    'language' => $data->language,
                );
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $sql = new Sql($adapter);
            $insert = $sql->insert();
            $insert->into('categories_articles_translate');
            $insert->columns(array('categories_articles_id','categories_articles_title', 'categories_articles_alias','seo_title','seo_keywords','seo_description','language'));
            $insert->values($row);
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $delsql = "DELETE FROM `categories_articles_translate` WHERE `categories_articles_id`={$categories_articles_id} and language={$data->language}";
            $adapter->query($delsql,$adapter::QUERY_MODE_EXECUTE);
            $adapter->query($insertString,$adapter::QUERY_MODE_EXECUTE);
            $adapter->getDriver()->getConnection()->commit();
            
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
    }

    public function updateOrder($data){
        $sql = "INSERT INTO categories_articles(categories_articles_id, ordering) VALUES ";
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

    public function deleteCategory($cats = array()){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            /*$sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('categories_articles');
            $select->columns(array('categories_articles_id'));
            $select->where(array(
                'parent_id' => $cats,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            if(count($results) > 0){
                $_SESSION['error'][] = "Bạn không thể xóa danh mục cha của danh mục khác";
                return FALSE;
            }
            $select = $sql->select();
            $select->from('articles');
            $select->columns(array('articles_id'));
            $select->where(array(
                'categories_articles_id' => $cats,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            if(count($results) > 0){
                $_SESSION['error'][] = "Danh mục có chứa bài viết, bạn không thể xóa";
                return FALSE;
            }*/
            $categories_articles_id = $cats;
            if( !is_array($cats) ){
                $categories_articles_id = array($cats);
            }
            $categories_articles_id = implode(',',$categories_articles_id);
            $delsql = "DELETE FROM `categories_articles` WHERE `categories_articles_id` IN ({$categories_articles_id})";
            $adapter->query($delsql,$adapter::QUERY_MODE_EXECUTE);
            $delsql = "DELETE FROM `categories_articles_translate` WHERE `categories_articles_id` IN ({$categories_articles_id})";
            $adapter->query($delsql,$adapter::QUERY_MODE_EXECUTE);

            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
        }
    }

    public function deleteCategories($ids)
    {
        $this->tableGateway->delete(array('categories_articles_id' => $ids));
    }

    public function updateCategories($ids, $data)
    {
        $this->tableGateway->update($data, array('categories_articles_id' => $ids));
    }

    public function removeArticleCategoryOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function getIdCol(){
        return 'categories_articles_id';
    }
} 