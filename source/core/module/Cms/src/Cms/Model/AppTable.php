<?php
namespace Cms\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AppTable extends AbstractTableGateway implements ServiceLocatorAwareInterface
{
    protected $table = null;
    public $tableGateway;
    protected $adapter = null;
    protected $website_id = 1;
    protected $country_id = 43;
    protected $languages_id = 1;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        $this->adapter = $this->tableGateway->getAdapter();
        if( !empty($_SESSION['website_id']) ){
            $this->website_id = $_SESSION['website_id'];
        }
        if( !empty($_SESSION['country_id']) ){
            $this->country_id = $_SESSION['CMSMEMBER']['country_id'];
        }
        if( !empty($_SESSION['CMSMEMBER']['languages_id']) ){
            $this->languages_id = $_SESSION['CMSMEMBER']['languages_id'];
        }
    }

    private  $sm = NULL;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->sm;
    }

    public function createKeyCacheFromArray($arr)
    {
        if(is_array($arr)){
            foreach ($arr as $key => $value) {
                if(is_array($value)){
                    $value = $key.';'.$this->createKeyCacheFromArray($value);
                    $arr[$key] = $value;
                }
            }
            return implode("-",$arr);
        }else{
            return $arr;
        }
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

    protected function toAlias($txt, $plice = '-') {
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
        if( $plice != '-' ){
            $tmp3 = (str_replace ( '-' , $plice, $tmp3 ));
        }
        return $tmp3;
    }

    protected function getModelTable($name) {
        $sm = $this->getServiceLocator ();
        $model = $sm->get ( 'Cms\Model\\' . $name );
        return $model;
    }

    public function getLastestId(){
        return $this->tableGateway->getLastInsertValue();
    }

    protected function saveData($data, $id)
    {
        try {
            if (!$id) {
                $sql = 'SELECT max(ordering) as current_order FROM ' . $this->tableGateway->table . ' WHERE parent_id=' . $data['parent_id'];
                $adapter = $this->tableGateway->getAdapter();
                $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                $row = $result->current();
                if ($row) {
                    $data['ordering'] = $row->current_order + 1;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->getLastestId();
        } else {
            if ($this->getById($id)) {
                $this->tableGateway->update($data, array($this->getIdCol() => $id));
            } else {
                throw new \Exception('id does not exist');
            }
        }
        return $id;
    }

    public function hasLocation(){
        if( !empty($_SESSION['LOCATION'])
            && (    !empty($_SESSION['LOCATION']['country_id'])
                    || !empty($_SESSION['LOCATION']['cities_id'])
                    || !empty($_SESSION['LOCATION']['districts_id'])
                    || !empty($_SESSION['LOCATION']['wards_id'])) ){
            return TRUE;
        }
        return FALSE;
    }

    public function getLocation(){
        return $_SESSION['LOCATION'];
    }

    public function getWebsiteId(){
        return $this->website_id;
    }

    public function getCountryId(){
        return $this->country_id;
    }

    public function getLanguagesId(){
        return $this->languages_id;
    }

    public function getStringQueryNotInLocation(){
        $str = '';
        if( $this->hasLocation() ){
            $where = '';
            $str = 'Select products_target.products_id FROM products_target ';
            if( !empty($_SESSION['LOCATION']['country_id']) ){
                $str .= 'LEFT JOIN country ON FIND_IN_SET(country.id ,products_target.country_id)>0 ';
                $where .= '(  country.id != '.$_SESSION['LOCATION']['country_id'].'  AND FIND_IN_SET('.$_SESSION['LOCATION']['country_id'].' ,products_target.country_id) <=0 )';
            }
            if( !empty($_SESSION['LOCATION']['cities_id']) ){
                $str .= 'LEFT JOIN cities ON FIND_IN_SET(cities.cities_id ,products_target.city_id)>0 ';
                $where .= (empty($where) ? '' : 'OR ') .'( cities.cities_id != '. $_SESSION['LOCATION']['cities_id'].'  AND FIND_IN_SET('. $_SESSION['LOCATION']['cities_id'].' ,products_target.city_id) <=0  )';
            }
            if( !empty($_SESSION['LOCATION']['districts_id']) ){
                $str .= 'LEFT JOIN districts ON FIND_IN_SET(districts.districts_id ,products_target.district_id)>0 ';
                $where .= (empty($where) ? '' : 'OR ') .'( districts.districts_id != '. $_SESSION['LOCATION']['districts_id'].' AND FIND_IN_SET('. $_SESSION['LOCATION']['districts_id'].' ,products_target.district_id) <=0 )';
            }
            $str = $str.'WHERE '.$where;
        }
        return $str;
    }

    protected function getNamspaceCached() {
        $str = '';
        if(!empty($_SESSION['domain'])){
            $str = $_SESSION['domain'];
        }
        if(!empty($_SESSION['lang'])){
            $str .= ':'.$_SESSION['lang'];
        }
        $str .= ':Application';
        if( !empty($_SESSION['config'])
            && !empty($_SESSION['config']['cached']) 
            && !empty($_SESSION['config']['cached']['namespace']) ){
            $str .= '_'. $_SESSION['config']['cached']['namespace'];
        }
        if( $this->hasLocation() ){
            $str .= ':location_'.$_SESSION['LOCATION']['country_id'] .'_'. $_SESSION['LOCATION']['cities_id'] .'_'. $_SESSION['LOCATION']['districts_id'] .'_'. $_SESSION['LOCATION']['wards_id'];
        }
        //echo $str;die();
        return $str;
    }

    protected function hasPaging( $params ) {
        if( isset($params['page']) && !empty($params['limit']) ){
            return TRUE;
        }
        return FALSE;
    }

    protected function getOffsetPaging( $page, $limit ) {
        $ipage = 0;
        if ( $page > 1) {
            $ipage = ($page - 1) * $limit;
        }
        return $ipage;
    }

}