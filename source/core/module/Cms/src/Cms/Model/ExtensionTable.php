<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 9:22 AM
 */

namespace Cms\Model;

use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class ExtensionTable extends AppTable
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
            $where = $where. ' AND '. $str_where;
        }
        $sql = "SELECT *
                FROM {$this->tableGateway->table}
                {$where}
                {$order}
                LIMIT {$intPage}, {$intPageSize}";
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        return $result;
    }

    public function getExtension($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('ext_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getExtensions($ids){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('extensions')
               ->where(array(
                'ext_id' => $ids,
                'is_delete' => 0,
                'is_published' => 1,
            ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function saveExtension(Extension $ext)
    {
        $data = array(
            'website_id'          => $this->getWebsiteId(),
            'ext_name'          => $ext->ext_name,
            'ext_description'   => $ext->ext_description,
            'is_published'      => $ext->is_published,
            'is_delete'         => $ext->is_delete,
            'date_create'       => $ext->date_create,
            'date_update'       => $ext->date_update,
        );
        $id = (int) $ext->ext_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            return TRUE;
        } else {
            if ($this->getExtension($id)) {
                $this->tableGateway->update($data, array('ext_id' => $id));
                return TRUE;
            } else {
                throw new \Exception('id does not exist');
            }
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
        $sql = "UPDATE `articles` SET {$set} WHERE articles_id={$id}";
        try {
            $adapter = $this->tableGateway->getAdapter();
            return $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    public function getLastestId()
    {
        return $this->tableGateway->getLastInsertValue();
    }

    public function getIdCol()
    {
        return 'ext_id';
    }
} 