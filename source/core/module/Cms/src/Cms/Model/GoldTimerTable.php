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

class GoldTimerTable extends AppTable
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
            $where = $str_where. ' AND '. $str_where;
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

    public function countAll($str_where = '')
    {
        $where = 'WHERE website_id = '.$this->getWebsiteId();
        if ($str_where) {
            $where = $str_where. ' AND '. $str_where;
        }
        $sql = "SELECT count(articles.articles_id) as total
                FROM {$this->tableGateway->table}

                {$where}";
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        $row = $result->current();
        return $row['total'];
    }

    public function getGoldTimer($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('gold_timer_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveGoldTimer(GoldTimer $gold, $products = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            $data = array(
                'website_id' => $this->getWebsiteId(),
                'gold_timer_title' => $gold->gold_timer_title,
                'date_start' => $gold->date_start,
                'date_end' => $gold->date_end,
                'time_start' => $gold->time_start,
                'time_end' => $gold->time_end,
            );
            $id = (int)$gold->gold_timer_id;
            if ($id == 0) {
                $this->tableGateway->insert($data);
                $id = $this->tableGateway->getLastInsertValue();
            } else {
                if ($this->getGoldTimer($id)) {
                    $this->tableGateway->update($data, array('gold_timer_id' => $id));
                } else {
                    throw new \Exception('GoldTimer id does not exist');
                }
            }
            $del = "DELETE FROM gold_timer_detail WHERE gold_timer_id={$id}";
            $adapter->query($del, $adapter::QUERY_MODE_EXECUTE);
            if (count($products) > 0) {
                $sql = "INSERT INTO gold_timer_detail(gold_timer_id,products_id,price, price_sale) VALUES";
                $value = array();
                foreach ($products as $key => $product) {
                    $price = str_replace(',', '', $product['price']);
                    $price_sale = str_replace(',', '', $product['price_sale']);
                    $value[] = "({$id},{$key}, {$price}, {$price_sale})";
                }
                $value = implode(',', $value);
                $sql .= $value;
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }
            $adapter->getDriver()->getConnection()->commit();
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }

    }

    public function getProducts($id){
        $adapter = $this->tableGateway->getAdapter();
        try{
            $sql = new Sql($adapter);
            $select = $sql->select()->columns(array('price','price_sale','products_id'));
            $select->from('gold_timer_detail');
            $select->join('products', 'gold_timer_detail.products_id=products.products_id', array('products_code','products_title'),'left');
            $select->where(array(
                'gold_timer_id' => $id,
            ));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results->toArray();
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }

    }

    public function getIdCol()
    {
        return 'gold_timer_id';
    }
} 