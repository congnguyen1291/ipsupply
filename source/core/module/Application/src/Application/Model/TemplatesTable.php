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

use Application\Model\AppTable;

class TemplatesTable  extends AppTable{
    
    public function getAll(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('template');
        $select->where(array(
            'template.template_status' => 1
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

    public function getTemplateById($template_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('template');
        $select->join('websites', 'template.template_id = websites.template_id', array('website_id', 'website_name', 'website_domain', 'website_alias', 'website_domain_refer', 'logo', 'facebook_id','ship','type_buy',  'seo_title', 'seo_keywords', 'seo_description',
            'websites_dir', 'websites_folder', 'website_email_admin', 'website_email_customer', 'website_name_business', 'website_phone', 'website_address', 'website_city', 'website_contries', 'website_city_name', 'website_contries_name',
            'website_timezone', 'website_currency', 'website_currency_format', 'website_currency_decimals', 'website_currency_decimalpoint', 'website_currency_separator', 'website_order_code_prefix', 'website_order_code_suffix','website_ga_code', 'javascript','css','url_twister',
            'url_google_plus', 'url_facebook', 'url_pinterest', 'url_houzz','url_instagram', 'url_rss' , 'url_youtube' , 'date_create', 'is_master', 'is_try', 'templete_buy', 'is_local', 'is_published','total_websites' => new Expression('count(websites.website_id)')) );
        $select->where(array(
            'websites.is_demo' => 1,
            'websites.is_published' => 1,
            'template.template_id' => $template_id,
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

    public function countAll($where = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression('count(template.template_id)')));
        $select->from('template');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getList($where = array(), $order = array(), $offset=0, $limit = 20)
    {

        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('template');
        $select->join('websites', 'template.template_id = websites.template_id', array('website_domain','website_alias','website_name','total_websites' => new Expression('count(websites.website_id)')) );
        $select->where(array(
            'websites.is_demo' => 1,
            'websites.is_published' => 1,
            'template.template_status' => 1
        ));
        if(!empty($where)){
            $select->where($where);
        }
        if( empty($order) ){
            $select->order(array(
                'template.template_id' => 'ASC',
            ));
        }else{
            $select->order($order);
        }
        $select->group('template.template_id');
        $select->limit($limit);
        $select->offset($offset);

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getTemplatesHots($where = array(), $offset=0, $limit = 20)
    {

        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('template');
        $select->join('websites', 'template.template_id = websites.template_id', array('website_domain','website_alias','website_name','total_websites' => new Expression('count(websites.website_id)')) );
        $select->where(array(
            'websites.is_demo' => 1,
            'websites.is_published' => 1,
            'template.template_status' => 1
        ));
        if(!empty($where)){
            $select->where($where);
        }

        $select->order(array(
            'total_websites' => 'DESC',
        ));

        $select->group('template.template_id');
        $select->limit($limit);
        $select->offset($offset);

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }
    
}