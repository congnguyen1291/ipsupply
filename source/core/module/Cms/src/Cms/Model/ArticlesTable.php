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
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Filter\File\LowerCase;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\GreaterThan;
use Zend\Stdlib\Hydrator\ArraySerializable;
use Zend\Db\ResultSet\HydratingResultSet;

use Cms\Model\AppTable;


class ArticlesTable extends AppTable
{
    public function getAllCategories(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('categories_articles');
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0,
            'website_id' => $this->getWebsiteId(),
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->toArray();
    }

    public function getAllChildCategories($rows, $catid){
        $list = array();
        if( !empty($rows) ) {
            $newList = array();
            foreach($rows as $row){
                $newList[$row['parent_id']][] = $row;
            }
            if(isset($newList[$catid])){
                $list = array_merge($list,$this->getChilds($newList, $catid));
            }
        }
        return $list;
    }

    public function getChilds($list, $catid){
        $data = array();
        foreach($list[$catid] as $row){
            $data[] = $row['categories_articles_id'];
            if(isset($list[$row['categories_articles_id']])){
                $data = array_merge($data,$this->getChilds($list, $row['categories_articles_id']));
            }
        }
        return $data;
    }

    public function fetchAll( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('articles_id', 'users_id', 'website_id', 'users_fullname', 'categories_articles_id', 'is_new', 'is_hot', 'is_published', 'is_delete', 'date_create', 'date_update', 'ordering', 'number_views', 'thumb_images', 'is_faq', 'is_static', 'tags') );
        $select->from('articles');
        $select->join('articles_translate', new Expression("articles_translate.articles_id=articles.articles_id AND articles_translate.language = '{$this->getLanguagesId()}'"), array('articles_title' => new Expression("IFNULL(`articles_translate`.`articles_title`, `articles`.`articles_title`)"), 'articles_alias', 'articles_content','has_language' => new Expression("IFNULL(`articles_translate`.`articles_title`, '')"), 'articles_alias', 'articles_content', 'articles_sub_content', 'title_seo', 'keyword_seo', 'description_seo', 'language'),'left');
        $select->join('categories_articles', 'articles.categories_articles_id=categories_articles.categories_articles_id', array(),'left');
        $select->join('categories_articles_translate', new Expression("categories_articles_translate.categories_articles_id=categories_articles.categories_articles_id AND categories_articles_translate.language = '{$this->getLanguagesId()}'"), array('categories_articles_title' => new Expression("IFNULL(`categories_articles_translate`.`categories_articles_title`, `categories_articles`.`categories_articles_title`)")),'left');

        $select->where(array(
            'articles.website_id' => $this->getWebsiteId()
        ));
        
        if(isset($where['categories_articles_id'])){
            $rows = $this->getAllCategories();
            $cats = $this->getAllChildCategories($rows, $where['categories_articles_id']);
            $cats[] = $where['categories_articles_id'];
            $select->where(array(
                'articles.categories_articles_id' => $cats,
            ));
        }

        if( isset($where['articles_title']) ){
            $articles_title = $this->toAlias($where['articles_title']);
            $select->where->like('articles_translate.articles_alias', "%{$articles_title}%");
        }

        if( isset($where['is_static']) ){
            $select->where(array(
                'articles.is_static' => $where['is_static'],
            ));
        }

        $select->order(array(
            'articles.ordering' => 'ASC',
        ));

