<?php
namespace Application\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Filter\File\LowerCase;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\GreaterThan;

use Zend\View\Model\ViewModel;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

use Application\Model\AppTable;
use PHPImageWorkshop\ImageWorkshop;
use PHPImageWorkshop\ImageWorkshopException;
use PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException as ImageWorkshopLayerException;
use Screen\Capture;
use PHPImageCache\ImageCache;

class WebsitesTable extends AppTable {

    public function getWebsiteById($website_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':Websites:getWebsiteById('.$website_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('websites');
            $select->where(array(
                'website_id' => $website_id
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }

    public function getWebsite($domain){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':Websites:getwebsite('.$domain.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('website_id', 'template_id', 'website_name', 'website_domain', 'website_alias', 'website_domain_refer', 'logo', 'facebook_id','google_client_id','ship','type_buy',  'seo_title', 'seo_keywords', 'seo_description',
            'websites_dir', 'websites_folder', 'website_email_admin', 'website_email_customer', 'website_name_business', 'website_phone', 'website_address', 'website_city', 'website_contries', 'website_city_name', 'website_contries_name',
            'website_timezone', 'website_currency', 'website_currency_format', 'website_currency_decimals', 'website_currency_decimalpoint', 'website_currency_separator', 'website_order_code_prefix', 'website_order_code_suffix','website_ga_code', 'javascript','css','url_twister',
            'url_google_plus', 'url_facebook', 'url_pinterest', 'url_houzz','url_instagram', 'url_rss' , 'url_youtube' , 'date_create', 'is_master', 'is_try', 'templete_buy', 'is_local','is_multilanguage','has_login_facebook','has_login_google','has_login_twister','version_cart' ,'type_crop_image' ,'confirm_location' , 'is_published',
            'end_try' => new Expression('IF((websites.is_try = 0 OR (websites.date_create BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW())) , 0, 1)')));
            $select->from('websites');
            //$select->join('users', 'websites.website_id=users.website_id',array('user_name'));
            $select->join('template', 'websites.template_id=template.template_id',array('categories_template_id','template_name', 'template_dir', 'template_folder', 'files_js', 'files_css'));
            $select->where("websites.is_published = 1 AND (websites.website_domain LIKE '{$domain}' OR FIND_IN_SET('{$domain}',websites.website_domain_refer)>0 )");
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }

    public function getWebsiteDemoByAliasCoz($alias){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':Websites:getWebsiteDemoByAliasCoz('.$alias.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('website_id', 'template_id', 'website_name', 'website_domain', 'website_alias', 'website_domain_refer', 'logo', 'facebook_id','google_client_id','ship','type_buy',  'seo_title', 'seo_keywords', 'seo_description',
            'websites_dir', 'websites_folder', 'website_email_admin', 'website_email_customer', 'website_name_business', 'website_phone', 'website_address', 'website_city', 'website_contries', 'website_city_name', 'website_contries_name',
            'website_timezone', 'website_currency', 'website_currency_format', 'website_currency_decimals', 'website_currency_decimalpoint', 'website_currency_separator', 'website_order_code_prefix', 'website_order_code_suffix','website_ga_code', 'javascript','css','url_twister',
            'url_google_plus', 'url_facebook', 'url_pinterest', 'url_houzz','url_instagram', 'url_rss' , 'url_youtube' , 'date_create', 'is_master', 'is_try', 'templete_buy', 'is_local','is_multilanguage','has_login_facebook','has_login_google','has_login_twister','version_cart' ,'type_crop_image' ,'confirm_location' ,  'is_published',
            'end_try' => new Expression('IF((websites.is_try = 0 OR (websites.date_create BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW())) , 0, 1)')));
            $select->from('websites');
            //$select->join('users', 'websites.website_id=users.website_id',array('user_name'));
            $select->join('template', 'websites.template_id=template.template_id',array('categories_template_id','template_name', 'template_dir', 'template_folder', 'files_js', 'files_css'));

            $select->where("websites.is_demo = 1 AND websites.is_published = 1 AND template.template_status = 1 AND (websites.website_alias LIKE '{$alias}') ");

            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }

