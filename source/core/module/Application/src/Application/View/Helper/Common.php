<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Common extends App
{
    public function __invoke()
    {
        return $this;
		
    }

    public function isMobile(){
    	$useragent=$_SERVER['HTTP_USER_AGENT'];
    	if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
    		return true;
        return false;
    }
	
	public function replace_string($str){
		//$str=htmlspecialchars($str, ENT_QUOTES, "UTF-8");
		$str=htmlspecialchars(stripslashes(trim($str)), ENT_QUOTES,'UTF-8',true);
		$marked=array("<br/>","<br>","</br>","  ","        ");
		$unmarked=array("","",""," ","");
		return str_replace ( $marked, $unmarked, $str );
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
	public function addcontentCache($idarticle){
		
        $content = ROOT_DIR_FOLDER.'/static_data/article_'.$idarticle.'.cache';
        if(is_file($content)){
            $htmlSub = @file_get_contents($content);
            return $htmlSub;
        }
        return FALSE;
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
	public function getBreadCrumbHome($model = NULL)
    {
        $menu_dir = './module/menus';
        $menu_dir .= '/menus.data';
        $menus = array();
        if(is_file($menu_dir)){
            $menus_string = file_get_contents($menu_dir);
            $menus = json_decode($menus_string, TRUE);
        }
        @usort($menus, function($a, $b){
            if($a['ordering'] == $b['ordering']){
                return 0;
            }
            return $a['ordering'] > $b['ordering'] ? 1 : -1;
        });
        $breadCrumb = "";
        if(count($menus)){
            $breadCrumb = "<div class='breakcum-top cl-box' >";
            foreach($menus as $menu){
                $breadCrumb .=" <a ".(isset($menu['in_page']) && !$menu['in_page'] ? 'target="__blank"' : '')." href= '" . (strpos($menu['link'],'http://') !== FALSE ? $menu['link'] : (FOLDERWEB.'/'.trim($menu['link'],'/'))) . "' class='item-breakcum' title='".$this->view->translate($menu['title'])."' >
                                    ".($menu['icon'] ? ("<span class='icon breadIcon'><img src='".$menu['icon']."' style='display:none' /></span>") : '')."
                                    <span class='txt' >".$this->view->translate($menu['title'])."</span>
                                    ".(isset($menu['is_new']) && $menu['is_hot'] ? ("<span class='icon newho'>".$this->view->translate("new")."</span>") : '')."
                                    ".(isset($menu['is_hot']) && $menu['is_new'] ? ("<span class='icon newhot'>".$this->view->translate("hot")."</span>") : '')."
                                </a>";
            }
			$breadCrumb .= "</div>";
        }

        /*
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
				<a href='" . FOLDERWEB . "/articles/thu-tuc-tra-gop-cua-cong-ty-thien-thien-tan-49' class='item-breakcum' title='".$this->view->translate("installmentpurchase")."' >
					<span class='txt' >".$this->view->translate("installmentpurchase")."</span>		
				</a>
			</div>";
			return $breadCrumb;
        */
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

    public function getMenuRoot($catid){
        if(!is_dir('./module/menu_cached')){
            @mkdir('./module/menu_cached',0777);
        }
        $menu_cached = './module/menu_cached/menu-sub-'.$catid.'.cache';
        if(is_file($menu_cached)){
            $htmlSub = @file_get_contents($menu_cached);
            return $htmlSub;
        }
        return FALSE;
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
			
			$html.= '<span class="rate_1" >' . number_format($price_sale, 0, ',', '.') . ' '.PRICE_UNIT.'</span>';
			if(!empty($price) && $price != $price_sale){
    			$html .= '<span class="rate_2" >' . number_format($price, 0, ',', '.') . ' '.PRICE_UNIT.'</span>';
    		}
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
				$html = '<h3 class="rate-1">Giá : <span class="txt">' . number_format($price_sale, 0, ',', '.') . ' '.PRICE_UNIT.'</span></h3>';
    		    //$html .= '<h3 class="rate-2">Giá khuyến mãi : <span class="txt">' . number_format($price_sale, 0, ',', '.') . ' '.PRICE_UNIT.'</span></h3>';
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
        $url = FOLDERWEB . '/'.$product['products_alias'].'-'.$product['products_id'];
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

    public function getThumbCatImageIcon($catinfo, $width, $height){
        $thumb = new Tts_Thumbnail($width.'x'.$height);
        //$sourceName = str_replace('/custom/domain_1/products/fullsize/', '', $product['thumb_image']);
        $sourceName = explode('/', $catinfo['icon']);
        $image_name = end($sourceName);
        $sourceFolder = 'custom'.DS.'domain_1'.DS.'categories_icons';
        if(!is_dir(PATH_BASE_ROOT.DS.$sourceFolder.DS.'icon-'.$catinfo['categories_id'])){
            @mkdir(PATH_BASE_ROOT.DS.$sourceFolder.DS.'icon-'.$catinfo['categories_id'], 0777);
        }
        $destinyFolder = 'custom'.DS.'domain_1'.DS.'categories_icons'.DS.'icon-'.$catinfo['categories_id'].DS.'thumb-'.$width.'x'.$height;
        $thumbFolder = PATH_BASE_ROOT.DS.$destinyFolder;
        if(is_file($thumbFolder.DS.$image_name)){
            return FOLDERWEB.'/custom/domain_1/categories_icons/'.'icon-'.$catinfo['categories_id'].'/thumb-'.$width.'x'.$height.'/'.$image_name;
        }
        if(!is_dir($thumbFolder)){
            @mkdir($thumbFolder,0777);
        }
        try{
            $result = $thumb->thumbnailFile($image_name, $sourceFolder.DS, $destinyFolder.DS);
            if($result){
                return FOLDERWEB.'/custom/domain_1/categories_icons/'.'icon-'.$catinfo['categories_id'].'/thumb-'.$width.'x'.$height.'/'.$image_name;
            }else{
                return FOLDERWEB.$catinfo['icon'];
            }
        }catch(\Exception $ex){
            return FOLDERWEB.$catinfo['icon'];
        }
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
        $sourceName = explode('/', $image);
		$image_name = end($sourceName);
		$foloder_name='product'.$product['products_id'];
		$listfolder=explode($foloder_name,$image);
        $sourceFolder = $listfolder[0].DS.'product'.$product['products_id'].DS;
        $destinyFolder = $sourceFolder.DS.'product'.$product['products_id'].DS.'fullsize';
		$thumbFolder = PATH_BASE_ROOT.$destinyFolder;
		if(is_file($thumbFolder.DS.$image_name)){
			return $sourceFolder.'/fullsize/'.$image_name;
		}
		if(!is_dir($thumbFolder)){
			@mkdir($thumbFolder,0777);
		}
        $result = $thumb->thumbnailFile($image_name, $sourceFolder, $destinyFolder.DS);
		if($result){
			return $sourceFolder.'/fullsize/'.$image_name;
		}else{
			return $product['thumb_image'];
		}
	}
	public function getThumbProductSlideshow_BK($product, $image, $width, $height){
		$thumb = new Tts_Thumbnail($width.'x'.$height);
		
		// /custom/domain_1/products/2016/02/01/product1454338406/flouncing-folding-lotus-leaves-princess-dome-parasol-sun-rain-umbrella-pink-4862-8194061-1-zoom.jpg
		
        $sourceName = explode('/', $image);
		$image_name = end($sourceName);
		$foloder_name='product'.$product['products_id'];
		$listfolder=explode($foloder_name,$image);
        $sourceFolder = $listfolder[0].DS.'product'.$product['products_id'].DS;
        $destinyFolder = $sourceFolder.DS.'product'.$product['products_id'].DS.'thumb-'.$width.'x'.$height;
		$thumbFolder = PATH_BASE_ROOT.$destinyFolder;
		if(is_file($thumbFolder.DS.$image_name)){
			return $sourceFolder.'/thumb-'.$width.'x'.$height.'/'.$image_name;
		}
		if(!is_dir($thumbFolder)){
			@mkdir($thumbFolder,0777);
		}
        $result = $thumb->thumbnailFile($image_name, $sourceFolder, $destinyFolder.DS);
		if($result){
			return $sourceFolder.'/thumb-'.$width.'x'.$height.'/'.$image_name;
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
	public function subChars($string="", $string_leng=250){
		if(strlen($string) > $string_leng){
			$string = substr($string, 0, $string_leng);
            $string .= " ... ";
			/*$space = strripos($string, " ");
			$string = substr($string, 0, $space )." ... ";*/
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
		foreach ($cart as $products_id => $product) {
			if( $products_id == 'coupon' || $products_id == 'shipping' ) continue;
			foreach ($product['product_type'] as $product_type_id => $product_type) {
				$price_total += $product_type['price_total'];
				$price_total_old += $product_type['price_total_old'];
			}
		}
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

	public function getNumberProductInCart($carts = array()){
		$number = 0;
		if(!empty($_SESSION['cart']) && empty($carts)){
            $carts = $_SESSION['cart'];
        }
		foreach ($carts as $products_id => $product) {
			if( $products_id == 'coupon' || $products_id == 'shipping' ) continue;
			foreach ($product['product_type'] as $product_type_id => $product_type) {
				$number += $product_type['quantity'];
			}
		}
		return $number;
	}

	public function getCurrentPageURL() {
		$pageURL = 'http';
		if (isset ( $_SERVER ["HTTPS"] ) && $_SERVER ["HTTPS"] == "on") {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if (isset ( $_SERVER ["SERVER_PORT"] ) && $_SERVER ["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER ["SERVER_NAME"] . ":" . $_SERVER ["SERVER_PORT"] . $_SERVER ["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER ["SERVER_NAME"] . $_SERVER ["REQUEST_URI"];
		}
		return $pageURL;
	}
	function strip_tags_content($text, $tags = '', $invert = FALSE) {
	  preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags); 
	  $tags = array_unique($tags[1]);
	  if(is_array($tags) AND count($tags) > 0) { 
		if($invert == FALSE) { 
		  return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text); 
		} 
		else { 
		  return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text); 
		} 
	  } 
	  elseif($invert == FALSE) { 
		return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text); 
	  } 
	  return $text; 
	}

	/* code toan*/
	public function buildParamsForUrlFromArray( $params ){
		$url = '';
		foreach ( $params as $key => $value) {
			if( !empty($value) ){
				$url .= '&' .$key .'='. (is_array($value) ? implode('', $value) : $value);
			}
		}
		if( !empty($value) )
        	return substr($url, 1);
        return $url;
    }

    public function getPageOffset($page, $page_size ){
    	$page = 0;
		if ($page > 1) {
            $page = ($page - 1) * $page_size;
        }
        return $page;
    }

    public function getDayBetweenDate( $to , $from = '' ){
    	$day = 0;
    	try{
	    	$now = time();
	    	if( !empty($from) ){
	    		$now = strtotime($from);
	    	}
	        $your_date = strtotime($to);
			$datediff = $now - $your_date;
			$day = floor($datediff / (60 * 60 * 24));
		}catch(\Exception $ex){
			$day = 0;
		}
		return $day; 
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
        if(FOLDERWEB == 'http://thientan.local'){//sua cho nay them cai site cua cac dai ca vo day ||
            return false;
        }
        $_imageSource=$_SERVER['DOCUMENT_ROOT']."/".$_imageSource;
        $_destination=$_SERVER['DOCUMENT_ROOT']."/".$_destination;
		/*
        if (true === $this->checkExtensionFileName($_fileName)){
            $this->setImgSource($_imageSource.$_fileName);
            $_thumb = new \Imagick($this->_source);
            $_thumb->thumbnailImage($this->getThumbData('width',$_thumb),$this->getThumbData('height',$_thumb));
            $_thumb->writeImage($_destination.$_fileName);
        } */
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
        $width = $w;
        $height = $h;
        if($w <= $this->_max_width && $h <= $this->_max_height){
            $width = $w;
            $height = $h;
        }elseif($w <= $this->_max_width && $h >= $this->_max_height){
            $height = $this->_max_height;
            $ratio = $height/$h;
            $width = round($ratio * $w, 0);
        }elseif($w > $this->_max_width && $h <= $this->_max_height){
            $width = $this->_max_width;
            $ratio = $width / $w;
            $height = round($ratio * $h,0);
        }elseif($w > $this->_max_width && $h >= $this->_max_height){
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