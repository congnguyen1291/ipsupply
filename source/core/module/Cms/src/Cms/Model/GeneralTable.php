<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Cms\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class GeneralTable
{
    protected $tableGateway;
    protected $cache = NULL;
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    protected function randText($characters)
    {
        $possible = '1234567890abcdefghjkmnpqrstvwxyzABCDEFGHJKMNPQRSTVWXYZ';
        $code = '';
        $i = 0;
        while ($i < $characters) {
            $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $i++;
        }
        return $code;
    }

//    public function setMemcache($memcache){
//        if(!$this->cache){
//            $this->cache = $memcache;
//        }
//    }

    public function softUpdateData($ids,$data){
		try{
			$result=$this->tableGateway->update($data, array($this->getIdCol() => $ids));
			return TRUE;
		}catch(\Exception $ex){
			throw new \Exception($ex->getMessage());
		}
    }
    public function updateorderData($data){
        $sql = "INSERT INTO {$this->tableGateway->table}({$this->getIdCol()}, {$this->getOrderCol()}) VALUES ";
        $val = array();
        foreach($data as $id => $order){
            $val[] = "({$id}, {$order})";
        }
        $val = implode(',', $val);
        $sql .= $val;
        $sql .= " ON DUPLICATE KEY UPDATE {$this->getOrderCol()}=VALUES({$this->getOrderCol()})";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($sql,$adapter::QUERY_MODE_EXECUTE);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    protected function getIdCol(){
        return '';
    }
    protected function getOrderCol(){
        return 'ordering';
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

    protected function getNamspaceCached() {
        $str = 'Application';
        if(!empty($_SESSION['domain'])){
            $str = $_SESSION['domain'].':Application';
        }
        if( !empty($_SESSION['config'])
            && !empty($_SESSION['config']['cached']) 
            && !empty($_SESSION['config']['cached']['namespace']) ){
            $str .= '_'. $_SESSION['config']['cached']['namespace'];
        }
        return $str;
    }

}