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

class ProductTable extends GeneralTable
{
    public function __construct(TableGateway $tableGateway)
    {
        parent::__construct($tableGateway);
    }

    public function fetchAll($where = array(), $order = array(), $intPage, $intPageSize)
    {
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }

        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
        $select->from('products');
        $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
        $select->join('products_articles','products_articles.products_id = products.products_id', array('total_articles' => new Expression('count(products_articles.id)')), 'left');
        $select->where(array(
            'products.website_id' => $_SESSION['CMSMEMBER']['website_id'],
        ));
        if(isset($where['categories_id'])){
            $rows = $this->getAllCategories();
            $cats = $this->getAllChildCategories($rows, $where['categories_id']);
            $cats[] = $where['categories_id'];
            $select->where(array(
                'products.categories_id' => $cats,
            ));
        }
        if(isset($where['products_title'])){
            $products_title = $this->toAlias($where['products_title']);
            $select->where->like('products_alias', "%{$products_title}%");
        }

        if(isset($where['date_create'])){
            $date_create = $where['date_create'];
            $between = explode(' _to_ ', $date_create);
            if(count($between) == 2){
                $select->where("date_create BETWEEN '{$between[0]}' AND '{$between[1]}'");
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
		$select->where(array(
            'products.is_delete' => 0,
        ));
        if(!count($order)){
            $select->order(array(
                'products.ordering' => 'ASC',
            ));
        }else{
            $select->order($order);
        }
        $select->group('products.products_id');
        $select->limit($intPageSize);
        $select->offset($intPage);
        /*
        if (!$order) {
            $order = 'ORDER BY ordering ASC';
        }
        $where1 = "WHERE is_delete = 0";
        if ($where) {
            $where1 .= " AND {$where}";
        }

        $sql = "SELECT {$this->tableGateway->table}.*, CASE WHEN count(products_articles.id) IS NULL THEN 0 ELSE count(products_articles.id) END as total_articles
                FROM {$this->tableGateway->table}
                LEFT JOIN products_articles ON products_articles.products_id = {$this->tableGateway->table}.products_id
                {$where1}
                GROUP BY products.products_id
                {$order}
                LIMIT {$intPage}, {$intPageSize}";
        $adapter = $this->tableGateway->getAdapter();
        */
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getAllChildCategories($rows, $catid){
        $list = array();
        if(count($rows)) {
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
            'website_id' => $_SESSION['CMSMEMBER']['website_id'],
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->toArray();
    }

    public function countAll($where = array())
    {
        /*
        if ($where) {
            $where = "WHERE {$where}";
        }
        $sql = "SELECT count(products.products_id) as total
                FROM {$this->tableGateway->table}
                {$where}";
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
        */

        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression('count(products.products_id)')));
        $select->from('products');
        $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
        $select->where(array(
            'website_id' => $_SESSION['CMSMEMBER']['website_id'],
        ));
        if(isset($where['categories_id'])){
            $select->where(array(
                'products.categories_id' => $where['categories_id'],
            ));
        }
        if(isset($where['products_title'])){
            $products_title = $this->toAlias($where['products_title']);
            $select->where->like('products_alias', "%{$products_title}%");
        }

        if(isset($where['date_create'])){
            $date_create = $where['date_create'];
            $between = explode(' _to_ ', $date_create);
            if(count($between) == 2){
                $select->where("date_create BETWEEN '{$between[0]}' AND '{$between[1]}'");
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

    public function saveProduct1(Product $product)
    {
        $data = array(
            'website_id' => $_SESSION['CMSMEMBER']['website_id'],
            'products_code' => $product->products_code,
            'categories_id' => $product->categories_id,
            'manufacturers_id' => $product->manufacturers_id,
            'users_id' => $product->users_id,
            'users_fullname' => $product->users_fullname,
            'products_title' => $product->products_title,
            'products_alias' => $product->products_alias,
            'products_description' => $product->products_description,
            'products_longdescription' => $product->products_longdescription,
            'promotion' => $product->promotion,
            'promotion_description' => $product->promotion_description,
            'promotion_ordering' => $product->promotion_ordering,
            'promotion1' => $product->promotion1,
            'promotion1_description' => $product->promotion1_description,
            'promotion1_ordering' => $product->promotion1_ordering,
            'promotion2' => $product->promotion2,
            'promotion2_description' => $product->promotion2_description,
            'promotion2_ordering' => $product->promotion2_ordering,
            'promotion3' => $product->promotion3,
            'promotion3_description' => $product->promotion3_description,
            'promotion3_ordering' => $product->promotion3_ordering,
            'seo_keywords' => $product->seo_keywords,
            'seo_description' => $product->seo_description,
            'seo_title' => $product->seo_title,
            'is_published' => $product->is_published,
            'is_delete' => $product->is_delete,
            'is_new' => $product->is_new,
            'is_available' => $product->is_available,
            'date_create' => $product->date_create,
            'price' => $product->price,
            'price_sale' => $product->price_sale,
            'ordering' => $product->ordering,
            'quantity' => $product->quantity,
            'thumb_image' => $product->thumb_image,
            'number_views' => $product->number_views,
            'vat' => $product->vat,
        );

        $id = (int)$product->products_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getProduct($id)) {
                $this->tableGateway->update($data, array('products_id' => $id));
            } else {
                throw new \Exception('Product id does not exist');
            }
        }
    }

    public function saveProduct(Product $p, $request = NULL)
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'website_id' => $_SESSION['CMSMEMBER']['website_id'],
                'products_code' => $p->products_code,
                'categories_id' => $p->categories_id,
                'manufacturers_id' => $p->manufacturers_id,
                'users_id' => $p->users_id,
                'users_fullname' => $p->users_fullname,
                'products_title' => $p->products_title,
                'products_alias' => $p->products_alias,
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
                'price' => $p->price >= $p->price_sale ? $p->price : $p->price_sale ,
                'price_sale' => $p->price_sale <= $p->price ? ($p->price_sale ? $p->price_sale : $p->price) : $p->price,
                'ordering' => $p->ordering,
                'quantity' => $p->quantity,
                'thumb_image' => $p->thumb_image,
                'number_views' => $p->number_views,
                'vat' => $p->vat,
                'youtube_video' => trim($p->youtube_video),
            );
            /*
            if($data['youtube_video']){
                $url = $data['youtube_video'];
                if (preg_match('/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $id)) {
                    $values = $id[1];
                } else if (preg_match('/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $id)) {
                    $values = $id[1];
                } else if (preg_match('/youtube\.com\/v\/([^\&\?\/]+)/', $url, $id)) {
                    $values = $id[1];
                } else if (preg_match('/youtu\.be\/([^\&\?\/]+)/', $url, $id)) {
                    $values = $id[1];
                } else {
                    throw new \Exception('Youtube Link not valid');
                }
                $data['youtube_video'] = 'https://www.youtube.com/watch?v='.$values;
            }*/
            $seo_keyword = $this->getBackCategories($p->categories_id);
            $data['seo_keywords'] = $data['products_title'] . ',' . $seo_keyword . ',' . $data['seo_keywords'];

            $id = (int)$p->products_id;
            if ($id == 0) {
                $this->tableGateway->insert($data);
            } else {
                if ($this->getProduct($id)) {
                    $this->tableGateway->update($data, array('products_id' => $id));
                } else {
                    throw new \Exception('Product id does not exist');
                }
            }
            if ($id == 0) {
                $pid = $this->getLastestId();
            } else {
                $pid = $id;
            }
            $data = $request->getPost('featureid');
            $feature_value = $request->getPost('feature_val');
            $this->insertFeature($pid, $data, $feature_value);
            $extension = $request->getPost('ext');
            $this->insertExtension($pid, $extension);
            $extension_require = $request->getPost('ext_require');
            $this->insertExtensionRequire($pid, $extension_require);
//            if ($request->getPost('hinh')) {
            $folder = $request->getPost('folder');
            $images = $request->getPost('hinh');
            $folderProducts = PATH_BASE_ROOT . DS . 'custom' . DS . 'domain_1' . DS . 'products';
            if (!is_dir($folderProducts)) {
                mkdir($folderProducts, 0777);
            }
			$domain=$_SERVER['SERVER_NAME'];
			$folderProductsdomain = PATH_BASE_ROOT . DS . 'custom' . DS . 'domain_1' . DS . 'products'. DS .$domain;
            if (!is_dir($folderProductsdomain)) {
                mkdir($folderProductsdomain, 0777);
            }
            
			$year=date("Y");
			$month=date("m");
			$day=date("d");
			$ten_thu_muc_year = $folderProductsdomain.DS.$year;
			if(!is_dir($ten_thu_muc_year)) {
				@mkdir($ten_thu_muc_year);
			}
			$ten_thu_muc_month=$ten_thu_muc_year."/".$month;
			if(!is_dir($ten_thu_muc_month)) {
				@mkdir($ten_thu_muc_month);
			}
			$ten_thu_muc_day=$ten_thu_muc_month."/".$day;
			if(!is_dir($ten_thu_muc_day)) {
				@mkdir($ten_thu_muc_day);
			}
			
            $folder1 = $ten_thu_muc_day.DS."product{$pid}";
            if (!is_dir($folder1)) {
                mkdir($folder1, 0777);
            }
			$folder1 .= DS.'fullsize';
            if (!is_dir($folder1)) {
                mkdir($folder1, 0777);
            }
			
			$linkfolderimages="/custom/domain_1/products/".$domain.DS.$year."/".$month."/".$day."/product{$pid}".DS."fullsize";
			
            if (isset ($folder) && $folder != '') {
                $list_image = array();
                foreach ($images as $image) {
                    if ($image) {
                        $file = explode('.', $image);
                        $ext = end($file);
                        $newFileName = $request->getPost('products_alias') . '-' . time() . '-' . $this->randText(6) . '.' . $ext;
                        $listImages[$image] = $newFileName;
                        $file = PATH_BASE_ROOT . DS . 'hinhtam' . DS . $folder . DS . $image;
                        if (is_file($file)) {
                            $newfile1 = $folder1 . "/" . $newFileName;
                            copy($file, $newfile1);
                            $list_image[] = "{$linkfolderimages}/{$newFileName}";
                        }
                        @unlink($file);
                    }
                }


                if (count($list_image) > 0) {
                    $this->insertImages($pid, $list_image);
                    unset($list_image);
                }


                $path = PATH_BASE_ROOT . DS . 'hinhtam' . DS . $folder;
                $files = glob($path . DS . '*');
                foreach ($files as $filename) {
                    $file_name = basename($filename);
                    $temp = explode('.', $file_name);
                    $ext = end($temp);
                    $file = $path . DS . $file_name;
                    $newFileName = $request->getPost('products_alias') . '-' . time() . '-' . $this->randText(6) . '.' . $ext;
                    $listImages[$file_name] = $newFileName;
                    if (is_file($file)) {
                        $newfile1 = $folder1 . "/" . $newFileName;
                        copy($file, $newfile1);
                    }
                    @unlink($file);
                }

                @rmdir(PATH_BASE_ROOT . DS . 'hinhtam' . DS . $folder);

            }

            $html = mb_convert_encoding($request->getPost('products_description'), 'HTML-ENTITIES', "UTF-8");
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
                        $src = str_replace('hinhtam/' . $folder . '/' . $fileNameTemp, "{$linkfolderimages}/{$listImages[$fileNameTemp]}", $src);
                        $img->setAttribute('src', $src);
                    }

                }
            }

            $html = $dom->saveXML($dom->getElementsByTagName('body')->item(0));
            $data_product ['products_description'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html);
            $data_product ['products_description'] = htmlentities($data_product ['products_description'], ENT_QUOTES, 'UTF-8');


            $html1 = mb_convert_encoding($request->getPost('products_longdescription'), 'HTML-ENTITIES', "UTF-8");
            @$dom->loadHTML($html1);
            $imgs = $dom->getElementsByTagName('img');
            if (count($imgs) > 0) {
                foreach ($imgs as $img) {
                    $src = $img->getAttribute('src');
                    $fileNameTemp = explode('/', $src);
                    $fileNameTemp = end($fileNameTemp);
                    if (isset($listImages[$fileNameTemp])) {
                        $src = str_replace('hinhtam/' . $folder . '/' . $fileNameTemp, "{$linkfolderimages}/{$listImages[$fileNameTemp]}", $src);
                        $img->setAttribute('src', $src);
                    }

                }
            }

            $html1 = $dom->saveXML($dom->getElementsByTagName('body')->item(0));
            $data_product ['products_longdescription'] = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html1);
            $data_product ['products_longdescription'] = htmlentities($data_product ['products_longdescription'], ENT_QUOTES, 'UTF-8');
