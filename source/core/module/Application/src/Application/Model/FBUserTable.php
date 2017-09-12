<?php
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
use Zend\Db\Adapter\ParameterContainer;

use Application\Model\AppTable;

class FBUserTable  extends AppTable {

    public function getUser($id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $temp = $this->createKeyCacheFromArray($id);
        $key = md5($this->getNamspaceCached().':FBUserTable:getUser('.$temp.')');
        $results = $cache->getItem($key);
        if( !$results ){
            $adapter = $this->adapter;
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('fb_user');
            $select->where(array(
                'fb_user.id' => $id,
                'fb_user.website_id' => $this->getWebsiteId()
            ));
            $select->order('fb_user.id desc');
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

    public function insertOrUpdate( $insertData, $updateData)
    {
        try{
            if( !empty($insertData) 
                && !empty($updateData) ){
                $sqlStringTemplate = 'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s';
                $adapter = $this->adapter;
                $driver = $adapter->getDriver();
                $platform = $adapter->getPlatform();

                $tableName = $platform->quoteIdentifier('fb_user');
                $parameterContainer = new ParameterContainer();
                $statementContainer = $adapter->createStatement();
                $statementContainer->setParameterContainer($parameterContainer);

                /* Preparation insert data */
                $insertQuotedValue = [];
                $insertQuotedColumns = [];
                foreach ($insertData as $column => $value) {
                    $insertQuotedValue[] = $driver->formatParameterName($column);
                    $insertQuotedColumns[] = $platform->quoteIdentifier($column);
                    $parameterContainer->offsetSet($column, $value);
                }

                /* Preparation update data */
                $updateQuotedValue = [];
                foreach ($updateData as $column => $value) {
                    $updateQuotedValue[] = $platform->quoteIdentifier($column) . '=' . $driver->formatParameterName( $column);
                    $parameterContainer->offsetSet( $column, $value);
                }

                $query = sprintf(
                    $sqlStringTemplate,
                    $tableName,
                    implode(',', $insertQuotedColumns),
                    implode(',', array_values($insertQuotedValue)),
                    implode(',', $updateQuotedValue)
                );
                $statementContainer->setSql($query);
                return $statementContainer->execute();
            }
        }catch(\Exception $e ){}
    }

}