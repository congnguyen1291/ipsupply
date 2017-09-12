<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/2/14
 * Time: 1:40 PM
 */

namespace Cms\Model;

use Zend\Db\TableGateway\TableGateway;

class GeneralModel {
    //protected $tableGateway;
    protected $dbAdapter;
    public function __construct($adapter)
    {
        $this->dbAdapter = $adapter;
    }

    /**
     * Return Data Statement
     * Use for query type select datatable
     */
    /**
     * @param $store_name : Tên store
     * @param array $params : array(  array('value' => ... , 'type' => \PDO::PARAM_INT...),...   )
     * @return mixed
     */
    protected function SelectQuery($store_name, $params = array()){
        $stmt = $this->dbAdapter->createStatement();
        $ps_mask = array_fill(0, count($params),'?');
        $ps_mask = implode(',', $ps_mask);
        $stmt->prepare("CALL {$store_name}({$ps_mask})");
        foreach($params as $key => $param){
            $stmt->getResource()->bindParam($key, $param['value'], $param['type']);
        }
        $result = $stmt->execute();
        return $result;
    }

    protected function ExecQuery(){

    }

    /**
     * Return numrow statement
     * Use for INSERT, UPDATE, DELETE query
     */
    protected function ExecNonQuery(){

    }

}