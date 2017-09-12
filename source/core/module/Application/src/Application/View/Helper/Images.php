<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

use PHPImageWorkshop\ImageWorkshop;
use PHPImageWorkshop\ImageWorkshopException;
use PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException as ImageWorkshopLayerException;

class Images extends App
{
    public function getImages($products_id)
    {
        $images = $this->getModelTable('ProductsTable')->getImages($products_id);
        return $images;
    }
    
    public function checkRemoteFile($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        // don't download content
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if(curl_exec($ch)!==FALSE)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getUrlImage_25_11($url,$width=null, $height=null, $scrop = false,$bacground = 'ffffff'){
        $website_id = $_SESSION['website']['website_id'];
        $domain= md5($_SESSION['website']['website_domain'].':'.$website_id);
        $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
        if(!is_dir($websiteFolder)){
            @mkdir ( $websiteFolder, 0777 );
        }
        $websiteThumb = $websiteFolder.'/thumb';
        if(!is_dir($websiteThumb)){
            @mkdir ( $websiteThumb, 0777 );
        }
        $result = '/image/placeholder/no-photo-150x150.png';
        if(!empty($url)){
            if(is_file(PATH_BASE_ROOT.$url)){
                try{
                    $result = $url;
                    $thumbFolder = '';
                    if(!empty($width)){
                        $thumbFolder = $width;
                    }
                    if(!empty($height)){
                        $thumbFolder = $thumbFolder.'x'.$height;
                    }
                    if(!empty($thumbFolder)){
                        $websiteThumb = $websiteThumb.'/'.$thumbFolder;
                        if(!is_dir($websiteThumb)){
                            @mkdir ( $websiteThumb, 0777 );
                        }
                        $name = $this->getFileName($url);
                        $extension = $this->getFileExtension($name);
                        $result = "/custom/domain_1/" . $domain.'/thumb/'.$thumbFolder.'/'.$name;
                        if(!is_file(PATH_BASE_ROOT.DS.$result)){
                            $url_font = PATH_BASE_ROOT.'/public/assets/fonts/arial.ttf';
                            $layer = ImageWorkshop::initFromPath(PATH_BASE_ROOT.$url);
                            $layer->resizeInPixel($width, $height, true, 0, 0, 'MM');
                            $backgroundColor = null;
                            if(strtolower($extension) != 'png' ){
                                $backgroundColor = $bacground;
                            }
                            $layer->save($websiteThumb, $name, true, $backgroundColor, 85); 
                        }
                    }
                } catch (\Exception $e) {
                    $result = '/placeholder/no-photo-50x50'; 
                }
            }else{
                $result = $url;
            }
        }else{
            $size = '';
            if(!empty($width)){
                $size = $width;
            }
            if(!empty($height)){
                $size = $size.'x'.$height;
            }
            $result = '/image/placeholder/no-photo-'.$size.'.png';
        }
        /*$pre_dm = substr ( $result , 0 , 17 );
        if($pre_dm == '/custom/domain_1/' ){
            $result = '//photos.coz.vn/'.substr ( $result , 17-strlen($result) );
        }*/
        return $result;
    }

    public function getUrlImage_($url,$width=null, $height=null, $scrop = false,$bacground = 'ffffff'){
        $website_id = $_SESSION['website']['website_id'];
        $domain= md5($_SESSION['website']['website_domain'].':'.$website_id);
        $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
        if(!is_dir($websiteFolder)){
            @mkdir ( $websiteFolder, 0777 );
        }
        $websiteThumb = $websiteFolder.'/thumb';
        if(!is_dir($websiteThumb)){
            @mkdir ( $websiteThumb, 0777 );
        }
        $result = '/image/placeholder/no-photo-150x150.png';
        if(!empty($url)){
            if( is_file(PATH_BASE_ROOT.$url) ){
                $result = $url;
                $thumbFolder = '';
                if(!empty($width)){
                    $thumbFolder = $width;
                }
                if(!empty($height)){
                    $thumbFolder = $thumbFolder.'x'.$height;
                }
                if( !empty($thumbFolder) ){
                    $websiteThumb = $websiteThumb.'/'.$thumbFolder;
                    if(!is_dir($websiteThumb)){
                        @mkdir ( $websiteThumb, 0777 );
                    }
                    $name = $this->getFileName($url);
                    $extension = $this->getFileExtension($name);
                    $result = "/custom/domain_1/" . $domain.'/thumb/'.$thumbFolder.'/'.$name;
                    if( !is_file(PATH_BASE_ROOT.$result) ){
                        $url_font = PATH_BASE_ROOT.'/public/assets/fonts/arial.ttf';
                        try{
                            $layer = ImageWorkshop::initFromPath(PATH_BASE_ROOT.$url);
                            $largestSide = $width;
                            if( (empty($width) && !empty($height))
                                || (!empty($width) && !empty($height) && ($height > $width)) ){
                                $largestSide = $height;
                            }
                            if( !empty($largestSide) ){
                                $layer->cropMaximumInPercent(0, 0, "MM");
                                $layer->resizeInPixel($largestSide, $largestSide);
                                $layer->cropInPixel($width, $height, 0, 0, 'MM');
                            }else{
                                $layer->resizeInPixel($width, $height, true, 0, 0, 'MM');
                            }
                            
                            $backgroundColor = null;
                            if(strtolower($extension) != 'png' ){
                                $backgroundColor = $bacground;
                            }

                            $layer->save($websiteThumb, $name, true, $backgroundColor, 85);
                        } catch (\Exception $e) {
                            $result = '/image/placeholder/no-photo-'.$thumbFolder.'.png';
                        } catch (\ImageWorkshopException $e) {
                            $result = '/image/placeholder/no-photo-'.$thumbFolder.'.png';
                        }
                    }
                }
            }else{
                $result = $url;
            }
        }else{
            $size = '';
            if(!empty($width)){
                $size = $width;
            }
            if(!empty($height)){
                $size = $size.'x'.$height;
            }
            $result = '/image/placeholder/no-photo-'.$size.'.png';
        }
        /*$pre_dm = substr ( $result , 0 , 17 );
        if($pre_dm == '/custom/domain_1/' ){
            $result = '//photos.coz.vn/'.substr ( $result , 17-strlen($result) );
        }*/
        return $result;
    }

    public function getUrlImage_30_11($url,$width=null, $height=null, $scrop = false,$bacground = 'eeeeee'){
        $website_id = $_SESSION['website']['website_id'];
        $domain= md5($_SESSION['website']['website_domain'].':'.$website_id);
        $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
        if(!is_dir($websiteFolder)){
            @mkdir ( $websiteFolder, 0777 );
        }
        $websiteThumb = $websiteFolder.'/thumb';
        if(!is_dir($websiteThumb)){
            @mkdir ( $websiteThumb, 0777 );
        }
        $result = '/image/placeholder/no-photo-150x150.png';
        if(!empty($url)){
            if(is_file(PATH_BASE_ROOT.$url)){
                try{
                    $result = $url;
                    $thumbFolder = '';
                    if(!empty($width)){
                        $thumbFolder = $width;
                    }
                    if(!empty($height)){
                        $thumbFolder = $thumbFolder.'x'.$height;
                    }
                    if(!empty($thumbFolder)){
                        $websiteThumb = $websiteThumb.'/'.$thumbFolder;
                        if(!is_dir($websiteThumb)){
                            @mkdir ( $websiteThumb, 0777 );
                        }
                        $name = $this->getFileName($url);
                        $extension = $this->getFileExtension($name);
                        $result = "/custom/domain_1/" . $domain.'/thumb/'.$thumbFolder.'/'.$name;
                        if(!is_file(PATH_BASE_ROOT.DS.$result)){
                            $fileroot = PATH_BASE_ROOT.$url;
                            switch ($extension){
                                case 'png':
                                    $image = imagecreatefrompng($fileroot);
                                    break;
                                case 'gif':
                                    $image = imagecreatefromgif($fileroot);
                                    break;
                                default :
                                    $image = imagecreatefromjpeg($fileroot);
                            }
                            if(!empty($width) && !empty($height)){
                                $thumb_width = $width;
                                $thumb_height = $height;
                                $width = imagesx($image);
                                $height = imagesy($image);
                                $original_aspect = $width / $height;
                                $thumb_aspect = $thumb_width / $thumb_height;
                                if ( $original_aspect >= $thumb_aspect )
                                {
                                   // If image is wider than thumbnail (in aspect ratio sense)
                                   $new_height = $thumb_height;
                                   $new_width = $width / ($height / $thumb_height);
                                }else{
                                   // If the thumbnail is wider than the image
                                   $new_width = $thumb_width;
                                   $new_height = $height / ($width / $thumb_width);
                                }
                            }else if(!empty($width) && empty($height)){
                                $thumb_width = $width;
                                $width = imagesx($image);
                                $height = imagesy($image);
                                $new_width = $thumb_width;
                                $new_height = $height / ($width / $thumb_width);
                                $thumb_height = $new_height;
                            }else if(!empty($width) && empty($height)){
                                $thumb_height = $new_height;
                                $width = imagesx($image);
                                $height = imagesy($image);
                                $new_height = $thumb_height;
                                $new_width = $width / ($height / $thumb_height);
                                $thumb_width = $new_width;
                            }
                            $thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
                            if($extension=="png") {
                                imagealphablending($thumb, false);
                                $colorTransparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
                                imagefill($thumb, 0, 0, $colorTransparent);
                                imagesavealpha($thumb, true);
                            }
                            // Resize and crop
                            imagecopyresampled($thumb,
                                               $image,
                                               0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                                               0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                                               0, 0,
                                               $new_width, $new_height,
                                               $width, $height);
                            imagejpeg($thumb, PATH_BASE_ROOT.DS.$result, 95);
                            /*switch ($extension){
                                case 'png':
                                    imagepng($thumb, PATH_BASE_ROOT.DS.$result, 95);
                                    break;
                                case 'gif':
                                    imagegif($thumb, PATH_BASE_ROOT.DS.$result, 95);
                                    break;
                                default :
                                    imagejpeg($thumb, PATH_BASE_ROOT.DS.$result, 95);

                            }*/

                            imagedestroy($thumb);
                        }
                    }
                } catch (\Exception $e) {
                    $result = '/placeholder/no-photo-50x50'; 
                }
            }else{
                $result = $url;
            }
        }else{
            $size = '';
            if(!empty($width)){
                $size = $width;
            }
            if(!empty($height)){
                $size = $size.'x'.$height;
            }
            $result = '/image/placeholder/no-photo-'.$size.'.png';
        }
        return $result;
    }

    public function getUrlImage_bk_17_12($url,$width=null, $height=null, $scrop = false,$bacground = 'ffffff'){
        $website_id = $_SESSION['website']['website_id'];
        $domain= md5($_SESSION['website']['website_domain'].':'.$website_id);
        $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
        if(!is_dir($websiteFolder)){
            @mkdir ( $websiteFolder, 0777 );
        }
        $websiteThumb = $websiteFolder.'/thumb';
        if(!is_dir($websiteThumb)){
            @mkdir ( $websiteThumb, 0777 );
        }
        $thumbFolder = '';
        if(!empty($width)){
            $thumbFolder = $width;
        }
        if(!empty($height)){
            $thumbFolder = $thumbFolder.'x'.$height;
        }
        if( !empty($thumbFolder) ){
            $websiteThumb = $websiteThumb.'/'.$thumbFolder;
            if(!is_dir($websiteThumb)){
                @mkdir ( $websiteThumb, 0777 );
            }
        }       
        //$result = '/image/placeholder/no-photo-150x150.png';
        $result = '/image/placeholder/no-photo-'.$thumbFolder.'.png';
        if(!empty($url)){
            if( is_file(PATH_BASE_ROOT.$url) ){
                $result = $url;
                if( !empty($thumbFolder) ){
                    $name = $this->getFileName($url);
                    $extension = $this->getFileExtension($name);
                    $result = "/custom/domain_1/" . $domain.'/thumb/'.$thumbFolder.'/'.$name;
                    if( !is_file(PATH_BASE_ROOT.$result) ){
                        $url_font = PATH_BASE_ROOT.'/public/assets/fonts/arial.ttf';
                        try{
                            $layer = ImageWorkshop::initFromPath(PATH_BASE_ROOT.$url);
                            $largestSide = $width;
                            if( (empty($width) && !empty($height))
                                || (!empty($width) && !empty($height) && ($height > $width)) ){
                                $largestSide = $height;
                            }
                            if( !empty($largestSide) ){
                                $layerWidth = $layer->getWidth();
                                $layerHeight = $layer->getHeight();

                                if(!empty($width) && !empty($height)){
                                    $thumb_width = $width;
                                    $thumb_height = $height;
                                    $original_aspect = $layerWidth / $layerHeight;
                                    $thumb_aspect = $thumb_width / $thumb_height;
                                    if ( $original_aspect >= $thumb_aspect )
                                    {
                                       $new_height = $thumb_height;
                                       $new_width = $layerWidth / ($layerHeight / $thumb_height);
                                    }else{
                                       $new_width = $thumb_width;
                                       $new_height = $layerHeight / ($layerWidth / $thumb_width);
                                    }
                                }else if(!empty($width) && empty($height)){
                                    $thumb_width = $width;
                                    $new_width = $thumb_width;
                                    $new_height = $layerHeight / ($layerWidth / $thumb_width);
                                    $thumb_height = $new_height;
                                }else if( empty($width) && !empty($height)){
                                    $thumb_height = $height;
                                    $new_height = $thumb_height;
                                    $new_width = $layerWidth / ($layerHeight / $thumb_height);
                                    $thumb_width = $new_width;
                                }

                                $layer->resizeInPixel($new_width, $new_height);
                                $layer->cropInPixel($thumb_width, $thumb_height, 0, 0, 'MM');
                            }else{
                                $layer->resizeInPixel($width, $height, true, 0, 0, 'MM');
                            }
                            
                            $backgroundColor = null;
                            if( strtolower($extension) != 'png' ){
                                $backgroundColor = $bacground;
                            }

                            $layer->save($websiteThumb, $name, true, $backgroundColor, 95);
                            /*$factory = new \ImageOptimizer\OptimizerFactory();
                            if( strtolower($extension) == 'png' ){
                                $optimizer = $factory->get('png');
                            }else if( strtolower($extension) == 'gif' ){
                                $optimizer = $factory->get('gif');
                            }else{
                                $optimizer = $factory->get();
                            }
                            $filepath = $websiteThumb .'/'. $name;
                            $optimizer->optimize($filepath);*/
                        } catch (\Exception $e) {
                            $result = '/image/placeholder/no-photo-'.$thumbFolder.'.png';
                        } catch (\ImageWorkshopException $e) {
                            $result = '/image/placeholder/no-photo-'.$thumbFolder.'.png';
                        } catch (\ImageWorkshopLayerException $e) {
                            $result = '/image/placeholder/no-photo-'.$thumbFolder.'.png';
                        }
                    }
                }
            }
        }
        /*$pre_dm = substr ( $result , 0 , 17 );
        if($pre_dm == '/custom/domain_1/' ){
            $result = '//photos.coz.vn/'.substr ( $result , 17-strlen($result) );
        }*/
        return $result;
    }

    public function getUrlImage($url,$width=null, $height=null, $scrop = false,$bacground = 'ffffff'){
        $website_id = 1;
        $domain= md5(MASTERPAGE.':'.$website_id);
        $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
        if(!is_dir($websiteFolder)){
            @mkdir ( $websiteFolder, 0777 );
        }
        $websiteThumb = $websiteFolder.'/thumb';
        if(!is_dir($websiteThumb)){
            @mkdir ( $websiteThumb, 0777 );
        }
        $result = $this->getUrlPrefixLang().'/image/placeholder/no-photo-150x150.png';
        if(!empty($url)){
            if( is_file(PATH_BASE_ROOT.$url) ){
                $result = $url;
                $thumbFolder = '';
                if(!empty($width)){
                    $thumbFolder = $width;
                }
                if(!empty($height)){
                    $thumbFolder = $thumbFolder.'x'.$height;
                }
                if( !empty($thumbFolder) ){
                    $websiteThumb = $websiteThumb.'/'.$thumbFolder;
                    if(!is_dir($websiteThumb)){
                        @mkdir ( $websiteThumb, 0777 );
                    }
                    $name = $this->getFileName($url);
                    $extension = $this->getFileExtension($name);
                    $result = "/custom/domain_1/" . $domain.'/thumb/'.$thumbFolder.'/'.$name;
                    if( !is_file(PATH_BASE_ROOT.$result) ){
                        $url_font = PATH_BASE_ROOT.'/public/assets/fonts/arial.ttf';
                        try{
							
                            $layer = ImageWorkshop::initFromPath(PATH_BASE_ROOT.$url);
                            if( empty($this->view->Websites()->getTypeCropImage()) ){
                                $largestSide = $width;
                                if( (empty($width) && !empty($height))
                                    || (!empty($width) && !empty($height) && ($height > $width)) ){
                                    $largestSide = $height;
                                }
                                if( !empty($largestSide) ){
                                    $layerWidth = $layer->getWidth();
                                    $layerHeight = $layer->getHeight();

                                    if(!empty($width) && !empty($height)){
                                        $thumb_width = $width;
                                        $thumb_height = $height;
                                        $original_aspect = $layerWidth / $layerHeight;
                                        $thumb_aspect = $thumb_width / $thumb_height;
                                        if ( $original_aspect >= $thumb_aspect )
                                        {
                                           $new_height = $thumb_height;
                                           $new_width = $layerWidth / ($layerHeight / $thumb_height);
                                        }else{
                                           $new_width = $thumb_width;
                                           $new_height = $layerHeight / ($layerWidth / $thumb_width);
                                        }
                                    }else if(!empty($width) && empty($height)){
                                        $thumb_width = $width;
                                        $new_width = $thumb_width;
                                        $new_height = $layerHeight / ($layerWidth / $thumb_width);
                                        $thumb_height = $new_height;
                                    }else if( empty($width) && !empty($height)){
                                        $thumb_height = $height;
                                        $new_height = $thumb_height;
                                        $new_width = $layerWidth / ($layerHeight / $thumb_height);
                                        $thumb_width = $new_width;
                                    }

                                    $layer->resizeInPixel($new_width, $new_height);
                                    $layer->cropInPixel($thumb_width, $thumb_height, 0, 0, 'MM');
                                }else{
                                    $layer->resizeInPixel($width, $height, true, 0, 0, 'MM');
                                }
                                
                                $backgroundColor = null;
                                if( strtolower($extension) != 'png' ){
                                    $backgroundColor = $bacground;
                                }

                            }else{
                                $layer->resizeInPixel($width, $height, true, 0, 0, 'MM');
                                $backgroundColor = null;
                                if( strtolower($extension) != 'png' ){
                                    $backgroundColor = $bacground;
                                }
                            }
                            
                            $layer->save($websiteThumb, $name, true, $backgroundColor, 80); 
							
							/*
							$fileroot = PATH_BASE_ROOT.$url;
							switch ($extension){        
								case 'jpg':
									$image = imagecreatefromjpeg($fileroot);
								break;

								case 'png':
									$image = imagecreatefrompng($fileroot);
								break;
								case 'gif':
									$image = imagecreatefromgif($fileroot);
								break;
							}
                            if(!empty($width) && !empty($height)){
    							$thumb_width = $width;
    							$thumb_height = $height;
    							$width = imagesx($image);
    							$height = imagesy($image);
    							$original_aspect = $width / $height;
    							$thumb_aspect = $thumb_width / $thumb_height;
    							if ( $original_aspect >= $thumb_aspect )
    							{
    							   $new_height = $thumb_height;
    							   $new_width = $width / ($height / $thumb_height);
    							}else{
    							   $new_width = $thumb_width;
    							   $new_height = $height / ($width / $thumb_width);
    							}
                            }else if(!empty($width) && empty($height)){
                                $thumb_width = $width;
                                $width = imagesx($image);
                                $height = imagesy($image);
                                $new_width = $thumb_width;
                                $new_height = $height / ($width / $thumb_width);
                                $thumb_height = $new_height;
                            }else if(!empty($width) && empty($height)){
                                $thumb_height = $new_height;
                                $width = imagesx($image);
                                $height = imagesy($image);
                                $new_height = $thumb_height;
                                $new_width = $width / ($height / $thumb_height);
                                $thumb_width = $width;
                            }
							$thumb = imagecreatetruecolor( $thumb_width, $thumb_height );
							// Resize and crop
							imagecopyresampled($thumb,
											   $image,
											   0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
											   0 - ($new_height - $thumb_height) / 2, // Center the image vertically
											   0, 0,
											   $new_width, $new_height,
											   $width, $height);
							imagejpeg($thumb, PATH_BASE_ROOT.DS.$result, 95);
							*/
                            /*$factory = new \ImageOptimizer\OptimizerFactory();
                            if( strtolower($extension) == 'png' ){
                                $optimizer = $factory->get('png');
                            }else if( strtolower($extension) == 'gif' ){
                                $optimizer = $factory->get('gif');
                            }else{
                                $optimizer = $factory->get();
                            }
                            $filepath = $websiteThumb .'/'. $name;
                            $optimizer->optimize($filepath);*/
                        } catch (\Exception $e) {
                            $result = $this->getUrlPrefixLang().'/image/placeholder/no-photo-'.$thumbFolder.'.png';
                        } catch (\ImageWorkshopException $e) {
                            $result = $this->getUrlPrefixLang().'/image/placeholder/no-photo-'.$thumbFolder.'.png';
                        } catch (\ImageWorkshopLayerException $e) {
                            $result = $this->getUrlPrefixLang().'/image/placeholder/no-photo-'.$thumbFolder.'.png';
                        }
                    }
                }
            }else{
                $result = $url;
            }
        }else{
            $size = '';
            if(!empty($width)){
                $size = $width;
            }
            if(!empty($height)){
                $size = $size.'x'.$height;
            }
            $result = $this->getUrlPrefixLang().'/image/placeholder/no-photo-'.$size.'.png';
        }
        $pre_dm = substr ( $result , 0 , 17 );
        if($pre_dm == '/custom/domain_1/' ){
            $result = PHOTOLINK.substr ( $result , 17-strlen($result) );
        }
        return $result;
    }

    protected function getFileName($url) {
        $list = explode('/', $url);
        $image_name = end($list);
        return $image_name;
    }
    protected function getFileExtension($url) {
        $list = explode ( '.', $url );
        $file_ext = strtolower(end($list));
        return $file_ext;
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
}
