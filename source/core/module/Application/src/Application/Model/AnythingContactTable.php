<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/16/14
 * Time: 3:18 PM
 */

namespace Application\Model;
use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Application\Model\AppTable;

class AnythingContactTable extends AppTable{

    public function saveAnythingContact(AnythingContact $anything){
        $row = get_object_vars($anything);
        $id = 0;
        try {
            $this->tableGateway->insert($row);
            $id = $this->getLastestId();
        }catch(\Exception $e ) {
        }
        return $id;
    }

    public function getRow($id) {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':AnythingContactTable:getRow('.$id.')');
        $results = $cache->getItem($key);
        if(!$results){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('website_contact');
            $select->join('service_contact', 'website_contact.id = service_contact.contact_id', array('service_name', 'service_price', 'quantity'), 'left');
            $select->where(array(
                'website_contact.id' => $id,
                'website_contact.website_id'=>$this->getWebsiteId(),
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->current();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function saveServiceContact($contact_id, $service){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert('service_contact');
        $insert->columns(array('contact_id','service_id','service_name','service_price','quantity'));
        $insert->values(array(
            'contact_id' => $contact_id,
            'service_id' => $service['service_id'],
            'service_name' => $service['service_name'],
            'service_price' => $service['service_price'],
            'quantity' => $service['quantity'],
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $ex){
        }
    }

    public function updateData($data, $id){
        if( $this->getRow($id)){
            $this->tableGateway->update($data, array('id' => $id, 'website_id'=>$this->getWebsiteId()));
        }else{
            throw new \Exception('Invoice not exists');
        }
    }

} 