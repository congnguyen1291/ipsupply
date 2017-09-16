<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Debug\Debug;
use Zend\Feed\Writer\Entry;
use Zend\Feed\Writer\Feed;
use Zend\Feed\Writer\Renderer\Feed\Rss;
use PHPImageIco\ImageIco;
use Application\Model\Languages;
use Ubench\Ubench;

class FrontEndController extends AbstractActionController {
    protected $langMap = array(
            'au' => array(
                    'name' => 'vi_VN',
                    'id' => 1
                ),
            'en' => array(
                    'name' => 'en_US',
                    'id' => 2
                )
        );
    protected $prefixUlrLang = 'au';
    protected $languages = array();
    protected $categories = array();
    protected $country_code = '';
    protected $country_id = 0;
    protected $location = array();
    protected $countries = array();
    protected $top_keywords = array();
    protected $language = array();
    protected $languages_id = 1;
    protected $menus = array();
    protected $translator;
    protected $config = array();
    protected $pageInfo = array();
    
    protected $favicon = '';
    protected $domain = '';
    protected $prefix_tran = '';
    protected $baseUrl = '';
    protected $protocol = '';
    protected $website = array();
    protected $js = array();
    protected $css = array();
    protected $model = array();
    protected $keywords = array();
    protected $jsonLd = array();

    protected $current_module = '';
    protected $current_controller = '';
    protected $current_action = '';
    protected $current_alias = '';
    protected $page_key = '';
    protected $page_key_md5 = '';
    protected $isDebug = FALSE;
    protected $isMinify = FALSE;

    protected $has_header = TRUE;
    protected $has_footer = TRUE;
    protected $data_view = array();

    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $helperCategories = $this->getServiceLocator()->get('viewhelpermanager')->get('Categories');
        $helperMenus = $this->getServiceLocator()->get('viewhelpermanager')->get('Menus');
        $this->setDataView('has_header', $this->has_header);
        $this->setDataView('has_footer', $this->has_footer);

        $this->domain = $_SERVER['HTTP_HOST'];
        $this->protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $this->baseUrl = $this->protocol.''.$this->domain;
        if (substr($this->domain, 0, 4) == "www.")
            $this->domain = substr($this->domain, 4);

        $this->setInfoAction();
        $this->setInfoLanguage();

        $this->website = $this->getModelTable('WebsitesTable')->getWebsiteById(ID_MASTERPAGE);
        $this->setDataView('protocol', $this->protocol);
        $this->setDataView('domain', $this->domain);
        $this->setDataView('baseUrl', $this->baseUrl);

        $this->getInfoWebsite();
        $this->initArrayLd();
        $_SESSION['protocol'] = $this->protocol;
        $_SESSION['baseUrl'] = $this->baseUrl;
        $_SESSION['website_id'] = ID_MASTERPAGE;
        $_SESSION['website'] = $this->website;
        $_SESSION['domain'] = $this->domain;
        
        $this->categories = $helperCategories->getAllCategoriesSort();
        $this->setDataView('categories', $this->categories);

        $this->location =  !empty($_SESSION['LOCATION']) ? $_SESSION['LOCATION'] : array();
        /*$this->countries = $this->getModelTable('CountryTable')->getContries();
        if( empty($this->website['is_local']) || empty($this->website['website_contries']) ){
            $this->countries = $this->getModelTable('CountryTable')->getContries();
        }else{
            $idcs = explode(',', $this->website['website_contries']);
            $this->countries = $this->getModelTable('CountryTable')->getContriesLimit( array('id'=> $idcs) );
        }
        if( !empty($this->location['country_id']) ){
            $this->country_id = $this->location['country_id'];
        }
        if( !empty($this->location['country_code']) ){
            $this->country_code = $this->location['country_code'];
        }*/
        $this->setDataView('country_id', $this->country_id);
        $this->setDataView('country_code', $this->country_code);
        $this->setDataView('location', $this->location);
        $this->setDataView('countries', $this->countries);
        $_SESSION['LOCATION'] = $this->location;
        
        $this->menus = $helperMenus->getAllMenuAndSort();
        $this->setDataView('menus', $this->menus);

        $this->favicon = $this->createIcoImage();
        $this->setDataView('favicon', $this->favicon);

