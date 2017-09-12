<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;

use MatthiasMullie\Minify;


class JsController extends FrontEndController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getRequestHeaders() 
    { 
        if (function_exists("apache_request_headers")) 
        { 
            if($headers = apache_request_headers()) 
            { 
                return $headers; 
            } 
        } 

        $headers = array();
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) 
        { 
            $headers['If-Modified-Since'] = $_SERVER['HTTP_IF_MODIFIED_SINCE']; 
        } 
        return $headers; 
    }

    public function indexAction()
    {
        $_cache = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $file_cache = '/cache/minifier_js_'.md5($_cache).'.txt';
        $fileModTime = null;
        if (is_file(PATH_BASE_ROOT.$file_cache)) {
            $fileModTime = filemtime(PATH_BASE_ROOT.$file_cache);
        }
        $headers = $this->getRequestHeaders();
        if (isset($headers['If-Modified-Since']) && 
            (!empty($fileModTime) && strtotime($headers['If-Modified-Since']) == $fileModTime)){
            header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModTime).
                    ' GMT', true, 304); 
        }else{
            $gmdate_expires = gmdate ('D, d M Y H:i:s', strtotime ('now +10 days')) . ' GMT';
            $gmdate_modified = gmdate ('D, d M Y H:i:s') . ' GMT';
            header ('Content-Type: application/javascript');
            header ('Accept-Ranges: none'); //Changed this because we don't accept range requests
            header ('Last-Modified: ' . $gmdate_modified);
            header('Cache-Control: max-age=8640000, must-revalidate');
            header('Expires: ' . $gmdate_expires);
            
            $name = $this->params()->fromRoute('name', '');
            @unlink(PATH_BASE_ROOT.$file_cache);
            file_put_contents(PATH_BASE_ROOT.$file_cache, $name);
            
            if(!empty($name) && !empty($_SESSION['js'][$name])){
                $minifier = new Minify\JS();
                foreach ($_SESSION['js'][$name] as $key => $js) {
                    $minifier->add($js);
                }
                //$list_js = implode('","', $_SESSION['js'][$name]);
                //$minifier = new Minify\JS('"'.$list_js.'"');
                echo $minifier->minify();
            }
        }
        die();
    }

}
