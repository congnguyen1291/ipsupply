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

class ContactTable extends AppTable{

    public function save(Contact $contact){
        $data = array(
            'website_id'=>$this->getWebsiteId(),
            'title'  => $contact->title,
            'full_name'      => $contact->full_name,
            'content'      => $contact->content,
            'email'      => $contact->email,
            'phone'      => $contact->phone,
            'type'      => $contact->type,
            'date_create'      => $contact->date_create,
            'is_viewed'      => $contact->is_viewed,
        );
        try {
            $this->tableGateway->insert($data);
        }catch(\Exception $ex) {
            return false;
        }
        return TRUE;
    }
	public function saveContactPopup($name, $content, $phone, $email, $page_request, $utm_source, $utm_campaign, $utm_medium){

		$adapter = $this->tableGateway->getAdapter();
		$adapter->getDriver()->getConnection()->beginTransaction();
		$sql = new Sql($adapter);
		$insert = $sql->insert();
		$insert->into('website_contact');
		$data=array('website_id' => $this->getWebsiteId(), 'fullname' => $name, 'email' => $email, 'telephone' => $phone, 'description' => $content, 'link' => $page_request, 'utm_source' => $utm_source, 'utm_campaign' => $utm_campaign, 'utm_medium' => $utm_medium);
        $insert->columns(array('website_id','fullname','email','telephone','description','link','utm_source','utm_campaign','utm_medium'));  
        try {
			$insert->values($data);
			$insertString = $sql->getSqlStringForSqlObject($insert); 
			$result=$adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
			$adapter->getDriver()->getConnection()->commit();
        }catch(\Exception $ex) {
			  $adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
            //return false;
        }
        return TRUE;
    }
} 