        $select->group('articles.articles_id');

        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch (\Exception $ex){}
        return array();
    }

    public function countAll( $where = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(articles.articles_id)")));
        $select->from('articles');
        $select->join('articles_translate', 'articles_translate.articles_id=articles.articles_id', array(),'left');
        $select->join('categories_articles', 'articles.categories_articles_id=categories_articles.categories_articles_id', array('categories_articles_title'),'left');

        $select->where(array(
            'articles.website_id' => $this->getWebsiteId(),
        ));
        
        if(isset($where['categories_articles_id'])){
            $rows = $this->getAllCategories();
            $cats = $this->getAllChildCategories($rows, $where['categories_articles_id']);
            $cats[] = $where['categories_articles_id'];
            $select->where(array(
                'articles.categories_articles_id' => $cats,
            ));
        }

        if( isset($where['articles_title']) ){
            $articles_title = $this->toAlias($where['articles_title']);
            $select->where->like('articles_translate.articles_alias', "%{$articles_title}%");
        }

        if( isset($where['is_static']) ){
            $select->where(array(
                'articles.is_static' => $where['is_static'],
            ));
        }

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function getArticleLanguage($id, $language = 1)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('articles_id', 'users_id', 'website_id', 'users_fullname', 'categories_articles_id', 'is_new', 'is_hot', 'is_published', 'is_delete', 'date_create', 'date_update', 'ordering', 'number_views', 'thumb_images', 'is_faq', 'is_static', 'tags') );
        $select->from('articles');
        $select->join('articles_translate', new Expression("articles_translate.articles_id=articles.articles_id AND articles_translate.language = '{$language}'"), array('articles_title','articles_title_root' => new Expression("IFNULL(`articles_translate`.`articles_title`, `articles`.`articles_title`)"), 'articles_alias', 'articles_content', 'articles_sub_content', 'title_seo', 'keyword_seo', 'description_seo', 'language'), 'left');
        $select->where(array(
            'articles.articles_id' =>  $id,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            /*chuyen thanh object Articles*/
            /*$hydrator     = new ArraySerializable();
            $rowPrototype = new Articles();
            $resultSet    = new HydratingResultSet($hydrator, $rowPrototype);
            $resultSet->initialize($results);
            $results = $resultSet->toArray();*/
            return $results;
        }catch(\Exception $ex){
            return array();
        }
        return array();
    }

    public function getArticle($id)
    {
        return $this->getById($id);
    }

    public function getById($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('articles_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return array();
        }
        return $row;
    }

    public function saveArticle( Articles $data )
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $row = array(
                'website_id' =>  $this->getWebsiteId(),
                'users_id' => $data->users_id,
                'users_fullname' => $data->users_fullname,
                'categories_articles_id' => $data->categories_articles_id,
                'articles_title' => $data->articles_title,
                'articles_alias' => $data->articles_alias,
                'articles_content' => $data->articles_content,
                'title_seo' => $data->title_seo,
                'keyword_seo' => $data->keyword_seo,
                'description_seo' => $data->description_seo,
                'is_new' => $data->is_new,
                'is_hot' => $data->is_hot,
                'is_published' => $data->is_published,
                'articles_sub_content' => $data->articles_sub_content,
                'ordering' => $data->ordering,
                'thumb_images' => $data->thumb_images,
                'is_faq' => $data->is_faq,
                'is_static' => $data->is_static,
                'tags' => $data->tags,
            );
            if(!empty($row['tags'])){
                $tags_id = array();
                $tags = explode(',', $row['tags']);
                foreach ($tags as $key => $tag_name) {
                    $tags_val = $this->getModelTable('TagsTable')->getTagByName($tag_name);
                    if(empty($tags_val)){
                        $row_tags = array('website_id' =>  $this->getWebsiteId(),
                            'tags_name'=>$tag_name,
                            'tags_alias'=>$this->toAlias($tag_name));
                        $tags_id[] = $this->getModelTable('TagsTable')->saveTag($row_tags);
                    }else{
                        $tags_id[] = $tags_val[0]['tags_id'];
                    }
                }
                $row['tags'] = implode(',', $tags_id);
            }else{
                $row['tags'] = '';
            }
            
            if ($articles_id == 0) {
                $updateOrdering = "UPDATE `articles` SET `ordering` = `ordering`+1 WHERE `website_id` = '{$this->getWebsiteId()}' ";
                $adapter->query($updateOrdering,$adapter::QUERY_MODE_EXECUTE);

                $this->tableGateway->insert($row);
                $articles_id = $this->getLastestId();
            } else {
                if ($this->getArticle($articles_id)) {
                    $this->tableGateway->update($row, array('articles_id' => $articles_id));
                } else {
                    throw new \Exception('Article id does not exist');
                }
            }

            $picture_ids = $data->picture_id;
            if (!empty($picture_ids) > 0) {
                $pictures = $this->getModelTable('PictureTable')->getPictureInIds($picture_ids);
                if(!empty($pictures)){
                    $list_image = array();
                    foreach ($pictures as $key => $picture) {
                        $list_image[] = $picture['folder'].'/'.$picture['name'].'.'.$picture['type'];
                    }
                    $this->insertImages($articles_id, $list_image);
                }
            }

            $adapter->getDriver()->getConnection()->commit();
            return $articles_id;
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            //echo $ex->getMessage();die();
            throw new \Exception($ex->getMessage());
        }

    }

    public function saveArticleTranslate( Articles $data , $articles_id = '')
    {
        $row = array(
                    'articles_id' => $articles_id,
                    'articles_title' => $data->articles_title,
                    'articles_alias' => $data->articles_alias,
                    'articles_sub_content' => $data->articles_sub_content,
                    'articles_content' => $data->articles_content,
                    'title_seo' => $data->title_seo,
                    'keyword_seo' => $data->keyword_seo,
                    'description_seo' => $data->description_seo,
                    'language' => $data->language,
                );
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $sql = new Sql($adapter);
            $insert = $sql->insert();
            $insert->into('articles_translate');
            $insert->columns(array('articles_id','articles_title', 'articles_alias','articles_sub_content','articles_content','title_seo','keyword_seo','description_seo','language'));
            $insert->values($row);
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $delsql = "DELETE FROM `articles_translate` WHERE `articles_id`={$articles_id} and language={$data->language}";
            $adapter->query($delsql,$adapter::QUERY_MODE_EXECUTE);
            $adapter->query($insertString,$adapter::QUERY_MODE_EXECUTE);
            $adapter->getDriver()->getConnection()->commit();
            
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
    }

    /*public function deleteArticles($articleids)
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $ids = implode(',', $articleids);
            $sql = "DELETE FROM `articles_images` WHERE articles_id IN ({$ids})";
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            foreach ($articleids as $id) {
                $folder = PATH_BASE_ROOT . DS . 'custom' . DS . 'domain_1' . DS . 'articles' . DS . 'fullsize' . DS . "article{$id}";
                $files = glob($folder . DS . '*');
                foreach ($files as $filename) {
                    $file = $folder . DS . basename($filename);
                    @unlink($file);
                }
                @rmdir($folder);
            }
            $sql = "DELETE FROM `articles` WHERE articles_id IN($ids)";
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            $this->getModelTable('ArticlesLanguageTable')->delete(array('articles_id' => $ids));
            $adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            die($ex->getMessage());
            return FALSE;
        }
    }*/

    public function insertImages($id, $data)
    {
//        $delsql = "DELETE FROM products_images WHERE products_id={$id}";
        $sql = "INSERT INTO articles_images(`articles_id`,`image`,`is_published`,`ordering`) VALUES ";
        $val = array();
        foreach ($data as $key => $img) {
            $val[] = "({$id}, '{$img}', 1, {$key})";
        }
        $val = implode(',', $val);
        $sql .= $val;
        try {
            $adapter = $this->tableGateway->getAdapter();
//            $adapter->query($delsql,$adapter::QUERY_MODE_EXECUTE);
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getImageList($id)
    {
        $sql = "SELECT *
                FROM articles_images
                WHERE articles_id={$id}";
        try {
            $adapter = $this->tableGateway->getAdapter();
            return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateContent($id, $data)
    {
        $set = array();
        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $pre = "";
            } else {
                $pre = "'";
            }
            $set[] = "{$key}={$pre}{$value}{$pre}";
        }
        $set = implode(',', $set);
        $sql = "UPDATE `articles` SET {$set} WHERE articles_id={$id}";
        try {
            $adapter = $this->tableGateway->getAdapter();
            return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteImageArticle($id, $image)
    {
        $sql = "DELETE FROM articles_images WHERE articles_id={$id} AND image LIKE '{$image}'";
        try {
            $adapter = $this->tableGateway->getAdapter();
            return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function findArticles($str)
    {
        $str = $this->toAlias($str);
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('articles_id','data' => 'articles_id', 'users_id', 'website_id', 'users_fullname', 'categories_articles_id', 'is_new', 'is_hot', 'is_published', 'is_delete', 'date_create', 'date_update', 'ordering', 'number_views', 'thumb_images', 'is_faq', 'is_static', 'tags') );
        $select->from('articles');
        $select->join('articles_translate', new Expression("articles_translate.articles_id=articles.articles_id AND articles_translate.language = '{$this->getLanguagesId()}'"), array('articles_title' => new Expression("IFNULL(`articles_translate`.`articles_title`, `articles`.`articles_title`)"), 'value' => new Expression("IFNULL(`articles_translate`.`articles_title`, `articles`.`articles_title`)"), 'has_language' => new Expression("IFNULL(`articles_translate`.`articles_title`, '')"), 'articles_alias', 'articles_content', 'articles_sub_content', 'title_seo', 'keyword_seo', 'description_seo', 'language'),'left');
        $select->where(array(
            'website_id' =>  $this->getWebsiteId(),
        ));
        if( !empty($str) ){
            $select->where->like('articles_translate.articles_alias', "%{$str}%");
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }
	
	public function filter($params = array()){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$select = $sql->select();
		$select->columns( array('articles_id', 'users_id', 'website_id', 'users_fullname', 'categories_articles_id', 'is_new', 'is_hot', 'is_published', 'is_delete', 'date_create', 'date_update', 'ordering', 'number_views', 'thumb_images', 'is_faq', 'is_static', 'tags') );
        $select->from('articles');
        $select->join('articles_translate', new Expression("articles_translate.articles_id=articles.articles_id AND articles_translate.language = '{$this->getLanguagesId()}'"), array('articles_title' => new Expression("IFNULL(`articles_translate`.`articles_title`, `articles`.`articles_title`)"), 'has_language' => new Expression("IFNULL(`articles_translate`.`articles_title`, '')"), 'articles_alias', 'articles_content', 'articles_sub_content', 'title_seo', 'keyword_seo', 'description_seo', 'language'),'left');
        $select->where(array(
            'website_id' =>  $this->getWebsiteId(),
        ));
		if($params['articles_title']){
            $str = $this->toAlias($params['articles_title']);
			$select->where->like('articles_translate.articles_alias', "%{$str}%");
		}
		if($params['categories_articles_id']){
			$select->where(array(
				'articles.categories_articles_id' => $params['categories_articles_id'],
			));
		}
		if($params['date_create']){
			$date_create = $params['date_create'];
			$date_create = explode(' _to_ ', $date_create);
			if(count($date_create) == 2){
				$select->where("articles.date_create BETWEEN '{$date_create[0]}' AND '{$date_create[1]}'");
			}
		}
		if($params['is_published']){
			$select->where(array(
				'articles.is_published' => 1,
			));
		}
		try{
			$selectString = $sql->getSqlStringForSqlObject($select);
			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
			$results = $results->toArray();
			return $results;
		}catch(\Exception $ex){
			throw new \Exception($ex->getMessage());
		}
	}

    public function updateOrder($data){
        $sql = "INSERT INTO articles(articles_id, ordering) VALUES ";
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

    public function removeArticleOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function getLastestId()
    {
        return $this->tableGateway->getLastInsertValue();
    }

    public function getIdCol()
    {
        return 'articles_id';
    }

    public function deleteArticles($ids)
    {
        $this->tableGateway->delete(array('articles_id' => $ids));
    }

    public function updateArticles($ids, $data)
    {
        $this->tableGateway->update($data, array('articles_id' => $ids));
    }
} 