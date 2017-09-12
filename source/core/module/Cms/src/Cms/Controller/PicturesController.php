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

class PicturesController extends BackEndController{

	public function uploadAction(){
		$request = $this->getRequest();
        $items = array('status' => 'fall','msg' => 'Not found');
		if ($request->isPost()) {
            $website_id = $this->website->website_id;
            $domain= md5($_SESSION['website']['website_domain'].':'.$website_id);
			$websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
			if(!is_dir($websiteFolder)){
				@mkdir ( $websiteFolder, 0777 );
			}

			$size = $_FILES['user_file']['size'][0];
			if($size > 20971520)
			{
				@unlink($_FILES['user_file']['tmp_name'][0]);
				$items = array( 'status' => 'fall','msg' => 'Limit Picture 2M');
			}else{

                $temp = preg_split ( '/[\/\\\\]+/', $_FILES ['user_file'] ["name"][0] );
                $filename = $temp [count ( $temp ) - 1];
                if (!empty($filename)) {
                    $name = $this->file_name ( $filename );
                    $extention = $this->file_extension ( $filename );
                    $name = strtolower($this->toAlias($name));
                    $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                    if(is_file($upload_url)){
                        $name = $name.'-'.date("YmdHis");
                        $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                    }
                    if (copy ( $_FILES ['user_file']["tmp_name"][0], $upload_url )) {
                        chmod ( $upload_url, 0777 );
                        $data = file_get_contents($upload_url);
                        $base64 = 'data:image/' . $extention . ';base64,' . base64_encode($data);

                        $row = array('website_id' => 1,
                                        'users_id' => 0,
                                        'id_album' => 0,
                                        'full_name' => $name.'.'.$extention,
                                        'name' => $name,
                                        'string_data' => '',
                                        'folder' => '/custom/domain_1/' . $domain,
                                        'caption' => $name,
                                        'type' => $extention,
                                        'order' => 0,
                                        'detector' => 0,
                                        'number_comment' => 0);
                        $picture = $this->getModelTable('PictureTable')->savePicture($row);
                        $datajs = array('trash' => false, 'picture' => $picture);
                        $items = array('status' => 'ok','data' => $datajs);
                    }
                }else{
                    $items = array('status' => 'fall','msg' => 'File not exit');
                }
			}

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

		}
        $result = new JsonModel($items);
        return $result;
	}

    
    public function uploadBase64Action(){
        $request = $this->getRequest();
        $items = array('status' => 'fall','msg' => 'Not found');
        if($request->isPost()){
            $website_id = $this->website->website_id;
            $domain= md5($_SESSION['website']['website_domain'].':'.$website_id);
            $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
            if(!is_dir($websiteFolder)){
                @mkdir ( $websiteFolder, 0777 );
            }

            $base64 = $request->getPost('data', '');
            if(!empty($base64)){
                //$data = 'data:image/png;base64,AAAFBfj42Pj4';
                $name = strtolower($_SESSION['website']['website_domain']).'-'.date("YmdHis");
                $extention = 'png';
                $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                $base64 = str_replace('data:image/jpeg;base64,', '', $base64);
                $base64 = str_replace('data:image/png;base64,', '', $base64);
				$base64 = str_replace(' ', '+', $base64);
                $data = base64_decode($base64);
                file_put_contents($upload_url, $data);

                $base64 = 'data:image/' . $extention . ';base64,' . $base64;

                $row = array('website_id' => $website_id,
                                'users_id' => 0,
                                'id_album' => 0,
                                'full_name' => $name.'.'.$extention,
                                'name' => $name,
                                'string_data' => '',
                                'folder' => '/custom/domain_1/' . $domain,
                                'caption' => $name,
                                'type' => $extention,
                                'order' => 0,
                                'detector' => 0,
                                'number_comment' => 0);
                $picture = $this->getModelTable('PictureTable')->savePicture($row);
                $datajs = array('trash' => false, 'picture' => $picture);
                $items = array('status' => 'ok','data' => $datajs);
            }

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        $result = new JsonModel($items);
        return $result;
    }

    public function getPictureAction(){
        $items = array('status' => 'fall','msg' => 'Not found');
        $request = $this->getRequest();
        if($request->isPost()){
            $items = 10;
            $page = $request->getPost('page', 1);
            $offset = ($page-1)*$items;
            $pictures = $this->getModelTable('PictureTable')->getPictures($offset, $items);
            $done = '';
            if(count($pictures) < $items){
                $done = 'done';
            }
            $items = array('status' => 'ok', 'done' => $done, 'page' => ($page+1), 'data' => $pictures);
        }
        $result = new JsonModel($items);
        return $result;
    }

    public function deleteAction(){
        $items = array('status' => 'fall','msg' => 'Not found');
        $request = $this->getRequest();
        if($request->isPost()){
            $picture_id = $request->getPost('id', 0);
            $picture = $this->getModelTable('PictureTable')->getPicture($picture_id);
            if(!empty($picture)){
                $this->getModelTable('PictureTable')->deletePicture($picture_id);
                $file = PATH_BASE_ROOT.$picture->folder.'/'.$picture->name.'.'.$picture->type;
                if(is_file($file)){
                    @unlink($file);
                }

                $items = array('status' => 'ok','msg' => 'Xóa thành công', 'picture' => $picture);
            }else{
                $items = array('status' => 'fall','msg' => 'File not exit');
            }

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }

        $result = new JsonModel($items);
        return $result;
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
} 