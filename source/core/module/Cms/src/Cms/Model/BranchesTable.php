<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/9/14
 * Time: 11:01 AM BranchesTable
 */
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class BranchesTable extends AppTable
{
    public function fetchAll( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('branches');
        $select->where(array(
            'branches.website_id' => $this->getWebsiteId()
        ));

        if(isset($params['branches_title'])){
            $select->where->like('branches_title', "%{$params['branches_title']}%");
        }

        if(isset($params['address'])){
            $select->where->like('address', "%{$params['address']}%");
        }

        if(isset($params['phone'])){
            $select->where->like('phone', "%{$params['phone']}%");
        }

        if(isset($params['email'])){
            $select->where->like('email', "%{$params['email']}%");
        }

        $select->order(array(
            'branches.ordering' => 'ASC',
        ));
        
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

    public function countAll( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(branches.branches_id)")));
        $select->from('branches');
        $select->where(array(
            'branches.website_id' => $this->getWebsiteId()
        ));

        if(isset($params['branches_title'])){
            $select->where->like('branches_title', "%{$params['branches_title']}%");
        }

        if(isset($params['address'])){
            $select->where->like('address', "%{$params['address']}%");
        }

        if(isset($params['phone'])){
            $select->where->like('phone', "%{$params['phone']}%");
        }

        if(isset($params['email'])){
            $select->where->like('email', "%{$params['email']}%");
        }

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }

    public function getById($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('branches_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
	public function update_isdefault($ids,$data){
		try{
			$result=$this->tableGateway->update($data, array("website_id" => $ids));
			return TRUE;
		}catch(\Exception $ex){
			throw new \Exception($ex->getMessage());
		}
    }
    public function saveBranch(Branches $branch)
    {
        $data = array(
            'website_id' => $this->getWebsiteId(),
            'branches_title' => $branch->branches_title,
            'phone' => $branch->phone,
            'website' => $branch->website,
            'email' => $branch->email,
            'description' => $branch->description,
            'address'  => $branch->address,
            'address01'  => $branch->address01,
            'zipcode'  => $branch->zipcode,
            'longitude'  => $branch->longitude,
            'latitude'  => $branch->latitude,
            'country_id'  => $branch->country_id,
            'city'  => $branch->city,
            'state'  => $branch->state,
            'suburb'  => $branch->suburb,
            'region'  => $branch->region,
            'province'  => $branch->province,
            'cities_id'  => $branch->cities_id,
            'districts_id'  => $branch->districts_id,
            'wards_id'  => $branch->wards_id,
            'is_default'  => $branch->is_default,
            'is_published' => $branch->is_published,
            'is_delete' => $branch->is_delete,
            'date_create' => $branch->date_create
        );

        $id = (int)$branch->branches_id;
        $adapter = $this->tableGateway->getAdapter();
        $adapter->getDriver()->getConnection()->beginTransaction();
        try {
            if ($id == 0) {
    			try {
                    $updateOrdering = "UPDATE `branches` SET `ordering` = `ordering`+1 WHERE `website_id` = '{$this->getWebsiteId()}' ";
                    $adapter->query($updateOrdering,$adapter::QUERY_MODE_EXECUTE);

    				$this->tableGateway->insert($data);
                    $id = $this->getLastestId();
    			} catch (\Exception $ex) {
    				throw new \Exception($ex->getMessage());
    			}
            } else {
                if ($this->getById($id)) {
                    $this->tableGateway->update($data, array('branches_id' => $id));
                } else {
                    throw new \Exception('Category id does not exist');
                }
            }
            $adapter->getDriver()->getConnection()->commit();
            return $id;
        } catch (\Exception $ex) {
            $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
    }

    public function updateOrder($data){
        $sql = "INSERT INTO branches(branches_id, ordering) VALUES ";
        $val = array();
        foreach($data as $id => $order){
            $val[] = "({$id}, {$order})";
        }
        $val = implode(',', $val);
        $sql .= $val;
        $sql .= " ON DUPLICATE KEY UPDATE ordering=VALUES(ordering)";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function getLastestId()
    {
        return $this->tableGateway->getLastInsertValue();
    }

	protected function getIdCol()
    {
        return 'branches_id';
    }
	protected function getOrderCol()
    {
        return 'ordering';
    }

    public function deleteBranchs($ids)
    {
        $this->tableGateway->delete(array('branches_id' => $ids));
    }

    public function updateBranchs($ids, $data)
    {
        $this->tableGateway->update($data, array('branches_id' => $ids));
    }
}