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
    protected $languages = array();
    protected $categories = array();
    protected $cities = array();
    protected $top_keywords = array();
    protected $language = array();
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
    protected $page_key = '';
    protected $page_key_md5 = '';
    protected $isDebug = FALSE;
    protected $isMinify = FALSE;

    protected $has_header = TRUE;
    protected $has_footer = TRUE;
    protected $data_view = array();

    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $this->setDataView('has_header', $this->has_header);
        $this->setDataView('has_footer', $this->has_footer);

        $this->setInfoAction();
        $this->setInfoLanguage();

        $this->domain = $_SERVER['HTTP_HOST'];
        $this->protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $this->baseUrl = $this->protocol.''.$this->domain;
        if (substr($this->domain, 0, 4) == "www.")
            $this->domain = substr($this->domain, 4);
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
        $_SESSION['language'] = $this->language;

        $this->categories = $this->getModelTable('CategoriesTable')->getAllCategoriesSort();
        $this->setDataView('categories', $this->categories);
        
        $this->menus = $this->getModelTable('MenusTable')->getAllAndSort();
        $this->setDataView('menus', $this->menus);

        $this->favicon = $this->createIcoImage();
        $this->setDataView('favicon', $this->favicon);

        $this->addJS("//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js");
        $this->addJS("//cdn.coz.vn/tether/dist/js/tether.min.js");
        $this->addJS("//cdn.coz.vn/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js");
        $this->addJS("//cdn.coz.vn/html5shiv/html5shiv.min.js");
        $this->addJS("//cdn.coz.vn/jquery-migrate/jquery-migrate-1.2.0.min.js");
        $this->addJS("//cdn.coz.vn/owl-carousel/owl.carousel.min.js");
        $this->addJS("//cdn.coz.vn/magnific-popup/js/jquery.magnific-popup.min.js");
        $this->addJS("//cdn.coz.vn/iGrowl/js/igrowl.min.js");
        $this->addJS("//cdn.coz.vn/jquery-tmpl/jquery.tmpl.min.js");
        $this->addJS("//cdn.coz.vn/nprogress/nprogress.js");
        $this->addJS("//cdn.coz.vn/jquery-pjax/jquery.pjax.js");
        $this->addJS($this->baseUrl.'/styles/js/script.js');
		$this->addJS($this->baseUrl.'/styles/js/design.js');

        $this->addCSS('//fonts.googleapis.com/css?family=Roboto+Condensed:400,700,700italic,400italic,300italic,300&amp;subset=vietnamese');
        $this->addCSS('//cdn.coz.vn/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css');
        $this->addCSS('//cdn.coz.vn/font-awesome/4.7.0/css/font-awesome.min.css');
        $this->addCSS('//cdn.coz.vn/owl-carousel/owl.carousel.min.css');
        $this->addCSS('//cdn.coz.vn/owl-carousel/owl.theme.min.css');
        $this->addCSS('//cdn.coz.vn/magnific-popup/css/magnific-popup.css');
        $this->addCSS('//cdn.coz.vn/iGrowl/css/igrowl.min.css');
        $this->addCSS('//cdn.coz.vn/iGrowl/css/fonts/feather.css');
        $this->addCSS('//cdn.coz.vn/nprogress/nprogress.css');
        $this->addCSS($this->baseUrl.'/styles/css/style.css');
        $this->addCSS($this->baseUrl.'/styles/css/design.css');
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
            $ico_src = "/custom/domain_1/" . $domain.'/ico/favicon.ico';
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
        $this->languages = $this->getModelTable('LanguagesTable')->getLanguages();
        $lang = !empty($_SESSION['lang']) ? $_SESSION['lang'] : 'vi_VN';
        $this->language = $this->getModelTable('LanguagesTable')->getByCode( $lang );
        if( empty($this->language) ){
            $lang = 'vi_VN';
            $this->language = $this->getModelTable('LanguagesTable')->getByCode( $lang );
        }

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
}
