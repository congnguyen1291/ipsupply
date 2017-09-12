<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;


class DbTable extends AbstractTableGateway {

    protected $table    = null;
    protected $primary  = null;
    protected $entity   = null;
    protected $metadata = null;

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->initialize();
    }

    /**
     * Lấy danh sách column trong table
     *
     * @return multitype:
     * @author Giau Le
     */
    public function getColumnsInTable() {
        $columns = array();
        $adapter = $this->adapter;
        $query = "DESCRIBE {$this->table}";
        $columnObjects = $adapter->query($query, $adapter::QUERY_MODE_EXECUTE);
        foreach ($columnObjects as $columnObject) {
            $columns[] = $columnObject->Field;
        }
        return $columns;
    }

    /**
     * Lọc lại data theo column
     *
     * @param array $data
     * @return multitype:unknown
     * @author Giau Le
     */
    public function filterColumns($data) {
        $dataFormat = array();

        $columns = $this->getColumnsInTable();

        foreach ($data as $column => $value) {
            if (in_array($column, $columns)) {
                $dataFormat[$column] = $value;
            }
        }
        return $dataFormat;
    }

    /**
     * fetch all
     *
     * @param Zend\Db\Sql\Sql $select
     * @return \Zend\Db\ResultSet\ResultSet
     * @author Giau Le
     */
    public function fetchAll($select = null) {
        $statement = $this->adapter->createStatement();
        $select->prepareStatement($this->adapter, $statement);

        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet;
    }

    /**
     * fetch row
     *
     * @param Zend\Db\Sql\Sql $select
     * @return \Zend\Db\ResultSet\ResultSet
     * @author Giau Le
     */
    public function fetchRow($select = null) {
        $statement = $this->adapter->createStatement();
        $select->prepareStatement($this->adapter, $statement);

        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return $resultSet->current();
    }

    /**
     * Enter description here ...
     * @param unknown $id
     * @return boolean|Ambigous <multitype:, ArrayObject, NULL, \ArrayObject, unknown>
     * @author Giau Le
     */
    public function getItem($id) {
        $row = $this->select(array($this->primary => (int) $id))->current();
        if (!$row)
            return false;
        return $row;
    }

    /**
     * remove item
     *
     * @param int $id
     * @return boolean|number
     * @author Giau Le
     */
    public function removeItem($id) {
        $id = (int) $id;
        if (!$id)
            return false;
        $data = array(
            'IsDeleted'    => 1,
        );
        return $this->update($data, array($this->primary => $id));
    }
    
    protected function toAlias($txt) {
    	if ($txt == '')
    		return '';
    	$marked = array ("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă",
    
    			"ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề",
    
    			"ế", "ệ", "ể", "ễ", "ế",
    
    			"ì", "í", "ị", "ỉ", "ĩ",
    
    			"ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ",
    
    			"ờ", "ớ", "ợ", "ở", "ỡ",
    
    			"ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
    
    			"ỳ", "ý", "ỵ", "ỷ", "ỹ",
    
    			"đ",
    
    			"À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă",
    
    			"Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
    
    			"È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
    
    			"Ì", "Í", "Ị", "Ỉ", "Ĩ",
    
    			"Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ",
    
    			"Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
    
    			"Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
    
    			"Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
    
    			"Đ",
    
    			" ", "&", "?", "/", ".", ",", "$", ":", "(", ")", "'", ";", "+", "–", "’" );
    
    	$unmarked = array ("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a",
    
    			"a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e",
    
    			"e", "e", "e", "e", "e",
    
    			"i", "i", "i", "i", "i",
    
    			"o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o",
    
    			"o", "o", "o", "o", "o",
    
    			"u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
    
    			"y", "y", "y", "y", "y",
    
    			"d",
    
    			"A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A",
    
    			"A", "A", "A", "A", "A",
    
    			"E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
    
    			"I", "I", "I", "I", "I",
    
    			"O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O",
    
    			"O", "O", "O", "O", "O",
    
    			"U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
    
    			"Y", "Y", "Y", "Y", "Y",
    
    			"D",
    
    			"-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-" );
    
    	$tmp3 = (str_replace ( $marked, $unmarked, $txt ));
    	$tmp3 = rtrim ( $tmp3, "-" );
    	$tmp3 = preg_replace ( array ('/\s+/', '/[^A-Za-z0-9\-]/' ), array ('-', '' ), $tmp3 );
    	$tmp3 = preg_replace ( '/-+/', '-', $tmp3 );
    	$tmp3 = strtolower ( $tmp3 );
    	return $tmp3;
    }
}