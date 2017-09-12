<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 10:19 AM
 */

namespace Cms\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class CategoryTemplateTable extends AppTable
{

    public function getAll()
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('categories_template');
        //$select->where(array('is_published'=>1));
        $select->group('categories_template.categories_template_id');
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

    public function saveCategory($row)
    {
        $data = array(
            'parent_id' => $row['parent_id'],
            'categories_title' => $row['categories_title'],
            'categories_alias' => $row['categories_alias'],
            'is_published' => $row['is_published']
        );
        $id = (int)$row['categories_template_id'];
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->getLastestId();
        } else {
            if ($this->getCategory($id)) {
                $this->tableGateway->update($data, array('categories_template_id' => $id));
            } else {
                throw new \Exception('Category id does not exist');
            }
        }
    }

    public function update($ids, $row)
    {
        $this->tableGateway->update($row, array('categories_template_id' => $ids));
    }

    public function getLastestId()
    {
        return $this->tableGateway->getLastInsertValue();
    }

} 