    public function getInfoPageTemplet($template_id, $page){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':Websites:getinfopagetemplet('.$template_id.';'.$page.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('template_pages_id', 'template_id', 'pages_title', 'pages_type', 'files_js', 'files_css'));
            $select->from('template_pages');
            $select->where(array(
                'template_id' => $template_id,
                'pages_type' => $page
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }

	 public function getWebsiteInfo(){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':Websites');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('websites');
            $select->where(array(
                'website_id' => $_SESSION['website_id']
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $resultSet = new ResultSet();
                $resultSet->initialize($results);
                $results = (is_array($_SESSION['website_id']) ? $resultSet->toArray() : $resultSet->current());
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }
	
    /*public function createWebsite($data){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            


            $adapter->getDriver()->getConnection()->commit();
            return TRUE;
        } catch (\Exception $e) {
            $adapter->getDriver()->getConnection()->rollback();
            return false;

        }
    }*/

    public function createWebsite_($data){
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }

    public function editWebsite($data, $where){
        $this->tableGateway->update($data, $where);
    }

    public function updateWebsite($data, $website){
        $cache->setItem($key, null);
        $this->tableGateway->update($data, array('website_id' => $website->website_id, 'website_domain' => $website->website_domain));
    }

    public function createWebsite($data){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $BusinessTypeId = $data['business_type'];
            $BusinessType = array($data['business_type']);
            $template = $this->getModelTable('TemplatesTable')->getTemplateById($data['template_id']);
            if(!empty($template)){
                $master_website_id = $template->website_id;
                //tao website
                $row = array('template_id'=>$data['template_id'],
                            'pack_id'=>$data['pack_id'],
                            'user_id'=> (isset($_SESSION ['MEMBER'])? $_SESSION ['MEMBER']['users_id'] : 0),
                            'website_name'=>$data['store_name'],
                            'website_domain'=>$data['sub_domain'],
                            'website_alias'=>$data['alias'],
                            'website_email_admin'=>$data['email'],
                            'website_email_customer'=>$data['email'],
                            'website_name_business'=>$data['store_name'],
                            'seo_title'=>$data['store_name'],
                            'seo_keywords'=>$data['store_name'],
                            'seo_description'=>$data['store_name'],
                            'website_phone'=>$data['phone'],
                            'website_address'=>$data['address'],

                            'logo'=>$template->logo, 
                            'facebook_id'=>$template->facebook_id,
                            'ship'=>$template->ship,
                            'type_buy'=>$template->type_buy,  
                            'website_city'=>$template->website_city, 
                            'website_contries'=>$template->website_contries, 
                            'website_city_name'=>$template->website_city_name, 
                            'website_contries_name'=>$template->website_contries_name,
                            'website_timezone'=>$template->website_timezone, 
                            'website_currency'=>$template->website_currency, 
                            'website_currency_format'=>$template->website_currency_format, 
                            'website_currency_decimals'=>$template->website_currency_decimals, 
                            'website_currency_decimalpoint'=>$template->website_currency_decimalpoint, 
                            'website_currency_separator'=>$template->website_currency_separator, 
                            'website_order_code_prefix'=>$template->website_order_code_prefix, 
                            'website_order_code_suffix'=>$template->website_order_code_suffix,
                            'website_ga_code'=>$template->website_ga_code, 
                            'javascript'=>$template->javascript,
                            'css'=>$template->css,
                            'url_twister'=>$template->url_twister,
                            'url_google_plus'=>$template->url_google_plus, 
                            'url_facebook'=>$template->url_facebook, 
                            'url_pinterest'=>$template->url_pinterest, 
                            'url_houzz'=>$template->url_houzz,
                            'url_instagram'=>$template->url_instagram, 
                            'url_rss' =>$template->url_rss, 
                            'url_youtube' =>$template->url_youtube,

                            'date_create'=> date("d-m-Y"),
                            'is_master'=>0,
                            'is_try'=>1,
                            'is_published'=>1);
                $this->tableGateway->insert($row);
                $website_id = $this->getLastestId();

                $Default= PATH_BASE_ROOT . '/templates/Sources/'.$template['template_folder'];
                $name_folder = 'Websites_'.str_replace('.', '_' ,trim($data['store_name'])).'_'.$website_id;
                $name_folder = md5($name_folder);
                $New=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder;

                $row_edit = array('websites_dir'=>'/templates/Websites',
                            'websites_folder'=>$name_folder);
                $this->getModelTable('WebsitesTable')->editWebsite($row_edit, array('website_id' => $website_id));

                /*remove
                $dir='your/directory';
                exec('rm -rf '.escapeshellarg($dir));*/

                //tao user
                $alias = $data['alias_user'];
                $total = $this->getModelTable('UserTable')->getTotalFreAlias($alias);
                $row_user = array(
                    'website_id'         => $website_id,
                    'full_name'         => $data['full_name'],
                    'user_name'  => $data['email'],
                    'password'      => md5($data['password']),
                    'users_alias'      => $alias.'.'.$total['total'],
                    'phone'      => $data['phone'],
                    'address'      => $data['address'],
                    'is_published'      => 1,
                    'is_delete'      => 0,
                    'is_administrator'      => 1,
                    'type'      => 'admin',
                );
                $users_id = $this->getModelTable('UserTable')->createUser($row_user);

                $list_categories = $this->getModelTable('CategoriesTable')->getAllCategoriesOfWebsiteAndSort($master_website_id);
                $categories_new = array();
                foreach ($list_categories as $key => $categories) {
                    foreach ($categories as $key => $categorie) {
                        $cate_id = $categorie['categories_id'];
                        unset($categorie['categories_id']);
                        unset($categorie['children']);
                        $categorie['website_id'] = $website_id;
                        if( isset($categories_new[$categorie['parent_id']]) ){
                            $categorie['parent_id'] = $categories_new[$categorie['parent_id']];
                        }
                        $categories_new[$cate_id] = $this->getModelTable('CategoriesTable')->insertCategory($categorie);
                    }
                }

                $list_article_categories = $this->getModelTable('CategoriesArticlesTable')->getAllCategoriesArticlesOfWebsiteAndSort($master_website_id);
                $article_category_new = array();
                foreach ($list_article_categories as $key => $article_categories) {
                    foreach ($article_categories as $key => $item_article_category) {
                        $categories_articles_id = $item_article_category['categories_articles_id'];
                        unset($item_article_category['categories_articles_id']);
                        $item_article_category['website_id'] = $website_id;
                        if( isset($article_category_new[$item_article_category['parent_id']]) ){
                            $item_article_category['parent_id'] = $article_category_new[$item_article_category['parent_id']];
                        }
                        $article_category_new[$categories_articles_id] = $this->getModelTable('CategoriesArticlesTable')->insertArticleCategory($item_article_category);
                        $item_article_category_languages = array('categories_articles_id' => $article_category_new[$categories_articles_id],
                            'languages_id' => 3,
                            'categories_articles_title' => $item_article_category['categories_articles_title'],
                            'categories_articles_alias' => $item_article_category['categories_articles_alias']);
                        $id_categories_languages = $this->getModelTable('CategoriesArticlesLanguagesTable')->insert($item_article_category_languages);
                    }
                }

                $list_features = $this->getModelTable('FeatureTable')->getAllFeatureOfWebsiteAndSort($master_website_id);
                $features_new = array();
                foreach ($list_features as $key => $features) {
                    foreach ($features as $key => $feature) {
                        $feature_id = $feature['feature_id'];
                        unset($feature['feature_id']);
                        $feature['website_id'] = $website_id;
                        if( isset($features_new[$feature['parent_id']]) ){
                            $feature['parent_id'] = $features_new[$feature['parent_id']];
                        }
                        $features_new[$feature_id] = $this->getModelTable('FeatureTable')->insertFeatures($feature);
                    }
                }

                $list_categories_features = $this->getModelTable('CategoriesTable')->getCategoriesFeaturesOfWebsite($master_website_id);
                foreach ($list_categories_features as $key => $categories_feature) {
                    unset($categories_feature['id']);
                    if( isset($categories_new[$categories_feature['categories_id']]) ){
                        $categories_feature['categories_id'] = $categories_new[$categories_feature['categories_id']];
                    }
                    if( isset($features_new[$categories_feature['feature_id']]) ){
                        $categories_feature['feature_id'] = $features_new[$categories_feature['feature_id']];
                    }
                    $this->getModelTable('CategoriesTable')->insertCategoryFeatures($categories_feature);
                }

                $manufacturers = $this->getModelTable('ManufacturersTable')->getManufacturersOfWebsite($master_website_id);
                $manufacturers_new = array();
                foreach ($manufacturers as $key => $manufacturer) {
                    $manu_id = $manufacturer['manufacturers_id'];
                    unset($manufacturer['manufacturers_id']);
                    $manufacturer['website_id'] = $website_id;
                    $manufacturers_new[$manu_id] = $this->getModelTable('ManufacturersTable')->insertManufacturers($manufacturer);
                }

                $tags = $this->getModelTable('TagsTable')->getTagsOfWebsite($master_website_id);
                $tag_new = array();
                foreach ($tags as $key => $tag) {
                    $tags_id = $tag['tags_id'];
                    unset($tag['tags_id']);
                    $tag['website_id'] = $website_id;
                    $tag_new[$tags_id] = $this->getModelTable('TagsTable')->insertTags($tag);
                }

                $modules = $this->getModelTable('ModulesTable')->getPackModule($data['pack_id']);
                if(!empty($modules)){
                    $this->getModelTable('ModulesTable')->saveModulesWebsite($modules, $website_id);
                }

                $banner_positions = $this->getModelTable('BannerPositionTable')->getBannersPositionOfWebsite($master_website_id);
                $banner_position_new = array();
                if(!empty($banner_positions)){
                    foreach ($banner_positions as $key => $banner_position) {
                        $position_id = $banner_position['position_id'];
                        unset($banner_position['position_id']);
                        $banner_position['website_id'] = $website_id;
                        $banner_position_new[$position_id] = $this->getModelTable('BannerPositionTable')->insertBannersPosition($banner_position);
                    }
                }

                $banner_sizes = $this->getModelTable('BannerSizeTable')->getBannersSizeOfWebsite($master_website_id);
                $banner_size_new = array();
                if(!empty($banner_sizes)){
                    foreach ($banner_sizes as $key => $banner_size) {
                        $size_id = $banner_size['id'];
                        unset($banner_size['id']);
                        $banner_size['website_id'] = $website_id;
                        $banner_size_new[$size_id] = $this->getModelTable('BannerSizeTable')->insertBannersSize($banner_size);
                    }
                }

                $banners = $this->getModelTable('BannersTable')->getBannersOfWebsite($master_website_id);
                $banners_new = array();
                if(!empty($banners)){
                    foreach ($banners as $key => $banner) {
                        $banners_id = $banner['banners_id'];
                        $position_id = $banner['position_id'];
                        $size_id = $banner['size_id'];
                        unset($banner['banners_id']);
                        $banner['website_id'] = $website_id;
                        if(isset($banner_position_new[$position_id])){
                            $banner['position_id'] = $banner_position_new[$position_id];
                        }
                        if(isset($banner_size_new[$size_id])){
                            $banner['size_id'] = $banner_size_new[$size_id];
                        }
                        $banners_new[$banners_id] = $this->getModelTable('BannersTable')->insertBanners($banner);
                    }
                }

                $payments = $this->getModelTable('PaymentTable')->getPaymentsOfWebsite($master_website_id);
                $payments_new = array();
                if(!empty($payments)){
                    foreach ($payments as $key => $payment) {
                        $payment_id = $payment['payment_id'];
                        unset($payment['payment_id']);
                        $payment['website_id'] = $website_id;
                        $payments_new[$payment_id] = $this->getModelTable('PaymentTable')->insertPayments($payment);
                    }
                }

                /*$transportations = $this->getModelTable('TransportationTable')->getTransportationOfWebsite($master_website_id);
                $transportations_new = array();
                if(!empty($transportations)){
                    foreach ($transportations as $key => $transportation) {
                        $transportation_id = $transportation['transportation_id'];
                        unset($transportation['transportation_id']);
                        $transportation['website_id'] = $website_id;
                        $transportations_new[$transportation_id] = $this->getModelTable('TransportationTable')->insertTransportation($transportation);
                    }
                }*/
                
                $products = $this->getModelTable('ProductsTable')->getProductOfWebsite($master_website_id);
                $products_new = array();
                foreach ($products as $key => $product) {
                    $products_id = $product['products_id'];
                    $cate_id = $product['categories_id'];
                    $manu_id = $product['manufacturers_id'];
                    unset($product['products_id']);
                    $product['categories_id'] = 0;
                    $product['manufacturers_id'] = 0;
                    $product['website_id'] = $website_id;
                    $product['users_id'] = $users_id;
                    $product['users_fullname'] = $row_user['full_name'];
                    if( isset($categories_new[$cate_id]) ){
                        $product['categories_id'] = $categories_new[$cate_id];
                    }
                    if( isset($manufacturers_new[$manu_id]) ){
                        $product['manufacturers_id'] = $manufacturers_new[$manu_id];
                    }

                    if( !empty($product['tags']) ){
                        $p_tags = $product['tags'];
                        $p_tags = explode(',', $p_tags);
                        $r_tags = array();
                        foreach ($p_tags as $key => $tag_id) {
                            if ( isset($tag_new[$tag_id]) ){
                                $r_tags[] = $tag_new[$tag_id];
                            }
                        }
                        $product['tags'] = implode(',', $r_tags);
                    }

                    $products_new[$products_id] = $this->getModelTable('ProductsTable')->insertProduct($product);
                }

                $product_types = $this->getModelTable('ProductsTable')->getProductsTypesOfWebsite($master_website_id);
                $products_type_new = array();
                foreach ($product_types as $key => $product_type) {
                    $products_type_id = $product_type['products_type_id'];
                    unset($product_type['products_type_id']);
                    if( isset($products_new[$product_type['products_id']]) ){
                        $product_type['products_id'] = $products_new[$product_type['products_id']];
                    }
                    $products_type_new[$products_type_id] = $this->getModelTable('ProductsTable')->insertProductsTypes($product_type);
                }

                $list_products_extensions = $this->getModelTable('ProductsTable')->getProductsExtensionsOfWebsite($master_website_id);
                foreach ($list_products_extensions as $key => $products_extension) {
                    unset($products_extension['id']);
                    if( isset($products_new[$products_extension['products_id']]) ){
                        $products_extension['products_id'] = $products_new[$products_extension['products_id']];
                    }
                    $this->getModelTable('ProductsTable')->insertProductsExtensions($products_extension);
                }

                $list_products_features = $this->getModelTable('ProductsTable')->getProductsFeaturesOfWebsite($master_website_id);
                foreach ($list_products_features as $key => $products_feature) {
                    unset($products_feature['id']);
                    if( isset($products_new[$products_feature['products_id']]) ){
                        $products_feature['products_id'] = $products_new[$products_feature['products_id']];
                    }
                    if( isset($features_new[$products_feature['feature_id']]) ){
                        $products_feature['feature_id'] = $features_new[$products_feature['feature_id']];
                    }
                    $this->getModelTable('ProductsTable')->insertProductsFeatures($products_feature);
                }

                $list_products_images = $this->getModelTable('ProductsTable')->getProductsImagesOfWebsite($master_website_id);
                foreach ($list_products_images as $key => $products_image) {
                    unset($products_image['id']);
                    if( isset($products_new[$products_image['products_id']]) ){
                        $products_image['products_id'] = $products_new[$products_image['products_id']];
                    }
                    $this->getModelTable('ProductsTable')->insertProductsImages($products_image);
                }

                $articles = $this->getModelTable('ArticlesTable')->getArticleOfWebsite($master_website_id);
                $article_new = array();

                foreach ($articles as $key => $item_article) {
                    $articles_id = $item_article['articles_id'];
                    $categories_articles_id = $item_article['categories_articles_id'];
                    unset($item_article['articles_id']);
                    $item_article['website_id'] = $website_id;
                    $item_article['users_id'] = $users_id;
                    if( isset($article_category_new[$categories_articles_id]) ){
                        $item_article['categories_articles_id'] = $article_category_new[$categories_articles_id];
                    }else{
                        $item_article['categories_articles_id'] = 0;
                    }

                    if( !empty($item_article['tags']) ){
                        $p_tags = $item_article['tags'];
                        $p_tags = explode(',', $p_tags);
                        $r_tags = array();
                        foreach ($p_tags as $key => $tag_id) {
                            if ( isset($tag_new[$tag_id]) ){
                                $r_tags[] = $tag_new[$tag_id];
                            }
                        }
                        $item_article['tags'] = implode(',', $r_tags);
                    }

                    $article_new[$articles_id] = $this->getModelTable('ArticlesTable')->insertArticle($item_article);
                    $item_article_languages = array('languages_id' => 3,
                         'articles_id' => $article_new[$articles_id],
                         'articles_title' => (!empty($item_article['articles_title']) ? $item_article['articles_title'] : '' ),
                         'articles_alias' => (!empty($item_article['articles_alias']) ? $item_article['articles_alias'] : '' ),
                         'articles_sub_content' => (!empty($item_article['articles_sub_content']) ? $item_article['articles_sub_content'] : '' ),
                         'articles_content' => (!empty($item_article['articles_content']) ? $item_article['articles_content'] : '' ));
                    $articles_languages_id = $this->getModelTable('ArticlesLanguagesTable')->insert($item_article_languages);
                }

                $list_articles_images = $this->getModelTable('ArticlesTable')->getArticlesImagesOfWebsite($master_website_id);
                foreach ($list_articles_images as $key => $articles_image) {
                    unset($articles_image['id']);
                    if( isset($article_new[$articles_image['articles_id']]) ){
                        $articles_image['articles_id'] = $article_new[$articles_image['articles_id']];
                    }
                    $this->getModelTable('ArticlesTable')->insertArticlesImages($articles_image);
                }

                $menus = $this->getModelTable('MenusTable')->getMenusOfWebsite($master_website_id);
                $menu_new = array();
                foreach ($menus as $key => $menu) {
                    $menu_id = $menu['menus_id'];
                    unset($menu['menus_id']);
                    $menu['website_id'] = $website_id;
                    if($menu['parent_id']!=0){
                        if(isset($menu_new[$menu['parent_id']])){
                            $menu['parent_id'] = $menu_new[$menu['parent_id']];
                        }
                        if($menu['menus_type'] == 'collection'){
                            //danh muc san pham
                            if(isset($categories_new[$menu['menus_reference_id']])){
                                $menu['menus_reference_id'] = $categories_new[$menu['menus_reference_id']];
                            }
                        }else if($menu['menus_type'] == 'product'){
                            //san pham
                            if(isset($products_new[$menu['menus_reference_id']])){
                                $menu['menus_reference_id'] = $products_new[$menu['menus_reference_id']];
                            }
                        }else if($menu['menus_type'] == 'page' || $menu['menus_type'] == 'article'){
                            //bai viet
                            if(isset($article_new[$menu['menus_reference_id']])){
                                $menu['menus_reference_id'] = $article_new[$menu['menus_reference_id']];
                            }
                        }else if($menu['menus_type'] == 'blog'){
                            //danh muc bai viet
                            if( isset($article_category_new[$menu['menus_reference_id']]) ){
                                $menu['menus_reference_id'] = $article_new[$menu['menus_reference_id']];
                            }
                        }
                    }
                    $menu_new[$menu_id] = $this->getModelTable('MenusTable')->insertMenus($menu);
                }

                $invoices = $this->getModelTable('InvoiceTable')->getAllInvoiceOfWebsite($master_website_id);
                $invoices_new = array();
                foreach ($invoices as $key => $invoice) {
                    $invoice_id = $invoice['invoice_id'];
                    unset($invoice['invoice_id']);
                    $invoice['website_id'] = $website_id;
                    $invoices_new[$invoice_id] = $this->getModelTable('InvoiceTable')->insertInvoices($invoice);
                }

                $list_products_invoices = $this->getModelTable('InvoiceTable')->getInvoiceProductsOfWebsite($master_website_id);
                foreach ($list_products_invoices as $key => $products_invoice) {
                    unset($products_invoice['id']);
                    if( isset($products_new[$products_invoice['products_id']]) ){
                        $products_invoice['products_id'] = $products_new[$products_invoice['products_id']];
                    }
                    if( isset($products_type_new[$products_invoice['products_type_id']]) ){
                        $products_invoice['products_type_id'] = $products_type_new[$products_invoice['products_type_id']];
                    }
                    if( isset($invoices_new[$products_invoice['invoice_id']]) ){
                        $products_invoice['invoice_id'] = $invoices_new[$products_invoice['invoice_id']];
                    }
                    $this->getModelTable('InvoiceTable')->insertInvoiceProducts($products_invoice);
                }

                $list_products_extension_invoices = $this->getModelTable('InvoiceTable')->getInvoiceProductsExtensionOfWebsite($master_website_id);
                foreach ($list_products_extension_invoices as $key => $products_extension_invoice) {
                    unset($products_extension_invoice['id']);
                    if( isset($products_new[$products_extension_invoice['products_id']]) ){
                        $products_extension_invoice['products_id'] = $products_new[$products_extension_invoice['products_id']];
                    }
                    if( isset($products_type_new[$products_extension_invoice['products_type_id']]) ){
                        $products_extension_invoice['products_type_id'] = $products_type_new[$products_extension_invoice['products_type_id']];
                    }
                    if( isset($invoices_new[$products_extension_invoice['invoice_id']]) ){
                        $products_extension_invoice['invoice_id'] = $invoices_new[$products_extension_invoice['invoice_id']];
                    }
                    $this->getModelTable('InvoiceTable')->insertInvoiceProductsExtension($products_extension_invoice);
                }

                $list_invoices_shipping = $this->getModelTable('InvoiceTable')->getInvoiceShippingOfWebsite($master_website_id);
                foreach ($list_invoices_shipping as $key => $invoices_shipping) {
                    unset($invoices_shipping['invoice_shipping_id']);
                    /*if( isset($products_new[$invoices_shipping['products_id']]) ){
                        $invoices_shipping['products_id'] = $products_new[$invoices_shipping['products_id']];
                    }*/
                    if( isset($invoices_new[$invoices_shipping['invoice_id']]) ){
                        $invoices_shipping['invoice_id'] = $invoices_new[$invoices_shipping['invoice_id']];
                    }
                    $invoices_shipping['website_id'] = $website_id;
                    $this->getModelTable('InvoiceTable')->insertInvoiceShipping($invoices_shipping);
                }

                $list_coupons = $this->getModelTable('CouponsTable')->getCouponsOfWebsite($master_website_id);
                $coupons_new = array();
                foreach ($list_coupons as $key => $coupon) {
                    $coupons_id = $coupon['coupons_id'];
                    unset($coupon['coupons_id']);
                    $coupon['website_id'] = $website_id;
                    $coupons_new[$coupons_id] = $this->getModelTable('CouponsTable')->insertCoupons($coupon);
                }

                $list_invoices_coupons = $this->getModelTable('InvoiceTable')->getInvoiceCouponsOfWebsite($master_website_id);

                foreach ($list_invoices_coupons as $key => $invoices_coupon) {
                    unset($invoices_coupon['id']);
                    if( isset($invoices_new[$invoices_coupon['invoice_id']]) ){
                        $invoices_coupon['invoice_id'] = $invoices_new[$invoices_coupon['invoice_id']];
                    }
                    if( isset($coupons_new[$invoices_coupon['coupon_id']]) ){
                        $invoices_coupon['coupon_id'] = $coupons_new[$invoices_coupon['coupon_id']];
                    }
                    $this->getModelTable('InvoiceTable')->insertInvoiceCoupons($invoices_coupon);
                }

                $branches = $this->getModelTable('BranchesTable')->getBranchesOfWebsite($master_website_id);
                $branches_new = array();
                if(!empty($branches)){
                    foreach ($branches as $key => $branche) {
                        $branches_id = $branche['branches_id'];
                        unset($branche['branches_id']);
                        $branche['website_id'] = $website_id;
                        $branches_new[$branches_id] = $this->getModelTable('BranchesTable')->insertBranches($branche);
                    }
                }

                /*$group_regions = $this->getModelTable('CitiesTable')->getGroupRegionsOfWebsite($master_website_id);
                $group_regions_new = array();
                if(!empty($group_regions)){
                    foreach ($group_regions as $key => $group_region) {
                        $group_regions_id = $group_region['group_regions_id'];
                        unset($group_region['group_regions_id']);
                        $group_region['website_id'] = $website_id;
                        $group_regions_new[$group_regions_id] = $this->getModelTable('CitiesTable')->insertGroupRegions($group_region);
                    }
                }

                $list_shippings = $this->getModelTable('CitiesTable')->getShippingOfWebsite($master_website_id);
                foreach ($list_shippings as $key => $shipping) {
                    unset($shipping['shipping_id']);
                    if( isset($transportations_new[$shipping['transportation_id']]) ){
                        $shipping['transportation_id'] = $transportations_new[$shipping['transportation_id']];
                    }
                    if( isset($group_regions_new[$shipping['group_regions_id']]) ){
                        $shipping['group_regions_id'] = $group_regions_new[$shipping['group_regions_id']];
                    }
                    $shipping['website_id'] = $website_id;
                    $this->getModelTable('CitiesTable')->insertShipping($shipping);
                }*/

                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                $viewModel->setTemplate("application/websites/email_template_create_websites");
                $viewModel->setVariables($data);

                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);
                $html = new MimePart($html);
                $html->type = "text/html";

                $body = new MimeMessage();
                $body->setParts(array($html));

                $message = new Message();
                $message->addTo($data['email'])
                    ->addFrom(EMAIL_ADMIN_SEND, $data['store_name'])
                    ->setSubject('[COZ.VN] Khởi tạo website '.$data['store_name'].".coz.vn".' thành công !')
                    ->setBody($body)
                    ->setEncoding("UTF-8");

                // Setup SMTP transport using LOGIN authentication
                $transport = new SmtpTransport();
                $options = new SmtpOptions(array(
                    'name' => HOST_MAIL,
                    'host' => HOST_MAIL,
                    'port' => 25,
                    'connection_class' => 'login',
                    'connection_config' => array(
                        'username' => USERNAME_HOST_MAIL,
                        'password' => PASSWORD_HOST_MAIL,
                    ),
                ));

                $transport->setOptions($options);
                try {
                    $transport->send($message);
                } catch(\Zend\Mail\Exception $e) {
                    //echo json_encode(array('flag'=>FALSE, 'msg'=> $e->getMessage()));
                    return false;
                }catch(\Exception $ex) {
                    //echo json_encode(array('flag'=>FALSE, 'msg'=> $ex->getMessage()));
                    return false;
                }

                $adapter->getDriver()->getConnection()->commit();

                //copy source
                exec("cp -r $Default $New");

                return TRUE;
            }else{
                return FALSE;
            }
        } catch (\Exception $e) {
            $adapter->getDriver()->getConnection()->rollback();
            //echo $e->getMessage();
            //die('asa');
            return FALSE;

        }
    }

