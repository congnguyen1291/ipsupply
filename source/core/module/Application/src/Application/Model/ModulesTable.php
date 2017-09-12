<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 11:54 AM
 */
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;

use Application\Model\AppTable;

class ModulesTable  extends AppTable{

    public function saveModulesWebsite($modules, $website_id)
    {
        $adapter = $this->tableGateway->getAdapter();
        if (count($modules) > 0) {
            $value = array();
            foreach ($modules as $module) {
                $value[] = "({$module['module_id']}, {$website_id})";
            }
            $value = implode(',', $value);
            $insertSql = "INSERT INTO module_websites(module_id,website_id) VALUES {$value}";
            $adapter->query($insertSql, $adapter::QUERY_MODE_EXECUTE);
            return true;
        }
        return false;

    }

    public function getPackModule($pack_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('pack_module');
        $select->where(array(
            'pack_id' => $pack_id,
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }
    
}