//                    $this->_db->update ( 'news', array ('content_vi' => $dataBind ['content_vi']), "id ='{$id}'" );

            $thumb_image = $request->getPost('thumb_image');
            if (isset($listImages[$thumb_image])) {
                //$src = str_replace ( 'hinhtam/'.$folder.'/'.$fileNameTemp, "custom/domain_1/products/fullsize/product{$pid}/{$listImages[$fileNameTemp]}", $src );
                $src = "{$linkfolderimages}/{$listImages[$thumb_image]}";
                $data_product['thumb_image'] = $src;
            }
            $this->updateContent($pid, $data_product);
            $products_recommend = $request->getPost('products_recommend');
            $recommends = array();
            if($products_recommend){
                $recommends = explode(',', $products_recommend);
            }
            $this->addRecommendProduct($pid, $recommends);
//            }
            $adapter->getDriver()->getConnection()->commit();
            return array('success' => TRUE);
        } catch (\Exception $e) {
            $adapter->getDriver()->getConnection()->rollback();
            return array('success' => FALSE, 'msg' => $e->getMessage());

        }
        //$list_image = $request->getPost('list_image');
//                $this->getModelTable('ProductTable')->insertImages($pid, $list_image);

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

    public function insertExtension($id, $data)
    {

        if (count($data) > 0) {
            foreach ($data as $key => $row) {
                $row['price'] = str_replace(',', '', $row['price']);
                $data[$key]['price'] = $row['price'];
                if (trim($row['ext_name']) == '') {
                    $error = 'Tên loại chi phí không được bỏ trống';
                } elseif (trim($row['price']) == '' || !is_numeric($row['price'])) {
                    $error = 'Giá của chi phí phải là số.';
                } elseif (trim($row['ext_description']) == '') {
                    $error = 'Mô tả loại chi phí không được bỏ trống';
                }

            }
        } else {
            return;
        }

        if (isset($error)) {
            throw new \Exception($error);
        }

        $delsql = "DELETE FROM products_extensions WHERE products_id={$id}";
        if ($data) {
            $sql = 'INSERT INTO products_extensions(`ext_id`,`products_id`,`ext_name`, `price`, `ext_description`) VALUES ';
            $val = array();
            foreach ($data as $ext) {
                $val[] = "('{$ext['ext_id']}',{$id}, '{$ext['ext_name']}', {$ext['price']}, '{$ext['ext_description']}')";
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

    public function insertExtensionRequire($id, $data)
    {

        if (count($data) > 0) {
            foreach ($data as $key => $row) {
                $row['price'] = str_replace(',', '', $row['price']);
                $data[$key]['price'] = $row['price'];
                if (trim($row['ext_name']) == '') {
                    $error = 'Tên loại chi phí không được bỏ trống';
                } elseif (trim($row['price']) == '' || !is_numeric($row['price'])) {
                    $error = 'Giá của chi phí phải là số.';
                } elseif (trim($row['ext_description']) == '') {
                    $error = 'Mô tả loại chi phí không được bỏ trống';
                }

            }
        } else {
            return;
        }

        if (isset($error)) {
            throw new \Exception($error);
        }

        $delsql = "DELETE FROM products_extensions WHERE products_id={$id} AND ext_require=1";
        if ($data) {
            $sql = 'INSERT INTO products_extensions(`ext_id`,`products_id`,`ext_name`, `price`, `ext_description`,`ext_require`) VALUES ';
            $val = array();
            foreach ($data as $ext) {
                $val[] = "('{$ext['ext_id']}',{$id}, '{$ext['ext_name']}', {$ext['price']}, '{$ext['ext_description']}',1)";
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
                'website_id' => $_SESSION['CMSMEMBER']['website_id'],
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $rows = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

            if ($rows) {
                foreach ($rows as $row) {
                    $this->parentId = $row['categories_id'];
                    if ($row['parent_id'] > 0) {
                        self::getBackCategories($row['parent_id']);
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
                WHERE products_feature.products_id={$id} AND website_id = {$_SESSION['CMSMEMBER']['website_id']}";
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

    public function getExtensionByProductId($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_extensions')
            ->where(array(
                'products_id' => $id,
                'ext_require' => 0,
            ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getExtensionRequireByProductId($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('products_extensions')
            ->where(array(
                'products_id' => $id,
                'ext_require' => 1
            ));
        try {
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
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
        $select = $sql->select()->columns(array('id' => 'products_id', 'text' => 'products_title','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
        $select->from('products');
        $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
        $select->where(array(
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
        $where[] = "is_delete=0 AND website_id={$_SESSION['CMSMEMBER']['website_id']}";
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
                    WHERE articles.is_delete=0 AND articles.is_published=1 AND website_id = {$_SESSION['CMSMEMBER']['website_id']}";
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
        $code = $str;
        $str = $this->toAlias($str);
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('*','title_extention_always'=> new Expression('(SELECT GROUP_CONCAT(`products_extensions`.`ext_name`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )'),'total_price_extention'=> new Expression('(SELECT SUM(`products_extensions`.`price`) FROM `products_extensions` WHERE `products_extensions`.`products_id` = `products`.`products_id` AND `products_extensions`.`is_always` = 1 )')));
        $select->from('products');
        $select->join('products_type',new Expression('`products_type`.`products_id`=`products`.`products_id` AND `products_type`.`is_default` = 1'),array('products_type_id','type_name','t_price' => 'price', 't_price_sale'=>'price_sale', 't_quantity' => 'quantity', 't_is_available'=>'is_available'), 'left');
        $select->columns(array(
            '*',
            'data' => 'products_id',
            'value' => 'products_title',
        ));
        $select->where(array(
            'is_published' => 1,
            'is_delete' => 0,
            'website_id' => $_SESSION['CMSMEMBER']['website_id'],
        ));
        $select->where("(LCASE(products_code) LIKE '%{$code}%' OR LCASE(products_alias) LIKE '%{$str}%')");
        /*$sql = "SELECT products_id as data, products_title as value, products_id, products_id,products_code, products_title, price, is_available
                FROM products WHERE is_published=1 AND is_delete=0 AND (LCASE(products_code) LIKE '%{$str}%' OR LCASE(products_alias) LIKE '%{$str}%')";*/
        try {
            //$adapter = $this->tableGateway->getAdapter();
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result->toArray();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
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
        $where = "WHERE website_id={$_SESSION['CMSMEMBER']['website_id']}";
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
        $where = "WHERE website_id={$_SESSION['CMSMEMBER']['website_id']}";
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
                                    price,
                                    price_sale,
                                    ordering,
                                    quantity,
                                    thumb_image,
                                    number_views,
                                    vat,
                                    rating,
                                    number_like,
                                    total_sale,
                                    convert_search,
                                    youtube_video
                                    )
                                SELECT
                                    ".$_SESSION['CMSMEMBER']['website_id'].",
                                    '".time()."',
                                    categories_id,
                                    manufacturers_id,
                                    ".$_SESSION['CMSMEMBER']['users_id'].",
                                    '".$_SESSION['CMSMEMBER']['full_name']."',
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
                                    price,
                                    price_sale,
                                    ordering,
                                    quantity,
                                    thumb_image,
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
//                foreach ($images as $image) {
//                    if ($image) {
//                        $file = explode('.', $image);
//                        $ext = end($file);
//                        $newFileName = $request->getPost('products_alias') . '-' . time() . '-' . $this->randText(6) . '.' . $ext;
//                        $listImages[$image] = $newFileName;
//                        $file = PATH_BASE_ROOT . DS . 'hinhtam' . DS . $folder . DS . $image;
//                        if (is_file($file)) {
//                            $newfile1 = $folder1 . "/" . $newFileName;
//                            copy($file, $newfile1);
//                            $list_image[] = "/custom/domain_1/products/fullsize/product{$pid}/{$newFileName}";
//                        }
//                        @unlink($file);
//                    }
//                }

                if (count($list_image) > 0) {
                    $this->insertImages($pid, $list_image);
                    unset($list_image);
                }

//                $path = PATH_BASE_ROOT . DS . 'hinhtam' . DS . $folder;
//                $files = glob($path . DS . '*');
//                foreach ($files as $filename) {
//                    $file_name = basename($filename);
//                    $temp = explode('.', $file_name);
//                    $ext = end($temp);
//                    $file = $path . DS . $file_name;
//                    $newFileName = $request->getPost('products_alias') . '-' . time() . '-' . $this->randText(6) . '.' . $ext;
//                    $listImages[$file_name] = $newFileName;
//                    if (is_file($file)) {
//                        $newfile1 = $folder1 . "/" . $newFileName;
//                        copy($file, $newfile1);
//                    }
//                    @unlink($file);
//                }
//
//                @rmdir(PATH_BASE_ROOT . DS . 'hinhtam' . DS . $folder);

            //}

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
//            $html1 = $product->products_longdescription;
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
//            $products_recommend = $request->getPost('products_recommend');
//            $products_recommend = explode(',', $products_recommend);
//            $this->addRecommendProduct($pid, $products_recommend);

            $adapter->getDriver()->getConnection()->commit();
            return $pid;
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
    }

    public function copyProductNoTransition($product){
        $adapter = $this->tableGateway->getAdapter();
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
                                price,
                                price_sale,
                                ordering,
                                quantity,
                                thumb_image,
                                number_views,
                                vat,
                                rating,
                                number_like,
                                total_sale,
                                convert_search,
                                youtube_video
                                )
                            SELECT
                                ".$_SESSION['CMSMEMBER']['website_id'].",
                                '".time()."',
                                categories_id,
                                manufacturers_id,
                                ".$_SESSION['CMSMEMBER']['users_id'].",
                                '".$_SESSION['CMSMEMBER']['full_name']."',
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
                                price,
                                price_sale,
                                ordering,
                                quantity,
                                thumb_image,
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
//                foreach ($images as $image) {
//                    if ($image) {
//                        $file = explode('.', $image);
//                        $ext = end($file);
//                        $newFileName = $request->getPost('products_alias') . '-' . time() . '-' . $this->randText(6) . '.' . $ext;
//                        $listImages[$image] = $newFileName;
//                        $file = PATH_BASE_ROOT . DS . 'hinhtam' . DS . $folder . DS . $image;
//                        if (is_file($file)) {
//                            $newfile1 = $folder1 . "/" . $newFileName;
//                            copy($file, $newfile1);
//                            $list_image[] = "/custom/domain_1/products/fullsize/product{$pid}/{$newFileName}";
//                        }
//                        @unlink($file);
//                    }
//                }

            if (count($list_image) > 0) {
                $this->insertImages($pid, $list_image);
                unset($list_image);
            }

//                $path = PATH_BASE_ROOT . DS . 'hinhtam' . DS . $folder;
//                $files = glob($path . DS . '*');
//                foreach ($files as $filename) {
//                    $file_name = basename($filename);
//                    $temp = explode('.', $file_name);
//                    $ext = end($temp);
//                    $file = $path . DS . $file_name;
//                    $newFileName = $request->getPost('products_alias') . '-' . time() . '-' . $this->randText(6) . '.' . $ext;
//                    $listImages[$file_name] = $newFileName;
//                    if (is_file($file)) {
//                        $newfile1 = $folder1 . "/" . $newFileName;
//                        copy($file, $newfile1);
//                    }
//                    @unlink($file);
//                }
//
//                @rmdir(PATH_BASE_ROOT . DS . 'hinhtam' . DS . $folder);

        //}

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
//            $html1 = $product->products_longdescription;
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
//            $products_recommend = $request->getPost('products_recommend');
//            $products_recommend = explode(',', $products_recommend);
//            $this->addRecommendProduct($pid, $products_recommend);

        return $pid;
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
			                        WHERE convert_search=0 AND website_id={$_SESSION['CMSMEMBER']['website_id']}) ON DUPLICATE KEY UPDATE products_title=VALUES(products_title),keyword=VALUES(keyword),keyword1=VALUES(keyword1),link=VALUES(link)";
			$adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
			$sql = "UPDATE products SET convert_search=1 WHERE convert_search=0 AND website_id={$_SESSION['CMSMEMBER']['website_id']}";
			$adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
			$adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $e) {
		$adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($e->getMessage());
        }
	}

    public function addCopyProductRecommend($new_pid, $old_pid){

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
} 