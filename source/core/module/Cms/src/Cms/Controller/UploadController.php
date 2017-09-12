<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 11:50 AM
 */

namespace Cms\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class UploadController extends AbstractActionController{
    
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
    public function html5uploadAction(){
        $request = $this->getRequest();
        $items = array('flag' =>'false', 'msg' => 'Not found');
        if ($request->isPost()) {
            $website_id = $_SESSION['website']['website_id'];
            $domain= md5($_SESSION['website']['website_domain'].':'.$website_id);
            $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
            if(!is_dir($websiteFolder)){
                @mkdir ( $websiteFolder, 0777 );
            }

            $size = $_FILES['uploadfile']['size'];
            if($size > 2097152)
            {
                @unlink($_FILES['uploadfile']['tmp_name']);
                $items = array( 'flag' =>'false', 'msg' => 'Limit Picture 2M');
            }else{

                $temp = preg_split ( '/[\/\\\\]+/', $_FILES ['uploadfile'] ["name"] );
                $filename = $temp [count ( $temp ) - 1];
                if (!empty($filename)) {
                    $name = $this->file_name ( $filename );
                    $extention = $this->file_extension ( $filename );
                    $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                    if(is_file($upload_url)){
                        $name = $name.'-'.date("YmdHis");
                        $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                    }
                    if (copy ( $_FILES ['uploadfile']["tmp_name"], $upload_url )) {
                        chmod ( $upload_url, 0777 );
                        $data = file_get_contents($upload_url);
                        //$base64 = 'data:image/' . $extention . ';base64,' . base64_encode($data);
                        $base64 = '';

                        $row = array('website_id' => $website_id,
                                        'users_id' => 0,
                                        'id_album' => 0,
                                        'full_name' => $name.'.'.$extention,
                                        'name' => $name,
                                        'string_data' => $base64,
                                        'folder' => '/custom/domain_1/' . $domain,
                                        'caption' => $name,
                                        'type' => $extention,
                                        'order' => 0,
                                        'detector' => 0,
                                        'number_comment' => 0);
                        $picture = $this->getModelTable('PictureTable')->savePicture($row);
                        $items = array('flag' =>'true', 'file' => $name.'.'.$extention,'url' => '/custom/domain_1/' . $domain.'/'.$name.'.'.$extention);
                    }
                }else{
                    $items = array('status' => 'fall','msg' => 'File not exit');
                }
            }

        }
        $result = new JsonModel($items);
        return $result;
    }

    public function ckuploadAction(){
        $request = $this->getRequest();
        $website_id = $_SESSION['website']['website_id'];
        $domain= md5($_SESSION['website']['website_domain'].':'.$website_id);
        $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
        if(!is_dir($websiteFolder)){
            @mkdir ( $websiteFolder, 0777 );
        }
        
        $size = $_FILES['upload']['size'];
        if($size > 2097152)
        {
            @unlink($_FILES['upload']['tmp_name']);
            echo "error file size > 2 MB";
            exit;
        }else{

            $temp = preg_split ( '/[\/\\\\]+/', $_FILES ['upload'] ["name"] );
            $filename = $temp [count ( $temp ) - 1];
            if (!empty($filename)) {
                $name = $this->file_name ( $filename );
                $extention = $this->file_extension ( $filename );
                $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                if(is_file($upload_url)){
                    $name = $name.'-'.date("YmdHis");
                    $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                }
                if (copy ( $_FILES ['upload']["tmp_name"], $upload_url )) {
                    chmod ( $upload_url, 0777 );
                    $data = file_get_contents($upload_url);
                    //$base64 = 'data:image/' . $extention . ';base64,' . base64_encode($data);
                    $base64 = '';

                    $row = array('website_id' => $website_id,
                                    'users_id' => 0,
                                    'id_album' => 0,
                                    'full_name' => $name.'.'.$extention,
                                    'name' => $name,
                                    'string_data' => $base64,
                                    'folder' => '/custom/domain_1/' . $domain,
                                    'caption' => $name,
                                    'type' => $extention,
                                    'order' => 0,
                                    'detector' => 0,
                                    'number_comment' => 0);
                    $picture = $this->getModelTable('PictureTable')->savePicture($row);

                    $funcNum = $_GET['CKEditorFuncNum'] ;
                    // Optional: instance name (might be used to load a specific configuration file or anything else).
                    $CKEditor = $_GET['CKEditor'] ;
                    // Optional: might be used to provide localized messages.
                    $langCode = $_GET['langCode'] ;
                    $url = '/custom/domain_1/' . $domain.'/'.$name.'.'.$extention;
                    echo "<script type='text/javascript'>
                                window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', function(){
                                    /*window.parent.document.getElementById('folder').value = '';*/
                                });
                         </script>";
                    die();
                }
            }else{
                @unlink($_FILES['upload']['tmp_name']);
                echo "File not exit";
                exit;
            }
        }
        echo "File not exit";
        exit;
    }

    protected function upload_file_copy($tagname, $upload_dir, $ex_name, $folder = '') {
        $temp = preg_split ( '/[\/\\\\]+/', $_FILES [$tagname] ["name"] );
        $filesize = $_FILES [$tagname] ["size"];
        $filename = $temp [count ( $temp ) - 1];
        if ($filename != "") {

            if (! isset ( $_FILES [$tagname] ["error"] ) || $_FILES [$tagname] ["error"] != 0) {
                print ("<script>alert('The uploaded file exceeds max size file.');window.history.back();</script>") ;
                // return false;
                exit ( - 1 );
            } // end if
            $extention = $this->file_extension ( $filename );
            $upload_file = $upload_dir . $folder . "/" . $ex_name;
            // echo $upload_file;exit;
            if (copy ( $_FILES [$tagname] ["tmp_name"], $upload_file )) {
                chmod ( $upload_file, 0777 );
                return $ex_name . "." . $extention;
            }
        }
        return "";
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