        $full_module = strtoupper($this->current_module.'_'.$this->current_controller.'_'.$this->current_action);

        $this->addJS("//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js");
        $this->addJS("//cdn.coz.vn/tether/dist/js/tether.min.js");
        $this->addJS("//cdn.coz.vn/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js");
        $this->addJS("//cdn.coz.vn/html5shiv/html5shiv.min.js");
        $this->addJS("//cdn.coz.vn/jquery-migrate/jquery-migrate-1.2.0.min.js");
        $this->addJS("//cdn.coz.vn/owl-carousel/owl.carousel.min.js");
        $this->addJS("//cdn.coz.vn/elevatezoom/3.0.8/jquery.elevateZoom-3.0.8.min.js");
        $this->addJS("//cdn.coz.vn/ion.rangeSlider/2.1.4/js/ion.rangeSlider.min.js");


        $this->addJS("//cdn.coz.vn/magnific-popup/js/jquery.magnific-popup.min.js");
        //$this->addJS("//cdn.coz.vn/jQuery.Marquee/jquery.marquee.min.js");
        //$this->addJS("//cdn.coz.vn/jquery.scrollbar/jquery.scrollbar.js");
        $this->addJS("//cdn.coz.vn/datepicker/dist/datepicker.min.js");
        $this->addJS("//cdn.coz.vn/select2/dist/js/select2.min.js");
        //$this->addJS("//cdn.coz.vn/tooltipster/dist/js/tooltipster.bundle.min.js");
        $this->addJS("//cdn.coz.vn/iGrowl/js/igrowl.min.js");
        $this->addJS("//cdn.coz.vn/jquery-tmpl/jquery.tmpl.min.js");
        $this->addJS("//cdn.coz.vn/nprogress/nprogress.js");
        $this->addJS("//cdn.coz.vn/jquery-pjax/jquery.pjax.js");
        if( in_array(strtoupper($this->current_module.'_'.$this->current_controller) , array('APPLICATION_PROFILE')) ) {
            $this->addJS('//cdn.coz.vn/jquery-html5-upload/jquery.html5_upload.js');
        }

        if( in_array($full_module, array('APPLICATION_PROFILE_INDEX', 'APPLICATION_CONTACT_INDEX', 'APPLICATION_PROFILE_EDIT')) ) {
            $this->addJS('http://maps.google.com/maps/api/js?key=AIzaSyAHa_nwcjR0pfJ6w0S0SGA7MG9jMzNm_K0');
            $this->addJS('//cdn.coz.vn/gmaps/gmaps.min.js');
        }

        if( in_array($full_module, array('APPLICATION_PROFILE_INDEX', 'APPLICATION_PROFILE_HISTORY', 'APPLICATION_PROFILE_PAYMENT', 'APPLICATION_PROFILE_ASSIGN')) ) {
            $this->addJS('//cdn.coz.vn/chartjs/Chart.min.js');
        }
        if( in_array($full_module, array('APPLICATION_PROFILE_INDUSTRY', 'APPLICATION_PROFILE_EDIT' )) ) {
            $this->addJS('//cdn.coz.vn/datepicker/dist/datepicker.min.js');
        }
        $this->addJS($this->baseUrl.'/styles/js/script.js');
        //$this->addJS($this->baseUrl.'/styles/js/design.js');
        $this->addJS($this->baseUrl.'/styles/js/neo.js');
        $this->addJS($this->baseUrl.'/styles/js/statistical.js');
        if( isset($_SESSION['CMSMEMBER']) ){
            if( ($_SESSION['CMSMEMBER']['type'] == 'admin' 
                && $_SESSION['CMSMEMBER']['website_id'] == ID_MASTERPAGE)
                || (isset($_SESSION['CMSMEMBER']['permissions']) 
                    && isset($_SESSION['CMSMEMBER']['permissions']['cms']['language']['manage-keywords']) ) ){
                $this->addJS($this->baseUrl.'/styles/js/translate.js');
            }
        }

