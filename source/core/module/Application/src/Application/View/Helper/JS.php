<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class JS extends App
{
    public function getJavascriptHead()
    {
        $html = '';
        if( !empty($this->getDatas()) ){
            $categories = $this->getDatas()->categories;
            $keywords = $this->getDatas()->keywords;
            $website = $this->getDatas()->website;
            $pageInfo = $this->getDatas()->pageInfo;
            $jsonLd = $this->getDatas()->jsonLd;

            $html = "<script type='text/javascript' >
            var coz={};";

            if( $this->getContentview()->Common()->isMobile() ){ 
                $html .= "var isMobile = true;"; 
            }else{ 
                $html .= "var isMobile = false;"; 
            }

            if( !empty($_SESSION['CMSMEMBER']['preview']) ){ 
                $html .= "var tid_preview = ".$_SESSION['CMSMEMBER']['preview']['template_id'].";"; 
            }

            $html .= "var baseUrl = '".FOLDERWEB.$this->getUrlPrefixLang()."';";
            $html .= "var baseUrlCms = '/cms';";
            $html .= "var currency = ".json_encode($this->getContentview()->Currency()->getDataCurrency()).";";
            $html .= "var categories_ = ".json_encode($categories).";";
            $html .= "var langs = ".json_encode($keywords).";";
            $carts = $this->view->Cart()->getCart();
            $coupon = $this->view->Coupons()->getCoupon();
            $html .= "var carts = ".json_encode($carts).";";
            $html .= "var coupon = ".json_encode($coupon).";";
            if( isset($_SESSION['MEMBER']) && !empty($_SESSION['MEMBER'])){
                $html .= "var MEMBER = ".json_encode($_SESSION['MEMBER']).";coz.member = MEMBER;";
            }
            $_website = $website;

            $html .= '  coz.isMobile = isMobile;
                        coz.baseUrl = baseUrl;
                        coz.baseUrlCms = baseUrlCms;
                        coz.currency = currency;
                        coz.categories = categories_;
                        coz.langs = langs;
                        coz.carts = carts;
                        coz.coupon = coupon;
                        coz.website = '.json_encode($_website).';';
                        
            if( !empty($carts)  && !empty($_SESSION['PAYMENT_BUYER']) ){
                $html .= "coz.buyer = ".json_encode($_SESSION['PAYMENT_BUYER']).";";
            }

            if( !empty($carts)  && !empty($_SESSION['PAYMENT_SHIPPER']) ){
                $html .= "coz.shipper = ".json_encode($_SESSION['PAYMENT_SHIPPER']).";";
            }

            if( !empty($this->view->Websites()->getGoogleClientId()) ){
                $html .= "coz.google_client_id = '".$this->view->Websites()->getGoogleClientId()."';";
            }
            if( !empty($this->view->Websites()->getFacebookId()) ){
                $html .= "coz.facebook_id = '".$this->view->Websites()->getFacebookId()."';";
            }
            if( !empty($_SESSION['LOCATION']) ){
                $html .= "coz.location = ".json_encode($_SESSION['LOCATION']).";";
            }
            if( !empty($pageInfo) ){
                $html .= "coz.pageInfo = ".json_encode($pageInfo).";";
            }
            if( !empty($_SESSION['VISITED']) ){
                $html .= "coz.is_visited = true;";
            }else{
                $html .= "coz.is_visited = false;";
            }
            $html .= "coz.model = {};";
            $html .= "coz.model.products = {};";
            if( $this->getDatas()->c_module == 'application'
                && $this->getDatas()->c_controller == 'product'
                && $this->getDatas()->c_action == 'detail'
                && !empty($this->getDatas()->product) ){
                $item = array(
                                'product' => $this->getDatas()->product,
                                'extensions' => (!empty($this->getDatas()->extensions) ? $this->getDatas()->extensions : array()),
                                'types' => (!empty($this->getDatas()->types) ? $this->getDatas()->types : array())
                            );
                $html .= "coz.model.products = {".$this->getDatas()->product->products_id." : ".json_encode($item)."};";
            }
            $html .= "</script>";

            if( !empty($this->view->Websites()->getGaCode()) ){
                $html .= "<script>
                            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                            ga('create', '".$this->view->Websites()->getGaCode()."', 'auto');
                            ga('send', 'pageview');
                        </script>";
            }

            if( !empty($jsonLd) ){
                $html .= '<script type="application/ld+json">'.json_encode($jsonLd).'</script>';
            }

            if( !empty($this->view->Websites()->getCss()) ){
                $html .= '<style>'.$this->view->Websites()->getCss().'</style>';
            }
        }
        return $html;
    }

    public function getJavascriptFoot()
    {
        $html = '';
        /*if( !empty($this->getDatas()) ){
            $website = $this->getDatas()->website;*/

            if( !empty($this->view->Websites()->getFacebookId()) ){
                $html .= "<div id='fb-root' ></div>
                    <script>
                        window.fbAsyncInit = function() {
                            FB.init({
                              appId      : '".$this->view->Websites()->getFacebookId()."',
                              xfbml      : true,
                              version    : 'v2.8'
                            });
                            FB.AppEvents.logPageView();
                        };

                        (function(d, s, id){
                            var js, fjs = d.getElementsByTagName(s)[0];
                            if (d.getElementById(id)) {return;}
                            js = d.createElement(s); js.id = id;
                            js.src = \"//connect.facebook.net/en_US/sdk.js\";
                            js.onload = function () {};
                            fjs.parentNode.insertBefore(js, fjs);
                        }(document, 'script', 'facebook-jssdk'));
                    </script>";
            }
        //}
        return $html;
    }

    public function getHeadScript()
    {
        $html = $this->getContentview()->headScript();
        if( !empty($this->view->Websites()->getJavascript()) ){
            $html .= $this->view->Websites()->getJavascript();
        }
        return $html;
    }

    public function appendFile($value, $type, $attrs)
    {
        $this->getContentview()->headScript()->appendFile($value, $type, $attrs);
        return $this;
    }

    public function addVersionToSrc( $link, $version = ''){
        if( !empty($link) && !empty($version) ){
            if ( strpos($link, '?') !== false) {
                $link .= '&coz='. $version;
            }else{
                $link .= '?coz='. $version;
            }
        }
        return $link;
    }

    public function getJavascripts()
    {
        if( !empty($this->getDatas()) && !empty($this->getDatas()->js) ){
            foreach ($this->getDatas()->js as $key => $value) {
                if( !empty($value['src']) ){
                    $type  = 'text/javascript';
                    if(!empty($value['type'])){
                        $type  = $value['type'];
                    }
                    $attrs = array();
                    if(is_array($value)){
                        $attrs = $value;
                        unset($attrs['src']);
                    }

                    $version = '';
                    if( !empty($_SESSION['config'])
                        && !empty($_SESSION['config']['cached']) 
                        && !empty($_SESSION['config']['cached']['namespace']) ){
                        $version = $_SESSION['config']['cached']['namespace'];
                        $value['src'] = $this->addVersionToSrc($value['src'], $version);
                    }

                    $this->getContentview()->headScript()->appendFile($value['src'], $type, $attrs);
                }
            }
        }
        return $this;
    }

}
