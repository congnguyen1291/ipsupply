<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 11:42 AM
 */

namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class ProductTable extends AppTable
{
    public $_breadCrumb = array();
    public $parentId = 0;

    public function getPublisherOfProduct( $pId )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('merchant');
        $select->join('products', new Expression('merchant.merchant_id = products.publisher_id OR FIND_IN_SET(merchant.merchant_id ,products.publisher_id)>0') , array());
        $select->where(array(
            'merchant.website_id' => $this->getWebsiteId(),
            'products.products_id' => $pId
        ));
        $select->group('merchant.merchant_id');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getProductOrderUpper( $ordering )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products');
        $select->where(array(
            'products.website_id' => $this->getWebsiteId(),
        ));
        $select->where(" products.ordering <= $ordering ");
        $select->order(array(
            'products.ordering' => 'ASC',
        ));
        $select->group('products.products_id');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getProductOrderDowner( $ordering )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products');
        $select->where(array(
            'products.website_id' => $this->getWebsiteId(),
        ));
        $select->where(" products.ordering >= $ordering ");
        $select->order(array(
            'products.ordering' => 'ASC',
        ));
        $select->group('products.products_id');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            if(isset($_GET["demo"])){
            die($selectString);
            }
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function fetchAll( $where = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('products_id', 'products_code', 'categories_id', 'manufacturers_id', 'users_id', 'website_id', 'transportation_id', 'users_fullname', 'promotion1', 'promotion2', 'promotion3', 'promotion_description', 'promotion_ordering', 'promotion1_description', 'promotion1_ordering', 'promotion2_description', 'promotion2_ordering', 'promotion3_description', 'promotion3_ordering', 'is_published', 'is_delete', 'is_new', 'is_hot', 'is_available', 'is_goingon', 'is_sellonline', 'tra_gop', 'date_create', 'date_update', 'hide_price', 'wholesale', 'price', 'price_sale', 'ordering', 'quantity', 'thumb_image', 'list_thumb_image', 'number_views', 'vat', 'rating', 'number_like', 'total_sale', 'convert_search', 'is_viewed', 'position_view', 'youtube_video', 'price_total', 'url_crawl', 'convert_sitemap', 'convert_images', 'tags', 'type_view', 'publisher_id','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
        $select->from('products');
        $select->join('products_translate', new Expression("products.products_id=products_translate.products_id AND products_translate.language = '{$this->getLanguagesId()}'"), array('products_title' => new Expression("IFNULL(`products_translate`.`products_title`, `products`.`products_title`)"),'has_language' => new Expression("IFNULL(`products_translate`.`products_title`, '')"), 'products_alias', 'products_description', 'products_longdescription_2', 'products_longdescription','bao_hanh','products_more', 'seo_keywords', 'seo_description', 'seo_title', 'promotion_description', 'promotion1_description', 'promotion2_description', 'promotion3_description', 'tags', 'language'),'left');
        $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available', 't_thumb_image'=>'thumb_image'), 'left');
        $select->join('products_type_translate', new Expression("products_type_translate.products_type_id=products_type.products_type_id AND products_type_translate.language={$this->getLanguagesId()}" ), array('type_name', 'language'),'left');
		$select->join('products_category', 'products_category.products_id=products.products_id', array(),'left');
        $select->join('categories', 'categories.categories_id=products_category.categories_id', array(),'left');
        $select->join('categories_translate', new Expression("categories.categories_id=categories_translate.categories_id AND categories_translate.language = '{$this->getLanguagesId()}'"), array('categories_title' => new Expression("IFNULL(`categories_translate`.`categories_title`, `categories`.`categories_title`)")),'left');
        $select->where(array(
            'products.website_id' => $this->getWebsiteId(),
        ));
        
        if(isset($where['categories_id'])){
            $rows = $this->getAllCategories();
            $cats = $this->getAllChildCategories($rows, $where['categories_id']);
            $cats[] = $where['categories_id'];
            $select->where(array(
                'products_category.categories_id' => $cats,
            ));
        }

        if( isset($where['products_title']) ){
            $products_title = $this->toAlias($where['products_title']);
            $select->where->like('products_translate.products_alias', "%{$products_title}%");
        }

        if( isset($where['products_code'])){
            $products_code = $this->toAlias($where['products_code']);
            $select->where->like('products.products_code', "%{$products_code}%");
        }

        if( isset($where['price'])){
            $select->where(array(
                'products.price' => $where['price']
            ));
        }

        if(isset($where['quantity'])){
            $select->where(array(
                'products.quantity' => $where['quantity']
            ));
        }

        if(isset($where['date_create'])){
            $date_create = $where['date_create'];
            $between = explode(' _to_ ', $date_create);
            if(count($between) == 2){
                $select->where("products.date_create BETWEEN '{$between[0]}' AND '{$between[1]}'");
            }
        }

        if(isset($where['is_published'])){
            $select->where(array(
                'products.is_published' => 1,
            ));
        }
        if(isset($where['is_available'])){
            $select->where(array(
                'products.is_available' => 1,
            ));
        }
		if(isset($where['is_hot'])){
            $select->where(array(
                'products.is_hot' => 1,
            ));
        }
		$select->where(array(
            'products.is_delete' => 0,
        ));
        $select->order(array(
            'products.ordering' => 'ASC',
        ));
        $select->group('products.products_id');

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
            $data[] = $row['categories_id'];
            if(isset($list[$row['categories_id']])){
                $data = array_merge($data,$this->getChilds($list, $row['categories_id']));
            }
        }
        return $data;
    }

    public function getAllCategories(){
        /**
         * @var $adapter \Zend\Db\Adapter\Adapter
         */
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('categories');
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0,
            'website_id' => $this->getWebsiteId(),
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->toArray();
    }

    public function countAll($where = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression('count( DISTINCT products.products_id)')));
        $select->from('products');
        $select->join('products_translate', new Expression("products.products_id=products_translate.products_id AND products_translate.language = '{$this->getLanguagesId()}'"), array(),'left');
		$select->join('products_category', 'products_category.products_id=products.products_id', array(),'left');
        $select->where(array(
            'website_id' => $this->getWebsiteId(),
        ));
        if(isset($where['categories_id'])){
            $rows = $this->getAllCategories();
            $cats = $this->getAllChildCategories($rows, $where['categories_id']);
            $cats[] = $where['categories_id'];
            $select->where(array(
                'products_category.categories_id' => $cats,
            ));
        }

        if(isset($where['products_title'])){
            $products_title = $this->toAlias($where['products_title']);
            $select->where->like('products_translate.products_alias', "%{$products_title}%");
        }

        if(isset($where['products_code'])){
            $products_code = $this->toAlias($where['products_code']);
            $select->where->like('products.products_code', "%{$products_code}%");
        }

        if(isset($where['price'])){
            $select->where(array(
                'products.price' => $where['price']
            ));
        }

        if(isset($where['quantity'])){
            $select->where(array(
                'products.quantity' => $where['quantity']
            ));
        }

        if(isset($where['date_create'])){
            $date_create = $where['date_create'];
            $between = explode(' _to_ ', $date_create);
            if(count($between) == 2){
                $select->where("products.date_create BETWEEN '{$between[0]}' AND '{$between[1]}'");
            }
        }
        if(isset($where['is_published'])){
            $select->where(array(
                'products.is_published' => 1,
            ));
        }
        if(isset($where['is_available'])){
            $select->where(array(
                'products.is_available' => 1,
            ));
        }
		if(isset($where['is_hot'])){
            $select->where(array(
                'products.is_hot' => 1,
            ));
        }
        $select->where(array(
            'products.is_delete' => 0,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
			$result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getProduct($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('products_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

	public function getProductLanguage($id, $language=1)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('products_id', 'products_code', 'categories_id', 'manufacturers_id', 'users_id', 'website_id', 'transportation_id', 'users_fullname', 'promotion1', 'promotion2', 'promotion3', 'promotion_description', 'promotion_ordering', 'promotion1_description', 'promotion1_ordering', 'promotion2_description', 'promotion2_ordering', 'promotion3_description', 'promotion3_ordering', 'is_published', 'is_delete', 'is_new', 'is_hot', 'is_available', 'is_goingon', 'is_sellonline', 'tra_gop', 'date_create', 'date_update', 'hide_price', 'wholesale', 'price', 'price_sale', 'ordering', 'quantity', 'thumb_image', 'list_thumb_image', 'number_views', 'vat', 'rating', 'number_like', 'total_sale', 'convert_search', 'is_viewed', 'position_view', 'youtube_video', 'price_total', 'url_crawl', 'convert_sitemap', 'convert_images', 'tags', 'type_view', 'publisher_id'));
        $select->from('products');
        $select->join('products_translate', new Expression("products.products_id=products_translate.products_id AND products_translate.language = '{$language}'"), array('products_title', 'products_title_root' => new Expression("IFNULL(`products_translate`.`products_title`, `products`.`products_title`)"),'has_language' => new Expression("IFNULL(`products_translate`.`products_title`, '')"), 'products_alias', 'products_description', 'products_longdescription_2', 'products_longdescription','bao_hanh','products_more', 'seo_keywords', 'seo_description', 'seo_title', 'promotion_description', 'promotion1_description', 'promotion2_description', 'promotion3_description', 'tags', 'language'),'left');
        $select->where(array(
            'products.products_id' => $id
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
			 $row = $result->current();
            return $row;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function saveProduct(Product $p, $request = NULL)
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
			$products_alias=str_replace(" ","-",$p->products_alias);
            $data = array(
                'website_id' => $this->getWebsiteId(),
                'products_code' => $p->products_code,
                'categories_id' => $p->categories_id,
                'manufacturers_id' => $p->manufacturers_id,
                'users_id' => $p->users_id,
                'transportation_id' => $p->transportation_id,
                'users_fullname' => $p->users_fullname,
                'products_title' => $p->products_title,
                'products_alias' => $products_alias,
                'products_description' => $p->products_description,
                'products_longdescription' => $p->products_longdescription,
                'bao_hanh' => $p->bao_hanh,
                'promotion' => $p->promotion,
                'promotion_description' => $p->promotion_description,
                'promotion_ordering' => $p->promotion_ordering,
                'promotion1' => $p->promotion1,
                'promotion1_description' => $p->promotion1_description,
                'promotion1_ordering' => $p->promotion1_ordering,
                'promotion2' => $p->promotion2,
                'promotion2_description' => $p->promotion2_description,
                'promotion2_ordering' => $p->promotion2_ordering,
                'promotion3' => $p->promotion3,
                'promotion3_description' => $p->promotion3_description,
                'promotion3_ordering' => $p->promotion3_ordering,
                'seo_keywords' => $p->seo_keywords,
                'seo_description' => $p->seo_description,
                'seo_title' => $p->seo_title,
                'products_more' => $p->products_more,
                'is_published' => $p->is_published,
                'is_delete' => $p->is_delete,
                'is_new' => $p->is_new,
                'is_available' => $p->is_available,
                'is_hot' => $p->is_hot,
                'is_goingon' => $p->is_goingon,
                'is_sellonline' => $p->is_sellonline,
                'is_viewed' => $p->is_viewed,
                'position_view' => $p->position_view,
                'tra_gop' => $p->tra_gop,
                'date_create' => $p->date_create,
                'hide_price' => $p->hide_price,
                'wholesale' => $p->wholesale,
                'price' => $p->price >= $p->price_sale ? $p->price : $p->price_sale ,
                'price_sale' => $p->price_sale <= $p->price ? ($p->price_sale ? $p->price_sale : $p->price) : $p->price,
                'ordering' => $p->ordering,
                'quantity' => $p->quantity,
                'thumb_image' => $p->thumb_image,
                'list_thumb_image' => $p->list_thumb_image,
                'number_views' => $p->number_views,
                'vat' => $p->vat,
                'type_view' => $p->type_view,
                'youtube_video' => trim($p->youtube_video),
                'publisher_id' => $p->publisher_id
            );
            if(!empty($p->tags)){
                $tags_id = array();
                $tags = explode(',', $p->tags);
                foreach ($tags as $key => $tag_name) {
                    $tags_val = $this->getModelTable('TagsTable')->getTagByName($tag_name);
                    if(empty($tags_val)){
                        $row_tags = array('website_id' => $this->getWebsiteId(),
                                        'tags_name'=>$tag_name,
                                        'tags_alias'=>$this->toAlias($tag_name));
                        $tags_id[] = $this->getModelTable('TagsTable')->saveTag($row_tags);
                    }else{
                        $tags_id[] = $tags_val[0]['tags_id'];
                    }
                }
                $data['tags'] = implode(',', $tags_id);
            }else{
                $data['tags'] = '';
            }


            $seo_keyword = $this->getBackCategories($p->categories_id);
            $data['seo_keywords'] = $data['products_title'] . ',' . $seo_keyword . ',' . $data['seo_keywords'];

            $products_id = (int)$p->products_id;
            if ($products_id == 0) {
                $updateOrdering = "UPDATE `products` SET `ordering` = `ordering`+1 WHERE `website_id` = '{$this->getWebsiteId()}' ";
                $adapter->query($updateOrdering,$adapter::QUERY_MODE_EXECUTE);

                $this->tableGateway->insert($data);
                $products_id = $this->getLastestId();
            } else {
                if ($this->getProduct($products_id)) {
                    $this->tableGateway->update($data, array('products_id' => $products_id));
                } else {
                    throw new \Exception('Product id does not exist');
                }
            }
            $data = $request->getPost('featureid');
            $products_type = $request->getPost('products_type');
            $products_type_default = $request->getPost('products_type_default', 0);
            $this->insertProductsType($products_id, $products_type, $products_type_default, $p->language);
            $feature_value = $request->getPost('feature_val');
            $this->insertFeature($products_id, $data, $feature_value);
            $extension = $request->getPost('ext');
            $this->insertExtension($products_id, $extension, $p->language);
            $extension_require = $request->getPost('ext_require');
            $this->insertExtensionRequire($products_id, $extension_require);

            $picture_ids = $request->getPost('picture_id', 0);
            if (!empty($picture_ids) > 0) {
                $pictures = $this->getModelTable('PictureTable')->getPictureInIds($picture_ids);
                if(!empty($pictures)){
                    $list_image = array();
                    foreach ($pictures as $key => $picture) {
                        $list_image[] = $picture['folder'].'/'.$picture['name'].'.'.$picture['type'];
                    }
                    $this->insertImages($products_id, $list_image);
                }
            }

            $products_recommend = $request->getPost('products_recommend');
            $recommends = array();
            if($products_recommend){
                $recommends = explode(',', $products_recommend);
            }
            $this->addRecommendProduct($products_id, $recommends);

            $adapter->getDriver()->getConnection()->commit();
            return array('success' => TRUE, 'productid'=>$products_id);
        } catch (\Exception $e) {
            $adapter->getDriver()->getConnection()->rollback();
            return array('success' => FALSE, 'msg' => $e->getMessage());
        }

    }
	public function saveProductTranslate(Product $p, $products_id = '')
    {
        $data = array(
			'products_id'  => $products_id,
            'products_title'  => $p->products_title,
			'products_alias'=>$p->products_alias,
            'products_description' => $p->products_description,
			'products_longdescription_2' => $p->products_longdescription_2,
            'products_longdescription' => $p->products_longdescription,
			'bao_hanh'=>$p->bao_hanh,
            'products_more'  => $p->products_more,
            'seo_keywords' => $p->seo_keywords,
            'seo_description' => $p->seo_description,
            'seo_title' => $p->seo_title,
			'tags'=>$p->tags,
			'promotion_description' => $p->promotion_description,
            'promotion1_description' => $p->promotion1_description,
            'promotion2_description' => $p->promotion2_description,
			'promotion3_description' => $p->promotion3_description,
			'language' => $p->language,
        );
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
			$sql = new Sql($adapter);
            $insert = $sql->insert();
            $insert->into('products_translate');
            $insert->columns(array('products_id','products_title', 'products_alias','products_description','products_longdescription_2','products_longdescription','bao_hanh','products_more','seo_keywords','seo_description','seo_title','promotion_description','promotion1_description','promotion2_description','promotion3_description', 'tags','language'));
            $insert->values($data);
			$insertString = $sql->getSqlStringForSqlObject($insert);
			$delsql = "DELETE FROM `products_translate` WHERE `products_id`={$products_id} and language={$p->language}";
            $adapter->query($delsql,$adapter::QUERY_MODE_EXECUTE);
            $adapter->query($insertString,$adapter::QUERY_MODE_EXECUTE);
            $adapter->getDriver()->getConnection()->commit();
			
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
    }
    public function updateProduct( $datas = array() , $where = array() )
    {
        if( !empty($datas) && !empty($where) ){
            $this->tableGateway->update($datas, $where);
        }
    }

    public function addRecommendProduct($id, $products)
    {
        $adapter = $this->tableGateway->getAdapter();
        try {
            $del = "DELETE FROM products_recommend WHERE from_products_id={$id}";
            $adapter->query($del, $adapter::QUERY_MODE_EXECUTE);
            if (count($products) > 0) {
                $val = array();
                foreach ($products as $p) {
                    $val[] = "({$id}, {$p})";
                }
                $val = implode(',', $val);
                $insert = "INSERT INTO products_recommend(from_products_id,to_products_id) VALUES {$val}";
                $adapter->query($insert, $adapter::QUERY_MODE_EXECUTE);
            }
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }
	
    public function getRecommendProducts($id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('products_id' => 'to_products_id'));
        $select->from('products_recommend');
        $select->where(array(
            'from_products_id' => $id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try{
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
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

    public function insertFeature($id, $data, $feature_value = array())
    {
        $delsql = "DELETE FROM products_feature WHERE products_id={$id}";
        if ($data) {
            $sql = 'INSERT INTO products_feature(`products_id`,`feature_id`, `value`) VALUES ';
            $val = array();
            foreach ($data as $feat) {
                if (isset($feature_value[$feat])) {
                    $val[] = "({$id}, {$feat},'{$feature_value[$feat]}')";
                } else {
                    $val[] = "({$id}, {$feat}, '')";
                }
            }
            $val = implode(',', $val);
            $sql .= $val;
        }
        try {
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($delsql, $adapter::QUERY_MODE_EXECUTE);
            if ($data) {
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function insertExtension($id, $data, $language = 0)
    {
        if (!empty($data)) {
            foreach ($data as $key => $row) {
                $row['price'] = str_replace(',', '', $row['price']);
                $row['ext_description'] = str_replace("'", '"', $row['ext_description']);
                $data[$key]['price'] = $row['price'];
                $data[$key]['ext_description'] = $row['ext_description'];
                if (trim($row['ext_name']) == '') {
                    $error = 'Tên loại chi phí không được bỏ trống';
                } elseif ($row['type'] == 'default' && (trim($row['price']) == '' || !is_numeric($row['price'])) ) {
                    $error = 'Giá của chi phí phải là số.';
                }

            }
        }
        if (isset($error)) {
            throw new \Exception($error);
        }

        if (!empty($data)) {
            foreach ($data as $ext) {
                $refer_product_id = '';
                if( $ext['type'] != 'default' ){
                    $refer_product_id = $ext['refer_product_id'];
                }
                $type = array(
                    'id' => $ext['id'],
                    'ext_id' => $ext['ext_id'],
                    'products_id' => $id,
                    'ext_name' => $ext['ext_name'],
                    'ext_description' => '',
                    'price' => $ext['price'],
                    'quantity' => $ext['quantity'],
                    'is_always' => !empty($ext['is_always']) ? $ext['is_always'] : 0,
                    'type' => $ext['type'],
                    'refer_product_id' => $refer_product_id,
                    'language' => $language,
                    'icons' => !empty($ext['icons']) ? $ext['icons'] : '',
                );
                //print_r($type);die();
                $this->getModelTable('ProductExtensionTable')->saveProductExtension( $type );
            }
        }
    }

    public function insertExtensionRequire($id, $datas)
    {
        if (count($datas) > 0) {
            foreach ($datas as $index=>$data) {
                foreach ($data['data'] as $key => $row) {
					if(trim($row['title'])!="" && $row['price']!=""){
						$row['price'] = str_replace(',', '', $row['price']);
						$row['descriptrion'] = str_replace("'", '"', $row['descriptrion']);
						$row['title'] = str_replace("'", '"', $row['title']);
						$datas[$index]['data'][$key]['price'] = $row['price'];
						$datas[$index]['data'][$key]['title'] = $row['title'];
						$datas[$index]['data'][$key]['descriptrion'] = $row['descriptrion'];
						if (trim($row['title']) == '') {
							$error = 'Tên loại chi phí không được bỏ trống';
						} elseif (trim($row['price']) == '' || !is_numeric($row['price'])) {
							$error = 'Giá của chi phí phải là số.';
						}
					}
                }
            }
        }

        if (isset($error)) {
            throw new \Exception($error);
        }

        $delsql = "DELETE FROM products_transportation WHERE products_id={$id}";
        if ($datas) {
            $sql = 'INSERT INTO products_transportation(`products_id`,`transportation_type`,`transportation_name`,`transportation_description`, `transportation_price`, `transportation_cities`) VALUES ';
            $val = array();
            foreach ($datas as $data) {
                foreach ($data['data'] as $key => $ext) {
                    $str_cities = '';
                    if($data['type'] != 'all'){
                        if(empty($ext['area'])){
                            continue;
                        }
                        $str_cities = implode(',', $ext['area']);
                    }
                    
                    $val[] = "({$id}, '{$data['type']}', '{$ext['title']}', '{$ext['descriptrion']}', '{$ext['price']}','{$str_cities}')";
                }
            }
            $val = implode(',', $val);
            $sql .= $val;
        }
        //echo $sql;die();
        try {
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($delsql, $adapter::QUERY_MODE_EXECUTE);
            if ($datas) {
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function insertProductsType($id, $datas, $products_type_default = 0, $language = 0)
    {
        $pass_next = false;
        $error = '';
        if (!empty($datas)) {
            foreach ($datas as $key=>$row) {
                $row['price'] = str_replace(',', '', $row['price']);
                $row['price_sale'] = str_replace(',', '', $row['price_sale']);
                $datas[$key]['price'] = $row['price'];
                $datas[$key]['price_sale'] = $row['price_sale'];
                if (trim($row['type_name']) == '') {
                    $error = 'Tên loại sản phẩm  không được bỏ trống';
                } elseif (trim($row['price']) == '' || !is_numeric($row['price']) 
                    || trim($row['price_sale']) == '' || !is_numeric($row['price_sale'])) {
                    $error = 'Giá của loại sản phẩm phải là số.';
                }
                if(!empty($error)){
                    break;
                }
                $pass_next = TRUE;
            }
        }else{
            $this->getModelTable('ProductTypeTable')->deleteProductType( array('products_id' => $id) );
        }

        if (!empty($datas) && $pass_next) {
            $is_default = 0;
            $i_time = 0;
            $ids = array();
            foreach ($datas as $key=>$data) {
                if( !empty($data['type_name']) ){
                    if(($products_type_default==$key || (count($datas)-1) == $i_time) && $is_default != 1){
                        $is_default = 1;
                    }else{
                        $is_default = 0;
                    }
                    $type = array(
                            'products_type_id' => $data['products_type_id'],
                            'products_id' => $id,
                            'type_name' => $data['type_name'],
                            'price' => $data['price'],
                            'price_sale' => $data['price_sale'],
                            'quantity' => $data['quantity'],
                            'is_available' => $data['is_available'],
                            'thumb_image' => !empty($data['thumb_image']) ? $data['thumb_image'] : '',
                            'is_default' => $is_default,
                            'language' => $language,
                        );
                    $products_type_id = $this->getModelTable('ProductTypeTable')->saveProductType( $type );
                    $ids[] = $products_type_id;
                }
                $i_time++;
            }

            $idDe = array();
            $listType = $this->getModelTable('ProductTypeTable')->getProductTypes( array('products_id' => $id) );
            foreach ($listType as $key => $te) {
                if( !in_array($te['products_type_id'], $ids)){
                    $idDe[] = $te['products_type_id'];
                }
            }
            if( !empty($idDe) ){
                $this->getModelTable('ProductTypeTable')->deleteProductType( array('products_type_id' => $idDe) );
            }
        }
    }

    public function getBackCategories($cate_id = '')
    {
        $breadCrumb = '';
        if ($cate_id) {
            $adapter = $this->tableGateway->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('categories');
            $select->where(array(
                'is_published' => 1,
                'is_delete' => 0,
                'categories_id' => $cate_id,
                'website_id' => $this->getWebsiteId(),
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $rows = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

            if ($rows) {
                foreach ($rows as $row) {
                    $this->parentId = $row['categories_id'];
                    if ( !empty($row['parent_id']) ) {
                        $this->getBackCategories($row['parent_id']);
                    }
                    $this->_breadCrumb[] = $row['categories_title'];

                }
            }
            ksort($this->_breadCrumb);
            $breadCrumb = implode(',', $this->_breadCrumb);
        }
        return $breadCrumb;

    }

    public function insertImages($id, $data)
    {
//        $delsql = "DELETE FROM products_images WHERE products_id={$id}";
        $sql = "INSERT INTO products_images(`products_id`,`images`,`is_published`,`ordering`) VALUES ";
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

    public function getFeature($id)
    {
        $sql = "SELECT feature.*, products_feature.value
                FROM feature
                INNER JOIN products_feature ON products_feature.feature_id = feature.feature_id
                WHERE products_feature.products_id={$id} AND website_id = {$this->getWebsiteId()}";
        try {
            $adapter = $this->tableGateway->getAdapter();
            return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getImageList($id)
    {
        $sql = "SELECT *
                FROM products_images
                WHERE products_id={$id}";
        try {
            $adapter = $this->tableGateway->getAdapter();
            return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getExtensionByProductId($id, $language = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('id', 'ext_id', 'products_id', 'ext_require', 'price', 'quantity', 'is_available', 'is_always', 'type', 'refer_product_id', 'icons') );
        $select->from('products_extensions');
        $select->join('products_extensions_translate', new Expression("(products_extensions.id = products_extensions_translate.id AND products_extensions_translate.language = '{$language}' )"), array('ext_name', 'ext_description', 'language'));
        $select->join('products', new Expression("(products_extensions.type = 'product' AND  products_extensions.refer_product_id = products.products_id)"), array(), 'left');
        $select->join('products_translate', new Expression("(products.products_id = products_translate.products_id  AND products_translate.language = '{$language}')"), array('products_title', 'products_title_root' => new Expression("IFNULL(`products_translate`.`products_title`, `products`.`products_title`)"),'has_language' => new Expression("IFNULL(`products_translate`.`products_title`, '')")), 'left');
        $select->where(array(
                'products_extensions.products_id' => $id,
                'products_extensions.ext_require' => 0
            ));
        $select->group('products_extensions.id');
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        } catch (\Exception $e) {}
        return array();
    }

    public function getExtensionRequireByProductId($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_transportation')
            ->where(array(
                'products_id' => $id
            ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getCitiesInExtensionRequireByProductId($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_transportation');
        $select->join('cities', new Expression('cities.area_id LIKE products_transportation.transportation_type'), array('cities_id','country_id','area_id','cities_title'))
            ->where(array(
                'products_id' => $id
            ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getProductType($id, $language = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_type');
        $select->where(array(
            'products_id' => $id
        ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getProductTypeLanguage($id, $language = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('products_type_id', 'products_id', 'price', 'price_sale', 'quantity', 'is_available', 'thumb_image', 'is_default') );
        $select->from('products_type');
        $select->join('products_type_translate', new Expression("products_type_translate.products_type_id=products_type.products_type_id AND products_type_translate.language={$language}" ), array('type_name','type_name_root' => new Expression("IFNULL(`products_type_translate`.`type_name`, `products_type`.`type_name`)"),'has_language' => new Expression("IFNULL(`products_type_translate`.`type_name`, '')"), 'language'),'left');
        $select->where(array(
            'products_type.products_id' => $id
        ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        } catch (\Exception $e) {}
        return array();
    }

    /*public function getProductType($id, $language = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns( array('products_type_id', 'products_id', 'price', 'price_sale', 'quantity', 'is_available', 'thumb_image', 'is_default') );
        $select->from('products_type');
        $select->join('products_type_translate', new Expression("products_type_translate.products_type_id = products_type.products_type_id AND products_type_translate.language = {$language}"), array('type_name','language'), 'left');
        $select->where(array(
            'products_id' => $id
        ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }*/

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
        $sql = "UPDATE products SET {$set} WHERE products_id={$id}";
		
        try {
            $adapter = $this->tableGateway->getAdapter();
            return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteImageProduct($id, $image)
    {
        $sql = "DELETE FROM products_images WHERE products_id={$id} AND images LIKE '{$image}'";
        try {
            $adapter = $this->tableGateway->getAdapter();
            return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getProductsByIds($ids)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('id' => 'products_id', 'text' => 'products_title'));
        $select->from('products')
            ->where(array(
                'products_id' => $ids
            ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getProductsByAlias($products_alias)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products')
            ->where(array(
                'products_alias' => $products_alias,
                'website_id' => $this->getWebsiteId(),
            ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->current();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function filter($data)
    {
        $where = array();
        if (isset($data['products_title']) && trim($data['products_title']) != '') {
            $data['products_title'] = $this->toAlias($data['products_title']);
            $where[] = "LCASE(products_alias) LIKE '%{$data['products_title']}%'";
        }
        if (isset($data['categories_id']) && $data['categories_id'] != 0) {
            $where[] = "categories_id = {$data['categories_id']}";
        }
        if (isset($data['date_create']) && trim($data['date_create']) != '') {
            $range = explode('-', $data['date_create']);
            if (count($range) == 2) {
                $start = explode('/', trim($range[0]));
                if (count($start) == 3) {
                    $temp[0] = $start[2];
                    $temp[1] = $start[0];
                    $temp[2] = $start[1];
                    $start = implode('-', $temp) . ' 0:0:0';
                    $end = explode('/', trim($range[1]));
                    if (count($end) == 3) {
                        $temp[0] = $end[2];
                        $temp[1] = $end[0];
                        $temp[2] = $end[1];
                        $end = implode('-', $temp) . ' 0:0:0';
                        $where[] = "date_create BETWEEN ('{$start}' AND '{$end}')";
                    }
                }
            }
        }
        if (isset($data['is_available'])) {
            $where[] = "is_available=1";
        }
        if (isset($data['is_published'])) {
            $where[] = "is_published=1";
        }
        $where[] = "is_delete=0 AND website_id={$this->getWebsiteId()}";
        $where = implode(' AND ', $where);
        $sql = "SELECT `products`.*,CASE WHEN count(products_articles.id) IS NULL THEN 0 ELSE count(products_articles.id) END as total_articles
                FROM {$this->tableGateway->table}
                LEFT JOIN products_articles ON products_articles.products_id = {$this->tableGateway->table}.products_id
                WHERE {$where}
                GROUP BY products.products_id";
        try {
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function loadArticles()
    {
        try {
            $sql = "SELECT articles.*, products_articles.products_id as productid
                    FROM articles
                    LEFT JOIN products_articles ON products_articles.articles_id = articles.articles_id
                    WHERE articles.is_delete=0 AND articles.is_published=1 AND website_id = {$this->getWebsiteId()}";
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function loadArticlesProduct($products_id)
    {
        try {
            $sql = "SELECT articles.*, products_articles.products_id as productid
                    FROM articles
                    LEFT JOIN products_articles ON products_articles.articles_id = articles.articles_id
                    WHERE articles.is_delete=0 AND articles.is_published=1 AND products_articles.products_id={$products_id}";
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function countAllProductArticles($pid)
    {
        try {
            $sql = "SELECT count(id) as total
                    FROM products_articles
                    WHERE products_id={$pid}";
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            return $result->current();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function addArticles($productid, $articles_ids = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $delsql = "DELETE FROM products_articles WHERE products_id={$productid}";
            if (count($articles_ids) > 0) {
                $sql = "INSERT INTO products_articles(`products_id`,`articles_id`) VALUES ";
                $val = array();
                foreach ($articles_ids as $id) {
                    $val[] = "({$productid}, {$id})";
                }
                $val = implode(',', $val);
                $sql .= $val;
            }
            $adapter->query($delsql, $adapter::QUERY_MODE_EXECUTE);
            if (count($articles_ids) > 0) {
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }
            $adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $e) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function findProducts($str)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('data' => 'products_id','products_id', 'products_code', 'categories_id', 'manufacturers_id', 'users_id', 'website_id', 'transportation_id', 'users_fullname', 'promotion1', 'promotion2', 'promotion3', 'promotion_description', 'promotion_ordering', 'promotion1_description', 'promotion1_ordering', 'promotion2_description', 'promotion2_ordering', 'promotion3_description', 'promotion3_ordering', 'is_published', 'is_delete', 'is_new', 'is_hot', 'is_available', 'is_goingon', 'is_sellonline', 'tra_gop', 'date_create', 'date_update', 'hide_price', 'wholesale', 'price', 'price_sale', 'ordering', 'quantity', 'thumb_image', 'list_thumb_image', 'number_views', 'vat', 'rating', 'number_like', 'total_sale', 'convert_search', 'is_viewed', 'position_view', 'youtube_video', 'price_total', 'url_crawl', 'convert_sitemap', 'convert_images', 'tags', 'type_view', 'publisher_id','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
        $select->from('products');
        $select->join('products_translate', new Expression("products.products_id=products_translate.products_id AND products_translate.language = '{$this->getLanguagesId()}'"), array('value' => new Expression("IFNULL(`products_translate`.`products_title`, `products`.`products_title`)"),'products_title' => new Expression("IFNULL(`products_translate`.`products_title`, `products`.`products_title`)"),'has_language' => new Expression("IFNULL(`products_translate`.`products_title`, '')"), 'products_alias', 'products_description', 'products_longdescription_2', 'products_longdescription','bao_hanh','products_more', 'seo_keywords', 'seo_description', 'seo_title', 'promotion_description', 'promotion1_description', 'promotion2_description', 'promotion3_description', 'tags', 'language'),'left');
        $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available', 't_thumb_image'=>'thumb_image'), 'left');
        $select->join('products_type_translate', new Expression("products_type_translate.products_type_id=products_type.products_type_id AND products_type_translate.language={$this->getLanguagesId()}" ), array('type_name', 'language'),'left');
        $select->join('products_category', 'products_category.products_id=products.products_id', array(),'left');
        $select->join('categories', 'categories.categories_id=products_category.categories_id', array(),'left');
        $select->join('categories_translate', new Expression("categories.categories_id=categories_translate.categories_id AND categories_translate.language = '{$this->getLanguagesId()}'"), array('categories_title' => new Expression("IFNULL(`categories_translate`.`categories_title`, `categories`.`categories_title`)")),'left');
        $select->where(array(
            'products.website_id' => $this->getWebsiteId(),
        ));

        if( !empty($str) ){
            $code = $str;
            $str = $this->toAlias($str);
            $select->where("(LCASE(products_code) LIKE '%{$code}%' OR LCASE(products_alias) LIKE '%{$str}%')");
        }

        $select->group('products.products_id');

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

    public function getReviews($str_where = '', $order = '', $intPage, $intPageSize)
    {
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }
        if (!$order) {
            $order = 'ORDER BY comments_datecrerate DESC';
        }
        $where = "WHERE website_id={$this->getWebsiteId()}";
        if ($str_where) {
            $where .= " AND ".$str_where;
        }

        $sql = "SELECT comments.*, users.full_name
                FROM comments
                INNER JOIN users ON comments.comments_member=users.users_id
                {$where}
                {$order}
                LIMIT {$intPage}, {$intPageSize}";
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        return $result;
    }

    public function countAllReview($str_where = '')
    {
        $where = "WHERE website_id={$this->getWebsiteId()}";
        if ($str_where) {
            $where .= " AND ".$str_where;
        }

        $sql = "SELECT count(comments_id) as total
                FROM comments
                {$where}";
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
    }

    public function deleteReview($ids = array())
    {
        try {
            $ids = implode(',', $ids);
            $sql = "DELETE FROM comments WHERE comments_id IN ({$ids})";
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $ex) {
            throw new \Exception($ex);
        }
    }
    public function copyProduct($product){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < 2; $i++) {
			$randomString.= $characters[rand(0, $charactersLength - 1)];
		}
        try{
            $sql = "INSERT INTO products(
                                    website_id,
                                    products_code,
                                    categories_id,
                                    manufacturers_id,
                                    users_id,
                                    users_fullname,
                                    products_title,
                                    products_alias,
                                    products_description,
                                    products_longdescription,
                                    seo_keywords,
                                    seo_description,
                                    seo_title,
                                    promotion,
                                    promotion1,
                                    promotion2,
                                    promotion3,
                                    promotion_description,
                                    promotion1_description,
                                    promotion2_description,
                                    promotion3_description,
                                    promotion_ordering,
                                    promotion1_ordering,
                                    promotion2_ordering,
                                    promotion3_ordering,
                                    is_published,
                                    is_delete,
                                    is_new,
                                    is_hot,
                                    is_available,
                                    is_goingon,
                                    date_create,
                                    date_update,
                                    hide_price,
                                    price,
                                    price_sale,
                                    ordering,
                                    quantity,
                                    thumb_image,
                                    list_thumb_image,
                                    number_views,
                                    vat,
                                    rating,
                                    number_like,
                                    total_sale,
                                    convert_search,
                                    youtube_video
                                    )
                                SELECT ".$this->getWebsiteId().",'".time()."',categories_id,manufacturers_id,".$_SESSION['CMSMEMBER']['users_id'].",
                                    '".$_SESSION['CMSMEMBER']['full_name']."',products_title,CONCAT(products_alias, '".$randomString."') as products_alias, products_description, products_longdescription,
                                    seo_keywords,
                                    seo_description,
                                    seo_title,
                                    promotion,
                                    promotion1,
                                    promotion2,
                                    promotion3,
                                    promotion_description,
                                    promotion1_description,
                                    promotion2_description,
                                    promotion3_description,
                                    promotion_ordering,
                                    promotion1_ordering,
                                    promotion2_ordering,
                                    promotion3_ordering,
                                    is_published,
                                    is_delete,
                                    is_new,
                                    is_hot,
                                    is_available,
                                    is_goingon,
                                    date_create,
                                    date_update,
                                    hide_price,
                                    price,
                                    price_sale,
                                    ordering,
                                    quantity,
                                    thumb_image,
                                    list_thumb_image,
                                    number_views,
                                    vat,
                                    rating,
                                    number_like,
                                    total_sale,
                                    convert_search,
                                    youtube_video
                                FROM products
                                WHERE products_id={$product->products_id}";
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            $pid = $adapter->getDriver()->getLastGeneratedValue();
//            $data = $request->getPost('featureid');
//            $feature_value = $request->getPost('feature_val');
            $this->insertCopyFeature($pid, $product->products_id);
//            $extension = $request->getPost('ext');
             $this->insertCopyExt($pid, $product->products_id);
			 $this->insertCopyCategoryProduct($pid,$product->products_id);
//            if ($request->getPost('hinh')) {
            //$folder = $request->getPost('folder');
//            $folder='product'
            //$images = $request->getPost('hinh');
            $folderProducts = PATH_BASE_ROOT . DS . 'custom' . DS . 'domain_1' . DS . 'products';
            if (!is_dir($folderProducts)) {
                mkdir($folderProducts, 0777);
            }
            $folder1 = PATH_BASE_ROOT . DS . 'custom' . DS . 'domain_1' . DS . 'products' . DS . 'fullsize';
            if (!is_dir($folder1)) {
                mkdir($folder1, 0777);
            }
            $folder2 = $folder1.DS."product{$product->products_id}";
            $folder1 .= DS . "product{$pid}";

            if (!is_dir($folder1)) {
                mkdir($folder1, 0777);
            }
            //if (isset ($folder) && $folder != '') {
                $list_image = array();
                $file_images = glob($folder2.DS.'*.*');
            foreach($file_images as $img){
                $file = explode('.', basename($img));
                $ext = end($file);
                $newFileName = $product->products_alias . '-' . time() . '-' . $this->randText(6) . '.' . $ext;
                $listImages[basename($img)] = $newFileName;
                $file = $folder2.DS.basename($img);
                if (is_file($file)) {
                    $newfile1 = $folder1 . "/" . $newFileName;
                    copy($file, $newfile1);
                    $list_image[] = "/custom/domain_1/products/fullsize/product{$pid}/{$newFileName}";
                }
            }

                if (count($list_image) > 0) {
                    $this->insertImages($pid, $list_image);
                    unset($list_image);
                }

            $html = mb_convert_encoding($product->products_description, 'HTML-ENTITIES', "UTF-8");
//            $html = $product->products_description;
            // Create a new DOM document
            $dom = new \DOMDocument ();
            $dom->encoding = 'utf-8';
            @$dom->loadHTML($html);
            $imgs = $dom->getElementsByTagName('img');
            if (count($imgs) > 0) {
                foreach ($imgs as $img) {
                    $src = $img->getAttribute('src');
                    $fileNameTemp = explode('/', $src);
                    $fileNameTemp = end($fileNameTemp);
                    if (isset($listImages[$fileNameTemp])) {
                        $src = str_replace("custom/domain_1/products/fullsize/product{$product->products_id}/" . $fileNameTemp, "custom/domain_1/products/fullsize/product{$pid}/{$listImages[$fileNameTemp]}", $src);
                        $img->setAttribute('src', $src);
                    }

                }
            }

            $html = $dom->saveXML($dom->getElementsByTagName('body')->item(0));
            $data_product ['products_description'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html);
            $data_product ['products_description'] = htmlentities($data_product ['products_description'], ENT_QUOTES, 'UTF-8');
            $html1 = mb_convert_encoding($product->products_longdescription, 'HTML-ENTITIES', "UTF-8");
			//$html1 = $product->products_longdescription;
            @$dom->loadHTML($html1);
            $imgs = $dom->getElementsByTagName('img');
            if (count($imgs) > 0) {
                foreach ($imgs as $img) {
                    $src = $img->getAttribute('src');
                    $fileNameTemp = explode('/', $src);
                    $fileNameTemp = end($fileNameTemp);
                    if (isset($listImages[$fileNameTemp])) {
                        $src = str_replace("custom/domain_1/products/fullsize/product{$product->products_id}/" . $fileNameTemp, "custom/domain_1/products/fullsize/product{$pid}/{$listImages[$fileNameTemp]}", $src);
                        $img->setAttribute('src', $src);
                    }

                }
            }

            $html1 = $dom->saveXML($dom->getElementsByTagName('body')->item(0));
            $data_product ['products_longdescription'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html1);
            $data_product ['products_longdescription'] = htmlentities($data_product ['products_longdescription'], ENT_QUOTES, 'UTF-8');
//                    $this->_db->update ( 'news', array ('content_vi' => $dataBind ['content_vi']), "id ='{$id}'" );

            $thumb_image = $product->thumb_image;
            $thumb_image = explode('/', $thumb_image);
            $thumb_image = end($thumb_image);
            if (isset($listImages[$thumb_image])) {
                //$src = str_replace ( 'hinhtam/'.$folder.'/'.$fileNameTemp, "custom/domain_1/products/fullsize/product{$pid}/{$listImages[$fileNameTemp]}", $src );
                $src = "/custom/domain_1/products/fullsize/product{$pid}/{$listImages[$thumb_image]}";
                $data_product['thumb_image'] = $src;
            }
            $this->updateContent($pid, $data_product);
			// $products_recommend = $request->getPost('products_recommend');
			// $products_recommend = explode(',', $products_recommend);
			// $this->addRecommendProduct($pid, $products_recommend);
            $adapter->getDriver()->getConnection()->commit();
            return $pid;
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
    }

    public function insertCopyFeature($new_pid, $old_pid){
        $features = $this->getFeature($old_pid);
        if ($features->count() > 0) {
            $sql = 'INSERT INTO products_feature(`products_id`,`feature_id`, `value`) VALUES ';
            $val = array();
            foreach ($features as $feat) {
                $val[] = "({$new_pid}, {$feat->feature_id},'{$feat->value}')";
            }
            $val = implode(',', $val);
            $sql .= $val;
			try {
            if(isset($sql) && $val){
                $adapter = $this->tableGateway->getAdapter();
				//die($sql);
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        }
        
    }

    public function insertCopyExt($new_pid, $old_pid){
        $exts = $this->getExtensionByProductId($old_pid);
        if (count($exts) > 0) {
            $sql = 'INSERT INTO products_extensions(`ext_id`,`products_id`,`ext_name`, `price`, `ext_description`) VALUES ';
            $val = array();
            foreach ($exts as $ext) {
                $val[] = "('{$ext['ext_id']}',{$new_pid}, '{$ext['ext_name']}', {$ext['price']}, '{$ext['ext_description']}')";
            }
            $val = implode(',', $val);
            $sql .= $val;
        }
        try {
            $adapter = $this->tableGateway->getAdapter();
            if (count($exts) > 0) {
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
	
	public function pushSearch(){
		$adapter = $this->tableGateway->getAdapter();
		$adapter->getDriver()->getConnection()->beginTransaction();
		try {
			$sql = "INSERT INTO `search_table`(
			                        products_id,
			                        products_title,
			                        keyword,
			                        keyword1,
			                        link) (SELECT
			                        products_id,
			                        products_title,
			                        CONCAT(products.seo_keywords,',',categories.categories_title,',',manufacturers.manufacturers_name,categories.seo_keywords,',',categories.seo_description),
			                        products.seo_description,
			                        CONCAT(products_alias,'-',products.products_id)
			                        FROM products
			                        INNER JOIN categories ON products.categories_id=categories.categories_id
			                        INNER JOIN manufacturers ON products.manufacturers_id=manufacturers.manufacturers_id
			                        WHERE convert_search=0 AND website_id={$this->getWebsiteId()}) ON DUPLICATE KEY UPDATE products_title=VALUES(products_title),keyword=VALUES(keyword),keyword1=VALUES(keyword1),link=VALUES(link)";
			$adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
			$sql = "UPDATE products SET convert_search=1 WHERE convert_search=0 AND website_id={$this->getWebsiteId()}";
			$adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
			$adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $e) {
		$adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($e->getMessage());
        }
	}

    public function addCopyProductRecommend($new_pid, $old_pid){

    }

    public function removeProductsOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }

    public function getLastestId()
    {
        return $this->tableGateway->getLastInsertValue();
    }

    protected function getIdCol()
    {
        return 'products_id';
    }

    protected function getOrderCol()
    {
        return 'ordering';
    }

	//CN 24/02/2017 Lấy dữ liệu product_category và categories
	public function getCategoryProduct($productid)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_category');
		$select->join('categories', new Expression('products_category.categories_id = categories.categories_id'), array('categories_title', 'categories_id'), 'left');
        $select->join('categories_translate', new Expression("categories.categories_id=categories_translate.categories_id AND categories_translate.language = '{$this->getLanguagesId()}'"), array('categories_title' => new Expression("IFNULL(`categories_translate`.`categories_title`, `categories`.`categories_title`)"),'has_language' => new Expression("IFNULL(`categories_translate`.`categories_title`, '')")),'left');
        $select->where(array(
            'products_id' => $productid,
        ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
	public function getCategoryProductIDCategory($productid)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_category');
        $select->join('categories', new Expression('products_category.categories_id = categories.categories_id'), array('categories_title', 'categories_id'), 'left');
        $select->join('categories_translate', new Expression("categories.categories_id=categories_translate.categories_id AND categories_translate.language = '{$this->getLanguagesId()}'"), array('categories_title' => new Expression("IFNULL(`categories_translate`.`categories_title`, `categories`.`categories_title`)"),'has_language' => new Expression("IFNULL(`categories_translate`.`categories_title`, '')")),'left');
		$select->where(array(
            'products_id' => $productid,
        ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
	public function addCategoryProduct($id, $category)
    {
        $adapter = $this->tableGateway->getAdapter();
        try {
			if($id!="" && count($category)>0){
				$del = "DELETE FROM products_category WHERE products_id={$id}";
				$adapter->query($del, $adapter::QUERY_MODE_EXECUTE);
				if (count($category) > 0) {
					$val = array();
					foreach ($category as $categorylist) {
						if($id!="" && trim($categorylist)!=""){
						$val[] = "({$id}, {$categorylist})";
						}
					}
					$val = implode(',', $val);
					$insert = "INSERT INTO products_category(products_id, categories_id) VALUES {$val}";
					$adapter->query($insert, $adapter::QUERY_MODE_EXECUTE);
				}
			}
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }
	// Add product target
	
	public function insertCopyCategoryProduct($new_pid, $old_pid){
        $CategoryProduct = $this->getCategoryProductIDCategory($old_pid);
		if (count($CategoryProduct) > 0) {
				$sql = 'INSERT INTO products_category(`products_id`,`categories_id`) VALUES ';
				$val = array();
				foreach ($CategoryProduct as $feat) {
					if($feat->categories_id!=""){
					$val[] = "({$new_pid}, {$feat->categories_id})";
					}
				}
				$val = implode(',', $val);
				$sql .= $val;
				try {
				if(isset($sql) && $val){
					$adapter = $this->tableGateway->getAdapter();
					//die($sql);
					$adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
				}
			} catch (\Exception $e) {
				//throw new \Exception($e->getMessage());
			}
        }
        
    }
	//End function sử lý category product 47 nguyen di tan, 119/54
	
	public function addProductTarget($id, $country_id, $city_id, $district_id, $ward_id)
    {
        $adapter = $this->tableGateway->getAdapter();
        try {
			if( !empty($id) 
                && (!empty($country_id) || !empty($city_id) || !empty($district_id) || !empty($ward_id) ) ){
				$del = "DELETE FROM products_target WHERE products_id={$id}";
				$adapter->query($del, $adapter::QUERY_MODE_EXECUTE);
				/*if ( !empty($districtid) ) {
					$val = array();
					foreach ($districtid as $districtidlist) {
						$val[] = "({$id}, '{$countryid}','{$cityid}', {$districtidlist}, 0)";
					}
					$val = implode(',', $val);
					$insert = "INSERT INTO products_target(products_id, country_id, city_id, district_id, ward_id) VALUES {$val}";
					$adapter->query($insert, $adapter::QUERY_MODE_EXECUTE);
				}else{*/

				$insert = "INSERT INTO products_target(products_id, country_id, city_id, district_id, ward_id) VALUES('{$id}','{$country_id}','{$city_id}','{$district_id}','{$ward_id}')";
                $adapter->query($insert, $adapter::QUERY_MODE_EXECUTE);
			}
        } catch (\Exception $ex) {   }
    }

	public function getProductTarget($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_target');
		$select->join('country', new Expression("FIND_IN_SET(country.id ,products_target.country_id)>0"), array('country_id' => 'id', 'country_title' => 'title'), 'left');
        $select->join('cities', new Expression("FIND_IN_SET(cities.cities_id ,products_target.city_id)>0"), array('cities_id', 'cities_title'), 'left');
        $select->join('districts', new Expression("FIND_IN_SET(districts.districts_id ,products_target.district_id)>0"), array('districts_id', 'districts_title'), 'left');
        $select->join('wards', new Expression("FIND_IN_SET(wards.wards_id ,products_target.ward_id)>0"), array('wards_id', 'wards_title'), 'left');
        $select->where(array(
            'products_id' => $id
        ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        } catch (\Exception $e) {}
    }

    public function updateOrder($data){
        $sql = "INSERT INTO products(products_id, ordering) VALUES ";
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

    public function updateProducts($ids, $data)
    {
        $this->tableGateway->update($data, array('products_id' => $ids));
    }

    public function deleteProducts($ids = array())
    {
        if( !empty($ids) ){
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            try{
                $sql = new Sql($adapter);

                $delete = $sql->delete('products');
                $delete->where(array(
                    'products_id' => $ids
                ));
                $deleteSql = $sql->getSqlStringForSqlObject($delete);
                $adapter->query($deleteSql,$adapter::QUERY_MODE_EXECUTE);

                /* -- */
                $deleteTran = $sql->delete('products_translate');
                $deleteTran->where(array(
                    'products_id' => $ids
                ));
                $deleteTranSql = $sql->getSqlStringForSqlObject($deleteTran);
                $adapter->query($deleteTranSql,$adapter::QUERY_MODE_EXECUTE);
                
                /* -- */
                $deleteTarget = $sql->delete('products_target');
                $deleteTarget->where(array(
                    'products_id' => $ids
                ));
                $deleteTargetSql = $sql->getSqlStringForSqlObject($deleteTarget);
                $adapter->query($deleteTargetSql,$adapter::QUERY_MODE_EXECUTE);

                /* -- */
                $deleteCategory = $sql->delete('products_category');
                $deleteCategory->where(array(
                    'products_id' => $ids
                ));
                $deleteCategorySql = $sql->getSqlStringForSqlObject($deleteCategory);
                $adapter->query($deleteCategorySql,$adapter::QUERY_MODE_EXECUTE);

                /* -- */
                $deleteExtensions = $sql->delete('products_extensions');
                $deleteExtensions->where(array(
                    'products_id' => $ids
                ));
                $deleteExtensionsSql = $sql->getSqlStringForSqlObject($deleteExtensions);
                $adapter->query($deleteExtensionsSql,$adapter::QUERY_MODE_EXECUTE);

                /* -- */
                $deleteFeature = $sql->delete('products_feature');
                $deleteFeature->where(array(
                    'products_id' => $ids
                ));
                $deleteFeatureSql = $sql->getSqlStringForSqlObject($deleteFeature);
                $adapter->query($deleteFeatureSql,$adapter::QUERY_MODE_EXECUTE);

                /* -- */
                $deleteArticles = $sql->delete('products_articles');
                $deleteArticles->where(array(
                    'products_id' => $ids
                ));
                $deleteArticlesSql = $sql->getSqlStringForSqlObject($deleteArticles);
                $adapter->query($deleteArticlesSql,$adapter::QUERY_MODE_EXECUTE);

                /* -- */
                $deleteImages = $sql->delete('products_images');
                $deleteImages->where(array(
                    'products_id' => $ids
                ));
                $deleteImagesSql = $sql->getSqlStringForSqlObject($deleteImages);
                $adapter->query($deleteImagesSql,$adapter::QUERY_MODE_EXECUTE);

                /* -- */
                $deleteTransportation = $sql->delete('products_transportation');
                $deleteTransportation->where(array(
                    'products_id' => $ids
                ));
                $deleteTransportationSql = $sql->getSqlStringForSqlObject($deleteTransportation);
                $adapter->query($deleteTransportationSql,$adapter::QUERY_MODE_EXECUTE);

                /* -- */
                $deleteRecommend = $sql->delete('products_recommend');
                $deleteRecommend->where(array(
                    'from_products_id' => $ids,
                ));
                $deleteRecommendSql = $sql->getSqlStringForSqlObject($deleteRecommend);
                $adapter->query($deleteRecommendSql,$adapter::QUERY_MODE_EXECUTE);

                /* -- */
                $deleteRecommend = $sql->delete('products_recommend');
                $deleteRecommend->where(array(
                    'to_products_id' => $ids,
                ));
                $deleteRecommendSql = $sql->getSqlStringForSqlObject($deleteRecommend);
                $adapter->query($deleteRecommendSql,$adapter::QUERY_MODE_EXECUTE);

                $adapter->getDriver()->getConnection()->commit();
                return TRUE;
            }catch(\Exception $ex){
                $adapter->getDriver()->getConnection()->rollback();
                echo $ex->getMessage();die();
                throw new \Exception($ex);
            }
        }
        return FALSE;
    }
	//End add product target
} 