<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

use Cms\Model\AppTable;

class PackTable  extends AppTable
{
    public function fetchAll($where = 0, $order = '', $intPage, $intPageSize)
    {
        $adapter = $this->tableGateway->getAdapter();
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('pack');
        if ($where) {
            $select->where($where);
        }
        if ($order) {
            $select->order($order);
        }
        $select->limit($intPageSize);
        $select->offset($intPage);
        $selectString = $sql->getSqlStringForSqlObject($select);
        try {
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $result;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    public function countAll($where = 0)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression("count(pack_id)")));
        $select->from('pack');
        if ($where) {
            $select->where($where);
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
    }

    public function getPack($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('pack_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function savePack(Pack $pack, $modules = array())
    {
        $data = array(
            'pack_name' => $pack->pack_name,
            'pack_description' => $pack->pack_description,
            'pack_price' => $pack->pack_price,
            'time' => $pack->time,
            'time_bonus' => $pack->time_bonus,
            'products' => $pack->products,
            'bandwidth' => $pack->bandwidth,
            'storage' => $pack->storage,
            'domain' => $pack->domain,
            'edit_seo' => $pack->edit_seo,
            'chat_live' => $pack->chat_live,
            'shop' => $pack->shop,
            'email_marketing' => $pack->email_marketing,
            'responsive' => $pack->responsive,
            'payment_online' => $pack->payment_online,
            'multi_language' => $pack->multi_language,
            'edit_layout' => $pack->edit_layout,
            'is_published' => $pack->is_published,
        );

        $id = (int)$pack->pack_id;
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        $sql = new Sql($adapter);
        try {
            if ($id == 0) {
                $this->tableGateway->insert($data);
                $id = $adapter->getDriver()->getConnection()->getLastGeneratedValue();
            } else {
                if ($this->getPack($id)) {
                    $this->tableGateway->update($data, array('pack_id' => $id));
                } else {
                    throw new \Exception('Pack id does not exist');
                }
            }
            $sql = "DELETE FROM pack_module WHERE pack_id={$id}";
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            if (count($modules) > 0) {
                $value = array();
                foreach ($modules as $module) {
                    $value[] = "({$id}, {$module})";
                }
                $value = implode(',', $value);
                $insertSql = "INSERT INTO pack_module(pack_id,module_id) VALUES {$value}";
                $adapter->query($insertSql, $adapter::QUERY_MODE_EXECUTE);
            }
            $adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

}