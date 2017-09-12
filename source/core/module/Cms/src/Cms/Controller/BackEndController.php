<?php

namespace Cms\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class BackEndController extends AbstractActionController
{
    protected $data_view = array();
    protected $intPage = 0;
    protected $intPageSize = 50;
    protected $domain = '';
    protected $baseUrl = '';
    protected $protocol = '';
    protected $website = array();
    protected $website_id = ID_MASTERPAGE;
    protected $languages = array();
    protected $languages_id = 1;

    public function __construct()
    {
        if(!isset($_SESSION['CMSMEMBER'])){
            header("Location:".FOLDERWEB.'/cms/login');
            exit();
        }
    }

    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $this->domain = $_SERVER['HTTP_HOST'];
        $this->protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $this->baseUrl = $this->protocol.''.$this->domain;
		
        if (substr($this->domain, 0, 4) == "www.")
            $this->domain = substr($this->domain, 4);
        if(!empty($this->domain)){
            $this->website = $this->getModelTable('WebsitesTable')->getWebsite($this->domain);
            $this->data_view['website'] = $this->website;
        }
		
        if(empty($this->website)){
            if(isset($_SESSION['CMSMEMBER'])){
                unset($_SESSION['CMSMEMBER']);
            }
            return $this->redirect()->toRoute('index');
        }
        $this->data_view['domain'] = $this->domain;
        $this->data_view['protocol'] = $this->protocol;
        $this->data_view['baseUrl'] = $this->baseUrl;
        if(!isset($_SESSION['CMSMEMBER'])){
            if(isset($_SESSION['CMSMEMBER'])){
                unset($_SESSION['CMSMEMBER']);
            }
            return $this->redirect()->toUrl('/cms/login?redirect='.$_SERVER["HTTP_REFERER"]);
        }
        $_SESSION['website_id'] = $this->website->website_id;
        $_SESSION['domain'] = $this->domain;
        $_SESSION['protocol'] = $this->protocol;
        $_SESSION['baseUrl'] = $this->baseUrl;
        $_SESSION['website'] = $this->website;
       
        if($_SESSION['CMSMEMBER']['type'] == 'admin'){
            $current_controller = $this->getEvent(  )->getRouteMatch()->getParam('controller');
            $current_controller = explode('\\', $current_controller);
            if( empty($_SESSION['CMSMEMBER']['is_administrator']) ){
                if( count($current_controller) == 3){
                    $current_module = strtolower($current_controller[0]);
                    $current_controller = strtolower($current_controller[2]);
                    $current_action = strtolower($this->getEvent()->getRouteMatch()->getParam('action'));
                    if( empty($_SESSION['CMSMEMBER']['permissions']) ){
                        $group_permissions = array();
                        //if($_SESSION['CMSMEMBER']['is_administrator'] == 1){
                            //$group_permissions = $this->getModelTable('PermissionsTable')->getGroupPermissionsForAdmin();
                        //}else{
                            $group_permissions = $this->getModelTable('PermissionsTable')->getGroupPermissionsForUser($_SESSION['CMSMEMBER']['groups_id']);
                        //}
                        $data_permissions = array();
                        foreach($group_permissions as $permit){
                            $data_permissions[$permit['module']][$permit['controller']][$permit['action']] = 1;
                        }
                        
                        $_SESSION['CMSMEMBER']['permissions'] = $data_permissions;
                    }
                    if(!isset($_SESSION['CMSMEMBER']['permissions'][$current_module][$current_controller][$current_action]) && !($current_controller == 'index' && $current_module == 'cms' && $current_action == 'index')){
                        return $this->redirect()->toRoute('cms');
                    }
                }else{
                    if(isset($_SESSION['CMSMEMBER'])){
                        unset($_SESSION['CMSMEMBER']);
                    }
                    return $this->redirect()->toUrl('/cms/login?redirect='.$_SERVER["HTTP_REFERER"]);
                }
            }
        }else{
            if(isset($_SESSION['CMSMEMBER'])){
                unset($_SESSION['CMSMEMBER']);
            }
            return $this->redirect()->toUrl('/cms/login?redirect='.$_SERVER["HTTP_REFERER"]);
        }

        $lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi_VN';
        $sm = $this->getServiceLocator();
        $translator = $sm->get('translator');
        $path_lang = LANG_PATH.'/'.$lang.'.php';
        $translator->addTranslationFile("phparray",$path_lang);

