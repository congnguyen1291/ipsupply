<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/9/14
 * Time: 11:01 AM
 */
namespace Cms\Model;

use Zend\Db\Sql\Sql;
use Cms\Model\AppTable;

class LanguagescmsTable extends AppTable
{

    public function fetchAll($where = array(), $order = '', $intPage, $intPageSize)
    {
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }
        if ($order) {
            $order = "ORDER BY {$order}";
        }
        if ($where) {
            $where = "WHERE {$where}";
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

    public function countAll($where = NULL)
    {
        if ($where) {
            $where = "WHERE {$where}";
        }
        try {
            $sql = "SELECT count(languages.languages_id) as total
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
        $rowset = $this->tableGateway->select(array('languages_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveLanguage(Languages $lang)
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'languages_name' => $lang->languages_name,
                'languages_file' => $lang->languages_file,
                'is_published' => $lang->is_published,
                'date_create' => $lang->date_create,
                'date_update' => $lang->date_update,
            );
            $id = (int)$lang->languages_id;
            if ($id == 0) {
                $this->tableGateway->insert($data);
                $id = $this->tableGateway->getLastInsertValue();
            } else {
                if ($this->getById($id)) {
                    $this->tableGateway->update($data, array('languages_id' => $id));
                } else {
                    throw new \Exception('Language id does not exist');
                }
            }
            $files_name = $lang->languages_file . '.php';
            if (!is_file('./module/lang/' . $files_name)) {
                $translate = "<?php\r\n return " . var_export(array(), TRUE) . ";";
                @file_put_contents('./module/lang/' . $files_name, $translate);
            }
//            $data = "<?php\r\n return ". var_export($translates,true). ";";
//            $dataUpdate['languages_data'] = $data;
//            $this->tableGateway->update($dataUpdate, array('languages_id' => $id));
//            $files_name = $lang->languages_file.'.php';
//            @file_put_contents('./module/lang/'.$files_name, $data);
            $adapter->getDriver()->getConnection()->commit();
            return $id;

        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            return FALSE;
        }
    }

    public function deleteLanguage($ids){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try{
            $deleteString = "DELETE FROM languages_keywords_cms WHERE languages_id IN (". implode(',', $ids) .")";
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
            $deleteString = "DELETE FROM languages WHERE languages_id IN (". implode(',', $ids) .")";
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
            $adapter->getDriver()->getConnection()->commit();

        }catch (\Exception $ex){
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex);
        }
    }

    public function getKeyword($keyword)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select('languages_keywords_cms');
        $select->join('languages', 'languages_keywords_cms.languages_id=languages.languages_id', array('languages_name'));
        $select->where(array(
            'keyword' => $keyword,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function getKeywords($intPage, $intPageSize)
    {
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }
        $adapter = $this->tableGateway->getAdapter();
//        $sql = new Sql($adapter);
//        $select = $sql->select('languages_keywords_cms');
//        $select->join('languages', 'languages_keywords_cms.languages_id=languages.languages_id', array('languages_name'));
//        $select->order(array(
//            'keyword' => 'ASC',
//            'languages_id' => 'ASC',
//        ));
//        $select->limit($intPageSize);
//        $select->offset($intPage);
//        $selectString = $sql->getSqlStringForSqlObject($select);
        $selectString = "SELECT keyword,GROUP_CONCAT(CONCAT(languages_id, '_transto_', translate) SEPARATOR '__AND__') as translate
                         FROM languages_keywords_cms
                         GROUP BY keyword
                         LIMIT {$intPage},{$intPageSize}";
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function countAllKeyword($str_where = ''){
        $where = 'WHERE 1=1 ';
        if($str_where){
            $where .= ' AND '.$str_where;
        }
        $sql = "SELECT count(DISTINCT keyword) as total
                FROM languages_keywords_cms
                {$where}";
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
    }

    public function getKeywordsBySearch($keyword){
        $adapter = $this->tableGateway->getAdapter();
        $selectString = "SELECT keyword,GROUP_CONCAT(CONCAT(languages_id, '_transto_', translate) SEPARATOR '__AND__') as translate
                         FROM languages_keywords_cms
                         WHERE (keyword LIKE '%{$keyword}%' OR translate LIKE '%{$keyword}%')
                         GROUP BY keyword";
        // '%{$keyword}%' OR translate LIKE '%{$keyword}%
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        if($results->count() > 0){
            $results = $results->toArray();
            $keywords = array();
            foreach($results as $word){
                $keywords[] = "'".$word['keyword']."'";
            }
            $keywords = implode(',', $keywords);
            $selectString = "SELECT keyword,GROUP_CONCAT(CONCAT(languages_id, '_transto_', translate) SEPARATOR '__AND__') as translate
                             FROM languages_keywords_cms
                             WHERE keyword IN ({$keywords})
                             GROUP BY keyword";
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;
        }
        return $results;
    }

    public function saveKeyword($keyword, $translates, $language)
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $sql = "INSERT INTO languages_keywords_cms(`languages_id`,`keyword`,`translate`,`website_id`) VALUES ";
            $value = array();
            foreach ($translates as $lang => $tran) {
                $value[] = "({$lang}, '{$keyword}', '{$tran}', '{$this->getWebsiteId()}')";
            }
            $value = implode(',', $value);
            $sql .= $value . " ON DUPLICATE KEY UPDATE translate=VALUES(translate);";
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

            foreach($language as $lang){
                //$path = LANG_PATH.'/'.$lang['languages_file']. '.php';
                $path = LANG_PATH.'/'.$lang['languages_file']. '.php';
                if($_SESSION['domain'] != MASTERPAGE ){
                    $name_folder = $_SESSION['website']['websites_folder'];
                    $path=PATH_BASE_ROOT . '/templates/langcms/'.$lang['languages_file']. '.php';
                }
                $lang_array = include($path);
                $translate = "";
                foreach($translates as $key => $tran){
                    if($key == $lang['languages_id']){
                        $translate = $tran;
                        break;
                    }
                }
                $lang_array[$keyword] = $translate;
                $data = "<?php\r\n return ". var_export($lang_array,true). ';';
                @file_put_contents($path, $data);
            }
            $adapter->getDriver()->getConnection()->commit();
            return TRUE;
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            return FALSE;
        }
    }

    public function deleteKeyword($keyword,$language){
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $sql = "DELETE FROM languages_keywords_cms WHERE keyword LIKE '{$keyword}'";
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);

            foreach($language as $lang){
                //$path = LANG_PATH.'/'.$lang['languages_file']. '.php';
                $path = LANG_PATH.'/'.$lang['languages_file']. '.php';
                if($_SESSION['domain'] != MASTERPAGE ){
                    $name_folder = $_SESSION['website']['websites_folder'];
                    $path=PATH_BASE_ROOT . '/templates/langcms/'.$lang['languages_file']. '.php';
                }
                $lang_array = include($path);
                if(isset($lang_array[$keyword])){
                    unset($lang_array[$keyword]);
                }
                $data = "<?php\r\n return ". var_export($lang_array,true). ';';
                @file_put_contents($path, $data);
            }
            $adapter->getDriver()->getConnection()->commit();
            return TRUE;
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            return FALSE;
        }
    }

    protected function getIdCol(){
        return 'languages_id';
    }

}