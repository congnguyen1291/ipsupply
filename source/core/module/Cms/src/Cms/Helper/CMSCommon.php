<?php
namespace Cms\Helper;
use Zend\View\Helper\AbstractHelper;

class CMSCommon extends AbstractHelper
{
    public function __invoke()
    {
        return $this;
    }

    public function hasPermission($module, $controller,$action){
        if(isset($_SESSION['CMSMEMBER'])){
           if($_SESSION['CMSMEMBER']['type'] == 'admin'){
                return TRUE;
           }
            if(isset($_SESSION['CMSMEMBER']['permissions'])){
                if(isset($_SESSION['CMSMEMBER']['permissions'][$module][$controller][$action])){
                    return TRUE;
                }
            }
        }
        return FALSE;
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
            'jpeg','png','jpg','gif',
        );
        if(in_array($ext,$image_ext)){
            return TRUE;
        }
        return FALSE;
    }

    public function isFlash($url){
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
            'swf',
        );
        if(in_array($ext,$image_ext)){
            return TRUE;
        }
        return FALSE;
    }

}