<?php
namespace Application\View\Helper;
use Application\View\Helper\App;
use Stringy\Stringy;
use Truncate\TruncateText;
use Truncate\DOMLettersIterator;
use Truncate\DOMWordsIterator;
use Truncate\TruncateHTML;

class Html extends App
{
    public function getHtmlOptionTagSortFilter() {
        $translator = $this->getTranslator ();
        $array_sort = array('price_asc'  => 'txt_sort_ascending' , 
                            'price_desc' => 'txt_sort_price_reduced', 
                            'new'  => 'txt_sort_newest',
                            'old'  => 'txt_sort_oldest', 
                            'az'   => 'txt_sort_az', 
                            'za'   => 'txt_sort_za');
        $html = "";
        $sort = $this->view->Params()->fromQuery('sort', '');
        foreach ($array_sort as $key => $isort) {
            $selected = "";
            if($sort == $key){
                $selected = "selected";
            }
            $html .= "<option value='{$key}' {$selected} >".$translator->translate($isort)."</option>";
        }
        return $html;
    }

    public function getMetaHeader() {
        $html = '';
        if( !empty($this->datas) ){
            $website = $this->getDatas()->website;
            $coz_domain = $this->getDatas()->domain;
            $domain = $this->view->Websites()->getDomain();
            $favicon = $this->getDatas()->favicon;
            $last_letter = substr($domain, -6, 6);
            $noindex = '';
            if( strtolower($last_letter) == 'coz.vn'){
               $noindex = "<meta name='robots' content='noindex, nofollow' >"; 
			   //<link rel='amphtml' href='http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}' />
            }else{
				 $noindex = "<meta name='robots' content='index,follow' />"; 
			}
            $html .= "<link rel='shortcut icon' href='{$favicon}' type='image/png' data-domain='{$last_letter}' />
                <meta charset='utf-8' />
                {$noindex}
                <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1' /><![endif]-->
                <meta content='width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=0' name='viewport' />
                <link rel='canonical' href='http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}' />
               
                <meta name='dc.created' content='{$website['date_create']}' />
                <meta name='dc.publisher' content='{$domain}' />
                <meta name='dc.rights.copyright' content='{$domain}' />
                <meta name='dc.creator.name' content='{$domain}' />
                <meta name='dc.creator.email' content='{$website['website_email_admin']}' />
                <meta name='dc.identifier' content='http://{$domain}' />
                <meta name='copyright' content='{$domain}' />
                <meta name='author' content='{$domain}' />
                <meta http-equiv='content-language' content='vi' />
                <meta http-equiv='X-UA-Compatible' content='IE=EmulateIE7' />
                <meta property='og:site_name' content='{$website['website_name']}' />
                <meta name='msnbot' content='all,index,follow' />
                <meta property='og:type' content='website' />
                <meta name='viewport' content='width=device-width, initial-scale=1' >
                <link rel='alternate' href='http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}' hreflang='vi-vn' />";
            if( !empty($this->view->Websites()->getFacebookId()) ){
                $html .= "<meta property='fb:app_id' content='".$this->view->Websites()->getFacebookId()."' >";
            }
            if( !empty($this->view->Websites()->getGoogleClientId()) ){
                $html .= "<meta name=\"google-signin-client_id\" content=\"".$this->view->Websites()->getGoogleClientId()."\"> ";
            }
        }
        return $html;
    }

    public function getHiddenTagFilter() {
        $page = $this->view->Params()->fromQuery('page', 0);
        $type = $this->view->Params()->fromQuery('type', '');
        $price = $this->view->Params()->fromQuery('price', '');
        $keyword = $this->view->Params()->fromQuery('keyword', '');
        $html = '';
        if( !empty($page) ){
            $html .= '<input name="page" type="hidden" value="'.$page.'" data-input="page" />';
        }
        if( !empty($type) ){
            $html .= '<input name="type" type="hidden" value="'.$type.'" data-input="type" />';
        }
        if( !empty($price) ){
            //$html .= '<input name="price" type="hidden" value="'.$price.'" data-input="price" />';
        }
        if( !empty($keyword) ){
            $html .= '<input name="keyword" type="hidden" value="'.$keyword.'" data-input="keyword" />';
        }
        return $html;
    }

