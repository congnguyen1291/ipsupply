<?php
namespace Application\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\I18n\Translator\Translator;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Articles extends AbstractHelper implements ServiceLocatorAwareInterface
{
	private  $sm = NULL;
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->sm;
    }

    public function __invoke()
    {
        return $this;
		
    }

    protected function getModelTable($name) {
        if (! isset ( $this->{$name} )) {
            $this->{$name} = NULL;
        }
        if (! $this->{$name}) {
            $sm = $this->getServiceLocator ();
            $this->{$name} = $sm->getServiceLocator()->get( 'Application\Model\\' . $name );
        }
        return $this->{$name};
    }

    /*interface cho khach hang*/

    public function toAlias($txt) {
        if ($txt == '')
            return '';
        $marked = array ("à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă","ằ", "ắ", "ặ", "ẳ", "ẵ", "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề","ế", "ệ", "ể", "ễ", "ế",             "ì", "í", "ị", "ỉ", "ĩ","ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ","ờ", "ớ", "ợ", "ở", "ỡ","ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ","ỳ", "ý", "ỵ", "ỷ", "ỹ","đ","À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă","Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ","È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ","Ì", "Í", "Ị", "Ỉ", "Ĩ","Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ","Ờ", "Ớ", "Ợ", "Ở", "Ỡ","Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ","Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ","Đ", " ", "&", "?", "/", ".", ",", "$", ":", "(", ")", "'", ";", "+", "–", "’" );
    
        $unmarked = array ("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "e", "e", "e", "e", "e", "e", "e","e", "e", "e", "e", "e", "i", "i", "i", "i", "i","o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o","o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",  "y", "y", "y", "y", "y", "d", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y", "D", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-" );
    
        $tmp3 = (str_replace ( $marked, $unmarked, $txt ));
        $tmp3 = rtrim ( $tmp3, "-" );
        $tmp3 = preg_replace ( array ('/\s+/', '/[^A-Za-z0-9\-]/' ), array ('-', '' ), $tmp3 );
        $tmp3 = preg_replace ( '/-+/', '-', $tmp3 );
        $tmp3 = strtolower ( $tmp3 );
        return $tmp3;
    }

    /*#pragram menu*/
    public function getMenuWithAlias($alias)
    {
        $menu = $this->getModelTable('MenusTable')->getMenuWithAlias($alias);
        return $menu;
    }

    public function getAllMenuAndSort()
    {
        $menus = $this->getModelTable('MenusTable')->getAllAndSort();
        return $menus;
    }

    public function getLinkForMenu($menu)
    {
        $link = '';
        if(!empty($menu)){
            switch ($menu['menus_type']) {
                case 'frontpage':
                    $link = FOLDERWEB;
                    break;
                case 'allcollection':
                    $link = FOLDERWEB . '/category';
                    break;
                case 'collection':
                    $link = FOLDERWEB . '/category/'.$this->toAlias($menu['menus_reference_name']).'-'.$menu['menus_reference_id'];
                    break;
                case 'product':
                    $link = FOLDERWEB . '/'.$this->toAlias($product['menus_reference_name']).'-'.$product['menus_reference_id'];
                    break;
                case 'catalog':
                    $link = FOLDERWEB . '/category';
                    break;
                case 'article':
                case 'page':
                    $link = FOLDERWEB . '/articles/'.$this->toAlias($menu['menus_reference_name']).'-'.$menu['menus_reference_id'].'.html';
                    break;
                case 'allblog':
                    $link = FOLDERWEB . '/articles';
                    break;
                case 'blog':
                    $link = FOLDERWEB . '/articles/'.$this->toAlias($menu['menus_reference_name']).'-'.$menu['menus_reference_id'];
                    break;
                case 'search':
                    $link = FOLDERWEB . '/search';
                    break;
                case 'http':
                    $link = $menu['menus_reference_url'];
                    break;
                default:
                    $link = 'javascript:void(0);';
                    break;
            }
        }
        return $link;
    }

    /*#pragram keyword*/
    public function getTopKeywords()
    {
        $keywords = $this->getModelTable('KeywordsTable')->getTopKeywords();
        return $keywords;
    }

    /*#pragram product*/
    public function getHotProduct()
    {
        $products = $this->getModelTable('ProductsTable')->getHotProduct();
        return $products;
    }

    public function getProductBanchay()
    {
        $products = $this->getModelTable('ProductsTable')->getProductBanchay();
        return $products;
    }

    public function getNewProduct()
    {
        $products = $this->getModelTable('ProductsTable')->getNewProduct();
        return $products;
    }

    public function getImages($products_id)
    {
        $images = $this->getModelTable('ProductsTable')->getImages($products_id);
        return $images;
    }

    public function getProductInCategory($categories_id, $params = array())
    {
        $child = $this->getModelTable('CategoriesTable')->getAllChildOfCate($categories_id);
        $child[] = $categories_id;
        $products = $this->getModelTable('ProductsTable')->getProductCate($child, $params);
        return $products;
    }
    /*
        $params
            page
    */
    public function getListProducts($params = array())
    {
        //$products = $this->getModelTable('ProductsTable')->getListProduct($params);
        $products = $this->getModelTable('ProductsTable')->getProductAll($params);
        return $products;
    }
    public function getFqaProduct($products_id, $intPage, $intPageSize)
    {
        $products = $this->getModelTable('ProductsTable')->getAllFqa($products_id, $intPage, $intPageSize);
        return $products;
    }

    public function getExtensionsRequireForProduct($products_id)
    {
        $extensions = $this->getModelTable('ProductsTable')->getExtensionsRequire($products_id);
        return $extensions;
    }

    public function getProductNearPrice($categories_id, $products_id, $price_sale, $price_min, $price_max)
    {
        $products = $this->getModelTable('ProductsTable')->getProductNearPrice($categories_id, $products_id, $price_sale, $price_min, $price_max);
        return $products;
    }
    public function getRecommendProductById($products_id, $limit)
    {
        $products = $this->getModelTable('ProductsTable')->getRecommendProductById($products_id, $limit);
        return $products;
    }

    /*#pragram Manufacturers*/
    public function getAllManufacturers($intPage, $intPageSize)
    {
        $manufacturers = $this->getModelTable('ManufacturersTable')->getAllManus($intPage, $intPageSize);
        return $manufacturers;
    }

    public function getManufacturers($list=array())
    {
        $manufacturers = $this->getModelTable('ManufacturersTable')->getRows($list);
        return $manufacturers;
    }

    /*#pragram Categories*/
    public function getAllGoldTimerCurrent($params = array(), $intPage=0, $intPageSize=100)
    {
        $products_deals = $this->getModelTable('CategoriesTable')->getAllGoldTimerCurrent($params, $intPage, $intPageSize);
        return $products_deals;
    }

    public function getAllCategoriesArticlesSort($offset=0, $limit=0)
    {
        $categories = $this->getModelTable('CategoriesArticlesTable')->getAllCategoriesArticlesSort();
        return $categories;
    }

    public function getCategoriesArticlesById($id)
    {
        $categories = $this->getModelTable('CategoriesArticlesTable')->getRow($id);
        return $categories;
    }

    public function getDatasForCategory($categories, $limit=100)
    {
        $datas = $this->getModelTable('CategoriesTable')->getDataHomepage($categories, $limit);
        return $datas;
    }

    public function getBreadCrumb($id, $breadCrumbMore = '')
    {
        $htmls = $this->getModelTable('CategoriesTable')->getBreadCrumb($id, $breadCrumbMore);
        return $htmls;
    }
    public function getLeftMenuPageCategory($id)
    {
        $htmls = $this->getModelTable('CategoriesTable')->getLeftMenuPageCategory($id);
        return $htmls;
    }
    public function countTotalProduct($cate)
    {
        $results = $this->getModelTable('CategoriesTable')->countTotalProduct($cate);
        return $results;
    }

    public function getHtmlLeftFilterFeature($list, $feature)
    {
        $results = $this->getModelTable('CategoriesTable')->getHtmlLeftFilterFeature($list, $feature);
        return $results;
    }

    /*#pragram Articles*/
    public function getTopArticles($offset=0, $limit=0)
    {
        $articles = $this->getModelTable('ArticlesTable')->getTopArticles($offset, $limit);
        return $articles;
    }
    public function getArticleOther($articles_id, $categories_articles_id)
    {
        $whereother = array('articles_id!=' . $articles_id, 'categories_articles_id=' . $categories_articles_id);
        $articles = $this->getModelTable('ArticlesTable')->getAllLimit($whereother);
        return $articles;
    }

    public function getPayments()
    {
        $payments = $this->getModelTable('UserTable')->getPaymentMethod();
        return $payments;
    }

    public function getTransportations()
    {
        $transportations = $this->getModelTable('UserTable')->loadTransportations();
        return $transportations;
    }

    public function getContries()
    {
        $contries = $this->getModelTable('CountryTable')->getContries();
        return $contries;
    }

    /*#pragram Banner*/
    public function getBannerWithPositionAlias($position, $size = '')
    {
        $banners = $this->getModelTable('BannersTable')->getBannerWithPositionAlias($position, $size = '');
        return $banners;
    }

    public function getUrlImage($url,$width='', $height='', $scrop = false,$bacground = '#FFF'){
        $preg_img01 = '/image\/(?P<year>\d+)\/(?P<month>\d+)\/(?P<day>\d+)\/(?P<type>(product|ckeditor|categories_icons|manufactures|articles|payments|logos|banners))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<id>\d+).(?P<ex>(jpg|png|gif|JPG|PNG|GIF))/';
        $preg_img02 = '/image\/(?P<year>\d+)\/(?P<month>\d+)\/(?P<day>\d+)\/(?P<type>(product|ckeditor|categories_icons|manufactures|articles|payments|logos|banners))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*)-(?P<folder>[a-zA-Z][a-zA-Z0-9_]*).(?P<ex>(jpg|png|gif|JPG|PNG|GIF))/';
        $preg_img03 = '/image\/(?P<year>\d+)\/(?P<month>\d+)\/(?P<day>\d+)\/(?P<type>(product|ckeditor|categories_icons|manufactures|articles|payments|logos|banners))-(?P<title>[a-zA-Z][a-zA-Z0-9_-]*).(?P<ex>(jpg|png|gif|JPG|PNG|GIF))/';
        preg_match($preg_img01, $url, $matches1);
        preg_match($preg_img02, $url, $matches2);
        preg_match($preg_img03, $url, $matches3);
        if(empty($matches1) && empty($matches2) && empty($matches3)){
            $src = PATH_BASE_ROOT.DS.trim($url,'/');
            if (!is_file($src) || !$this->isImage($url) ) {
                if(empty($width)){
                    $width = 100;
                }
                if(empty($height)){
                    $height = 100;
                }   
                $url='/image/placeholder/no-photo-'.$width.'x'.$height.'.png';                         
            }else{
                $de = $width;
                if(!empty($height)){
                    $de .= 'x'.$height;
                }
                if(!empty($de)){
                    $de = '-'.$de;
                }
                $preg_01 = '/\/custom\/domain_1\/(?P<folder>(products))\/(?P<domain>\w.+)\/(?P<year>\d+)\/(?P<month>\d+)\/(?P<day>\d+)\/(?P<type>(product))(?P<id>\d+)\/fullsize\/(?P<title>[a-zA-Z][a-zA-Z0-9_-]*).(?P<ex>(jpg|png|gif|JPG|PNG|GIF))/';
                $preg_02 = '/\/custom\/domain_1\/(?P<folder>(products))\/fullsize\/(?P<type>(product))(?P<id>\d+)\/(?P<title>[a-zA-Z][a-zA-Z0-9_-]*).(?P<ex>(jpg|png|gif|JPG|PNG|GIF))/';
                preg_match($preg_01, $url, $matches);
                preg_match($preg_02, $url, $matches01);
                if(!empty($matches)){
                    $ttl = $matches['type'].'-'.$matches['title'].'-'.$matches['id'].$de;
                    $url='/image/'.$matches['year'].'/'.$matches['month'].'/'.$matches['day'].'/'.$ttl.'.'.$matches['ex'];
                }else if(!empty($matches01)){
                    $ttl = $matches01['type'].'-'.$matches01['title'].'-'.$matches01['id'].$de;
                    $url='/image/'.$ttl.'.'.$matches01['ex'];
                }
            }
        }else{
            $de = $width;
            if(!empty($height)){
                $de .= 'x'.$height;
            }
            if(!empty($de)){
                $de = '-'.$de;
            }
            $stack = explode('.', $url);
            $ex = array_pop($stack);
            $stack = implode('.', $stack);
            $url = $stack .$de .'.'. $ex;
        }
        return $url;
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

    public function getAllFeatureAndSort()
    {
        $features = $this->getModelTable('FeatureTable')->getAllFeatureAndSort();
        return $features;
    }

    public function getFeatureByID($feature_id)
    {
        $features = $this->getModelTable('FeatureTable')->getFeatureByID($feature_id);
        return $features;
    }

    public function getFeatureByIDV2($feature_id)
    {
        $features = $this->getModelTable('FeatureTable')->getFeatureByIDV2($feature_id);
        return $features;
    }

    public function getBranches()
    {
        $branches = $this->getModelTable('BranchesTable')->getRows();
        return $branches;
    }

    public function getAllCategoryArticleAndSort()
    {
        $categories = $this->getModelTable('CategoriesArticlesTable')->getAllCategoriesArticlesSort();
        return $categories;
    }

    public function getCurrencySymbol()
    {
        return $_SESSION['website']['website_currency'];
    }

    public function getPositionCurrencySymbol()
    {
        $str ='';
        switch ($_SESSION['website']['website_currency']) {
            case 'USD':
            case 'EUR':
            case 'GBP':
            case 'JPY':
            case 'SGD':
            case 'KRW':
            case 'THB':
                $str = 'left';
                break;
            case 'VND':
            case 'CNY':
                $str = 'right';
                break;
            
            default:
                $str = 'right';
                break;
        }

        return $str;
    }

    public function fomatCurrency($number,$decimals=2,$decimalpoint='.',$separator=',')
    {
        $str ='';
        if ($_SESSION['website']['website_currency_decimals'] != '') {
            $decimals = $_SESSION['website']['website_currency_decimals'];
        }
        if (!empty($_SESSION['website']['website_currency_decimalpoint'])) {
            $decimalpoint = $_SESSION['website']['website_currency_decimalpoint'];
        }
        if (!empty($_SESSION['website']['website_currency_separator'])) {
            $separator = $_SESSION['website']['website_currency_separator'];
        }
        switch ($_SESSION['website']['website_currency']) {
            case 'VND':
                $str = number_format($number,$decimals,$decimalpoint,$separator). ' VND';
                break;
            case 'CNY':
                $str = number_format($number,$decimals,$decimalpoint,$separator). ' yuan';
                break;
            case 'USD':
                $str = '$'.number_format($number,$decimals,$decimalpoint,$separator);
                break;
            case 'EUR':
                $str = '€'.number_format($number,$decimals,$decimalpoint,$separator);
                break;
            case 'GBP':
                $str = '£'.number_format($number,$decimals,$decimalpoint,$separator);
                break;
            case 'JPY':
                $str = '¥'.number_format($number,$decimals,$decimalpoint,$separator);
                break;
            case 'SGD':
                $str = 'S$'.number_format($number,$decimals,$decimalpoint,$separator);
                break;
            case 'KRW':
                $str = '₩'.number_format($number,$decimals,$decimalpoint,$separator);
                break;
            case 'THB':
                $str = '฿'.number_format($number,$decimals,$decimalpoint,$separator);
                break;
            
            default:
                $str = number_format($number,$decimals,$decimalpoint,$separator);
                break;
        }

        return $str;
    }

    public function getArticleForEachCategories($cat_root, $limit = 5)
    {
        $datas = $this->getModelTable('ArticlesTable')->getArticleForEachCategories($cat_root, $limit);
        return $datas;
    }

    public function getNewsArticles($offset=0, $limit=0)
    {
        $articles = $this->getModelTable('ArticlesTable')->getNewsArticles($offset, $limit);
        return $articles;
    }

    public function getHotsArticles($offset=0, $limit=0)
    {
        $articles = $this->getModelTable('ArticlesTable')->getHotsArticles($offset, $limit);
        return $articles;
    }

    public function getStaticArticles($offset=0, $limit=0)
    {
        $articles = $this->getModelTable('ArticlesTable')->getStaticArticles($offset, $limit);
        return $articles;
    }

    public function getFqaArticles($offset=0, $limit=0)
    {
        $articles = $this->getModelTable('ArticlesTable')->getFqaArticles($offset, $limit);
        return $articles;
    }

    public function getArticlesInCategory($categories_articles_id, $offset=0, $limit=5)
    {
        $articles = $this->getModelTable('ArticlesTable')->getArticlesInCategory($categories_articles_id, $offset, $limit);
        return $articles;
    }
	
}