        $sm->get('ViewHelperManager')->get('translate')
            ->setTranslator($translator);

        $this->data_view['new_invoice'] = $this->getModelTable('InvoiceTable')->countNewInvoice();
        $this->data_view['new_question'] = $this->getModelTable('SettingTable')->countAllNewQuestion();
        $this->languages = $this->getModelTable('LanguagesTable')->getLanguages();
        $this->data_view['languages'] = $this->languages;
        $this->languages_id = (!empty($_SESSION['CMSMEMBER']) && !empty($_SESSION['CMSMEMBER']['languages_id'])) ? $_SESSION['CMSMEMBER']['languages_id'] : 1;
        $this->data_view['languages_id'] = $this->languages_id;
        $_SESSION['CMSMEMBER']['languages_id'] = $this->languages_id;
        return parent::onDispatch($e);
    }

    protected function treerecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1, $id_col, $parentid_col)
    {
        if (@$children [$id] && $level <= $maxlevel) {
            foreach ($children [$id] as $v) {
                $id = $v [$id_col];
                if ($type) {
                    $pre = '';
                    $spacer = '|____';
                } else {
                    $pre = '- ';
                    $spacer = '__';
                }
                if ($v [$parentid_col] == 0) {
                    $txt = $v ['name'];
                } else {
                    $txt = $pre . $v ['name'];
                }
                $list [$id] = $v;
                $list [$id] ['treename'] = "$indent$txt";
                $list [$id] ['children'] = count(@$children [$id]);
                $list = self::treerecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type, $id_col, $parentid_col);
            }
        }
        return $list;
    }

    protected function multiLevelData($return_data = TRUE, $rows, $id_col, $parentid_col, $title_col, $root_title = '__ROOT__')
    {
        if ($rows) {
            $children = array();
            foreach ($rows as $v) {
                if( is_object($v) ){
                    $v = get_object_vars($v);
                }
                $pt = $v[$parentid_col] ? $v[$parentid_col] : 0;
                $v ['name'] = $v [$title_col];
                $list = @$children [$pt] ? $children [$pt] : array();
                array_push($list, $v);
                $children [$pt] = $list;
            }

            $list = self::treerecurse(0, '', array(), $children, 10, 0, 1, $id_col, $parentid_col);
            if (!$return_data) {
                return $list;
            }
            $data[0] = $root_title;
            foreach ($list as $category) {
                $data[$category [$id_col]] = $category ['treename'];
            }
            return $data;
        }
        return array();
    }

    protected function getModelTable($name)
    {
        if (!isset($this->{$name})) {
            $this->{$name} = NULL;
        }
		
        if (!$this->{$name}) {
            $sm = $this->getServiceLocator();
            $this->{$name} = $sm->get('Cms\Model\\' . $name);
        }
		
        return $this->{$name};
    }
   protected function arrayToObject($d) {
        if (is_array($d)) {
            return (object) array_map(__FUNCTION__, $d);
        }
        else {
            return $d;
        }
    }
    protected function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    protected function  randText($characters)
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

    protected function creator_folder($upload_dir, $folder)
    {
        return @mkdir($upload_dir . "/" . $folder . "/", 0777);
    }

    protected function toAlias($txt, $tr = '-') {
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
        if( !empty($tr) ){
            $tmp3 = (str_replace ( '-', $tr, $tmp3 ));
        }
        return $tmp3;
    }

    protected function getStringUpcaseFriendly($txt) {
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

            "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_", "_" );

        $tmp3 = (str_replace ( $marked, $unmarked, $txt ));
        $tmp3 = rtrim ( $tmp3, "_" );
        $tmp3 = preg_replace ( array ('/\s+/', '/[^A-Za-z0-9_]/' ), array ('_', '' ), $tmp3 );
        $tmp3 = preg_replace ( '/_+/', '_', $tmp3 );
        $tmp3 = strtoupper ( $tmp3 );
        return $tmp3;
    }

    protected function isMasterPage() {
        if(empty($this->website) || $this->domain == MASTERPAGE )
            return TRUE;
        return FALSE;
    }

    /*protected function containsArray($array){
        foreach($array as $value){
            if(is_array($value)) {
              return true;
            }
        }
        return false;
    }*/

    protected function containsArray($array){
        $isArray = TRUE;
        $sum_ = 0;
        foreach($array as $k=>$value){
            if( !is_int($k) ){
                return FALSE;
                break;
            }else{
                $sum_ += ($k+1);
            }
        }
        if( $sum_ !=  (count($array)*(1+count($array)))/2 ){
            $isArray = FALSE;
        }
        return $isArray;
    }


    protected function getStringFomatJsonFriently($data, $str_tab = "\t", $is_ob = TRUE) {
        $str = '';
        $i = 0;
        foreach ($data as $key => $idata) {
            if( is_string($idata) ){
                $str .= "\n".$str_tab."\"".$key."\":\"".$idata."\"".( ($i >= (count($data)-1)) ? "" : "," );
            }else if( is_object($idata) ){
                if( !$is_ob ){
                    $str .= "\n".$str_tab."{".$this->getStringFomatJsonFriently($idata, $str_tab."\t", TRUE)."\n".$str_tab."}".( ($i >= (count($data)-1)) ? "" : "," );
                }else{
                    $str .= "\n".$str_tab."\"".$key."\":{".$this->getStringFomatJsonFriently($idata, $str_tab."\t", TRUE)."\n".$str_tab."}".( ($i >= (count($data)-1)) ? "" : "," );
                }
            }else if( is_array($idata) ){
                if( $this->containsArray($idata)){
                    $str .= "\n".$str_tab."\"".$key."\":[".$this->getStringFomatJsonFriently($idata, $str_tab."\t", FALSE)."\n".$str_tab."]".( ($i >= (count($data)-1)) ? "" : "," );
                }else{
                    if( !$is_ob ){
                        $str .= "\n".$str_tab."{".$this->getStringFomatJsonFriently($idata, $str_tab."\t", TRUE)."\n".$str_tab."}".( ($i >= (count($data)-1)) ? "" : "," );
                    }else{
                        $str .= "\n".$str_tab."\"".$key."\":{".$this->getStringFomatJsonFriently($idata, $str_tab."\t", TRUE)."\n".$str_tab."}".( ($i >= (count($data)-1)) ? "" : "," );
                    }
                }
            }else{
                $str .= "\n".$str_tab."\"".$key."\":".$idata.( ($i >= (count($data)-1)) ? "" : "," );
            }
            $i ++;
        }
        return $str;
    }

    protected function updateNamespaceCached() {
        $namespace = date("YmdHis");
        if(!$this->isMasterPage()){
            $name_folder = $this->website['websites_folder'];
            try{
                $url_config = PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/config.json';
                if(is_file($url_config)){
                    $config = file_get_contents($url_config);
                    $config = json_decode($config, true);

                    if(!empty($config['cached']) && !empty($config['cached']['namespace']) ){
                        $config['cached']['namespace'] = $namespace;
                    }else{
                        $config['cached'] = array('type'=>'memcache', 'time' => 86400, 'namespace' => $namespace);
                    }
                    $str = "{".$this->getStringFomatJsonFriently($config, "\t", TRUE)."}";
                    //$config = json_encode($config);
                    //$text="\t\n Your text to write \n\t\t ".date('d')."\n\t\t\t-".date('m')."\n\t\t\t\t-".date('Y')."\n\n";
                    $fp = fopen($url_config, 'w+');
                    fwrite($fp, $str);
                    fclose($fp);
                    //file_put_contents($url_config, $config);
                }
            }catch(\Exception $ex){}
        }
        return $namespace;
    }

    protected function isUrlHttp($url) {
        if(substr($url, 0, 5) == 'https' || substr($url, 0, 4) == 'http' || substr($url, 0, 3) == 'ftp' || substr($url, 0, 2) == '//') 
        { 
            return true; 
        }
        return false;
    }

    public function getWebsiteId(){
        return $this->website_id;
    }

    public function isImage($url){
        $data = explode('.', $url);
        $ext = end($data);
        if(strpos('?', $ext)){
            $ext = explode('?', $ext);
            if(count($ext) > 2){
                $ext = $ext[0];
            }
        }
        $ext = strtolower($ext);
        $image_ext = array(
            'jpeg','png','jpg','gif','ico',
        );
        if(in_array($ext,$image_ext)){
            return TRUE;
        }
        return FALSE;
    }

    protected function file_name($file_name) {
        $list = explode ( '.', $file_name );
        $dot = count ( $list );
        unset($list [$dot - 1]);
        return implode('-', $list);
    }
    
    protected function file_extension($file_name) {
        $list = explode ( '.', $file_name );
        $file_ext = strtolower(end($list));
        return $file_ext;
    }

}