    public function getDataIonRangeSlider() {
        $min_value_slider = $this->view->Websites()->getMinRangeSlider();
        $max_value_slider = $this->view->Websites()->getMaxRangeSlider();
        if( empty($min_value_slider) ){
            $min_value_slider = 0;
        }
        if( empty($max_value_slider) ){
            $max_value_slider = 10000000;
        }
        if( !empty($max_value_slider) && $min_value_slider < $max_value_slider){
            $price = $this->view->Params()->fromQuery('price', '');
            return 'data-neo="ionRangeSlider"  data-min="'.$min_value_slider.'" data-max="'.$max_value_slider.'" data-grid="true" value="' .$price .'" name="price" data-input="price"';
        }
    }

    public function getFilterBar($features) {
        $translator = $this->getTranslator ();
        $html = '';
        $F_ROOT = $this->view->Features()->getRootInFeatureSort($features); 
        if( !empty($F_ROOT) ){
            foreach ($F_ROOT as $key => $feature){
                $feature_id = $this->view->Features()->getID($feature);
                $CF_ROOT = $this->view->Features()->getLeafFeatureInFeatureSort($feature_id, $features);
                if( !empty($CF_ROOT) ){
                    $html  .=   "<div class='coz-filter-group' >
                                    <div class='coz-filter-group-title' >";
                    $html  .=           $this->view->Features()->getName($feature);
                    $html  .=       "</div>
                                    <ul class='coz-filter-list' >";
                            foreach ($CF_ROOT as $in => $fea){ 
                                if( $this->view->Features()->isColor($fea) ){
                    $html  .=           "<li class='coz-filter-item coz-boxcolor' >
                                            <a href='javascript:void(0)' >
                                                <label for='filter-{$this->view->Features()->getID($fea)}' >
                                                    <input type='checkbox' id='filter-{$this->view->Features()->getID($fea)}' value='{$this->view->Features()->getID($fea)}' ".($this->view->Features()->hasChoose($fea) ? 'checked' : ''). " name='feature[]' data-input='feature'  class='neo-trigger-filter' >
                                                    <span class='coz-vl-color' >
                                                        <span class='coz-invl-color' style='background: {$this->view->Features()->getColor($fea)}' ></span>
                                                    </span>
                                                </label>
                                            </a>
                                        </li>";
                            }else if( $this->view->Features()->isPattern($fea) ){
                    $html  .=           "<li class='coz-filter-item coz-boxcolor' >
                                            <a href='javascript:void(0)' >
                                                <label for='filter-{$this->view->Features()->getID($fea)}' >
                                                    <input type='checkbox' id='filter-{$this->view->Features()->getID($fea)}' value='{$this->view->Features()->getID($fea)}' " .($this->view->Features()->hasChoose($fea) ? 'checked' : '')." name='feature[]' data-input='feature'  class='neo-trigger-filter' >
                                                    <span class='coz-vl-color' >
                                                        <span class='coz-invl-color' style='background: url({$this->view->Features()->getPattern($fea)}) center center' ></span>
                                                    </span>
                                                </label>
                                            </a>
                                        </li>";
                            }else{
                    $html  .=           "<li class='coz-filter-item' >
                                            <a href='javascript:void(0);' >
                                                <label for='filter-{$this->view->Features()->getID($fea)}' >
                                                    <input type='checkbox' id='filter-{$this->view->Features()->getID($fea)}' value='{$this->view->Features()->getID($fea)}' ".($this->view->Features()->hasChoose($fea) ? 'checked' : '')." name='feature[]' data-input='feature'  class='neo-trigger-filter' >
                                                    <i class='fa' ></i>
                                                    {$this->view->Features()->getName($fea)}
                                                </label>
                                            </a>
                                        </li>";
                            }
                        }
                    $html  .=       "</ul>
                                </div>";
                }
            }
        }

        $html   .= "<div class='coz-filter-group' >
                        <div class='coz-filter-group-title' >
                            {$translator->translate('txt_gia')}
                        </div>
                        <div class='clearfix' >
                            <input type='text' {$this->getDataIonRangeSlider()} />
                        </div>
                    </div>";
        if( !empty($html) ){
            $html  = "<div class='coz-filter-bar' >".$html."</div>";
        }
        return $html;
    }

    public function TruncateText( $text = '' ) {
        $objTruncateText = new TruncateText($text);
        return $objTruncateText;
    }

    public function TruncateHTML( $html = '' ) {
        $objTruncateHTML = new TruncateHTML($html);
        return $objTruncateHTML;
    }
	
}