    public function createWebsite_bk($data){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $master_website_id = 1;
            $BusinessTypeId = $data['business_type'];
            $BusinessType = array($data['business_type']);
            $template = $this->getModelTable('TemplatesTable')->getTemplateById($data['template_id']);
            if(!empty($template)){
                //tao website
                $row = array('template_id'=>$data['template_id'],
                            'pack_id'=>$data['pack_id'],
                            'user_id'=> (isset($_SESSION ['MEMBER'])? $_SESSION ['MEMBER']['users_id'] : 0),
                            'website_name'=>$data['store_name'],
                            'website_domain'=>$data['sub_domain'],
                            'website_alias'=>$data['alias'],
                            'website_email_admin'=>$data['email'],
                            'website_email_customer'=>$data['email'],
                            'website_name_business'=>$data['store_name'],
                            'seo_title'=>$data['store_name'],
                            'seo_keywords'=>$data['store_name'],
                            'seo_description'=>$data['store_name'],
                            'website_phone'=>$data['phone'],
                            'website_address'=>$data['address'],
                            'website_currency_decimals'=>(!empty($data['website_currency_decimals']) ? $data['website_currency_decimals'] : 2),
                            'website_currency_decimalpoint'=>(!empty($data['website_currency_decimalpoint']) ? $data['website_currency_decimalpoint'] : '.'),
                            'website_currency_separator'=>(!empty($data['website_currency_separator']) ? $data['website_currency_separator'] : ','),
                            'website_order_code_prefix'=>(!empty($data['website_order_code_prefix']) ? $data['website_order_code_prefix'] : 'HD'),
                            'date_create'=> date("d-m-Y"),
                            'is_master'=>0,
                            'is_try'=>1,
                            'is_published'=>1);
                $this->tableGateway->insert($row);
                $website_id = $this->getLastestId();

                $Default= PATH_BASE_ROOT . '/templates/Sources/'.$template['template_folder'];
                $name_folder = 'Websites_'.str_replace('.', '_' ,trim($data['store_name'])).'_'.$website_id;
                $name_folder = md5($name_folder);
                $New=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder;

                $row_edit = array('websites_dir'=>'/templates/Websites',
                            'websites_folder'=>$name_folder,'logo'=>'/templates/Websites/'.$name_folder.'/images/logos/logo.png');
                $this->getModelTable('WebsitesTable')->editWebsite($row_edit, array('website_id' => $website_id));

                /*remove
                $dir='your/directory';
                exec('rm -rf '.escapeshellarg($dir));*/

                //tao user
                $alias = $data['alias_user'];
                $total = $this->getModelTable('UserTable')->getTotalFreAlias($alias);
                $row_user = array(
                    'website_id'         => $website_id,
                    'full_name'         => $data['full_name'],
                    'user_name'  => $data['email'],
                    'password'      => md5($data['password']),
                    'users_alias'      => $alias.'.'.$total['total'],
                    'phone'      => $data['phone'],
                    'address'      => $data['address'],
                    'is_published'      => 1,
                    'is_delete'      => 0,
                    'is_administrator'      => 1,
                    'type'      => 'admin',
                );
                $users_id = $this->getModelTable('UserTable')->createUser($row_user);

                $categories = $this->getModelTable('CategoriesTable')->getCategoryOfWebsite($master_website_id);
                $list_category = array();
                if($BusinessTypeId == -1){
                    foreach ($categories as $key => $categorie ) {
                        if($categorie['parent_id'] == 0){
                            $list_category[] = $categorie;
                            $list_category_chlid = $this->getModelTable('CategoriesTable')->getChilds($categories, $categorie['categories_id']);
                            $list_category = array_merge($list_category, $list_category_chlid);
                            break;
                        }
                    }
                }else{
                    foreach ($categories as $key => $categorie ) {
                        if($categorie['categories_id'] == $BusinessTypeId ){
                            $list_category[] = $categorie;
                            $list_category_chlid = $this->getModelTable('CategoriesTable')->getChilds($categories, $categorie['categories_id']);
                            $list_category = array_merge($list_category, $list_category_chlid);
                        }
                    }
                }

                $categories_new = array();
                $list_id_category_insert = array();
                foreach ($list_category as $key => $categorie) {
                    $cate_id = $categorie['categories_id'];
                    unset($categorie['categories_id']);
                    unset($categorie['children']);
                    $categorie['website_id'] = $website_id;
                    if(isset($categories_new[$categorie['parent_id']])){
                        $categorie['parent_id'] = $categories_new[$categorie['parent_id']];
                    }
                    $list_id_category_insert[] = $cate_id;
                    $categories_new[$cate_id] = $this->getModelTable('CategoriesTable')->insertCategory($categorie);
                }
                
                $manufacturers = $this->getModelTable('ManufacturersTable')->getManufacturersOfWebsite($master_website_id);
                $manufacturers_new = array();
                foreach ($manufacturers as $key => $manufacturer) {
                    $manu_id = $manufacturer['manufacturers_id'];
                    unset($manufacturer['manufacturers_id']);
                    $manufacturer['website_id'] = $website_id;
                    $manufacturers_new[$manu_id] = $this->getModelTable('ManufacturersTable')->insertManufacturers($manufacturer);
                }

                $products = $this->getModelTable('ProductsTable')->getProductInCategoriesOfWebsite($master_website_id, $list_id_category_insert);
                $products_new = array();
                foreach ($products as $key => $product) {
                    $products_id = $product['products_id'];
                    $cate_id = $product['categories_id'];
                    $manu_id = $product['manufacturers_id'];
                    unset($product['categories_id']);
                    unset($product['manufacturers_id']);
                    $product['website_id'] = $website_id;
                    $product['users_id'] = $users_id;
                    $product['users_fullname'] = $row_user['full_name'];
                    if(isset($categories_new[$cate_id])){
                        $product['categories_id'] = $categories_new[$cate_id];
                    }
                    if(isset($manufacturers_new[$manu_id])){
                        $product['manufacturers_id'] = $manufacturers_new[$manu_id];
                    }
                    $p_copy = new Products();
                    $p_copy->exchangeArray($product);
                    $products_new[$products_id] = $this->getModelTable('ProductsTable')->copyProduct($p_copy);
                }

                $menus = $this->getModelTable('MenusTable')->getMenusOfWebsite($master_website_id);
                $menu_new = array();
                $article_new = array();
                $article_category_new = array();
                foreach ($menus as $key => $menu) {
                    $menu_id = $menu['menus_id'];
                    unset($menu['menus_id']);
                    $menu['website_id'] = $website_id;
                    if($menu['parent_id']!=0){
                        if(isset($menu_new[$menu['parent_id']])){
                            $menu['parent_id'] = $menu_new[$menu['parent_id']];
                        }
                        if($menu['menus_type'] == 'collection'){
                            //danh muc san pham
                            if(isset($categories_new[$menu['menus_reference_id']])){
                                $menu['menus_reference_id'] = $categories_new[$menu['menus_reference_id']];
                            }
                        }else if($menu['menus_type'] == 'product'){
                            //san pham
                            if(isset($products_new[$menu['menus_reference_id']])){
                                $menu['menus_reference_id'] = $products_new[$menu['menus_reference_id']];
                            }
                        }else if($menu['menus_type'] == 'page' || $menu['menus_type'] == 'article'){
                            //bai viet
                            $article = $this->getModelTable('ArticlesTable')->getArticleOfWebsite($menu['menus_reference_id'], $master_website_id);
                            if(!empty($article)){
                                if(!isset($article_category_new[$article['categories_articles_id']]) && $article['categories_articles_id'] !=0 ){
                                    $article_category = $this->getModelTable('CategoriesArticlesTable')->getArticleCategoryOfWebsite($article['categories_articles_id'], $master_website_id);
                                    if(!empty($article_category)){
                                        $list_article_category = array();
                                        if(!is_array($article_category)){
                                            $list_article_category[] = (array)$article_category;
                                        }else{
                                            $list_article_category = $article_category;
                                        }
                                        foreach ($list_article_category as $key => $item_article_category) {
                                            $categories_articles_id = $article_category['categories_articles_id'];
                                            unset($item_article_category['categories_articles_id']);
                                            $item_article_category['website_id'] = $website_id;
                                            $article_category_new[$categories_articles_id] = $this->getModelTable('CategoriesArticlesTable')->insertArticleCategory($item_article_category);
                                            $item_article_category_languages = array('categories_articles_id' => $article_category_new[$categories_articles_id],
                                                'languages_id' => 3,
                                                'categories_articles_title' => $item_article_category['categories_articles_title'],
                                                'categories_articles_alias' => $item_article_category['categories_articles_alias']);
                                            $id_categories_languages = $this->getModelTable('CategoriesArticlesLanguagesTable')->insert($item_article_category_languages);
                                        }
                                    }
                                }
                                $list_articles = array();
                                if(!is_array($article)){
                                    $list_articles[] = (array)$article;
                                }else{
                                    $list_articles = $article;
                                }
                                foreach ($list_articles as $key => $item_article) {
                                    $articles_id = $item_article['articles_id'];
                                    $categories_articles_id = $item_article['categories_articles_id'];
                                    unset($item_article['articles_id']);
                                    $item_article['website_id'] = $website_id;
                                    $item_article['users_id'] = $users_id;
                                    if(isset($article_category_new[$categories_articles_id])){
                                        $item_article['categories_articles_id'] = $article_category_new[$categories_articles_id];
                                    }else{
                                        $item_article['categories_articles_id'] = 0;
                                    }
                                    $article_new[$articles_id] = $this->getModelTable('ArticlesTable')->insertArticle($item_article);
                                    $item_article_languages = array('languages_id' => 3,
                                         'articles_id' => $article_new[$articles_id],
                                         'articles_title' => (!empty($item_article['articles_title']) ? $item_article['articles_title'] : '' ),
                                         'articles_alias' => (!empty($item_article['articles_alias']) ? $item_article['articles_alias'] : '' ),
                                         'articles_sub_content' => (!empty($item_article['articles_sub_content']) ? $item_article['articles_sub_content'] : '' ),
                                         'articles_content' => (!empty($item_article['articles_content']) ? $item_article['articles_content'] : '' ));
                                    $articles_languages_id = $this->getModelTable('ArticlesLanguagesTable')->insert($item_article_languages);
                                    $menu['menus_reference_id'] = $article_new[$articles_id];
                                }
                            }
                        }else if($menu['menus_type'] == 'blog'){
                            //danh muc bai viet
                            if(!isset($article_category_new[$menu['menus_reference_id']]) && $menu['menus_reference_id'] !=0 ){
                                $article_category = $this->getModelTable('CategoriesArticlesTable')->getArticleCategoryOfWebsite($menu['menus_reference_id'], $master_website_id);
                                if(!empty($article_category)){
                                    $list_article_category = array();
                                    if(!is_array($article_category)){
                                        $list_article_category[] = (array)$article_category;
                                    }else{
                                        $list_article_category = $article_category;
                                    }
                                    foreach ($list_article_category as $key => $item_article_category) {
                                        $categories_articles_id = $item_article_category['categories_articles_id'];
                                        unset($item_article_category['categories_articles_id']);
                                        $item_article_category['website_id'] = $website_id;
                                        $article_category_new[$categories_articles_id] = $this->getModelTable('CategoriesArticlesTable')->insertArticleCategory($item_article_category);
                                        $item_article_category_languages = array('categories_articles_id' => $article_category_new[$categories_articles_id],
                                                'languages_id' => 3,
                                                'categories_articles_title' => $item_article_category['categories_articles_title'],
                                                'categories_articles_alias' => $item_article_category['categories_articles_alias']);
                                        $id_categories_languages = $this->getModelTable('CategoriesArticlesLanguagesTable')->insert($item_article_category_languages);
                                        $menu['menus_reference_id'] = $article_category_new[$categories_articles_id];
                                    }
                                }
                            }
                        }
                    }
                    $menu_new[$menu_id] = $this->getModelTable('MenusTable')->insertMenus($menu);
                }

                $modules = $this->getModelTable('ModulesTable')->getPackModule($data['pack_id']);
                if(!empty($modules)){
                    $this->getModelTable('ModulesTable')->saveModulesWebsite($modules, $website_id);
                }

                $banner_positions = $this->getModelTable('BannerPositionTable')->getBannersPositionOfWebsite($master_website_id);
                $banner_position_new = array();
                if(!empty($banner_positions)){
                    foreach ($banner_positions as $key => $banner_position) {
                        $position_id = $banner_position['position_id'];
                        unset($banner_position['position_id']);
                        $banner_position['website_id'] = $website_id;
                        $banner_position_new[$position_id] = $this->getModelTable('BannerPositionTable')->insertBannersPosition($banner_position);
                    }
                }

                $banner_sizes = $this->getModelTable('BannerSizeTable')->getBannersSizeOfWebsite($master_website_id);
                $banner_size_new = array();
                if(!empty($banner_sizes)){
                    foreach ($banner_sizes as $key => $banner_size) {
                        $size_id = $banner_size['id'];
                        unset($banner_size['id']);
                        $banner_size['website_id'] = $website_id;
                        $banner_size_new[$size_id] = $this->getModelTable('BannerSizeTable')->insertBannersSize($banner_size);
                    }
                }

                $banners = $this->getModelTable('BannersTable')->getBannersOfWebsite($master_website_id);
                $banners_new = array();
                if(!empty($banners)){
                    foreach ($banners as $key => $banner) {
                        $banners_id = $banner['banners_id'];
                        $position_id = $banner['position_id'];
                        $size_id = $banner['size_id'];
                        unset($banner['banners_id']);
                        $banner['website_id'] = $website_id;
                        if(isset($banner_position_new[$position_id])){
                            $banner['position_id'] = $banner_position_new[$position_id];
                        }
                        if(isset($banner_size_new[$size_id])){
                            $banner['size_id'] = $banner_size_new[$size_id];
                        }
                        $banners_new[$banners_id] = $this->getModelTable('BannersTable')->insertBanners($banner);
                    }
                }

                $payments = $this->getModelTable('PaymentTable')->getPaymentsOfWebsite($master_website_id);
                $payments_new = array();
                if(!empty($payments)){
                    foreach ($payments as $key => $payment) {
                        $payment_id = $payment['payment_id'];
                        unset($payment['payment_id']);
                        $payment['website_id'] = $website_id;
                        $payments_new[$payment_id] = $this->getModelTable('PaymentTable')->insertPayments($payment);
                    }
                }

                /*$transportations = $this->getModelTable('TransportationTable')->getTransportationOfWebsite($master_website_id);
                $transportations_new = array();
                if(!empty($transportations)){
                    foreach ($transportations as $key => $transportation) {
                        $transportation_id = $transportation['transportation_id'];
                        unset($transportation['transportation_id']);
                        $transportation['website_id'] = $website_id;
                        $transportations_new[$transportation_id] = $this->getModelTable('TransportationTable')->insertTransportation($transportation);
                    }
                }*/



                $viewModel = new ViewModel();
                $viewModel->setTerminal(true);
                $viewModel->setTemplate("application/websites/email_template_create_websites");
                $viewModel->setVariables($data);

                $viewRender = $this->getServiceLocator()->get('ViewRenderer');
                $html = $viewRender->render($viewModel);
                $html = new MimePart($html);
                $html->type = "text/html";

                $body = new MimeMessage();
                $body->setParts(array($html));

                $message = new Message();
                $message->addTo($data['email'])
                    ->addFrom(EMAIL_ADMIN_SEND, $data['store_name'])
                    ->setSubject('[COZ.VN] Khởi tạo website '.$data['store_name'].".coz.vn".' thành công !')
                    ->setBody($body)
                    ->setEncoding("UTF-8");

                // Setup SMTP transport using LOGIN authentication
                $transport = new SmtpTransport();
                $options = new SmtpOptions(array(
                    'name' => HOST_MAIL,
                    'host' => HOST_MAIL,
                    'port' => 25,
                    'connection_class' => 'login',
                    'connection_config' => array(
                        'username' => USERNAME_HOST_MAIL,
                        'password' => PASSWORD_HOST_MAIL,
                    ),
                ));

                $transport->setOptions($options);
                try {
                    $transport->send($message);
                } catch(\Zend\Mail\Exception $e) {
                    //echo json_encode(array('flag'=>FALSE, 'msg'=> $e->getMessage()));
                    return false;
                }catch(\Exception $ex) {
                    //echo json_encode(array('flag'=>FALSE, 'msg'=> $ex->getMessage()));
                    return false;
                }


                $adapter->getDriver()->getConnection()->commit();

                //copy source
                exec("cp -r $Default $New");

                return TRUE;
            }else{
                return false;
            }
        } catch (\Exception $e) {
            //echo $e->getMessage();
            //die('asa');
            $adapter->getDriver()->getConnection()->rollback();
            return false;

        }
    }

    public function file_name($file_name) {
        $list = explode ( '.', $file_name );
        $dot = count ( $list );
        unset($list [$dot - 1]);
        $file_name = implode('.', $list);
        $list = explode ( '/', $file_name );
        $file_ = end($list);
        return $file_;
    }

    public function file_extension($file_name) {
        $list = explode ( '.', $file_name );
        $file_ext = end($list);
        return $file_ext;
    }

    public function fixUrlImageForWebsite($from){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $master_website_id = $from;

            $ws = $this->getWebsiteById($master_website_id);
            $website_id = $ws->website_id;
            $domain= md5($ws->website_domain.':'.$website_id);
            $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
            if(!is_dir($websiteFolder)){
                @mkdir ( $websiteFolder, 0777 );
            }
            $products = $this->getModelTable('ProductsTable')->getProductOfWebsite($master_website_id);//echo count($products);die();
            //print_r($products);die();
            foreach ($products as $key => $product) {
                //if( $product['products_id'] == 1466620034 ){
                    echo $key.'</br >';
                    echo '> '.$product['products_id'].'</br >';
                    $images = $this->getModelTable('ProductsTable')->getImagesOfProduct($product['products_id']);
                    foreach ($images as $im => $img) {
                        echo 'i_> '.$img['images'].'</br >';
                        $url = $this->getUrlImg($img['images'], $ws);
                        echo 'i__> '.$url.'</br >';
                        $img['images'] = $url;
                        $this->getModelTable('ProductsTable')->updateImagesProduct($img, array('id' => $img['id'], 'products_id' => $product['products_id']));
                    }
                    echo '_> '.$product['thumb_image'].'</br >';
                    $url = $this->getUrlImg($product['thumb_image'], $ws);
                    echo '__> '.$url.'</br >';
                    $product['thumb_image'] =  $url;

                    if(!empty($product['products_description'])){
                        $html = mb_convert_encoding($product['products_description'], 'HTML-ENTITIES', "UTF-8");
                        $dom = new \DOMDocument ();
                        $dom->encoding = 'utf-8';
                        @$dom->loadHTML($html);
                        $imgs = $dom->getElementsByTagName('img');
                        if (count($imgs) > 0) {
                            foreach ($imgs as $img) {
                                $src = $img->getAttribute('src');
                                $src = $this->getUrlImg($src, $ws);
                                $img->setAttribute('src', $src);
                            }
                            $html = $dom->saveXML($dom->getElementsByTagName('body')->item(0));
                            $html = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html);
                            $product ['products_description'] = $html;
                        }
                    }
                    $product ['products_description'] = htmlspecialchars_decode($product ['products_description']);
                    
                    if(!empty($product['products_more'])){
                        $html = mb_convert_encoding($product['products_more'], 'HTML-ENTITIES', "UTF-8");
                        $dom = new \DOMDocument ();
                        $dom->encoding = 'utf-8';
                        @$dom->loadHTML($html);
                        $imgs = $dom->getElementsByTagName('img');
                        if (count($imgs) > 0) {
                            foreach ($imgs as $img) {
                                $src = $img->getAttribute('src');
                                $src = $this->getUrlImg($src, $ws);
                                $img->setAttribute('src', $src);
                            }
                            $html = $dom->saveXML($dom->getElementsByTagName('body')->item(0));
                            $html = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html);
                            $product ['products_more'] = $html;
                        }
                    }
                    $product ['products_more'] = htmlspecialchars_decode($product ['products_more']);

                    if(!empty($product['products_longdescription'])){
                        $html = mb_convert_encoding($product['products_longdescription'], 'HTML-ENTITIES', "UTF-8");
                        $dom = new \DOMDocument ();
                        $dom->encoding = 'utf-8';
                        @$dom->loadHTML($html);
                        $imgs = $dom->getElementsByTagName('img');
                        if (count($imgs) > 0) {
                            foreach ($imgs as $img) {
                                $src = $img->getAttribute('src');
                                $src = $this->getUrlImg($src, $ws);
                                $img->setAttribute('src', $src);
                            }
                            $html = $dom->saveXML($dom->getElementsByTagName('body')->item(0));
                            $html = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html);
                            $product ['products_longdescription'] = $html;
                        }
                    }
                    $product ['products_longdescription'] = htmlspecialchars_decode($product ['products_longdescription']);
                    $this->getModelTable('ProductsTable')->updateProduct($product, array('products_id'=>$product['products_id'], 'website_id'=>$master_website_id));
                //}
            }

            $adapter->getDriver()->getConnection()->commit();

            return TRUE;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die(' ? asa');
            $adapter->getDriver()->getConnection()->rollback();
        }
        return false;
    }

    public function isImage($url){
        $data = explode('.', $url);
        $ext = end($data);
        if(strpos('?', $ext)){
            $ext = explode('?', $ext);
            if(count($ext) > 2){
                $ext = $ext[0];
            }
        }
        $ext = strtolower($ext);
        $image_ext = array(
            'jpeg','png','jpg','gif',
        );
        if(in_array($ext,$image_ext)){
            return TRUE;
        }
        return FALSE;
    }

    public function getUrlImg($filename, $ws)
    {
        $website_domain = $ws->website_domain;
        $website_id = $ws->website_id;
        $domain= md5($ws->website_domain.':'.$website_id);
        $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
        $year = '';
        $month = '';
        $day = '';

        $list = explode ( '.', $filename );
        $dot = count ( $list );
        unset($list [$dot - 1]);
        $file_name_tmp = implode('.', $list);
        $list = explode ( '/', $file_name_tmp );
        $ttl = end($list);

        $ex = $this->file_extension ( $filename );
        if(count($list)>3){
            $day = $list[count($list)-2];
            $month = $list[count($list)-3];
            $year = $list[count($list)-4];
        }
        //echo $filename;
        //echo $day.'/'.$month.'/'.$year;die();
        $type = '';
        $title = '';
        $id = '';
        $folder = '';
        $dimensions = '';
        $crop = false;
        $src = '';
        $width = null;
        $height = null;

        if(!empty($ex) && !empty($ttl)){
            $preg_01 = '/^(?P<type>(product))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<id>\d+)-s(?P<dimensions>[0-9x]*)$/';
            $preg_02 = '/^(?P<type>(product))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<id>\d+)$/';
            
            $preg_03 = '/^(?P<type>(ckeditor))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<folder>[a-zA-Z][a-zA-Z0-9_]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_04 = '/^(?P<type>(ckeditor))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<folder>[a-zA-Z][a-zA-Z0-9_]*)$/';

            $preg_05 = '/^(?P<type>(categories_icons))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_06 = '/^(?P<type>(categories_icons))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_07 = '/^(?P<type>(manufactures))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_08 = '/^(?P<type>(manufactures))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_09 = '/^(?P<type>(articles))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<id>\d+)-s(?P<dimensions>[0-9x]*)$/';
            $preg_10 = '/^(?P<type>(articles))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<id>\d+)$/';

            $preg_11 = '/^(?P<type>(payments))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_12 = '/^(?P<type>(payments))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_13 = '/^(?P<type>(logos))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_14 = '/^(?P<type>(logos))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_15 = '/^(?P<type>(banners))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_16 = '/^(?P<type>(banners))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_17 = '/^(?P<type>(categories_banners))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_18 = '/^(?P<type>(categories_banners))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_19 = '/^(?P<type>(neo))-(?P<folder>[a-zA-Z0-9_]*)-(?P<title>[a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_20 = '/^(?P<type>(neo))-(?P<folder>[a-zA-Z0-9_]*)-(?P<title>[a-zA-Z0-9_-]*)$/';


            if(!empty(preg_match($preg_01, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $id = $matches['id'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_02, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $id = $matches['id'];
            }else if(!empty(preg_match($preg_03, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $folder = $matches['folder'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_04, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $folder = $matches['folder'];
            }else if(!empty(preg_match($preg_05, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_06, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_07, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_08, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_09, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $id = $matches['id'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_10, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $id = $matches['id'];
            }else if(!empty(preg_match($preg_11, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_12, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_13, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_14, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_15, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_16, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_17, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_18, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_19, $ttl, $matches))){
                $type = $matches['type'];
                $folder = $matches['folder'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_20, $ttl, $matches))){
                $type = $matches['type'];
                $folder = $matches['folder'];
                $title = $matches['title'];
            }
            switch ($type) {
                case 'product':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/products/'.$website_domain.'/'.$year.'/'.$month.'/'.$day.'/product'.$id.'/fullsize/'.$title.'.'.$ex;
                        $src_ = PATH_BASE_ROOT.DS.trim($src,'/');
                        if (!is_file($src_)) {
                            $src = '/custom/domain_1/products/'.$website_domain.'/'.$year.'/'.$month.'/'.$day.'/product'.$id.'/fullsize/'.$title.'.'.$ex;
                        }
                    }else{
                        $src = '/custom/domain_1/products/fullsize/product'.$id.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'ckeditor':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/ckeditor/'.$website_domain.'/'.$year.'/'.$month.'/'.$day.'/'.$folder.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'categories_icons':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/categories_icons/'.$website_domain.'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'manufactures':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/manufactures/'.$website_domain.'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'articles':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/articles/'.$website_domain.'/'.$year.'/'.$month.'/'.$day.'/article'.$id.'/fullsize/'.$title.'.'.$ex;
                    }
                    break;
                case 'payments':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/payments/'.$website_domain.'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'logos':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/logos/'.$website_domain.'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'banners':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/banners/'.$website_domain.'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'categories_banners':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/categories_banners/'.$website_domain.'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'neo':
                    $src = '/custom/domain_1/'.$folder.'/'.$title.'.'.$ex;
                    break;   
                
                default:
                    $src = '';
            }
            if( !empty($src) 
                && $this->isImage($src) 
                && is_file(PATH_BASE_ROOT.$src) ){
                copy(PATH_BASE_ROOT.$src, $websiteFolder.'/'.$title.'.'.$ex);
                return "/custom/domain_1/" . $domain.'/'.$title.'.'.$ex;
            }else if( is_file(PATH_BASE_ROOT."/custom/domain_1/" . $domain.'/'.$title.'.'.$ex) ){
                return "/custom/domain_1/" . $domain.'/'.$title.'.'.$ex;
            }
        }
        $src_ = PATH_BASE_ROOT.DS.trim($filename,'/');
        if (is_file($src_)) {
            copy($src_, $websiteFolder.'/'.$ttl.'.'.$ex);
            return "/custom/domain_1/" . $domain.'/'.$ttl.'.'.$ex;
        }else if( is_file(PATH_BASE_ROOT."/custom/domain_1/" . $domain.'/'.$ttl.'.'.$ex) ){
            return "/custom/domain_1/" . $domain.'/'.$ttl.'.'.$ex;
        }
        return $filename;
        //return "/custom/domain_1/" . $domain.'/no-photo.'.$ex;
    }

    public function syncDataWebsite($from, $to, 
        $remove_product = false, 
        $remove_manufacturers = false, 
        $remove_category = false, 
        $remove_article = false, 
        $remove_category_article = false, 
        $remove_banner_positions = false, 
        $remove_banner_sizes = false, 
        $remove_banners = false, 
        $remove_payments = false, 
        $remove_transportations = false, 
        $remove_menu = false){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $master_website_id = $from;
            $website_id = $to;
            $pack_id = 1;
            $users_id = 1;

            $row_user = $this->getModelTable('UserTable')->getOneAdminOfWebsite($website_id);
            //print_r($row_user->full_name);die();
            if($remove_product){
                $this->getModelTable('ProductsTable')->removeAllProductOfWebsite($website_id);
            }
            if($remove_category){
                $this->getModelTable('CategoriesTable')->removeAllCategoryOfWebsite($website_id);
            }
            if($remove_manufacturers){
                $this->getModelTable('ManufacturersTable')->removeAllManufacturersOfWebsite($website_id);
            }
            if($remove_category_article){
                $this->getModelTable('CategoriesArticlesTable')->removeAllArticleCategoryOfWebsite($website_id);
            }
            if($remove_article){
                $this->getModelTable('ArticlesTable')->removeAllArticleOfWebsite($website_id);
            }
            if($remove_banner_positions){
                $this->getModelTable('BannerPositionTable')->removeAllBannersPositionOfWebsite($website_id);
            }
            if($remove_banner_sizes){
                $this->getModelTable('BannerSizeTable')->removeAllBannersSizeOfWebsite($website_id);
            }
            if($remove_banners){
                $this->getModelTable('BannersTable')->removeAllBannersOfWebsite($website_id);
            }
            if($remove_payments){
                $this->getModelTable('PaymentTable')->removeAllPaymentsOfWebsite($website_id);
            }
            if($remove_transportations){
                $this->getModelTable('TransportationTable')->removeAllTransportationOfWebsite($website_id);
            }
            if($remove_menu){
                $this->getModelTable('MenusTable')->removeAllMenusOfWebsite($website_id);
            }
            $categories = $this->getModelTable('CategoriesTable')->getCategoryOfWebsite($master_website_id);

            $list_category = array();
            foreach ($categories as $key => $categorie ) {
                $list_category[] = $categorie;
                $list_category_chlid = $this->getModelTable('CategoriesTable')->getChilds($categories, $categorie['categories_id']);
                $list_category = array_merge($list_category, $list_category_chlid);
            }

            $categories_new = array();
            $list_id_category_insert = array();
            foreach ($list_category as $key => $categorie) {
                $cate_id = $categorie['categories_id'];
                unset($categorie['categories_id']);
                unset($categorie['children']);
                $categorie['website_id'] = $website_id;
                if(isset($categories_new[$categorie['parent_id']])){
                    $categorie['parent_id'] = $categories_new[$categorie['parent_id']];
                }
                $list_id_category_insert[] = $cate_id;
                $categories_new[$cate_id] = $this->getModelTable('CategoriesTable')->insertCategory($categorie);
            }
            
            $manufacturers = $this->getModelTable('ManufacturersTable')->getManufacturersOfWebsite($master_website_id);
            $manufacturers_new = array();
            foreach ($manufacturers as $key => $manufacturer) {
                $manu_id = $manufacturer['manufacturers_id'];
                unset($manufacturer['manufacturers_id']);
                $manufacturer['website_id'] = $website_id;
                $manufacturers_new[$manu_id] = $this->getModelTable('ManufacturersTable')->insertManufacturers($manufacturer);
            }

            $products = $this->getModelTable('ProductsTable')->getProductInCategoriesOfWebsite($master_website_id, $list_id_category_insert);
            $products_new = array();
            foreach ($products as $key => $product) {
                $products_id = $product['products_id'];
                $cate_id = $product['categories_id'];
                $manu_id = $product['manufacturers_id'];
                unset($product['categories_id']);
                unset($product['manufacturers_id']);
                $product['website_id'] = $website_id;
                $product['users_id'] = $row_user->users_id;
                $product['users_fullname'] = $row_user->full_name;
                if(isset($categories_new[$cate_id])){
                    $product['categories_id'] = $categories_new[$cate_id];
                }
                if(isset($manufacturers_new[$manu_id])){
                    $product['manufacturers_id'] = $manufacturers_new[$manu_id];
                }
                $p_copy = new Products();
                $p_copy->exchangeArray($product);
                $products_new[$products_id] = $this->getModelTable('ProductsTable')->copyProduct($p_copy);
            }

            $menus = $this->getModelTable('MenusTable')->getMenusOfWebsite($master_website_id);
            $menu_new = array();
            $article_new = array();
            $article_category_new = array();
            foreach ($menus as $key => $menu) {
                $menu_id = $menu['menus_id'];
                unset($menu['menus_id']);
                $menu['website_id'] = $website_id;
                if($menu['parent_id']!=0){
                    if(isset($menu_new[$menu['parent_id']])){
                        $menu['parent_id'] = $menu_new[$menu['parent_id']];
                    }
                    if($menu['menus_type'] == 'collection'){
                        //danh muc san pham
                        if(isset($categories_new[$menu['menus_reference_id']])){
                            $menu['menus_reference_id'] = $categories_new[$menu['menus_reference_id']];
                        }
                    }else if($menu['menus_type'] == 'product'){
                        //san pham
                        if(isset($products_new[$menu['menus_reference_id']])){
                            $menu['menus_reference_id'] = $products_new[$menu['menus_reference_id']];
                        }
                    }else if($menu['menus_type'] == 'page' || $menu['menus_type'] == 'article'){
                        //bai viet
                        $article = $this->getModelTable('ArticlesTable')->getArticleOfWebsite($menu['menus_reference_id'], $master_website_id);
                        if(!empty($article)){
                            if(!isset($article_category_new[$article['categories_articles_id']]) && $article['categories_articles_id'] !=0 ){
                                $article_category = $this->getModelTable('CategoriesArticlesTable')->getArticleCategoryOfWebsite($article['categories_articles_id'], $master_website_id);
                                if(!empty($article_category)){
                                    $list_article_category = array();
                                    if(!is_array($article_category)){
                                        $list_article_category[] = (array)$article_category;
                                    }else{
                                        $list_article_category = $article_category;
                                    }
                                    foreach ($list_article_category as $key => $item_article_category) {
                                        $categories_articles_id = $article_category['categories_articles_id'];
                                        unset($item_article_category['categories_articles_id']);
                                        $item_article_category['website_id'] = $website_id;
                                        $article_category_new[$categories_articles_id] = $this->getModelTable('CategoriesArticlesTable')->insertArticleCategory($item_article_category);
                                        $item_article_category_languages = array('categories_articles_id' => $article_category_new[$categories_articles_id],
                                            'languages_id' => 3,
                                            'categories_articles_title' => $item_article_category['categories_articles_title'],
                                            'categories_articles_alias' => $item_article_category['categories_articles_alias']);
                                        $id_categories_languages = $this->getModelTable('CategoriesArticlesLanguagesTable')->insert($item_article_category_languages);
                                    }
                                }
                            }
                            $list_articles = array();
                            if(!is_array($article)){
                                $list_articles[] = (array)$article;
                            }else{
                                $list_articles = $article;
                            }
                            foreach ($list_articles as $key => $item_article) {
                                $articles_id = $item_article['articles_id'];
                                $categories_articles_id = $item_article['categories_articles_id'];
                                unset($item_article['articles_id']);
                                $item_article['website_id'] = $website_id;
                                $item_article['users_id'] = $row_user->users_id;
                                if(isset($article_category_new[$categories_articles_id])){
                                    $item_article['categories_articles_id'] = $article_category_new[$categories_articles_id];
                                }else{
                                    $item_article['categories_articles_id'] = 0;
                                }
                                $article_new[$articles_id] = $this->getModelTable('ArticlesTable')->insertArticle($item_article);
                                $item_article_languages = array('languages_id' => 3,
                                     'articles_id' => $article_new[$articles_id],
                                     'articles_title' => (!empty($item_article['articles_title']) ? $item_article['articles_title'] : '' ),
                                     'articles_alias' => (!empty($item_article['articles_alias']) ? $item_article['articles_alias'] : '' ),
                                     'articles_sub_content' => (!empty($item_article['articles_sub_content']) ? $item_article['articles_sub_content'] : '' ),
                                     'articles_content' => (!empty($item_article['articles_content']) ? $item_article['articles_content'] : '' ));
                                $articles_languages_id = $this->getModelTable('ArticlesLanguagesTable')->insert($item_article_languages);
                                $menu['menus_reference_id'] = $article_new[$articles_id];
                            }
                        }
                    }else if($menu['menus_type'] == 'blog'){
                        //danh muc bai viet
                        if(!isset($article_category_new[$menu['menus_reference_id']]) && $menu['menus_reference_id'] !=0 ){
                            $article_category = $this->getModelTable('CategoriesArticlesTable')->getArticleCategoryOfWebsite($menu['menus_reference_id'], $master_website_id);
                            if(!empty($article_category)){
                                $list_article_category = array();
                                if(!is_array($article_category)){
                                    $list_article_category[] = (array)$article_category;
                                }else{
                                    $list_article_category = $article_category;
                                }
                                foreach ($list_article_category as $key => $item_article_category) {
                                    $categories_articles_id = $item_article_category['categories_articles_id'];
                                    unset($item_article_category['categories_articles_id']);
                                    $item_article_category['website_id'] = $website_id;
                                    $article_category_new[$categories_articles_id] = $this->getModelTable('CategoriesArticlesTable')->insertArticleCategory($item_article_category);
                                    $item_article_category_languages = array('categories_articles_id' => $article_category_new[$categories_articles_id],
                                            'languages_id' => 3,
                                            'categories_articles_title' => $item_article_category['categories_articles_title'],
                                            'categories_articles_alias' => $item_article_category['categories_articles_alias']);
                                    $id_categories_languages = $this->getModelTable('CategoriesArticlesLanguagesTable')->insert($item_article_category_languages);
                                    $menu['menus_reference_id'] = $article_category_new[$categories_articles_id];
                                }
                            }
                        }
                    }
                }
                $menu_new[$menu_id] = $this->getModelTable('MenusTable')->insertMenus($menu);
            }

            /*$modules = $this->getModelTable('ModulesTable')->getPackModule($pack_id);
            if(!empty($modules)){
                $this->getModelTable('ModulesTable')->saveModulesWebsite($modules, $website_id);
            }*/

            $banner_positions = $this->getModelTable('BannerPositionTable')->getBannersPositionOfWebsite($master_website_id);
            $banner_position_new = array();
            if(!empty($banner_positions)){
                foreach ($banner_positions as $key => $banner_position) {
                    $position_id = $banner_position['position_id'];
                    unset($banner_position['position_id']);
                    $banner_position['website_id'] = $website_id;
                    $banner_position_new[$position_id] = $this->getModelTable('BannerPositionTable')->insertBannersPosition($banner_position);
                }
            }

            $banner_sizes = $this->getModelTable('BannerSizeTable')->getBannersSizeOfWebsite($master_website_id);
            $banner_size_new = array();
            if(!empty($banner_sizes)){
                foreach ($banner_sizes as $key => $banner_size) {
                    $size_id = $banner_size['id'];
                    unset($banner_size['id']);
                    $banner_size['website_id'] = $website_id;
                    $banner_size_new[$size_id] = $this->getModelTable('BannerSizeTable')->insertBannersSize($banner_size);
                }
            }

            $banners = $this->getModelTable('BannersTable')->getBannersOfWebsite($master_website_id);
            $banners_new = array();
            if(!empty($banners)){
                foreach ($banners as $key => $banner) {
                    $banners_id = $banner['banners_id'];
                    $position_id = $banner['position_id'];
                    $size_id = $banner['size_id'];
                    unset($banner['banners_id']);
                    $banner['website_id'] = $website_id;
                    if(isset($banner_position_new[$position_id])){
                        $banner['position_id'] = $banner_position_new[$position_id];
                    }
                    if(isset($banner_size_new[$size_id])){
                        $banner['size_id'] = $banner_size_new[$size_id];
                    }
                    $banners_new[$banners_id] = $this->getModelTable('BannersTable')->insertBanners($banner);
                }
            }

            $payments = $this->getModelTable('PaymentTable')->getPaymentsOfWebsite($master_website_id);
            $payments_new = array();
            if(!empty($payments)){
                foreach ($payments as $key => $payment) {
                    $payment_id = $payment['payment_id'];
                    unset($payment['payment_id']);
                    $payment['website_id'] = $website_id;
                    $payments_new[$payment_id] = $this->getModelTable('PaymentTable')->insertPayments($payment);
                }
            }

            /*$transportations = $this->getModelTable('TransportationTable')->getTransportationOfWebsite($master_website_id);
            $transportations_new = array();
            if(!empty($transportations)){
                foreach ($transportations as $key => $transportation) {
                    $transportation_id = $transportation['transportation_id'];
                    unset($transportation['transportation_id']);
                    $transportation['website_id'] = $website_id;
                    $transportations_new[$transportation_id] = $this->getModelTable('TransportationTable')->insertTransportation($transportation);
                }
            }*/

            $adapter->getDriver()->getConnection()->commit();

            return TRUE;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die(' ? asa');
            $adapter->getDriver()->getConnection()->rollback();
            return false;

        }
    }

    public function getList($intPage=0, $intPageSize = 20)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':WebsitesTable:getList('.$intPage.';'.$intPageSize.')');
        $results = $cache->getItem($key);
        if(!$results){
            if ($intPage <= 1) {
                $intPage = 0;
            } else {
                $intPage = ($intPage - 1) * $intPageSize;
            }

            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('websites');
            $select->where(array(
                'is_published'=>1,
                'is_try'=>0
            ));
            $select->where->notIn('website_id', array(ID_MASTERPAGE));
            $select->order(array(
                'websites.website_id' => 'ASC',
            ));
            $select->group('websites.website_id');
            $select->limit($intPageSize);
            $select->offset($intPage);

            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $result->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                return array();
            }
        }
        return $results;
    }

    public function countAll()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':WebsitesTable:countAll');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('total' => new Expression('count(websites.website_id)')));
            $select->from('websites');
            $select->where(array(
                'is_published'=>1,
                'is_try'=>0
            ));
            $select->where->notIn('website_id', array(ID_MASTERPAGE));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $row = $result->current();
                $results = $row['total'];
                $cache->setItem($key, $results);
                return ;
            }catch(\Exception $ex){
                return 0;
            }
        }
        return $results;
    }

}