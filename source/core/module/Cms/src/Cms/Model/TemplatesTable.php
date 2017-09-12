<?php
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Filter\File\LowerCase;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\GreaterThan;

use Cms\Model\AppTable;

class TemplatesTable extends AppTable
{

    public function getAll(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('template');
        $select->where(array(
            'template.template_status' => 1
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch(\Exception $ex){
            return array();
        }
    }

    public function getTemplateById($template_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('template');

        $select->where(array(
            'template.template_id' => $template_id,
        ));

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $results->current();
            return $result;
        }catch(\Exception $ex){
            return array();
        }
    }

    public function saveTemplates(Templates $template, $picture_id = array()){
        $unzip_sucess = false;
        if($template->source['name'] != ''){
            
            try{
                $upload_dir = DS.'templates'.DS.'Sources';
                if (!is_dir(PATH_BASE_ROOT.$upload_dir)) {
                    mkdir(PATH_BASE_ROOT.$upload_dir, 0777);
                }
                $sources = $template->source;

                $filename = $sources["name"];
                $source = $sources["tmp_name"];
                $type = $sources["type"];

                $name_ = $sources['name'];
                $stack = @explode('.', $name_);
                $ext = array_pop($stack);
                $name_zip = implode('.', $stack);

                if(!empty($template->template_folder)){
                    $upload_dir .= DS.$template->template_folder;
                }else{
                    $upload_dir .= DS.$name_zip.'-'.$this->randText(6);
                }

                if (!is_dir(PATH_BASE_ROOT.$upload_dir)) {
                    mkdir(PATH_BASE_ROOT.$upload_dir, 0777);
                }
                
                $name = explode(".", $filename);
                $okay = true;
                $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
                foreach($accepted_types as $mime_type) {
                    if($mime_type == $type) {
                        $okay = true;
                        break;
                    } 
                }
                $continue = strtolower($name[1]) == 'zip' ? true : false;
                if($okay && $continue){
                    $target_path = $upload_dir.DS.$filename;
                    if(move_uploaded_file($source, PATH_BASE_ROOT.$target_path)) {
                        $zip = new \ZipArchive();
                        $x = $zip->open(PATH_BASE_ROOT.$target_path);
                        if ($x === true) {
                            $zip->extractTo(PATH_BASE_ROOT.$upload_dir);
                            $zip->close();
                            unlink(PATH_BASE_ROOT.$target_path);
                            
                            if($template->template_dir != $upload_dir && $template->template_folder != $name_zip){
                                if(!empty($template->template_dir) && !empty($template->template_folder)){ 
                                    $folder_old=PATH_BASE_ROOT.$template->template_dir.DS.$template->template_folder;
                                    exec('rm -rf '.escapeshellarg($folder_old));
                                }
                                $template->template_dir = $upload_dir;
                                $template->template_folder = $name_zip;
                            }
                        }
                    } else {
                        $unzip_sucess = false;
                    }
                }else{
                    $unzip_sucess = false;
                }

            }catch(\Exception $ex){
                $unzip_sucess = false;
            }
        }else{
            $unzip_sucess = true;
        }

        if($unzip_sucess){
            $id = (int) $template->template_id;
            $data = array(
                'categories_template_id'       => $template->categories_template_id,
                'template_name'       => $template->template_name,
                'template_alias'      => $template->template_alias,
                'template_description'      => $template->template_description,
                'templete_price'        => $template->templete_price,
                'template_status'        => $template->template_status,
            );
            
            if(!empty($picture_id) && !empty($picture_id['w'])){
                $picture = $this->getModelTable('PictureTable')->getPicture($picture_id['w']);
                if(!empty($picture)){
                    $data['template_thumb'] = $picture->folder.'/'.$picture->name.'.'.$picture->type;
                }
            }

            if(!empty($picture_id) && !empty($picture_id['m'])){
                $picture_m = $this->getModelTable('PictureTable')->getPicture($picture_id['m']);
                if(!empty($picture_m)){
                    $data['template_thumb_mobile'] = $picture_m->folder.'/'.$picture_m->name.'.'.$picture_m->type;
                }
            }
            if($template->source['name'] != ''){
                $data['template_dir']     = $template->template_dir;
                $data['template_folder']     = $template->template_folder;
            }
            
            if(empty($id)){
                $this->tableGateway->insert($data);
            }else{
                $this->tableGateway->update($data, array('template_id' => $id));
            }
        }
    }

    public function countAll($where = array())
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array('total' => new Expression('count(template.template_id)')));
        $select->from('template');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $row = $result->current();
            return $row['total'];
        }catch (\Exception $ex){
            return array();
        }
    }

    public function getList($where = array(), $order = array(), $intPage=0, $intPageSize = 20)
    {
        if ($intPage <= 1) {
            $intPage = 0;
        } else {
            $intPage = ($intPage - 1) * $intPageSize;
        }

        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('template');
        
        if(!count($order)){
            $select->order(array(
                'template.template_id' => 'ASC',
            ));
        }else{
            $select->order($order);
        }
        $select->group('template.template_id');
        $select->limit($intPageSize);
        $select->offset($intPage);

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){
            return array();
        }
    }

    public function updateTemplateWithId($data, $template_id){
        $this->tableGateway->update($data, array('template_id' => $template_id));
    }


}