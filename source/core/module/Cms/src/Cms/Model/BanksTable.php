<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/9/14
 * Time: 11:01 AM
 */
namespace Cms\Model;

use Cms\Model\AppTable;

class BanksTable  extends AppTable
{

    public function fetchAll($str_where = '', $order = '', $intPage, $intPageSize)
    {
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }
        if ($order) {
            $order = "ORDER BY {$order}";
        }
        $where = 'WHERE website_id = '.$this->getWebsiteId();
        if ($str_where) {
            $where = $where.' AND '.$str_where;
        }
        try {
            $sql = "SELECT *
                    FROM {$this->tableGateway->table}
                    {$where}
                    {$order}
                    LIMIT {$intPage}, {$intPageSize}";
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function getAllRate($intPage, $intPageSize){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('banks_title', 'is_published','banks_id'));
        $select->from('banks');
        $select->join('banks_config','banks_config.banks_id=banks.banks_id', array('percent_interest_rate','percent_must_pay','total_month','banks_config_id'));
        $select->where(array(
            'banks.is_published' => 1,
            'banks.website_id' => $this->getWebsiteId(),
        ));
        $select->order(array(
            'banks_id' => 'DESC',
            'total_month' => 'ASC',
        ));
        $select->limit($intPageSize);
        $select->offset($intPage);
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $result;
    }

    public function filter($params){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('banks_title', 'is_published','banks_id'));
        $select->from('banks');
        $select->join('banks_config','banks_config.banks_id=banks.banks_id', array('percent_interest_rate','percent_must_pay','total_month','banks_config_id'));
        $params['website_id'] = $this->getWebsiteId();
        $select->where($params);
        $select->order(array(
            'banks_id' => 'DESC',
            'total_month' => 'ASC',
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $result;
    }

    public function countAll($str_where = '')
    {
        $where = 'WHERE website_id = '.$this->getWebsiteId();
        if ($str_where) {
            $where = $where.' AND '.$str_where;
        }
        try {
            $sql = "SELECT count(banks.banks_id) as total
                    FROM {$this->tableGateway->table}
                    {$where}";
            $adapter = $this->tableGateway->getAdapter();
            $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function getById($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('banks_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveBanks(Banks $bank, $image_file, $old_file = "")
    {
        $data = array(
            'website_id' => $this->getWebsiteId(),
            'banks_title' => $bank->banks_title,
            'banks_description' => $bank->banks_description,
            'is_published' => $bank->is_published,
            'is_delete' => $bank->is_delete,
            'date_create' => $bank->date_create,
        );
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $upload_dir = PATH_BASE_ROOT.DS.'custom'.DS.'domain_1'.DS.'banks';
            if($image_file['name']){
                $filename = $image_file['name'];
                $filename = explode('.',$filename);
                if(count($filename) == 2){
                    $ext = end($filename);
                    $name = $this->toAlias($data['banks_title']).'-'.time().'.'.$ext;
                    $src = "/custom/domain_1/banks/".$name;
                    $data['thumb_image'] = $src;
                }
            }
            $id = (int)$bank->banks_id;
            if ($id == 0) {
                $this->tableGateway->insert($data);
            } else {
                if ($this->getById($id)) {
                    $this->tableGateway->update($data, array('banks_id' => $id));
                } else {
                    throw new \Exception('Row id does not exist');
                }
            }
            if($image_file['name']){
                if($old_file){
                    $old_name = explode('/', $old_file);
                    $old_name = end($old_name);
                    @unlink($upload_dir.DS.$old_name);
                }
                @move_uploaded_file($image_file['tmp_name'], $upload_dir.DS.$name);
            }
            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
        }
    }

    public function deleteBank($banks_id){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $delete = "DELETE FROM banks_config WHERE banks_id={$banks_id}";
            $adapter->query($delete, $adapter::QUERY_MODE_EXECUTE);
            $delete = "DELETE FROM banks WHERE banks_id={$banks_id}";
            $adapter->query($delete, $adapter::QUERY_MODE_EXECUTE);
            $adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
    }

    public function addConfig($data){
        $adapter = $this->tableGateway->getAdapter();
        $sql = "INSERT INTO banks_config(banks_id,percent_interest_rate,percent_must_pay,total_month)
                VALUES ({$data['banks_id']},{$data['percent_interest_rate']},{$data['percent_must_pay']},{$data['total_month']})
                ON DUPLICATE KEY UPDATE percent_interest_rate={$data['percent_interest_rate']},percent_must_pay={$data['percent_must_pay']}";
        try{
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            $_SESSION['current_bank_add'] = $data['banks_id'];
        }catch(\Exception $ex){
            throw new \Exception($ex);
        }
    }

    public function deleteConfig($ids){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $delete = $sql->delete('banks_config');
        $delete->where(array(
            'banks_config_id' => $ids,
        ));
        try{
            $deleteString = $sql->getSqlStringForSqlObject($delete);
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
            throw new \Exception($ex);
        }
    }

}