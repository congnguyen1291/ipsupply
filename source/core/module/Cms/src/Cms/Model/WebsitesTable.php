<?php
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

class WebsitesTable extends AppTable
{
    public function getWebsite($domain){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('website_id', 'template_id', 'website_name', 'website_domain', 'website_alias', 'website_domain_refer', 'logo', 'facebook_id','google_client_id','ship','type_buy', 'seo_title', 'seo_keywords', 'seo_description',
            'websites_dir', 'websites_folder', 'website_email_admin', 'website_email_customer', 'website_name_business', 'website_phone', 'website_address', 'website_city', 'website_contries', 'website_city_name', 'website_contries_name',
            'website_timezone', 'website_currency', 'website_currency_format', 'website_currency_decimals', 'website_currency_decimalpoint', 'website_currency_separator', 'website_order_code_prefix', 'website_order_code_suffix','website_ga_code','website_min_value_slider','website_max_value_slider','javascript','css', 'url_twister',
            'url_google_plus', 'url_facebook', 'url_pinterest', 'url_houzz','url_instagram', 'url_rss' ,'url_youtube' , 'date_create', 'is_master', 'is_try', 'templete_buy', 'is_local', 'is_multilanguage', 'has_login_facebook', 'has_login_google', 'has_login_twister', 'version_cart','type_crop_image'  ,'confirm_location'  , 'is_published',
            'end_try' => new Expression('IF((websites.is_try = 0 OR (websites.date_create BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW())) , 0, 1)')));
        $select->from('websites');
        //$select->join('users', 'websites.website_id=users.website_id',array('user_name'));
        //$select->join('template', 'websites.template_id=template.template_id',array('categories_template_id','template_name', 'template_dir', 'template_folder', 'files_js', 'files_css'));
        $select->where("websites.is_published = 1 AND (websites.website_domain LIKE '{$domain}' OR FIND_IN_SET('{$domain}',websites.website_domain_refer)>0 )");
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $results->current();
            return $result;
        }catch(\Exception $ex){
            return array();
        }
    }

    public function getWebsiteWithId($ids){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('website_id', 'template_id', 'website_name', 'website_domain', 'website_alias', 'website_domain_refer', 'logo', 'facebook_id','google_client_id', 'ship','type_buy','seo_title', 'seo_keywords', 'seo_description',
            'websites_dir', 'websites_folder', 'website_email_admin', 'website_email_customer', 'website_name_business', 'website_phone', 'website_address', 'website_city', 'website_contries', 'website_city_name', 'website_contries_name',
            'website_timezone', 'website_currency', 'website_currency_format', 'website_currency_decimals', 'website_currency_decimalpoint', 'website_currency_separator', 'website_order_code_prefix', 'website_order_code_suffix','website_ga_code','website_min_value_slider','website_max_value_slider','javascript','css', 'url_twister',
            'url_google_plus', 'url_facebook', 'url_pinterest', 'url_houzz','url_instagram', 'url_rss' ,'url_youtube' , 'date_create', 'is_master', 'is_try', 'templete_buy', 'is_published', 'is_local', 'is_multilanguage', 'has_login_facebook', 'has_login_google', 'has_login_twister', 'version_cart','type_crop_image'  ,'confirm_location' , 'is_published',
            'end_try' => new Expression('IF((websites.is_try = 0 OR (websites.date_create BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW())) , 0, 1)')));
        $select->from('websites');
        //$select->join('users', 'websites.website_id=users.website_id',array('user_name'));
        $select->join('template', 'websites.template_id=template.template_id',array('categories_template_id','template_name', 'template_dir', 'template_folder', 'files_js', 'files_css'));
        $select->where(array(
            'website_id' => $ids,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $results->current();
            return $result;
        }catch(\Exception $ex){
            return array();
        }
    }

    public function getListWebsiteWithId($ids){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('website_id', 'template_id', 'website_name', 'website_domain', 'website_alias', 'website_domain_refer', 'logo', 'facebook_id','google_client_id', 'ship','type_buy','seo_title', 'seo_keywords', 'seo_description',
            'websites_dir', 'websites_folder', 'website_email_admin', 'website_email_customer', 'website_name_business', 'website_phone', 'website_address', 'website_city', 'website_contries', 'website_city_name', 'website_contries_name',
            'website_timezone', 'website_currency', 'website_currency_format', 'website_currency_decimals', 'website_currency_decimalpoint', 'website_currency_separator', 'website_order_code_prefix', 'website_order_code_suffix','website_ga_code','website_min_value_slider','website_max_value_slider', 'javascript','css','url_twister',
            'url_google_plus', 'url_facebook', 'url_pinterest', 'url_houzz','url_instagram', 'url_rss' , 'url_youtube' ,'date_create', 'is_master', 'is_try', 'templete_buy', 'is_published', 'is_local', 'is_multilanguage', 'has_login_facebook', 'has_login_google', 'has_login_twister', 'version_cart','type_crop_image'  ,'confirm_location' , 'is_published',
            'end_try' => new Expression('IF((websites.is_try = 0 OR (websites.date_create BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW())) , 0, 1)')));
        $select->from('websites');
        //$select->join('users', 'websites.website_id=users.website_id',array('user_name'));
        $select->join('template', 'websites.template_id=template.template_id',array('categories_template_id','template_name', 'template_dir', 'template_folder', 'files_js', 'files_css'));
        $select->where(array(
            'website_id' => $ids,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch(\Exception $ex){
            return array();
        }
    }

    public function saveWebsite(Websites $website){
        
        $data = array(
            'website_domain_refer'       => $website->website_domain_refer,
            'website_name'       => $website->website_name,
            'seo_title'     => $website->seo_title,
            'seo_keywords'   => $website->seo_keywords,
            'seo_description'             => $website->seo_description, 
            'website_email_admin'      => $website->website_email_admin,
            'website_email_customer'        => $website->website_email_customer,
            'website_name_business'     => $website->website_name_business,
            'website_phone'  => $website->website_phone,
            'website_address'     => $website->website_address,
            'website_city'     => $website->website_city,
            'website_contries'     => $website->website_contries,
            'website_timezone'     => $website->website_timezone,
            'website_currency'     => $website->website_currency,
            'website_currency_format'     => $website->website_currency_format,
            'website_currency_decimals'     => $website->website_currency_decimals,
            'website_currency_decimalpoint'     => $website->website_currency_decimalpoint,
            'website_currency_separator'     => $website->website_currency_separator,
            'website_order_code_prefix'     => $website->website_order_code_prefix,
            'website_order_code_suffix'     => $website->website_order_code_suffix,
            'website_ga_code'     => $website->website_ga_code,
            'website_min_value_slider'     => str_replace(',','',$website->website_min_value_slider),
            'website_max_value_slider'     => str_replace(',','',$website->website_max_value_slider),
            'javascript'     => $website->javascript,
            'css'     => $website->css,
            'url_twister'     => $website->url_twister,
            'url_google_plus'     => $website->url_google_plus,
            'url_facebook'     => $website->url_facebook,
            'url_pinterest'     => $website->url_pinterest,
            'url_houzz'     => $website->url_houzz,
            'url_instagram'     => $website->url_instagram,
            'url_rss'     => $website->url_rss,
            'url_youtube'     => $website->url_youtube,
            'facebook_id'     => $website->facebook_id,
            'google_client_id'     => $website->google_client_id,
            'ship'     => $website->ship,
            'type_buy'     => $website->type_buy,
            'is_multilanguage'     => $website->is_multilanguage,
            'has_login_facebook'     => $website->has_login_facebook,
            'has_login_google'     => $website->has_login_google,
            'has_login_twister'     => $website->has_login_twister,
            'version_cart'     => $website->version_cart,
            'type_crop_image'     => $website->type_crop_image,
            'confirm_location'     => $website->confirm_location,
            'is_local'     => $website->is_local,
        );
        if(!empty($website->logo)){
            $data['logo'] = $website->logo;
        }
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':Websites:getwebsite('.$_SESSION['domain'].')');
        $cache->setItem($key, null);
        $this->tableGateway->update($data, array('website_id' => $this->getWebsiteId()));
    }

    public function saveWebsiteForSupperWeb(Websites $website , $modules = array()){
        $id = (int)$website->website_id;
        if ($id > 0) {
            
            $data = array(
                'website_domain_refer'       => $website->website_domain_refer,
                'website_name'       => $website->website_name,
                'seo_title'     => $website->seo_title,
                'seo_keywords'   => $website->seo_keywords,
                'seo_description'             => $website->seo_description, 
                'website_email_admin'      => $website->website_email_admin,
                'website_email_customer'        => $website->website_email_customer,
                'website_name_business'     => $website->website_name_business,
                'website_phone'  => $website->website_phone,
                'website_address'     => $website->website_address,
                'website_city'     => $website->website_city,
                'website_contries'     => $website->website_contries,
                'website_timezone'     => $website->website_timezone,
                'website_currency'     => $website->website_currency,
                'website_order_code_prefix'     => $website->website_order_code_prefix,
                'website_order_code_suffix'     => $website->website_order_code_suffix,
                'website_ga_code'     => $website->website_ga_code,
                'website_min_value_slider'     => str_replace(',','',$website->website_min_value_slider),
                'website_max_value_slider'     => str_replace(',','',$website->website_max_value_slider),
                'javascript'     => $website->javascript,
                'css'     => $website->css,
                'url_twister'     => $website->url_twister,
                'url_google_plus'     => $website->url_google_plus,
                'url_facebook'     => $website->url_facebook,
                'url_pinterest'     => $website->url_pinterest,
                'url_houzz'     => $website->url_houzz,
                'url_instagram'     => $website->url_instagram,
                'url_rss'     => $website->url_rss,
                'url_youtube'     => $website->url_youtube,
                'facebook_id'     => $website->facebook_id,
                'google_client_id'     => $website->google_client_id,
                'ship'     => $website->ship,
                'type_buy'     => $website->type_buy,
                'is_local'     => $website->is_local,
                'is_multilanguage'     => $website->is_multilanguage,
                'has_login_facebook'     => $website->has_login_facebook,
                'has_login_google'     => $website->has_login_google,
                'has_login_twister'     => $website->has_login_twister,
                'version_cart'     => $website->version_cart,
                'type_crop_image'     => $website->type_crop_image,
                'confirm_location'     => $website->confirm_location,
            );
            if(!empty($website->logo)){
                $data['logo'] = $website->logo;
            }
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            $sql = new Sql($adapter);
            try {
                $this->tableGateway->update($data, array('website_id' => $id));
                $sql = "DELETE FROM module_websites WHERE website_id={$id}";
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                if (count($modules) > 0) {
                    $value = array();
                    foreach ($modules as $module) {
                        $value[] = "({$id}, {$module})";
                    }
                    $value = implode(',', $value);
                    $insertSql = "INSERT INTO module_websites(website_id,module_id) VALUES {$value}";
                    $adapter->query($insertSql, $adapter::QUERY_MODE_EXECUTE);
                }

                $cache = $this->getServiceLocator()->get('cache');
                $key = md5('Websites:get-website-'.$_SESSION['domain']);
                $cache->setItem($key, null);
                
                $adapter->getDriver()->getConnection()->commit();
            } catch (\Exception $ex) {
                $adapter->getDriver()->getConnection()->rollback();
                die($ex->getMessage());
                throw new \Exception($ex->getMessage());
            }
        }
    }


    public function updateWebsite($data, $website){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':Websites:getwebsite('.$website->website_domain.')');
        $cache->setItem($key, null);
        $this->tableGateway->update($data, array('website_id' => $website->website_id, 'website_domain' => $website->website_domain));
    }

    public function updateWebsiteWithId($data, $website_id){
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':Websites:getwebsite('.$website->website_domain.')');
        $cache->setItem($key, null);
        $this->tableGateway->update($data, array('website_id' => $website_id));
    }

    public function deleteWebsite($website){
        if(!empty($website) && $website['website_id'] != ID_MASTERPAGE){
            $adapter = $this->tableGateway->getAdapter();
            $adapter->getDriver()->getConnection()->beginTransaction();
            try {
                $this->getModelTable('BannersTable')->removeBannerOfWebsite($website['website_id']);
                $this->getModelTable('BannerSizeTable')->removeBannersSizeOfWebsite($website['website_id']);
                $this->getModelTable('BannerPositionTable')->removeBannersPositionOfWebsite($website['website_id']);
                $this->getModelTable('ModulesTable')->removeModulesOfWebsite($website['website_id']);
                $this->getModelTable('CategoryArticlesTable')->removeArticleCategoryOfWebsite($website['website_id']);
                $this->getModelTable('ArticlesTable')->removeArticleOfWebsite($website['website_id']);
                $this->getModelTable('MenusTable')->removeMenusOfWebsite($website['website_id']);
                $this->getModelTable('ProductTable')->removeProductsOfWebsite($website['website_id']);
                $this->getModelTable('ManufacturersTable')->removeManufacturersOfWebsite($website['website_id']);
                $this->getModelTable('CategoryTable')->removeCategoryOfWebsite($website['website_id']);
                $this->getModelTable('UserTable')->removeUserOfWebsite($website['website_id']);
                $this->getModelTable('PaymentTable')->removePaymentOfWebsite($website['website_id']);
                $this->getModelTable('TransportationTable')->removeTransportationOfWebsite($website['website_id']);
                $this->removeWebsite($website['website_id']);

                $cache = $this->getServiceLocator()->get('cache');
                $key = md5($this->getNamspaceCached().':Websites:getwebsite('.$website['website_domain'].')');
                $cache->setItem($key, null);
                $template = $this->getModelTable('TemplatesTable')->getTemplateById($website['template_id']);
                if(!empty($template)){
                    $name_folder = $website['websites_folder'];
                    $view_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder;
                    //xoa source cu di
                    exec('rm -rf '.escapeshellarg($view_new));
                }
                $adapter->getDriver()->getConnection()->commit();
            } catch (\Exception $e) {
                //echo $e->getMessage();die('aa');
                $adapter->getDriver()->getConnection()->rollback();
            }
        }
    }

    public function getList($where = array(), $order = array(), $intPage=0, $intPageSize = 20)
    {
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }

        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('websites');
        
        if(!count($order)){
            $select->order(array(
                'products.date_create' => 'ASC',
            ));
        }else{
            $select->order($order);
        }
        $select->group('websites.website_id');
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

    public function countAll($where = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression('count(websites.website_id)')));
        $select->from('websites');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        }catch (\Exception $ex){
            return array();
        }
    }

    public function removeWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }


}