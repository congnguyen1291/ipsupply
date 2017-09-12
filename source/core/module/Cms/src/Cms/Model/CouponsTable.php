<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/11/14
 * Time: 11:54 AM
 */
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class CouponsTable extends AppTable{

    public function fetchAll( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('*', 'total'=> new Expression('COUNT(invoice_coupon.id)')));
        $select->from('coupons');
        $select->join('invoice_coupon', 'invoice_coupon.coupon_id=coupons.coupons_id', array(), 'left');
        $select->where(array(
            'coupons.website_id' => $this->getWebsiteId()
        ));

        if(isset($params['coupons_code'])){
            $coupons_code = $this->toAlias($params['coupons_code']);
            $select->where->like('coupons.coupons_code', "%{$coupons_code}%");
        }

        if( isset($params['coupon_price']) ){
            $select->where(array(
                'coupons.coupon_price' => $params['coupon_price']
            ));
        }

        if( $this->hasPaging($params) ){
            $select->offset($this->getOffsetPaging($params['page'], $params['limit']));
            $select->limit($params['limit']);
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){}
        return array();
    }

    public function countAll( $params = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(categories.categories_id)")));
        $select->from('coupons');
        $select->where(array(
            'coupons.website_id' => $this->getWebsiteId()
        ));
        if(isset($params['coupons_code'])){
            $coupons_code = $this->toAlias($params['coupons_code']);
            $select->where->like('coupons.coupons_code', "%{$coupons_code}%");
        }

        if( isset($params['coupon_price']) ){
            $select->where(array(
                'coupons.coupon_price' => $params['coupon_price']
            ));
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function getCoupon($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('coupons_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveCoupons(Coupons $coupon)
    {
        $data = array(
            'website_id' => $this->getWebsiteId(),
            'coupons_code' => $coupon->coupons_code,
            'coupons_type' => $coupon->coupons_type,
            'coupons_max_use' => $coupon->coupons_max_use,
            'start_date' => $coupon->start_date,
            'expire_date' => $coupon->expire_date,
            'min_price_use' => str_replace(',','',$coupon->min_price_use),
            'max_price_use' => str_replace(',','',$coupon->max_price_use),
            'coupon_price' => str_replace(',','',$coupon->coupon_price),
            'coupon_percent' => $coupon->coupon_percent,
            'is_published' => $coupon->is_published,
        );
        $id = (int) $coupon->coupons_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCoupon($id)) {
                $this->tableGateway->update($data, array('coupons_id' => $id));
            } else {
                throw new \Exception('id does not exist');
            }
        }
    }

    public function filter($params){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('coupons');
        $where = array('website_id = '.$this->getWebsiteId());
        if($params['coupons_code']){
            $where[] = "coupons_code LIKE '%{$params['coupons_code']}%'";
        }

        if($params['valid_time']){
            $time = $params['valid_time'];
            $time = explode(' _to_ ', $time);
            if(count($time) == 2){
                $where[] = "(expire_date >= '{$time[0]}' AND start_date <= '{$time[1]}')";
            }
        }
        if($params['coupons_type'] != -1){
            $where[] = "coupons_type={$params['coupons_type']}";
        }
        $where = implode(' AND ', $where);
        $select->where($where);
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            return $result;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function autoAdd($data = array()){
        $quantity = $data['total_code'];
        if($quantity){
            $insert = "INSERT INTO `coupons`(website_id,coupons_code,coupons_type,coupons_max_use,start_date,expire_date,min_price_use,max_price_use,coupon_price,coupon_percent,is_published) VALUES";
            $value = array();
            $code_list = array();
            while(count($code_list) < $quantity){
                $code = $this->randText(10);
                if(!in_array($code, $code_list)){
                    $code_list[] = $code;
                    $data['min_price_use'] = str_replace(',','', $data['min_price_use']);
                    $data['max_price_use'] = str_replace(',','', $data['max_price_use']);
                    $data['coupon_price'] = str_replace(',','', $data['coupon_price']);
                    $data['coupon_percent'] = $data['coupon_percent'] ? $data['coupon_percent'] : 0;
                    $data['coupons_max_use'] = $data['coupons_max_use'] ? $data['coupons_max_use'] : 0;
                    $value[] = "('{$this->getWebsiteId()}','{$code}',{$data['coupons_type']},{$data['coupons_max_use']},'{$data['start_date']}','{$data['expire_date']}',{$data['min_price_use']},{$data['max_price_use']},{$data['coupon_price']},{$data['coupon_percent']},{$data['is_published']})";
                }
            }
            $value = implode(',', $value);
            $insert .= $value;
            try{
                $adapter = $this->tableGateway->getAdapter();
                $adapter->query($insert,$adapter::QUERY_MODE_EXECUTE);
            }catch(\Exception $ex){
                throw new \Exception($ex);
            }
        }
    }

    public function getLogsCoupon( $params = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('date_use' => 'date_create','id','price_used','coupon_price'));
        $select->from('invoice_coupon');
        $select->join('invoice', 'invoice_coupon.invoice_id=invoice.invoice_id',array('invoice_code' => 'invoice_title','date_create'));
        if( isset($params['coupon_id']) ){
            $select->where(array(
                'invoice_coupon.coupon_id' => $params['coupon_id']
            ));
        }
        if( $this->hasPaging( $params ) ){
            $select->offset($this->getOffsetPaging($params['page'], $params['limit']));
            $select->limit($params['limit']);
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString,$adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch(\Exception $ex){}
        return array();
    }

    public function getTotalLogsCoupon( $params = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(*)")));
        $select->from('invoice_coupon');
        $select->join('invoice', 'invoice_coupon.invoice_id=invoice.invoice_id',array('invoice_code' => 'invoice_title','date_create'));
        if( isset($params['coupon_id']) ){
            $select->where(array(
                'invoice_coupon.coupon_id' => $params['coupon_id']
            ));
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){
            return 0;
        }
        return 0;
    }

    protected function getIdCol(){
        return 'coupons_id';
    }

    public function deleteCoupons($ids)
    {
        $this->tableGateway->delete(array('coupons_id' => $ids));
    }

    public function updateCoupons($ids, $data)
    {
        $this->tableGateway->update($data, array('coupons_id' => $ids));
    }

}