<?php
namespace Application\View\Helper;
use Zend\View\Helper\AbstractHelper;
//use Zend\I18n\Translator\Translator;
class Common extends AbstractHelper
{
    public function __invoke()
    {
        return $this;
		
    }
	
	public function getArticle($model, $id){
		try{
			$article = $model->getRow($id);
			if($article){
				return $article['articles_content'];
			}
		}catch(\Exception $ex){
		}
		return "";
	}
	public function bannershowlist($model, $position, $size, $type){
		try{
			$banner_show = $model->getBanner($position, $size, $type);
			$list_banner="";
			if(count($banner_show)>0){
				foreach($banner_show as $listdata){
					if(is_file(ROOT_DIR.$listdata["file"])){
						$list_banner.='<div class="ctadv-rbox">';
							$thumb=FOLDERWEB."/thumb.php?src=".FOLDERWEB.$listdata["file"]."&w=".$listdata["width"]."&h=".$listdata["height"]."";
							$list_banner.='<a href="'.$listdata["link"].'" target="_blank">';
								$list_banner.='<img src="'.$thumb.'" width="'.$listdata["width"].'" height="'.$listdata["height"].'" alt="'.$listdata["banners_title"].'"/>';
							$list_banner.='</a>';
						$list_banner.='</div>';
					}
				}
			}
			return $list_banner;
		}catch(\Exception $ex){
		}
		return "";
	}
	public function getBreadCrumbHome($model)
    {
		try{
			$news_tech_cat = $model->getCategoryArticleTech();		
			//var_dump($news_tech_cat );die();
			if($news_tech_cat){
				$htmlTech = "<a href=".FOLDERWEB.'/chuyen-muc/'.$news_tech_cat['categories_articles_alias'].'-'.$news_tech_cat['categories_articles_id']." class='item-breakcum' title='".$this->view->translate('technews')."' >";
				if(isset($news_tech_cat['categories_articles_alias'])){
					$htmlTech = "<a href=" . FOLDERWEB.'/chuyen-muc/'.$news_tech_cat['categories_articles_alias'].'-'.$news_tech_cat['categories_articles_id'] . " class='item-breakcum' title='".$this->view->translate('technews')."' >
								<span class='txt' >".$this->view->translate('technews')."</span>
							</a>";
				}
			}
		}catch(\Exception $ex){
			
		}
		$breadCrumb = '';
			$breadCrumb = "<div class='breakcum-top cl-box' >
				<a href= '" . FOLDERWEB . "/hang-sap-ve' class='item-breakcum newh' title='".$this->view->translate("commingsoon")."' >
					<span class='txt' >".$this->view->translate("commingsoon")."</span>
					<span class='icon newho'>".$this->view->translate("new")."</span>
				</a>
				<a href=" . FOLDERWEB.'/deals' . " class='item-breakcum' title='".$this->view->translate("hotdeal")."' >
					<span class='txt' >".$this->view->translate("hotdeal")."</span>
				</a>
				".(isset($htmlTech) ? $htmlTech : '')."
				<a href='" . FOLDERWEB . "/bai-viet/thu-tuc-tra-gop-cua-cong-ty-thien-thien-tan-49' class='item-breakcum' title='".$this->view->translate("installmentpurchase")."' >
					<span class='txt' >".$this->view->translate("installmentpurchase")."</span>		
				</a>
			</div>";
			return $breadCrumb;
    }
	function timeAgo($time_ago){
		$cur_time 	= time(); 
		$time_ago_ = strtotime($time_ago);
		$time_elapsed 	= $cur_time - $time_ago_;
		$time_elapsed = ($time_elapsed<0?0:$time_elapsed);
		$seconds 	= $time_elapsed ;
		$minutes 	= round($time_elapsed / 60 );
		$hours 		= round($time_elapsed / 3600);
		$days 		= round($time_elapsed / 86400 );
		$weeks 		= round($time_elapsed / 604800);
		$months 	= round($time_elapsed / 2600640 );
		$years 		= round($time_elapsed / 31207680 );
		// Seconds
		if($seconds <= 60){
			echo "$seconds seconds ago";
		}
		//Minutes
		else if($minutes <=60){
			if($minutes==1){
				echo "one minute ago";
			}
			else{
				echo "$minutes minutes ago";
			}
		}
		//Hours
		else if($hours <=24){
			if($hours==1){
				echo "an hour ago";
			}else{
				echo "$hours hours ago";
			}
		}
		//Days
		else if($days <= 7){
			if($days==1){
				echo "yesterday";
			}else{
				echo "$days days ago";
			}
		}
		//Weeks
		else if($weeks <= 4.3){
			if($weeks==1){
				echo "a week ago";
			}else{
				echo "$weeks weeks ago";
			}
		}
		//Months
		else if($months <=12){
			if($months==1){
				echo "a month ago";
			}else{
				echo "$months months ago";
			}
		}
		//Years
		else{
			if($years==1){
				echo "one year ago";
			}else{
				echo "$years years ago";
			}
		}
	}
	
