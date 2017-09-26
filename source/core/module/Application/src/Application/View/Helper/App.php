<?php
namespace Application\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\I18n\Translator\Translator;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\RequestInterface;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class App extends AbstractHelper implements ServiceLocatorAwareInterface
{
	protected  $sm = NULL;
    protected  $datas = NULL;
    protected  $contentview = NULL;

    public function createKeyCacheFromArray($arr)
    {
        if(is_array($arr)){
            foreach ($arr as $key => $value) {
                if(is_array($value)){
                    $value = $key.','.$this->createKeyCacheFromArray($value);
                    $arr[$key] = $value;
                }
            }
            return implode(",",$arr);
        }else{
            return '['.$arr.']';
        }
    }

    protected function randText($characters)
    {
        $possible = '1234567890abcdefghjkmnpqrstvwxyzABCDEFGHJKMNPQRSTVWXYZ';
        $code = '';
        $i = 0;
        while ($i < $characters) {
            $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $i++;
        }
        return $code;
    }

    protected function getNamspaceCached() {
        $str = '';
        if(!empty($_SESSION['domain'])){
            $str = $_SESSION['domain'];
        }
        if(!empty($_SESSION['lang'])){
            $str .= ':'.$_SESSION['lang'];
        }
        $str .= ':Application';
        if( !empty($_SESSION['config'])
            && !empty($_SESSION['config']['cached']) 
            && !empty($_SESSION['config']['cached']['namespace']) ){
            $str .= '_'. $_SESSION['config']['cached']['namespace'];
        }
        if( $this->hasLocation() ){
            $str .= ':location_:';
            if( !empty($_SESSION['LOCATION']['country_id']) ){
                $str .= '_country_id_'.$_SESSION['LOCATION']['country_id'];
            }
            if( !empty($_SESSION['LOCATION']['cities_id']) ){
                $str .= '_cities_id_'.$_SESSION['LOCATION']['cities_id'];
            }
            if( !empty($_SESSION['LOCATION']['districts_id']) ){
                $str .= '_districts_id_'.$_SESSION['LOCATION']['districts_id'];
            }
            if( !empty($_SESSION['LOCATION']['wards_id']) ){
                $str .= '_wards_id_'.$_SESSION['LOCATION']['wards_id'];
            }
        }
        return $str;
    }

    public function hasLocation(){
        if( !empty($_SESSION['LOCATION'])
            && (    !empty($_SESSION['LOCATION']['country_id'])
                    || !empty($_SESSION['LOCATION']['cities_id'])
                    || !empty($_SESSION['LOCATION']['districts_id'])
                    || !empty($_SESSION['LOCATION']['wards_id'])) ){
            return TRUE;
        }
        return FALSE;
    }

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

    protected function getTranslator() {
        $sm = $this->getServiceLocator ();
        $translator = $sm->getServiceLocator()->get( 'translator' );
        return $translator;
    }

    protected function getApp() {
        $sm = $this->getServiceLocator ();
        $app = $sm->getServiceLocator()->get( 'Application' );
        return $app;
    }

    public function setContentview($contentview)
    {
        $this->contentview = $contentview;
        return $this;
    }

    public function getContentview()
    {
        return $this->contentview;
    }

    public function setDatas($datas)
    {
        $this->datas = $datas;
        return $this;
    }

    public function getDatas()
    {
        return $this->datas;
    }

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

    public function getCookie( $name ) {
        $app = $this->getApp();
        if( !empty($name) ){
            $cookie = $app->getRequest()->getCookie();
            if( !empty($cookie->$name) ){
                return $cookie->$name;
            }
        }
        return '';
    }

    public function setCookie( $name, $value, $day = '' ) {
        $app = $this->getApp();
        if( !empty($name) && !empty($value) ){
            $expires = time() + (5 * 60);//5 phut
            if( !empty($day) ){
                $expires = time() + ($day * 24 * 60 * 60);
            }
            $cookie = new  \Zend\Http\Header\SetCookie($name, $value, $expires ,'/');
            $app->getResponse()->getHeaders()->addHeader($cookie);
        }
        return FALSE;
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

    public function getInlineScript( $name, $value, $day = '' ) {
        $scripts = $this->view->inlineScript();
        $scripts->appendScript('console.log("inline");');
    }

    public function setJavascriptProduct( $products, $type = '' ) {
        if( !empty($products) ){
            $scripts = $this->view->inlineScript();
            $scripts->appendScript('coz.addProductModel('.json_encode($products).');');
        }
        return FALSE;
    }

    public function setJavascriptFeatures( $features, $type = '' ) {
        if( !empty($features) ){
            $scripts = $this->view->inlineScript();
            $scripts->appendScript('coz.addFeaturesModel('.json_encode($features).');');
        }
        return FALSE;
    }

    public function setJavascriptFeaturesSort( $features, $type = '' ) {
        if( !empty($features) ){
            $scripts = $this->view->inlineScript();
            $scripts->appendScript('coz.addFeaturesModelSort('.json_encode($features).');');
        }
        return FALSE;
    }

    public function setJavascriptManufacturers( $manufacturers, $type = '' ) {
        if( !empty($manufacturers) ){
            $scripts = $this->view->inlineScript();
            $scripts->appendScript('coz.addManufacturersModel('.json_encode($manufacturers).');');
        }
        return FALSE;
    }
	
}