        $this->addCSS('//fonts.googleapis.com/css?family=Roboto');
        $this->addCSS('//cdn.coz.vn/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css');
        $this->addCSS('//cdn.coz.vn/font-awesome/4.7.0/css/font-awesome.min.css');
        $this->addCSS('//cdn.coz.vn/owl-carousel/owl.carousel.min.css');
        $this->addCSS('//cdn.coz.vn/owl-carousel/owl.theme.min.css');
        $this->addCSS('//cdn.coz.vn/ion.rangeSlider/2.1.4/css/ion.rangeSlider.css');
        $this->addCSS('//cdn.coz.vn/ion.rangeSlider/2.1.4/css/ion.rangeSlider.skinModern.css');
        $this->addCSS('//cdn.coz.vn/flags/flags.min.css');
        $this->addCSS('//cdn.coz.vn/jquery.scrollbar/jquery.scrollbar.css');
        $this->addCSS('//cdn.coz.vn/datepicker/dist/datepicker.min.css');
        $this->addCSS('//cdn.coz.vn/magnific-popup/css/magnific-popup.css');
        $this->addCSS('//cdn.coz.vn/select2/dist/css/select2.css');
        //$this->addCSS('//cdn.coz.vn/tooltipster/dist/css/tooltipster.bundle.min.css');
        $this->addCSS('//cdn.coz.vn/iGrowl/css/igrowl.min.css');
        $this->addCSS('//cdn.coz.vn/iGrowl/css/fonts/feather.css');
        $this->addCSS('//cdn.coz.vn/nprogress/nprogress.css');
        if( $full_module == 'APPLICATION_ERROR_INDEX'){
            $this->addCSS('//cdn.coz.vn/common/error/css/error.min.css');
        }
        if( in_array($full_module, array('APPLICATION_PROFILE_INDUSTRY')) ) {
            $this->addCSS('//cdn.coz.vn/datepicker/dist/datepicker.min.css');
        }
        $this->addCSS($this->baseUrl.'/styles/css/style.css');
        $this->addCSS($this->baseUrl.'/styles/css/neo.css');
        //$this->addCSS($this->baseUrl.'/styles/css/design.css');
        return parent::onDispatch($e);
    }

    protected function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    protected function getModelTable($name) {
        if (! isset ( $this->{$name} )) {
            $this->{$name} = NULL;
        }
        if (! $this->{$name}) {
            $sm = $this->getServiceLocator ();
            $this->{$name} = $sm->get ( 'Application\Model\\' . $name );
        }
        return $this->{$name};
    }
    
    protected function toAlias($txt, $str = '-') {
        if ($txt == '')
            return '';
        $marked = array ("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă","ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề","ế", "ệ", "ể", "ễ", "ế",             "ì", "í", "ị", "ỉ", "ĩ","ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ","ờ", "ớ", "ợ", "ở", "ỡ","ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ","ỳ", "ý", "ỵ", "ỷ", "ỹ","đ","À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă","Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ","È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ","Ì", "Í", "Ị", "Ỉ", "Ĩ","Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ","Ờ", "Ớ", "Ợ", "Ở", "Ỡ","Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ","Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ","Đ", " ", "&", "?", "/", ".", ",", "$", ":", "(", ")", "'", ";", "+", "–", "’" );
    
        $unmarked = array ("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e","e", "e", "e", "e", "e", "i", "i", "i", "i", "i","o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o","o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",  "y", "y", "y", "y", "y", "d", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "D", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-" );
    
        $tmp3 = (str_replace ( $marked, $unmarked, $txt ));
        $tmp3 = rtrim ( $tmp3, $str );
        $tmp3 = preg_replace ( array ('/\s+/', '/[^A-Za-z0-9\-]/' ), array ($str, '' ), $tmp3 );
        $tmp3 = preg_replace ( '/-+/', $str, $tmp3 );
        $tmp3 = strtolower ( $tmp3 );
        return $tmp3;
    }

    protected function isMasterPage() {
        if(empty($this->website) || $this->domain == MASTERPAGE )
            return TRUE;
        return FALSE;
    }

    protected function file_ext($filename) {
        if( !preg_match('/./', $filename) ) return '';
        return preg_replace('/^.*./', '', $filename);
    }

    protected function file_ext_strip($filename){
        return preg_replace('/.[^.]*$/', '', $filename);
    }

    protected function addJS($js) {
        $src = $js;
        $row = $js;
        if(is_array($js)){
            $src = $js['src'];
        }else{
            $row = array('src' => $src);
        }
        unset($row['only']);
        $file = basename($src);
        $key = md5($src);
        if(!isset($this->js[$key])){
            $this->js[$key] = $row;
        }
        $this->updateJS();
        return $this->js;
    }

    protected function removeJS($js) {
        $src = $js;
        if(is_array($js)){
            $src = $js['src'];
        }
        $file = basename($src);
        $key = md5($src);
        if(isset($this->js[$key])){
            unset($this->js[$key]);
        }
        $this->updateJS();
        return $this->js;
    }

    protected function getJS() {
        
        return $this->js;
    }

    protected function updateJS() {
        $this->data_view['js'] = $this->js;
        return $this->js;
    }

    protected function addCSS($css) {
        $href = $css;
        $row = $css;
        if(is_array($css)){
            $href = $css['href'];
        }else{
            $row = array('href' => $href);
        }
        unset($row['only']);
        $file = basename($href);
        $key = md5($href);
        if(!isset($this->css[$key])){
            $this->css[$key] = $row;
        }
        $this->updateCSS();
        return $this->css;
    }

    protected function removeCSS($css) {
        $href = $css;
        if(is_array($css)){
            $href = $css['href'];
        }
        $file = basename($href);
        $key = md5($href);
        if(isset($this->css[$key])){
            unset($this->css[$key]);
        }
        $this->updateCSS();
        return $this->css;
    }

    protected function getCSS() {
        
        return $this->css;
    }

    protected function updateCSS() {
        $this->data_view['css'] = $this->css;
        return $this->css;
    }

    protected function addModel($name) {
        if(!isset($this->model[$name]) && !empty($name)){
            $this->model[$name] = $this->getModelTable($name);
        }
        $this->updateModel();
        return $this->model;
    }

    protected function updateModel() {
        $this->data_view['model'] = $this->model;
        return $this->model;
    }

    protected function isUrl($url) {
        $regex = "((https?|ftp)\:\/\/)?"; // SCHEME 
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass 
        $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP 
        $regex .= "(\:[0-9]{2,5})?"; // Port 
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path 
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query 
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor 

        if(preg_match("/^$regex$/", $url)) 
        { 
            return true; 
        }
        return false;
    }
    
    protected function isUrlHttp($url) {
        if(substr($url, 0, 5) == 'https' || substr($url, 0, 4) == 'http' || substr($url, 0, 3) == 'ftp' || substr($url, 0, 2) == '//') 
        { 
            return true; 
        }
        return false;
    }

    protected function addModels() {
    }

    protected function createIcoImage() {
        $ico_src = '/img_/default.ico';
        if( !empty($this->website) && !empty($this->website->logo) ){
            $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $this->domain;
            if(!is_dir($websiteFolder)){
                @mkdir ( $websiteFolder, 0777 );
            }
            $websiteThumb = $websiteFolder.'/ico';
            if(!is_dir($websiteThumb)){
                @mkdir ( $websiteThumb, 0777 );
            }
            $ico_src = "/custom/domain_1/" . $this->domain.'/ico/favicon.ico';
            $source = PATH_BASE_ROOT.$this->website->logo;
            $destination = PATH_BASE_ROOT.$ico_src;
            if(!is_file($destination)){
                if(is_file($source)){
                    $sizes = array(
                        array( 16, 16 ),
                        array( 24, 24 ),
                        array( 32, 32 ),
                        array( 48, 48 ),
                    );

                    $ico_lib = new ImageIco( $source, $sizes );
                    $ico_lib->save_ico( $destination );
                }
            }
        }
        $pre_dm = substr ( $ico_src , 0 , 17 );
        return $ico_src;
    }

    public function isLogin()
    {
        if( !empty($_SESSION['MEMBER']) ) {
            return TRUE;
        }
        return FALSE;
    }

    public function getInfoWebsite()
    {
        /*$data = array(
                    'version' => '1.0.0',
                    'website_name' => '',
                    'seo_title'  =>  '',
                    'seo_keywords'  =>  '',
                    'seo_description' => '',
                    'website_phone' => '',
                    'website_address' => '',
                    'website_city' => '',
                    'website_timezone' => '',
                    'cached' => array(
                        'type' => 'memcache',
                        'time' => 86400,
                        'namespace' => 20170319002553
                    ),
                    'website_currency'  => 'VND',
                    'website_currency_format'  =>  '',
                    'website_currency_decimals'  => 2,
                    'website_currency_decimalpoint' => '.',
                    'website_currency_separator' => ',',
                    'order' => array(
                        'website_order_code_prefix' => 'HD',
                        'website_order_code_suffix' => ''
                    ),
                    'website_ga_code' => '',
                    'social' => array(
                        'facebook_id' => '',
                        'url_twister' => '',
                        'url_google_plus' => '',
                        'url_facebook' => '',
                        'url_pinterest' => '',
                        'url_houzz' => '',
                        'url_instagram' => '',
                        'url_rss' => '',
                        'url_youtube' => ''
                    ),
                    'email' => array(
                        'email_admin_receive' => '',
                        'email_admin_send' => '',
                        'host_mail' => '',
                        'name_host' => '',
                        'username_host_mail' => '',
                        'password_host_mail' => '',
                        'host_port' => ''
                    ),
                    'page_size' => 24,
                    'value_ship' => 5000000,
                    'date_create' => '2017-01-01',
                    'website_email_admin' => 'admin@admin',
                );
        $this->website = $data;*/
        $this->data_view['website'] = $this->website;
    }

    public function initArrayLd()
    {
        $this->jsonLd = array(
                '@context' => 'http://schema.org',
                '@type' => 'WebPage',
                'breadcrumb' => 'Books > Literature & Fiction > Classics',
                'mainEntity' => array(
                    array(
                      '@context' => 'http://schema.org',
                      '@type' => 'Product',
                      'aggregateRating' => array(
                        '@type' => 'AggregateRating',
                        'ratingValue' => '3.5',
                        'reviewCount' => '11'
                    ),
                      'description' => '0.7 cubic feet countertop microwave. Has six preset cooking categories and convenience features like Add-A-Minute and Child Lock.',
                      'name' => 'Kenmore White 17 Microwave',
                      'image' => 'kenmore-microwave-17in.jpg',
                      'offers' => array(
                            '@type' => 'Offer',
                            'availability' => 'http://schema.org/InStock',
                            'price' => '55.00',
                            'priceCurrency' => 'USD'
                        ),
                      'review' => array(
                        array(
                          '@type' => 'Review',
                          'author' => 'Ellie',
                          'datePublished' => '2011-04-01',
                          'description' => 'The lamp burned out and now I have to replace it.',
                          'name' => 'Not a happy camper',
                          'reviewRating' => array(
                            '@type' => 'Rating',
                            'bestRating' => '5',
                            'ratingValue' => '1',
                            'worstRating' => '1'
                          )
                        ),
                        array(
                          '@type' => 'Review',
                          'author' => 'Lucas',
                          'datePublished' => '2011-03-25',
                          'description' => 'Great microwave for the price. It is small and fits in my apartment.',
                          'name' => 'Value purchase',
                          'reviewRating' => array(
                            '@type' => 'Rating',
                            'bestRating' => '5',
                            'ratingValue' => '4',
                            'worstRating' => '1'
                          )
                        )
                      )
                    )
                ));
        $this->updateJsonLd();
    }

    protected function updateJsonLd() {
        $this->data_view['jsonLd'] = $this->jsonLd;
        return $this->jsonLd;
    }

    protected function getDataView( ) {
        return $this->data_view;
    }

    protected function setDataView( $id, $value ) {
        $this->data_view[$id] = $value;
        return $this;
    }

    protected function setInfoAction() {
        $alias = $this->getEvent()->getRouteMatch()->getParam('alias', '');
        $this->current_alias = strtolower($alias);

        $current_controller = $this->getEvent()->getRouteMatch()->getParam('controller');
        $current_controller = explode('\\', $current_controller);
        if(count($current_controller) == 3){
            $this->current_module = strtolower($current_controller[0]);
            $this->current_controller = strtolower($current_controller[2]);
            $this->current_action = strtolower($this->getEvent()->getRouteMatch()->getParam('action'));
        }
        $this->setDataView('c_module', $this->current_module);
        $this->setDataView('c_controller', $this->current_controller);
        $this->setDataView('c_action', $this->current_action);
        return $this;
    }

    protected function setInfoLanguage() {
        $lang = !empty($_SESSION['lang']) ? $_SESSION['lang'] : 'vi_VN';
        $prefixUlrLang = $this->params()->fromRoute('language', '');
        $isAJAX = $this->params()->fromQuery('_AJAX', 0);
        $is_pjax = $this->params()->fromHeader('X-PJAX', '');
        $matchedRouteName = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $rayRN = explode('/', $matchedRouteName);
        $request = $this->getRequest();
        if( $this->current_module  == 'application' 
            && !$request->isPost() && empty($isAJAX) && empty($is_pjax) ){
            if( in_array('router_vi' , $rayRN) || in_array('router_en', $rayRN) ){
                $prefixUlrLang = 'au';
                if( in_array('router_en' , $rayRN) ){
                    $prefixUlrLang = 'en';
                }
                $lang = $this->langMap[$prefixUlrLang]['name'];
                if( empty($_SESSION['lang']) || $lang != $_SESSION['lang'] ){
                    unset($_SESSION['language']);
                    unset($_SESSION['languages_id']);
                    unset($_SESSION['lang']);
                }
            }else{
                $prefixUlrLang = 'au';
                if( !empty($_SESSION['prefixUlrLang']) ){
                    $prefixUlrLang = $_SESSION['prefixUlrLang'];
                }
                if( $this->current_module == 'application' && $this->current_alias != 'cms' ){
                    $url = $this->getRequest()->getUriString();
                    $url = str_replace ( $this->domain, $this->domain.'/'.$prefixUlrLang, $url );
                    $url = str_replace ( $prefixUlrLang.'/'.$prefixUlrLang, $prefixUlrLang, $url );
                    return $this->redirect()->toUrl($url);
                }
                $lang = $this->langMap[$prefixUlrLang]['name'];
            }
        }
        if( !empty($_SESSION['lang']) ){
            $lang = $_SESSION['lang'];
        }
        $this->prefixUlrLang = $prefixUlrLang;
        $_SESSION['prefixUlrLang'] = $this->prefixUlrLang;

        $this->languages = $this->getModelTable('LanguagesTable')->getLanguages();
        //$lang = !empty($_SESSION['lang']) ? $_SESSION['lang'] : 'vi_VN';
        $this->language = !empty($_SESSION['language']) ? $_SESSION['language'] : array();
        $this->languages_id = !empty($_SESSION['languages_id']) ? $_SESSION['languages_id'] : 1;
        if( empty($this->language) ){
            $this->language = $this->getModelTable('LanguagesTable')->getByCode( $lang );
            $this->languages_id = $this->language->languages_id;
            $_SESSION['language'] = $this->language;
            $_SESSION['languages_id'] = $this->languages_id;
        }
        $this->setDataView('language', $this->language);
        $this->setDataView('languages_id', $this->languages_id);
        $_SESSION['lang'] = $lang;
        $sm = $this->getServiceLocator();
        $translator = $sm->get('translator');
        $path_lang = LANG_PATH.'/'.$lang.'.php';
        $translator->addTranslationFile("phparray",$path_lang);
        $sm->get('ViewHelperManager')->get('translate')
            ->setTranslator($translator);
        try{
            include_once $path_lang;
            $this->keywords = swapTranslateForAdmin($$lang, $lang);
        }catch(\Exception $ex){}
        $this->setDataView('keywords', $this->keywords);

        $sm = $this->getServiceLocator();
        $translator = $sm->get('translator');
        $translator->addTranslationFile('phparray',
            LANG_PATH.'/'.$lang.'.php');
        $sm->get('ViewHelperManager')->get('translate')
            ->setTranslator($translator);
        $this->translator = $translator;

        $this->setDataView('languages', $this->languages);
        $this->setDataView('language', $this->language);
        return $this;
    }

    public function validateInputContryPayment($data, $country)
    {
        $err = array();
        $translator = $this->getServiceLocator()->get('translator');
        if($country->country_type == 0){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 1 || $country->country_type == 2){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['state']) ) {
                $err['state'] = $translator->translate('txt_state_khong_duoc_bo_trong');
            }
            if ( empty($data['zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 3){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
            if ( empty($data['suburb']) ) {
                $err['suburb'] = $translator->translate('txt_suburb_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 4){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['state']) ) {
                $err['state'] = $translator->translate('txt_state_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 5){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['region']) ) {
                $err['region'] = $translator->translate('txt_region_khong_duoc_bo_trong');
            }
            if ( empty($data['zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 6){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['province']) ) {
                $err['province'] = $translator->translate('txt_province_khong_duoc_bo_trong');
            }
            if ( empty($data['zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 7){
            if ( empty($data['address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['cities_id']) ) {
                $err['cities_id'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['districts_id']) ) {
                $err['districts_id'] = $translator->translate('txt_districts_khong_duoc_bo_trong');
            }
            if ( empty($data['wards_id']) ) {
                $err['wards_id'] = $translator->translate('txt_wards_khong_duoc_bo_trong');
            }
        }
        return $err;
    }

    public function validateInputContryShipper($data, $country)
    {
        $err = array();
        $translator = $this->getServiceLocator()->get('translator');
        if($country->country_type == 0){
            if ( empty($data['ships_address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 1 || $country->country_type == 2){
            if ( empty($data['ships_address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_state']) ) {
                $err['state'] = $translator->translate('txt_state_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 3){
            if ( empty($data['ships_address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_suburb']) ) {
                $err['suburb'] = $translator->translate('txt_suburb_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 4){
            if ( empty($data['ships_address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_state']) ) {
                $err['state'] = $translator->translate('txt_state_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 5){
            if ( empty($data['ships_address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_region']) ) {
                $err['region'] = $translator->translate('txt_region_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 6){
            if ( empty($data['ships_address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_city']) ) {
                $err['city'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_province']) ) {
                $err['province'] = $translator->translate('txt_province_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_zipcode']) ) {
                $err['zipcode'] = $translator->translate('txt_zipcode_khong_duoc_bo_trong');
            }
        }else if($country->country_type == 7){
            if ( empty($data['ships_address']) ) {
                $err['address'] = $translator->translate('txt_dia_chi_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_cities_id']) ) {
                $err['cities_id'] = $translator->translate('txt_city_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_districts_id']) ) {
                $err['districts_id'] = $translator->translate('txt_districts_khong_duoc_bo_trong');
            }
            if ( empty($data['ships_wards_id']) ) {
                $err['wards_id'] = $translator->translate('txt_wards_khong_duoc_bo_trong');
            }
        }
        return $err;
    }

    public function isFreeShip( $shipping ){
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        if( !empty($shipping) 
            && !empty($shipping['shipping_from']) ){
            $shipping_from = $shipping['shipping_from'];
            $shipping_to = $shipping['shipping_to'];
            $calculate = 0;
            if( $shipping['shipping_type'] == 0){
                $calculate = $helper->sumSubTotalPriceInCart();
                $calculate = $calculate['price_total'];
            }else if( $shipping['shipping_type'] == 1){
                $calculate = $helper->getNumberProductInCart();
            }else if( $shipping['shipping_type'] == 2){
                return FALSE;
            }
            if( !empty($shipping_to) ){
                return ( $calculate >= $shipping_from && $calculate <= $shipping_to);
            }else{
                return ( $calculate >= $shipping_from);
            }
        }
        return FALSE;
    }

    public function isAvaiableShip( $shipping ){
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Cart');
        if( !empty($shipping) ){
            $shipping_from = $shipping['shipping_from'];
            $shipping_to = $shipping['shipping_to'];
            $calculate = 0;
            if( $shipping['shipping_type'] == 0){
                $calculate = $helper->sumSubTotalPriceInCart();
                $calculate = $calculate['price_total'];
            }else if( $shipping['shipping_type'] == 1){
                $calculate = $helper->getNumberProductInCart();
            }else if( $shipping['shipping_type'] == 2){
                return FALSE;
            }
            if( !empty($shipping_to) ){
                return ( $calculate >= $shipping_from && $calculate <= $shipping_to);
            }else{
                return ( $calculate >= $shipping_from);
            }
        }
        return FALSE;
    }

    public function getFeeShip( $shipping ,    $transport_type = 0){
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
        if( !empty($shipping) ){
            if( $transport_type == 0 ){
                $results = $shipping['shipping_value'];
                if( !empty($shipping['shipping_fixed_value']) && $shipping['shipping_fixed_value'] != 0 ){
                    $results = $shipping['shipping_fixed_value'];
                }
            }else{
                $results = $shipping['shipping_fast_value'];
                if( !empty($shipping['shipping_fast_fixed_value']) && $shipping['shipping_fast_fixed_value'] != 0 ){
                    $results = $shipping['shipping_fast_fixed_value'];
                }
            }
            return $results;
        }
        return 0;
    }

    public function getFeeShipNormal( $shipping ){
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
        if( !empty($shipping) ){
            $results = $shipping['shipping_value'];
            if( !empty($shipping['shipping_fixed_value']) && $shipping['shipping_fixed_value'] != 0 ){
                $results = $shipping['shipping_fixed_value'];
            }
            return $results;
        }
        return 0;
    }

    public function getFeeShipFast( $shipping ){
        $helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Common');
        if( !empty($shipping) ){
            $results = $shipping['shipping_fast_value'];
            if( !empty($shipping['shipping_fast_fixed_value']) && $shipping['shipping_fast_fixed_value'] != 0 ){
                $results = $shipping['shipping_fast_fixed_value'];
            }
            return $results;
        }
        return 0;
    }

    public function getVersionCart(){
        if( !empty($this->website->version_cart)
            && in_array( $this->website->version_cart , array('02')) ){
            return $this->website->version_cart;
        }
        return '02';
    }

    public function getRouteCart(){
        $uCart = 'cart';
        switch ( $this->getVersionCart() ) {
            case '02':
                $uCart = 'checkout';
                break;
            
            default:
                $uCart = 'cart';
                break;
        }
        return $uCart;
    }

    public function detectLocation(){
        if( empty($_SESSION['LOCATION']) ){
            $cookie = $this->getRequest()->getCookie();
            if( !empty($cookie) && !empty($cookie->location) ){
                try{
                    $location = json_decode($cookie->location);
                    if( isset($location->country_id)
                        && isset($location->cities_id)
                        && isset($location->districts_id)
                        && isset($location->wards_id)
                        && isset($location->address)
                        && (!empty($location->country_id)
                            || !empty($location->cities_id)
                            || !empty($location->districts_id)
                            || !empty($location->wards_id)) ){
                        $_SESSION['LOCATION'] = array(
                            'country_id' => !empty($location->country_id) ? $location->country_id : 0,
                            'cities_id' => !empty($location->cities_id) ? $location->cities_id : 0,
                            'districts_id' => !empty($location->districts_id) ? $location->districts_id : 0,
                            'wards_id' => !empty($location->wards_id) ? $location->wards_id : 0,
                            'address' => !empty($location->address) ? $location->address : 0,
                            'city' => !empty($location->city) ? $location->city : '',
                            'zipcode' => !empty($location->zipcode) ? $location->zipcode : '',
                            'categories_id' => !empty($location->categories_id) ? $location->categories_id : 0,
                        );
                    }
                }catch(\Exception $ex){}
            }
        }else if( !empty($_SESSION['LOCATION'])
                    && empty($_SESSION['LOCATION']['country_id'])
                    && empty($_SESSION['LOCATION']['cities_id'])
                    && empty($_SESSION['LOCATION']['districts_id'])
                    && empty($_SESSION['LOCATION']['wards_id'])
                    && empty($_SESSION['LOCATION']['city'])
                    && empty($_SESSION['LOCATION']['zipcode'])
                    && empty($_SESSION['LOCATION']['categories_id']) ){
            unset($_SESSION['LOCATION']);
        }
    }

    protected function updatePageInfo() {
        $this->data_view['pageInfo'] = $this->pageInfo;
        return $this->css;
    }

    protected function addLinkPageInfo( $link = '' ) {
        $this->pageInfo['link'] = $link;
        $this->updatePageInfo();
    }

    protected function addParamsPageInfo( $params = array() ) {
        $this->pageInfo['params'] = $params;
        $this->updatePageInfo();
    }

    public function getPrefixLang() {
        $lng = 'au';
        if( !empty($_SESSION['prefixUlrLang']) ){
            $lng = $_SESSION['prefixUlrLang'];
        }
        return $lng;
    }

    public function getUrlPrefixLang() {
        $lng = '/au';
        if( !empty($_SESSION['prefixUlrLang']) ){
            $lng = '/'.$_SESSION['prefixUlrLang'];
        }
        return $lng;
    }

    public function getUrlRouterLang() {
        $lng = 'router_vi/';
        if( !empty($_SESSION['prefixUlrLang']) ){
            $lng = 'router_'.$_SESSION['prefixUlrLang'].'/';
        }
        return $lng;
    }

}