	public function getAllCategoryMenuMobile($data,$parent,$lv=0){
        if(isset($data[$parent]) && COUNT($data[$parent])>0 ){
			$html = "<ul>";
			foreach($data[$parent] as $itemCate){
				$urlOwn_ = FOLDERWEB.'/danh-muc/'.$itemCate['categories_alias'].'-'.$itemCate['categories_id'];
				$html  .= 	"<li>
										<a href='".$urlOwn_."' >".$itemCate['categories_title']."</a>" 
										.$this->getAllCategoryMenuMobile($data,$itemCate['categories_id'],($lv+1)). "					
									</li>";
			}
			if($lv==0){
				if(!isset($_SESSION['MEMBER'])){
					$html .= 	"<li>
										<a href='#login-user' >Tài khoản</a>
										<ul class='btn-losin' >
											<li><a href='".FOLDERWEB."/sign-in' class='btnsiglog' >Đăng nhập</a></li>
											<li><a href='".FOLDERWEB."/sign-up' class='btnsiglog' >".$this->view->translate('register')."</a></li>
										</ul>
									</li>";
				}else{
					$html .= 	"<li>
										<a href='#info-user' >".$_SESSION['MEMBER']['full_name']."</a>
										<ul class='btn-losin' >
											<li>
												<a href='".FOLDERWEB."/profile' >".$this->view->translate('account_info')."</a>
											</li>
											<li  >
												<a href='".FOLDERWEB."/profile/edit' >".$this->view->translate('update_info') ."</a>
											</li>
											<li >
												<a href='".FOLDERWEB."/profile/point' >".$this->view->translate('score')."</a>
											</li>
											<li >
												<a href='".FOLDERWEB."/profile/history' >".$this->view->translate('history') ."</a>
											</li>
											<li>
												<a href='".FOLDERWEB."/profile/payment' >".$this->view->translate('buyed') ."</a>
											</li>
											<li>
												<a href='".FOLDERWEB."/profile/comments' >".$this->view->translate('review_list')."</a>
											</li>
											<li>
												<a href='".FOLDERWEB."/logout' >".$this->view->translate('logout')."</a>
											</li>
										</ul>
									</li>";
				}
			}
			$html .= 	"</ul>";
			return $html;
		}else{
			return '';
		}
    }
	
	public function getConfigFooter(){
        $folder = PATH_BASE_ROOT . "/cache/";
		$fileName = 'footer' . '.xml';
		$filePath = $folder . $fileName;
		$config = array();
		if(is_file($filePath)){
			try{
				$doc = new \DOMDocument();
				$doc->load($filePath);
				$x_help = $doc->getElementsByTagName( "help" );
				$x_aboutus = $doc->getElementsByTagName( "aboutus" );
				$x_lien = $doc->getElementsByTagName( "lien" );
				$x_linkmak = $doc->getElementsByTagName( "linkmak" );				
				$x_payment = $doc->getElementsByTagName( "payment" );				
				$x_transportation = $doc->getElementsByTagName( "transportation" );				
				if ($x_help->length > 0) {
					foreach( $x_help as $i => $node ){
						$config['help'] = $node->nodeValue;
						break;
					}
				}
				if ($x_aboutus->length > 0) {
					foreach( $x_aboutus as $i => $node ){
						$config['aboutus'] = $node->nodeValue;
						break;
					}
				}
				if ($x_lien->length > 0) {
					foreach( $x_lien as $i => $node ){
						$config['lien'] =$node->nodeValue;
						break;
					}
				}
				if ($x_linkmak->length > 0) {
					foreach( $x_linkmak as $i => $node ){
						$config['linkmk'] = $node->nodeValue;
						break;
					}
				}
				if ($x_payment->length > 0) {
					foreach( $x_payment as $i => $node ){
						$config['payment'] = $node->nodeValue;
						break;
					}
				}
				if ($x_transportation->length > 0) {
					foreach( $x_transportation as $i => $node ){
						$config['transportation'] = $node->nodeValue;
						break;
					}
				}
			 }catch (\Exception $ex){
				return $config;
			}
		}
		return $config;
    }
	
