<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;

use PHPImageWorkshop\ImageWorkshop;
use Screen\Capture;
use PHPImageCache\ImageCache;


class ImagesController extends FrontEndController
{
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
        // Grab the IF_MODIFIED_SINCE header 
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) 
        { 
            $headers['If-Modified-Since'] = $_SERVER['HTTP_IF_MODIFIED_SINCE']; 
        } 
        return $headers; 
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

    public function indexAction()
    {
        $year = $this->params()->fromRoute('y', '');
        $month = $this->params()->fromRoute('m', '');
        $day = $this->params()->fromRoute('d', '');
        $ex = $this->params()->fromRoute('ex', '');
        $ttl = $this->params()->fromRoute('params', '');

        $type = '';
        $title = '';
        $id = '';
        $folder = '';
        $dimensions = '';
        $crop = false;
        $src = '';
        $width = null;
        $height = null;

        if(!empty($ex) && !empty($ttl)){
            $preg_01 = '/^(?P<type>(product))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<id>\d+)-s(?P<dimensions>[0-9x]*)$/';
            $preg_02 = '/^(?P<type>(product))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<id>\d+)$/';
            
            $preg_03 = '/^(?P<type>(ckeditor))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<folder>[a-zA-Z][a-zA-Z0-9_]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_04 = '/^(?P<type>(ckeditor))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<folder>[a-zA-Z][a-zA-Z0-9_]*)$/';

            $preg_05 = '/^(?P<type>(categories_icons))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_06 = '/^(?P<type>(categories_icons))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_07 = '/^(?P<type>(manufactures))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_08 = '/^(?P<type>(manufactures))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_09 = '/^(?P<type>(articles))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<id>\d+)-s(?P<dimensions>[0-9x]*)$/';
            $preg_10 = '/^(?P<type>(articles))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<id>\d+)$/';

            $preg_11 = '/^(?P<type>(payments))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_12 = '/^(?P<type>(payments))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_13 = '/^(?P<type>(logos))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_14 = '/^(?P<type>(logos))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_15 = '/^(?P<type>(banners))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_16 = '/^(?P<type>(banners))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_17 = '/^(?P<type>(categories_banners))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_18 = '/^(?P<type>(categories_banners))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)$/';

            $preg_19 = '/^(?P<type>(neo))-(?P<folder>[a-zA-Z0-9_]*)-(?P<title>[a-zA-Z0-9_-]*)-s(?P<dimensions>[0-9x]*)$/';
            $preg_20 = '/^(?P<type>(neo))-(?P<folder>[a-zA-Z0-9_]*)-(?P<title>[a-zA-Z0-9_-]*)$/';


            if(!empty(preg_match($preg_01, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $id = $matches['id'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_02, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $id = $matches['id'];
            }else if(!empty(preg_match($preg_03, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $folder = $matches['folder'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_04, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $folder = $matches['folder'];
            }else if(!empty(preg_match($preg_05, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_06, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_07, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_08, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_09, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $id = $matches['id'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_10, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $id = $matches['id'];
            }else if(!empty(preg_match($preg_11, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_12, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_13, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_14, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_15, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_16, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_17, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_18, $ttl, $matches))){
                $type = $matches['type'];
                $title = $matches['title'];
            }else if(!empty(preg_match($preg_19, $ttl, $matches))){
                $type = $matches['type'];
                $folder = $matches['folder'];
                $title = $matches['title'];
                $dimensions = $matches['dimensions'];
            }else if(!empty(preg_match($preg_20, $ttl, $matches))){
                $type = $matches['type'];
                $folder = $matches['folder'];
                $title = $matches['title'];
            }

            switch ($type) {
                case 'product':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/products/'.$this->website['website_domain'].'/'.$year.'/'.$month.'/'.$day.'/product'.$id.'/fullsize/'.$title.'.'.$ex;
                        $src_ = PATH_BASE_ROOT.DS.trim($src,'/');
                        if (!is_file($src_)) {
                            $src = '/custom/domain_1/products/'.$_SERVER['SERVER_NAME'].'/'.$year.'/'.$month.'/'.$day.'/product'.$id.'/fullsize/'.$title.'.'.$ex;
                        }
                    }else{
                        $src = '/custom/domain_1/products/fullsize/product'.$id.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'ckeditor':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/ckeditor/'.$this->website['website_domain'].'/'.$year.'/'.$month.'/'.$day.'/'.$folder.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'categories_icons':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/categories_icons/'.$this->website['website_domain'].'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'manufactures':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/manufactures/'.$this->website['website_domain'].'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'articles':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/articles/'.$this->website['website_domain'].'/'.$year.'/'.$month.'/'.$day.'/article'.$id.'/fullsize/'.$title.'.'.$ex;
                    }
                    break;
                case 'payments':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/payments/'.$this->website['website_domain'].'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'logos':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/logos/'.$this->website['website_domain'].'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'banners':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/banners/'.$this->website['website_domain'].'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'categories_banners':
                    if(!empty($year) && !empty($month) && !empty($day)){
                        $src = '/custom/domain_1/categories_banners/'.$this->website['website_domain'].'/'.$year.'/'.$month.'/'.$day.'/'.$title.'.'.$ex;
                    }
                    break;
                case 'neo':
                    $src = '/custom/domain_1/'.$folder.'/'.$title.'.'.$ex;
                    break;   
                
                default:
                    $src = '';
            }
            //echo $src;die();
            if(!empty($src) && $this->isImage($src) ){
                $_cache = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $file_cache = '/cache/mini_'.md5($_cache).'.txt';
                $fileModTime = null;
                if (is_file(PATH_BASE_ROOT.$file_cache)) {
                    $fileModTime = filemtime(PATH_BASE_ROOT.$file_cache);
                }
                $headers = $this->getRequestHeaders();
                //print_r($headers).'<br />';
                //echo strtotime($headers['If-Modified-Since']).'<br />';
                //echo $fileModTime;
                //die();
                if (isset($headers['If-Modified-Since']) && 
                    (!empty($fileModTime) && strtotime($headers['If-Modified-Since']) == $fileModTime)){
                    header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModTime).
                            ' GMT', true, 304); 
                }else{
                    if(!empty($dimensions)){
                        $lsize = explode('x', $dimensions);
                        if(!empty($lsize[0])){
                            $width = $lsize[0];
                        }
                        if(!empty($lsize[1])){
                            $height = $lsize[1];
                        }
                    }
                    $url_font = PATH_BASE_ROOT.'/public/assets/fonts/arial.ttf';
                    $layer = ImageWorkshop::initFromPath(PATH_BASE_ROOT.$src);
                    if($type != 'logos' && $type != 'banners'){
                        /*if(!empty($this->website['logo']) && is_file(PATH_BASE_ROOT.$this->website['logo']) ){
                            $tuxLayer = ImageWorkshop::initFromPath(PATH_BASE_ROOT.$this->website['logo']);
                            $tuxLayer->resizeInPixel(20, null , true);
                            $layer->addLayerOnTop($tuxLayer, 20, 10, 'RT');
                        }

                        $textLayer = ImageWorkshop::initTextLayer('© '.$this->website['website_name'], $url_font, 18, 'ffffff', 0);
                        $layer->addLayerOnTop($textLayer, 12, 12, "LB");*/
                        if(!empty($dimensions)){
                            $layer->resizeInPixel($width, $height, true, 0, 0, 'MM');
                        }
                    }
                    $backgroundColor = "eeeeee";
                    if(strtolower($ex) == 'png' ){
                        $image = $layer->getResult();
                        imageAlphaBlending($image, true);
                        imageSaveAlpha($image, true);

                        $frinalLayer = ImageWorkshop::initFromResourceVar($image);
                        $image = $frinalLayer->getResult();
                    }else{
                        $image = $layer->getResult($backgroundColor);
                    }

                    //$imageDataSize = strlen($image);
                    $this->sendImageHeaders($ex, $fileModTime, PATH_BASE_ROOT.$src);
                    if(strtolower($ex) == 'jpg' || strtolower($ex) == 'jpeg'){
                        imagejpeg($image, null, 95);
                    } else if(strtolower($ex) == 'png' ){
                        imagepng($image, null, floor(95 * 0.09));
                    } else if(strtolower($ex) == 'gif' ){
                        imagegif($image, null);
                    }
                    @unlink(PATH_BASE_ROOT.$file_cache);
                    file_put_contents(PATH_BASE_ROOT.$file_cache, $src);
                }
                die();
            }
        }
        if($this->isImage($src))
            return $this->redirect()->toRoute($this->getUrlRouterLang().'image/placeholder', array('title'=> $title, 'dimensions'=> $dimensions, 'ex'=> $ex));
    }

    protected function sendImageHeaders($ex, $fileModTime, $graphicFileName){
        if(strtolower($ex) == 'jpg'){
            $mimeType = 'image/jpeg';
        }else{
            $mimeType = 'image/' . strtolower($ex);
        }
        /*
        header('Content-transfer-encoding: binary'); 
        header('Content-length: '.filesize($graphicFileName));

        header('Pragma: public');
        header('Cache-Control: max-age=86400, public');
        header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
        header ('Content-Type: ' . $mimeType);*/

        $gmdate_expires = gmdate ('D, d M Y H:i:s', strtotime ('now +10 days')) . ' GMT';
        $gmdate_modified = gmdate ('D, d M Y H:i:s') . ' GMT';
        header ('Content-Type: ' . $mimeType);
        header ('Accept-Ranges: none'); //Changed this because we don't accept range requests
        header ('Last-Modified: ' . $gmdate_modified);
        header('Content-length: '.filesize($graphicFileName));
        header('Cache-Control: max-age=8640000, must-revalidate');
        header('Expires: ' . $gmdate_expires);
    }


    public function placeholderAction()
    {
        $params = $this->params()->fromRoute();
        $getsize = $this->params()->fromRoute('dimensions', '100x100');
        header ("Content-type: image/png");

        $dimensions = explode('x', $getsize);
        if( !empty($dimensions[0]) && empty($dimensions[1]) ){
            $w_ = $dimensions[0];
            $h_ = ($w_*143)/194;
            $dimensions = array($w_, $h_);
        }else if( empty($dimensions[0]) && !empty($dimensions[1]) ){
            $h_ = $dimensions[1];
            $w_ = ($h_*194)/143;
            $dimensions = array($w_, $h_);
        }

        $url_place = PATH_BASE_ROOT.'/styles/dataimages/bg-img.jpg';
        $placeHolder = imagecreatefromjpeg($url_place);
        // Create image
        $image      = imagecreate($dimensions[0], $dimensions[1]);

        // Colours
        $bg         = isset($_GET['bg']) ? $_GET['bg'] : 'CCC';
        $bg         = $this->hex2rgb($bg);
        $setbg      = imagecolorallocate($image, $bg['r'], $bg['g'], $bg['b']);

        $fg         = isset($_GET['fg']) ? $_GET['fg'] : '333';
        $fg         = $this->hex2rgb($fg); 
        $setfg      = imagecolorallocate($image, $fg['r'], $fg['g'], $fg['b']);

        // Text
        $text       = isset($_GET['text']) ? strip_tags($_GET['text']) : $getsize;
        $text       = str_replace('+', ' ', $text);
        
        // Text positioning
        $fontsize   = 50;
        $fontwidth  = imagefontwidth($fontsize);    // width of a character
        $fontheight = imagefontheight($fontsize);   // height of a character
        $length     = strlen($text);                // number of characters
        $textwidth  = $length * $fontwidth;         // text width
        $xpos       = (imagesx($image) - $textwidth) / 2;
        $ypos       = (imagesy($image) - $fontheight) / 2;

        // Generate text
        imagestring($image, $fontsize, $xpos, $ypos, $text, $setfg);

        //dua hinh len tren anh
        list($width, $height) = getimagesize($url_place);
        /*$new_width_holder =  min($width, $dimensions[0]);
        $ratio = $height / $width;
        $new_height_holder = $new_width_holder * $ratio;

        if($new_height_holder > $dimensions[1]){
            $new_height_holder_ = $dimensions[1];
            $ratio = $new_width_holder / $new_height_holder;
            $new_width_holder_ = $new_height_holder_ * $ratio;
            $new_width_holder = $new_width_holder_;
            $new_height_holder = $new_height_holder_;
        }

        $imageResized = imagecreatetruecolor($dimensions[0], $dimensions[1]);
        imagecopyresampled($imageResized, $placeHolder, 0, 0, 0, 0, $new_width_holder, $new_height_holder, $width, $height);
        $x = ($dimensions[0]-$new_width_holder)/2;
        $y = ($dimensions[1]-$new_height_holder)/2;
        imagecopy($image, $imageResized, $x, $y, 0, 0, $new_width_holder, $new_height_holder);
        // Render image
        imagepng($image);
        */

        $new_width_holder =  $dimensions[0];
        $ratio = $height / $width;
        $new_height_holder = $new_width_holder * $ratio;

        if($dimensions[1]>$new_height_holder ){
            $new_height_holder_ = $dimensions[1];
            $ratio = $new_width_holder / $new_height_holder;
            $new_width_holder_ = $new_height_holder_ * $ratio;
            $new_width_holder = $new_width_holder_;
            $new_height_holder = $new_height_holder_;
        }

        $imageResized = imagecreatetruecolor($dimensions[0], $dimensions[1]);
        $x = -($new_width_holder-$dimensions[0])/2;
        $y = -($new_height_holder-$dimensions[1])/2;
        imagecopyresampled($imageResized, $placeHolder, $x, $y, 0, 0, $new_width_holder, $new_height_holder, $width, $height);

        // Render image
        imagepng($imageResized);
        die();
    }

    public function hex2rgb($colour)
    {
        $colour = preg_replace("/[^abcdef0-9]/i", "", $colour);
        if (strlen($colour) == 6)
        {
            list($r, $g, $b) = str_split($colour, 2);
            return Array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));
        }
        elseif (strlen($colour) == 3)
        {
            list($r, $g, $b) = array($colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]);
            return Array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));    
        }
        return false;
    }

    public function captureAction()
    {
        $title = $this->params()->fromRoute('title', '');
        $ex = $this->params()->fromRoute('ex', 'jpg');
        $dimensions = $this->params()->fromRoute('dimensions', '');
        $days = 1;
        $fileModTime = null;
        try {
            $width = null;
            $height = null;

            $src = '/templates/Websites/'.$this->website['websites_folder'].'/images';
            if (!is_dir(PATH_BASE_ROOT.$src)) {
                mkdir(PATH_BASE_ROOT.$src, 0777);
            }
            $src = $src.'/capture';
            if (!is_dir(PATH_BASE_ROOT.$src)) {
                mkdir(PATH_BASE_ROOT.$src, 0777);
            }

            if (file_exists(PATH_BASE_ROOT.$src.'/index.jpg')) {
                if (filemtime(PATH_BASE_ROOT.$src.'/index.jpg') < ( time() - ( $days * 24 * 60 * 60 ) ) )  
                {
                    unlink(PATH_BASE_ROOT.$src.'/index.jpg');  
                }
            }

            if (!file_exists(PATH_BASE_ROOT.$src.'/index.jpg')) {
                $url = $this->website['website_domain'];
                $screen = new Capture($url);
                $screen->setWidth(1200);
                $screen->setHeight(800);
                $screen->setBackgroundColor('#ffffff');
                $screen->setUserAgentString('© '.$this->website['website_name']);

                $screen->output->setLocation(PATH_BASE_ROOT.$src.'/');
                $result = $screen->save('index.jpg');
                if(!empty($result)){
                    $src = $src.'/index.jpg';
                }
            }else{
                $src = $src.'/index.jpg';
            }
            if (file_exists(PATH_BASE_ROOT.$src)) {
                $fileModTime = filemtime(PATH_BASE_ROOT.$src);
                if(!empty($dimensions)){
                    $lsize = explode('x', $dimensions);
                    if(!empty($lsize[0])){
                        $width = $lsize[0];
                    }
                    if(!empty($lsize[1])){
                        $height = $lsize[1];
                    }
                }

                $url_font = PATH_BASE_ROOT.'/public/assets/fonts/arial.ttf';
                $layer = ImageWorkshop::initFromPath(PATH_BASE_ROOT.$src);
                $textLayer = ImageWorkshop::initTextLayer('© '.$this->website['website_name'], $url_font, 18, 'ffffff', 0);
                $layer->addLayerOnTop($textLayer, 12, 12, "LB");
                if(!empty($dimensions)){
                    $layer->resizeInPixel($width, null, true, 0, 0, 'MM');
                    $layer->cropInPixel($width, $height, 0, 0, 'LT');
                }
                $backgroundColor = "FFFFFF";
                $image = $layer->getResult($backgroundColor);

                //$imageDataSize = strlen($image);
                $this->sendImageHeaders($ex, $fileModTime, PATH_BASE_ROOT.$src);
                if(strtolower($ex) == 'jpg' || strtolower($ex) == 'jpeg'){
                    imagejpeg($image, null, 95);
                } else if(strtolower($ex) == 'png' ){
                    imagepng($image, null, floor(95 * 0.09));
                } else if(strtolower($ex) == 'gif' ){
                    imagegif($image, null);
                }
                die();
            }else{
                return $this->redirect()->toRoute($this->getUrlRouterLang().'image/placeholder', array('title'=> $title, 'dimensions'=> $dimensions, 'ex'=> $ex));
            }

        } catch(\Exception $e) {
            return $this->redirect()->toRoute($this->getUrlRouterLang().'image/placeholder', array('title'=> $title, 'dimensions'=> $dimensions, 'ex'=> $ex));
        }
    }

    public function testAction()
    {
        $str = '/custom/domain_1/products/overflow.vietnamshops.net/2016/03/21/product1454344392/fullsize/tu_lanh-2_cua_toshiba_gr_wg66vdazgg_600l_guong_xanh_1458498321_qYgHD0.jpg';

        //preg_match('/\/custom\/domain_1\/products\/(?P<domain>\w+)\/(?P<year>\d+)\/(?P<month>\d+)\/(?P<day>\d+)\/product(?P<id>\d+)\/fullsize\/(?P<title>\w+).jpg/', $str, $matches);
        /*$preg_01 = '/\/custom\/domain_1\/(?P<folder>(products))\/(?P<domain>\w.+)\/(?P<year>\d+)\/(?P<month>\d+)\/(?P<day>\d+)\/(?P<type>(product))(?P<id>\d+)\/fullsize\/(?P<title>[a-zA-Z][a-zA-Z0-9_-]*).(?P<ex>(jpg|png|gif))/';
        preg_match($preg_01, $str, $matches);
        print_r($matches);
        die();*/

        $url_place = PATH_BASE_ROOT.'/root_style/frontend/clip-one/assets/dataimages/Placeholder.jpg';
        $url_font = PATH_BASE_ROOT.'/public/assets/fonts/arial.ttf';
        
        $layer = ImageWorkshop::initFromPath($url_place);
        // This is the text layer
        $textLayer = ImageWorkshop::initTextLayer('© '.$this->website['website_name'], $url_font, 18, 'ffffff', 0);
        $layer->addLayerOnTop($textLayer, 12, 12, "LB");

        $backgroundColor = "FFFFFF";
        $image = $layer->getResult($backgroundColor);

        header('Content-type: image/jpeg');
        imagejpeg($image, null, 95);
        die();
    }

}
