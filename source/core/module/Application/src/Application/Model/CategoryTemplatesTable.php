<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 10:19 AM
 */

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

class CategoryTemplatesTable extends AppTable{

    public function getAll()
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('categories_template');
        $select->where(array(
            'categories_template.is_published' => 1
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getCategory($id){
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('categories_template_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

} 