    public function viewPriceProductList($price_sale, $price){
    	
    	if(!empty($price_sale) ){
    		$html = '<h3 class="rite">';
			if(!empty($price) && $price != $price_sale){
    			$html .= '<span class="rate_2" >' . number_format($price, 0, ',', '.') . ' '.PRICE_UNIT.'</span>';
    		}
			$html.= '<span class="rate_1" >' . number_format($price_sale, 0, ',', '.') . ' '.PRICE_UNIT.'</span>';
    		$html .= '</h3>';
    		return $html;
    	}
    	
    	if(empty($price_sale) && !empty($price)){
    		$html = '<h3 class="rite">';
    		$html.= '<span class="rate_1" >' . number_format($price, 0, ',', '.') . ' '.PRICE_UNIT.'</span>';
    		$html .= '</h3>';
    		return $html;
    	}
    	//$translator = $this->getServiceManager()->get('translator');
    	//$translator = new Translator();
    	return '<h3 class="rite"><span>' . $this->view->translate('Call','','') . '</span></h3>';
    	//return '<h3 class="rite"><span>Call</span></h3>';
    }
    
    public function viewPriceProductDetail($price_sale, $price){
    	
    	if($price_sale && $price){   		
            if($price_sale != $price){
				$html = '<h3 class="rate-1">Giá : <span class="txt">' . number_format($price, 0, ',', '.') . ' '.PRICE_UNIT.'</span></h3>';
    		    $html .= '<h3 class="rate-2">Giá khuyến mãi : <span class="txt">' . number_format($price_sale, 0, ',', '.') . ' '.PRICE_UNIT.'</span></h3>';
            }else{
				$html = '<h3 class="rate-1 no-deal">Giá : <span class="txt">' . number_format($price, 0, ',', '.') . ' '.PRICE_UNIT.'</span></h3>';
			}
    		return $html;
    	}
    	
    	if($price_sale){
    		return '<h3 class="rate-1 no-deal">Giá : <span class="txt">' . number_format($price_sale, 0, ',', '.') . ' '.PRICE_UNIT.'</span></h3>';
    	}
    	
    	if($price){
    		return '<h3 class="rate-1 no-deal">Giá : <span class="txt">' . number_format($price, 0, ',', '.') . ' '.PRICE_UNIT.'</span></h3>';
    	}
    	return '<h3 class="rate-1 no-deal">Giá : <span class="txt">'.$this->translate('Call').'</span></h3>'; 
    	
    }
    
    public function getToBuy($price_sale, $price, $is_available){
    	if(($price_sale>0 || $price>0) && $is_available>0){
    		return true;
    	}
    	return false;
    }
    
    public function htmlItemProduct($row){    	
    	$html = "<div class=\"item-product nth-{$row['index']}\" >
    				<div class=\"ct-item-product\" >";
    	
    	$html .= "<a href=\"{$row['link']}\" class=\"img\" ><img src=\"" . FOLDERWEB . "/thumb.php?src={$row['img']}&w=162\" width=\"162\" /></a>";
    	$html .= '<div class="intro" >';
    		$html .= "<h2 class=\"name\" >
    				<a href=\"{$row['link']}\" title=\"{$row['products_title']}\" >{$row['products_title']}</a></h2>";
    	$html .= $this->viewPriceProductList($row['price_sale'], $row['price']);
    	$html .= '<div class="btn-grou cl-box" >';
    	if($this->getToBuy($row['price_sale'], $row['price'], $row['is_available'])){
    		$html .= "<a href=\"{$row['linkAddToCart']}\" class=\"btn-casd\"><i class=\"icon i-cart\" ></i><span>".$this->view->translate('Buy')."</span></a>";
    	}else{
    		$html .= '<a href="javascript:;" class="btn-casd btn-nobuy" ><i class="icon i-cart" ></i><span>'.$this->view->translate('Buy').'</span></a>';
    	}    		            	
    	$html .= '<a href="' . $row['link'] . '" title="' . $row['products_title'] . '" class="btn-detail" ><span>'.$this->view->translate('Detail').'</span></a>';
    	$html .= 	'</div></div></div></div>';
    	return $html;
    }

    
	public function getUrlImage($src)
    {
	    $srcResult = null;
		if(!empty($src) && is_file(FOLDERWEB.$src)){
			$srcResult = $src;
		}else{
			$srcResult = NoPhotoImage;
		}
		return $srcResult;
    }
	public function toAlias($txt) {
    	if ($txt == '')
    		return '';
    	$marked = array ("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă","ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề","ế", "ệ", "ể", "ễ", "ế",    			"ì", "í", "ị", "ỉ", "ĩ","ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ","ờ", "ớ", "ợ", "ở", "ỡ","ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ","ỳ", "ý", "ỵ", "ỷ", "ỹ","đ","À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă","Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ","È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ","Ì", "Í", "Ị", "Ỉ", "Ĩ","Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ","Ờ", "Ớ", "Ợ", "Ở", "Ỡ","Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ","Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ","Đ", " ", "&", "?", "/", ".", ",", "$", ":", "(", ")", "'", ";", "+", "–", "’" );
    
    	$unmarked = array ("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e","e", "e", "e", "e", "e", "i", "i", "i", "i", "i","o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o","o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",  "y", "y", "y", "y", "y", "d", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "D", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-" );
    
    	$tmp3 = (str_replace ( $marked, $unmarked, $txt ));
    	$tmp3 = rtrim ( $tmp3, "-" );
    	$tmp3 = preg_replace ( array ('/\s+/', '/[^A-Za-z0-9\-]/' ), array ('-', '' ), $tmp3 );
    	$tmp3 = preg_replace ( '/-+/', '-', $tmp3 );
    	$tmp3 = strtolower ( $tmp3 );
    	return $tmp3;
    }

    public function getCartBreadcrumb($config){
        $breadCrumb = "
        <div class='nav-add-cart cl-box' >
                    	<ul class='list-n01 cl-box' >
                            <li>
                            	<a href='{$config['id_article_freeship']}' class='ct-n45' >
                                	<span class='ct-omai'><i class='icon i-tran'></i>".$this->view->translate("freeshipping")."</span>
                                </a>
                            </li>
                            <li>
                            	<a href='{$config['id_article_payment']}' class='ct-n45' >
                                	<span class='ct-omai'><i class='icon i-money'></i>".$this->view->translate("paymentwhengetproduct")."</span>
                                </a>
                            </li>
                            <li>
                            	<a href='{$config['id_article_change']}' class='ct-n45' >
                                	<span class='ct-omai'><i class='icon i-refesh'></i>".$this->view->translate("changequickpay")."</span>
                                </a>
                            </li>
                         </ul>
                    </div>

        ";
        return $breadCrumb;
    }

    public function hasPermission($module, $controller, $action){
        if(isset($_SESSION['MEMBER'])){
            if($_SESSION['MEMBER']['type'] == 'admin'){
                return TRUE;
            }
            if(isset($_SESSION['MEMBER']['permissions'])){
                if(isset($_SESSION['MEMBER']['permissions'][$module][$controller][$action])){
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

    public function getCategoriesUrl($cat = array()){
        $url = FOLDERWEB . '/danh-muc/'.$cat['categories_alias'].'-'.$cat['categories_id'];
        return $url;
    }

    public function getProductsUrl($product = array()){
        $url = FOLDERWEB . '/san-pham/'.$product['products_alias'].'-'.$product['products_id'];
        return $url;
    }
	
    public function getManuUrl($manu = array()){
        $url = FOLDERWEB . '/listing/'.$this->toAlias($manu['manufacturers_name']).'-'.$manu['manufacturers_id'];
        return $url;
    }
	
    public function getManuCatUrl($manu = array(), $catinfo){
        $url = FOLDERWEB .'/'.$catinfo['categories_alias'].'-'.$catinfo['categories_id']. '/'.$this->toAlias($manu['manufacturers_name']).'-'.$manu['manufacturers_id'];
        return $url;
    }

    public function getThumbProductImage($product, $width, $height){
        $thumb = new Tts_Thumbnail($width.'x'.$height);
        //$sourceName = str_replace('/custom/domain_1/products/fullsize/', '', $product['thumb_image']);
        $sourceName = explode('/', $product['thumb_image']);
		$image_name = end($sourceName);
        $sourceFolder = 'custom'.DS.'domain_1'.DS.'products'.DS.'fullsize'.DS.'product'.$product['products_id'].DS;
        $destinyFolder = 'custom'.DS.'domain_1'.DS.'products'.DS.'fullsize'.DS.'product'.$product['products_id'].DS.'thumb-'.$width.'x'.$height;
		$thumbFolder = PATH_BASE_ROOT.DS.$destinyFolder;
		if(is_file($thumbFolder.DS.$image_name)){
			return FOLDERWEB.'/custom/domain_1/products/fullsize/product'.$product['products_id'].'/thumb-'.$width.'x'.$height.'/'.$image_name;
		}
		if(!is_dir($thumbFolder)){
			@mkdir($thumbFolder,0777);
		}
		try{
			$result = $thumb->thumbnailFile($image_name, $sourceFolder, $destinyFolder.DS);
			if($result){
				if(isset($_GET['dev']) && $product['products_id'] == 876){
					echo $image_name."<br />";
					echo $sourceFolder."<br />";
					echo $destinyFolder.DS."<br />";
					die;
				}
				
				return FOLDERWEB.'/custom/domain_1/products/fullsize/product'.$product['products_id'].'/thumb-'.$width.'x'.$height.'/'.$image_name;
			}else{
				return $product['thumb_image'];
			}
		}catch(\Exception $ex){
		
			return $product['thumb_image'];
		}
    }

    public function getThumbArticleImage($article, $width, $height){
        $thumb = new Tts_Thumbnail($width.'x'.$height);
        //$sourceName = str_replace('/custom/domain_1/articles/fullsize/', '', $article['thumb_images']);
        $sourceName = explode('/', $article['thumb_images']);
		$image_name = end($sourceName);
        $sourceFolder = 'custom'.DS.'domain_1'.DS.'articles'.DS.'fullsize'.DS.'article'.$article['articles_id'].DS;
        $destinyFolder = 'custom'.DS.'domain_1'.DS.'articles'.DS.'fullsize'.DS.'article'.$article['articles_id'].DS.'thumb-'.$width.'x'.$height;
		$thumbFolder = PATH_BASE_ROOT.DS.$destinyFolder;
		if(is_file($thumbFolder.DS.$image_name)){
			return FOLDERWEB.'/custom/domain_1/articles/fullsize/article'.$article['articles_id'].'/thumb-'.$width.'x'.$height.'/'.$image_name;
		}
		if(!is_dir($thumbFolder)){
			@mkdir($thumbFolder,0777);
		}
		
        $result = $thumb->thumbnailFile($image_name, $sourceFolder, $destinyFolder.DS);
		if($result){
			return FOLDERWEB.'/custom/domain_1/articles/fullsize/article'.$article['articles_id'].'/thumb-'.$width.'x'.$height.'/'.$image_name;
		}else{
			return $article['thumb_images'];
		}
    }
	
	public function getThumbProductSlideshow($product, $image, $width, $height){
		$thumb = new Tts_Thumbnail($width.'x'.$height);
        //$sourceName = str_replace('/custom/domain_1/products/fullsize/', '', $product['thumb_image']);
        $sourceName = explode('/', $image);
		$image_name = end($sourceName);
        $sourceFolder = 'custom'.DS.'domain_1'.DS.'products'.DS.'fullsize'.DS.'product'.$product['products_id'].DS;
        $destinyFolder = 'custom'.DS.'domain_1'.DS.'products'.DS.'fullsize'.DS.'product'.$product['products_id'].DS.'thumb-'.$width.'x'.$height;
		$thumbFolder = PATH_BASE_ROOT.DS.$destinyFolder;
		if(is_file($thumbFolder.DS.$image_name)){
			return FOLDERWEB.'/custom/domain_1/products/fullsize/product'.$product['products_id'].'/thumb-'.$width.'x'.$height.'/'.$image_name;
		}
		if(!is_dir($thumbFolder)){
			@mkdir($thumbFolder,0777);
		}
		
        $result = $thumb->thumbnailFile($image_name, $sourceFolder, $destinyFolder.DS);
		if($result){
			return FOLDERWEB.'/custom/domain_1/products/fullsize/product'.$product['products_id'].'/thumb-'.$width.'x'.$height.'/'.$image_name;
		}else{
			return $product['thumb_image'];
		}
	}
	
	public function subString($string="", $string_leng=250){
		if(strlen($string) > $string_leng){
			$string = substr($string, 0, $string_leng);
			$space = strripos($string, " ");
			$string = substr($string, 0, $space )." ... ";
		}
		return $string;
	}

    public function getBreadCrumbManu($row = 0){
        $html = '<div class="breakcum-top cl-box">
                    <a href="'.FOLDERWEB.'" class="item-breakcum" title="Trang chủ">
                        <span class="txt">'.$this->view->translate('home_page').'</span>
                        <span class="corer">&gt;&gt;</span>
                    </a>
                    <a href="'.FOLDERWEB.'/tat-ca-nha-san-xuat" class="item-breakcum '.(!$row ? 'active' : '').' " title="'.$this->view->translate('All_Manu').'">
                        <span class="txt">'.$this->view->translate('All_Manu').'</span>
                    </a>';
        if($row){
            $html .= '<a href="'.FOLDERWEB.'/nha-san-xuat/'.$this->toAlias($row['manufacturers_name']).'-'.$row['manufacturers_id'].'" class="item-breakcum active " title="'.$row['manufacturers_name'].'">
                        <span class="corer">&gt;&gt;</span>
                        <span class="txt">'.$row['manufacturers_name'].'</span>
                    </a>';
        }
        $html .= "</div>";
        return $html;
    }

    public function getBreadCrumbManuListing($catinfo, $manu){
		if(!$catinfo){
			return $this->getBreadCrumbManu($manu);
		}else{
			$html = '<div class="breakcum-top cl-box">
						<a href="'.FOLDERWEB.'" class="item-breakcum" title="'.$this->view->translate('home_page').'">
							<span class="txt">'.$this->view->translate('home_page').'</span>
							<span class="corer">&gt;&gt;</span>
						</a>
						<a href="'.FOLDERWEB.'/danh-muc/'.$catinfo['categories_alias'].'-'.$catinfo['categories_id'].'" class="item-breakcum" title="'.$catinfo['categories_title'].'">
							<span class="txt">'.$catinfo['categories_title'].'</span>
							<span class="corer">&gt;&gt;</span>
						</a>
						<a href="'.FOLDERWEB.'/'.$catinfo['categories_alias'].'-'.$catinfo['categories_id'].'/'.$this->toAlias($manu['manufacturers_name']).'-'.$manu['manufacturers_id'].'" class="item-breakcum active" title="'.$manu['manufacturers_name'].'">
							<span class="txt">'.$manu['manufacturers_name'].'</span>
						</a>';
			$html .= "</div>";
			return $html;
		}
    }
	
	public function getBreadCrumbDeal($row = 0){
		$html = '<div class="breakcum-top cl-box">
                    <a href="'.FOLDERWEB.'" class="item-breakcum" title="Trang chủ">
                        <span class="txt">Trang chủ</span>
                        <span class="corer">&gt;&gt;</span>
                    </a>
                    <a href="'.FOLDERWEB.'/deals" class="item-breakcum '.(!$row ? 'active' : '').' " title="'.$this->view->translate('all_deal').'">
                        <span class="txt">'.$this->view->translate('all_deal').'</span>
                    </a>';
        if($row){
            $html .= '<a href="'.FOLDERWEB.'/deals/'.$this->toAlias($row['categories_title']).'-'.$row['categories_id'].'" class="item-breakcum active " title="'.$row['categories_id'].'">
                        <span class="corer">&gt;&gt;</span>
                        <span class="txt">'.$row['categories_title'].'</span>
                    </a>';
        }
        $html .= "</div>";
        return $html;
	}
	
	public function calTotalCart($cart){
		$price_total = 0;
		$price_total_old = 0;
		$price_total = array_map(function($a){
			if(isset($a['price_total'])){
				return $a['price_total'];
			}
			return 0;
		}, $cart);
		$price_total = array_sum($price_total);
		$price_total_old = array_map(function($a){
			if(isset($a['price_total_old'])){
				return $a['price_total_old'];
			}
		}, $cart);
		$price_total_old = array_sum($price_total_old);
		$price_total_orig = $price_total;
		if(isset($cart['coupon'])){
			//if($price_total >= $cart['coupon']['min_price_use'] && $price_total <= $cart['coupon']['max_price_use']){
			$price_total =  $price_total > $cart['coupon']['coupon_price'] ? ($price_total - $cart['coupon']['coupon_price']) : 0;
			$price_total_old =  $price_total_old > $cart['coupon']['coupon_price'] ? ($price_total_old - $cart['coupon']['coupon_price']) : 0;
			//}
			//$price_total -= $cart['coupon']['coupon_price'];
			//$price_total_old -= $cart['coupon']['coupon_price'];
		}
		return array(
			'price_total' => $price_total,
			'price_total_old' => $price_total_old,
			'price_total_orig' => $price_total_orig,
		);
	}

}


class Tts_Thumbnail {
    protected $_source;
    protected $_max_width;
    protected $_max_height;
    public function __construct($_thumbSize = '800x600' ){
        $_thumbSize = explode("x",$_thumbSize);
        $this->setMaxSize($_thumbSize[0],$_thumbSize[1]);
    }
    public function thumbnail($_imageSource = 'uploaded/', $_destination = 'uploaded/thumb/'){
        $_imageSource=$_SERVER['DOCUMENT_ROOT']."/".$_imageSource;
        $_destination=$_SERVER['DOCUMENT_ROOT']."/".$_destination;
        $files = $_FILES;
        foreach ($files as $file => $f){
            if (true === $this->checkExtensionFileName($f['name'])){
                $this->setImgSource($_imageSource.$f['name']);
                $_thumb = new Imagick($this->_source);
                $_thumb->thumbnailImage($this->getThumbData('width',$_thumb),$this->getThumbData('height',$_thumb));
                $_thumb->writeImage($_destination.$f['name']);
            }
        }
        return true;
    }
    //thumbnail images file in zip file upload, check images file for thumbnail
    public function thumbnailFile($_fileName,$_imageSource = 'uploaded/source/', $_destination = 'uploaded/thumb/'){
        /*$_imageSource=$_SERVER['DOCUMENT_ROOT']."/".$_imageSource;
        $_destination=$_SERVER['DOCUMENT_ROOT']."/".$_destination;

        if (true === $this->checkExtensionFileName($_fileName)){
            $this->setImgSource($_imageSource.$_fileName);
            $_thumb = new \Imagick($this->_source);
            $_thumb->thumbnailImage($this->getThumbData('width',$_thumb),$this->getThumbData('height',$_thumb));
            $_thumb->writeImage($_destination.$_fileName);
        }*/
        return true;
    }
    // Set destination filename
    public function setImgSource( $source ){
        $this->_source = $source;
    }

    // Set maximum size of thumbnail
    public function setMaxSize ( $max_width = 0, $max_height = 0 ){
        $this->_max_width = $max_width;
        $this->_max_height = $max_height;
    }

    // Get info about original image
    public function getImageData( $data ,$thumb){
        switch ($data){
            case 'width':
                return $thumb->getImageWidth();
            case 'height':
                return $thumb->getImageHeight();
        }
    }

    // Get info about thumbnail
    public function getThumbData($data,$thumb){
        $w=$this->GetImageData('width',$thumb);
        $h=$this->GetImageData('height',$thumb);
        $w_ratio = $this->_max_width  / $w;
        $h_ratio = $this->_max_height / $h;

        if ($w_ratio>$h_ratio){
            if ($h>$this->_max_height){
                $width=round($w*$h_ratio,0);
                $height=$this->_max_height;
            }else {
                $width=round($w*$h_ratio,0);
                $height=$h;
            }
        }else {
            if ($w>$this->_max_width){
                $width=$this->_max_width;
                $height=round($h*$w_ratio,0);
            }else {
                $width=$w;
                $height=round($h*$w_ratio,0);
            }
        }
        switch ( $data ){
            case 'width':
                return $width;
                break;
            case 'height':
                return $height;
                break;
        }
    }
    //check file image thumbnail
    public function checkExtensionFileName($filename){
        $temp = explode(".",strtolower(str_replace(" ",'',$filename)));
        $extension = array('jpg','png','gif','bmp');
        if (!in_array($temp[count($temp)-1],$extension)){
            return false;
        }
        return true;
    }
	
	
	
}