<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/23/14
 * Time: 2:07 PM
 */
namespace Cms\Controller;
use Cms\Form\WebsiteForm;
use Cms\Model\Websites;
use Zend\View\Model\ViewModel;
use Cms\Lib\Paging;

use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\LessFilter;
use Assetic\Filter\Yui;
use Assetic\Factory\AssetFactory;
use Assetic\Util\TraversableString;
use Assetic\Filter\Yui\JsCompressorFilter as YuiCompressorFilter;
use Assetic\Filter\GoogleClosure\CompilerApiFilter;
use Assetic\Filter\GoogleClosure\CompilerJarFilter;
use Assetic\Filter\GssFilter;
use Assetic\Asset\HttpAsset;
use Assetic\FilterManager;
use Assetic\Filter\GoogleClosure\ApiFilter as ClosureFilter;
use Assetic\AssetWriter;
use Assetic\Filter\CssImportFilter;
use Assetic\Filter\CssMinFilter;
use Assetic\Filter\CleanCssFilter;
use Assetic\Filter\CssRewriteFilter;
use Assetic\Filter\JSMinFilter;

class WebsiteController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'website';
    }

    private $list_crop =    array( 0=>'Crop hình', 
                                1=>'Đặt hình vào trong khung'
                            );
    private $list_version_cart = array('0'=>'01', '02'=>'02');

    private $list_timezone = array("Dateline Standard Time"=>"(UTC-12:00) International Date Line West",
        "UTC-11"=>"(UTC-11:00) Coordinated Universal Time -11",
        "Hawaiian Standard Time"=>"(UTC-10:00) Hawaii",
        "Alaskan Standard Time"=>"(UTC-09:00) Alaska",
        "Pacific Standard Time"=>"(UTC-08:00) Pacific Time (US and Canada)",
        "Pacific Standard Time (Mexico)"=>"(UTC-08:00)Baja California",
        "Mountain Standard Time"=>"(UTC-07:00) Mountain Time (US and Canada)",
        "Mountain Standard Time (Mexico)"=>"(UTC-07:00) Chihuahua, La Paz, Mazatlan",
        "US Mountain Standard Time"=>"(UTC-07:00) Arizona",
        "Canada Central Standard Time"=>"(UTC-06:00) Saskatchewan",
        "Central America Standard Time"=>"(UTC-06:00) Central America",
        "Central Standard Time"=>"(UTC-06:00) Central Time (US and Canada)",
        "Central Standard Time (Mexico)"=>"((UTC-06:00) Guadalajara, Mexico City, Monterrey",
        "Eastern Standard Time"=>"(UTC-05:00) Eastern Time (US and Canada)",
        "SA Pacific Standard Time"=>"(UTC-05:00) Bogota, Lima, Quito",
        "US Eastern Standard Time"=>"(UTC-05:00) Indiana (East)",
        "Atlantic Standard Time"=>"(UTC-04:00) Atlantic Time (Canada)",
        "Central Brazilian Standard Time"=>"(UTC-04:00) Cuiaba",
        "Pacific SA Standard Time"=>"(UTC-04:00) Santiago",
        "SA Western Standard Time"=>"(UTC-04:00) Georgetown, La Paz, Manaus, San Juan",
        "Venezuela Standard Time"=>"(UTC-04:30) Caracas",
        "Paraguay Standard Time"=>"(UTC-04:00) Asuncion",
        "Newfoundland Standard Time"=>"(UTC-03:30) Newfoundland",
        "E. South America Standard Time"=>"(UTC-03:00) Brasilia",
        "Greenland Standard Time"=>"(UTC-03:00) Greenland",
        "Montevideo Standard Time"=>"(UTC-03:00) Montevideo",
        "SA Eastern Standard Time"=>"(UTC-03:00) Cayenne, Fortaleza",
        "Argentina Standard Time"=>"(UTC-03:00) Buenos Aires",
        "Mid-Atlantic Standard Time"=>"(UTC-02:00) Mid-Atlantic",
        "UTC-2"=>"(UTC-02:00) Coordinated Universal Time -02",
        "Azores Standard Time"=>"(UTC-01:00) Azores",
        "Cabo Verde Standard Time"=>"(UTC-01:00) Cabo Verde Is.",
        "GMT Standard Time"=>"(UTC) Dublin, Edinburgh, Lisbon, London",
        "Greenwich Standard Time"=>"(UTC) Monrovia, Reykjavik",
        "Morocco Standard Time"=>"(UTC) Casablanca",
        "UTC"=>"(UTC) Coordinated Universal Time",
        "Central Europe Standard Time"=>"(UTC+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague",
        "Central European Standard Time"=>"(UTC+01:00) Sarajevo, Skopje, Warsaw, Zagreb",
        "Romance Standard Time"=>"(UTC+01:00) Brussels, Copenhagen, Madrid, Paris",
        "W. Central Africa Standard Time"=>"(UTC+01:00) West Central Africa",
        "W. Europe Standard Time"=>"(UTC+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna",
        "Namibia Standard Time"=>"(UTC+01:00) Windhoek",
        "E. Europe Standard Time"=>"(UTC+02:00) Minsk",
        "Egypt Standard Time"=>"(UTC+02:00) Cairo",
        "FLE Standard Time"=>"(UTC+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius",
        "GTB Standard Time"=>"(UTC+02:00) Athens, Bucharest",
        "Israel Standard Time"=>"(UTC+02:00) Jerusalem",
        "Jordan Standard Time"=>"(UTC+02:00) Amman",
        "Middle East Standard Time"=>"(UTC+02:00) Beirut",
        "South Africa Standard Time"=>"(UTC+02:00) Harare, Pretoria",
        "Syria Standard Time"=>"(UTC+02:00) Damascus",
        "Turkey Standard Time"=>"(UTC+02:00) Istanbul",
        "Arab Standard Time"=>"(UTC+03:00) Kuwait, Riyadh",
        "Arabic Standard Time"=>"(UTC+03:00) Baghdad",
        "E. Africa Standard Time"=>"(UTC+03:00) Nairobi",
        "Kaliningrad Standard Time"=>"(UTC+03:00) Kaliningrad",
        "Russian Standard Time"=>"(UTC+04:00) Moscow, St. Petersburg, Volgograd",
        "Iran Standard Time"=>"(UTC+03:30) Tehran",
        "Arabian Standard Time"=>"(UTC+04:00) Abu Dhabi, Muscat",
        "Azerbaijan Standard Time"=>"(UTC+04:00) Baku",
        "Caucasus Standard Time"=>"(UTC+04:00) Yerevan",
        "Afghanistan Standard Time"=>"(UTC+04:30) Kabul",
        "Georgian Standard Time"=>"(UTC+04:00) Tbilisi",
        "Mauritius Standard Time"=>"(UTC+04:00) Port Louis",
        "Ekaterinburg Standard Time"=>"(UTC+06:00) Ekaterinburg",
        "West Asia Standard Time"=>"(UTC+05:00) Tashkent",
        "India Standard Time"=>"(UTC+05:30) Chennai, Kolkata, Mumbai, New Delhi",
        "Sri Lanka Standard Time"=>"(UTC+05:30) Sri Jayawardenepura",
        "Nepal Standard Time"=>"(UTC+05:45) Kathmandu",
        "Pakistan Standard Time"=>"(UTC+05:00) Islamabad, Karachi",
        "Central Asia Standard Time"=>"(UTC+06:00) Astana",
        "N. Central Asia Standard Time"=>"(UTC+07:00) Novosibirsk",
        "Myanmar Standard Time"=>"(UTC+06:30) Yangon (Rangoon)",
        "Bangladesh Standard Time"=>"(UTC+06:00) Dhaka",
        "North Asia Standard Time"=>"(UTC+08:00) Krasnoyarsk",
        "SE Asia Standard Time"=>"(UTC+07:00) Bangkok, Hanoi, Jakarta",
        "China Standard Time"=>"(UTC+08:00) Beijing, Chongqing, Hong Kong, Urumqi",
        "North Asia East Standard Time"=>"(UTC+09:00) Irkutsk",
        "Singapore Standard Time"=>"(UTC+08:00) Kuala Lumpur, Singapore",
        "Taipei Standard Time"=>"(UTC+08:00) Taipei",
        "W. Australia Standard Time"=>"(UTC+08:00) Perth",
        "Ulaanbaatar Standard Time"=>"(UTC+08:00) Ulaanbaatar",
        "Korea Standard Time"=>"(UTC+09:00) Seoul",
        "Tokyo Standard Time"=>"(UTC+09:00) Osaka, Sapporo, Tokyo",
        "Yakutsk Standard Time"=>"(UTC+10:00) Yakutsk",
        "AUS Central Standard Time"=>"(UTC+09:30) Darwin",
        "Cen. Australia Standard Time"=>"(UTC+09:30) Adelaide",
        "AUS Eastern Standard Time"=>"(UTC+10:00) Canberra, Melbourne, Sydney",
        "E. Australia Standard Time"=>"(UTC+10:00) Brisbane",
        "Tasmania Standard Time"=>"(UTC+10:00) Hobart",
        "Vladivostok Standard Time"=>"(UTC+11:00) Vladivostok",
        "West Pacific Standard Time"=>"(UTC+10:00) Guam, Port Moresby",
        "Central Pacific Standard Time"=>"(UTC+11:00) Solomon Is., New Caledonia",
        "Magadan Standard Time"=>"(UTC+12:00) Magadan",
        "Fiji Standard Time"=>"(UTC+12:00) Fiji",
        "New Zealand Standard Time"=>"(UTC+12:00) Auckland, Wellington",
        "UTC+12"=>"(UTC+12:00) Coordinated Universal Time +12",
        "Tonga Standard Time"=>"(UTC+13:00) Nuku'alofa",
        "Samoa Standard Time"=>"(UTC-11:00)Samoa");
    private $list_curency = array("VND"=>"Việt Nam đồng (VND)",
        "USD"=>"United States Dollars (USD)",
        "EUR"=>"Euro (EUR)",
        "GBP"=>"United Kingdom Pounds (GBP)",
        "JPY"=>"Japanese Yen (JPY)",
        "SGD"=>"Singapore Dollars (SGD)",
        "KRW"=>"South Korean Won (KRW)",
        "THB"=>"Thai baht (THB)",
        "CNY"=>"Chinese (Simplified, China)");

    private $type_buy = array(
                            "0"=>"Tất cả",
                            "1"=>"Giao hàng tận nơi",
                            "2"=>"Nhận hàng tại của hàng"
                        );

    public function indexAction()
    {
        $form = new WebsiteForm();
        $form->get('submit')->setValue('Cập nhật');
        $form->get('website_timezone')->setOptions(array(
            'options' => $this->list_timezone
        ));
        $form->get('website_currency')->setOptions(array(
            'options' => $this->list_curency
        ));
        $form->get('version_cart')->setOptions(array(
            'options' => $this->list_version_cart
        ));
        $form->get('type_crop_image')->setOptions(array(
            'options' => $this->list_crop
        ));
        $contries = $this->getModelTable('CountryTable')->getContries();
        $options_contries = array();
        foreach ($contries as $key => $contry) {
            $options_contries[$contry['id']] = $contry['title'];
        }
        $form->get('website_contries')->setOptions(array(
            'options' => $options_contries
        ));
        $form->get('type_buy')->setOptions(array(
            'options' => $this->type_buy
        ));
        $form->bind($this->website);
        $_website_contries = explode(',', $this->website->website_contries);
        $form->get('website_contries')->setValue($_website_contries);

        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $website = new Websites();
            $form->setInputFilter($website->getInputFilter());
            $form->setData($request->getPost());
            $domain = $_SESSION['website']['website_domain'];
            if($form->isValid()){
                $picture_id = $request->getPost('picture_id', '');
                if(!empty($picture_id)){
                    $picture = $this->getModelTable('PictureTable')->getPicture($picture_id);
                    if(!empty($picture)){
                        $data['logo'] = $picture->folder.'/'.$picture->name.'.'.$picture->type;
                    }
                }
                $data['website_domain'] = $domain;
                if( !empty($data['website_contries']) 
                    && is_array($data['website_contries']) ){
                    $data['website_contries'] = implode(',', $data['website_contries']);
                }
                $website->exchangeArray($data);
                $this->getModelTable('WebsitesTable')->saveWebsite($website);
                return $this->redirect()->toRoute('cms/website');
            }else{
                //print_r($form->getMessages());die();
            }

        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/website', array(
                'action' => 'list'
            ));
        }
        try {
            $website = $this->getModelTable('WebsitesTable')->getWebsiteWithId($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/website', array(
                'action' => 'list'
            ));
        }
        $form = new WebsiteForm();
        $form->get('submit')->setValue('Cập nhật');
        $form->get('website_timezone')->setOptions(array(
            'options' => $this->list_timezone
        ));
        $form->get('version_cart')->setOptions(array(
            'options' => $this->list_version_cart
        ));
        $form->get('type_crop_image')->setOptions(array(
            'options' => $this->list_crop
        ));
        $form->get('website_currency')->setOptions(array(
            'options' => $this->list_curency
        ));
        $form->get('type_buy')->setOptions(array(
            'options' => $this->type_buy
        ));
        $contries = $this->getModelTable('CountryTable')->getContries();
        $options_contries = array();
        foreach ($contries as $key => $contry) {
            $options_contries[$contry['id']] = $contry['title'];
        }
        $form->get('website_contries')->setOptions(array(
            'options' => $options_contries
        ));
        $form->bind($website);
        $_website_contries = explode(',', $website->website_contries);
        $form->get('website_contries')->setValue($_website_contries);

        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $website = new Websites();
            $form->setInputFilter($website->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                $picture_id = $request->getPost('picture_id', '');
                if(!empty($picture_id)){
                    $picture = $this->getModelTable('PictureTable')->getPicture($picture_id);
                    if(!empty($picture)){
                        $data['logo'] = $picture->folder.'/'.$picture->name.'.'.$picture->type;
                    }
                }
                if( !empty($data['website_contries']) 
                    && is_array($data['website_contries']) ){
                    $data['website_contries'] = implode(',', $data['website_contries']);
                }
                $website->exchangeArray($data);
                $modules = $request->getPost('modules');
                $this->getModelTable('WebsitesTable')->saveWebsiteForSupperWeb($website, $modules);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                return $this->redirect()->toRoute('cms/website', array(
                    'action' => 'list'
                ));
            }else{
                print_r($form->getMessages());die();
            }

        }
        $modules = $this->getModelTable('ModulesTable')->fetchAll('', array('date_create' => 'ASC', 'date_update' => 'ASC'), 0, 1000);
        $website_modules = $this->getModelTable('ModulesTable')->getWebsiteModule($website->website_id);

        $module_access = array();
        foreach($website_modules as $module){
            $module_access[] = $module['module_id'];
        }
        $this->data_view['id'] = $website->website_id;
        $this->data_view['mywebsite'] = $website;
        $this->data_view['form'] = $form;
        $this->data_view['modules'] = $modules;
        $this->data_view['module_access'] = $module_access;
        return $this->data_view;
    }

    public function listAction()
    {
        $params = array();
        $link = '';
        $page = $this->params()->fromQuery('page', 0);
        $order_type = $this->params()->fromQuery('order_type','desc');
        $order_by = $this->params()->fromQuery('order','website_id');

        $website_name = $this->params()->fromQuery('website_name', NULL);
        if($website_name){
            $params['website_name'] = $website_name;
            $link .= '&website_name='.$website_name;
        }
        $date_create = $this->params()->fromQuery('date_create', NULL);
        if($date_create){
            $params['date_create'] = $date_create;
            $link .= '&date_create='.$date_create;
        }
        $is_try = $this->params()->fromQuery('is_try', NULL);
        if($is_try){
            $params['is_try'] = $is_try;
            $link .= '&is_try='.$is_try;
        }
        $is_published = $this->params()->fromQuery('is_published', NULL);
        if($is_published){
            $params['is_published'] = $is_published;
            $link .= '&is_published='.$is_published;
        }


        $order = array(
            $order_by => $order_type,
        );
        $order_link = $link;

        $total = $this->getModelTable('WebsitesTable')->countAll($params);
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        $objPage = new Paging( $total, $page, $page_size, $link );
        $paging = $objPage->getListFooter ( $link );
        $websites = $this->getModelTable('WebsitesTable')->getList($params, $order, $this->intPage, $this->intPageSize);
        if(!$order_link){
            $order_link = FOLDERWEB.'/cms/website';
            if(isset($_GET['page'])){
                $order_link .= '?page='.$_GET['page'].'&';
            }else{
                $order_link .= '?';
            }
        }else{
            $order_link = FOLDERWEB.'/cms/website?'.trim($order_link,'&');
            if(isset($_GET['page'])){
                $order_link .= '&page='.$_GET['page'].'&';
            }else{
                $order_link .= '&';
            }
        }
        $this->data_view['websites'] = $websites;
        $this->data_view['paging'] = $paging;
        $this->data_view['order_link'] = $order_link;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $websites = $this->getModelTable('WebsitesTable')->getListWebsiteWithId($ids);
            foreach ($websites as $key => $website) {
                $this->getModelTable('WebsitesTable')->deleteWebsite($website);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }
        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function singleTryAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_try' => 1
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function singleNotTryAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_try' => 0
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function singlepublishAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function singleunpublishAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();


        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            if (count($ids) > 0) {
                $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }
        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function tryAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_try' => 1
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();


        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function notTryAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_try' => 0
            );
            if (count($ids) > 0) {
                $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }
        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function compressCss( $files )
    {
        $name_folder = $this->website['websites_folder'];
        try{
            if( !empty($files) ){
                $am = new AssetManager();
                $fm = new FilterManager();
                $fm->set('ImportFilter', new CssImportFilter());
                $fm->set('RewriteFilter', new CssRewriteFilter());
                $fm->set('CssMinFilter', new CssMinFilter());

                $factory = new AssetFactory(FOLDERWEB.'/templates/Websites/'.$name_folder.'/styles/');
                $factory->setAssetManager($am);
                $factory->setFilterManager($fm);
                $factory->setDebug(true);
                $factory->setDefaultOutput('/minify/css/');

                $cssAsset = $factory->createAsset($files, array(
                    'ImportFilter',
                    'RewriteFilter',
                    //'CssMinFilter',
                ));
                $urlComCs = '/styles/minify/css/'.$this->randText(10).date('dmYHms').'.css';
                file_put_contents(PATH_BASE_ROOT.'/templates/Websites/'.$name_folder.$urlComCs, $cssAsset->dump());
                return $urlComCs;
            }
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
        return '';
    }

    public function compressJS( $files )
    {
        $name_folder = $this->website['websites_folder'];
        try{
            if( !empty($files) ){
                $am = new AssetManager();
                $fm = new FilterManager();
                $fm->set('CompilerJarFilter', new CompilerJarFilter(PATH_BASE_ROOT.'/cdn/closure-compiler/closure-compiler.jar'));
                $fm->set('JSMinFilter', new JSMinFilter());
                $factory = new AssetFactory(FOLDERWEB.'/templates/Websites/'.$name_folder.'/styles/');
                $factory->setAssetManager($am);
                $factory->setFilterManager($fm);
                $factory->setDebug(true);
                $factory->setDefaultOutput('/minify/css/');
                $jsAsset = $factory->createAsset($files, array(
                    'JSMinFilter',
                    //'CompilerJarFilter',
                ));
                $urlComCs = '/styles/minify/js/'.$this->randText(10).date('dmYHms').'.js';
                file_put_contents(PATH_BASE_ROOT.'/templates/Websites/'.$name_folder.$urlComCs, $jsAsset->dump());
                return $urlComCs;
            }
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
        return '';
    }


    public function compressAction()
    {
        $result = array('flag' => FALSE, 'msg' => '');
        if(!$this->isMasterPage()){
            try{
                $ListFileAsset = array();
                $name_folder = $this->website['websites_folder'];
                $url_config = PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/config.json';
                if(is_file($url_config)){
                    $urlCom = '/templates/Websites/'.$name_folder.'/styles/minify';
                    if(!is_dir(PATH_BASE_ROOT.$urlCom)){
                        @mkdir ( PATH_BASE_ROOT.$urlCom, 0777 );
                    }
                    $urlComCs = $urlCom.'/css';
                    if(!is_dir(PATH_BASE_ROOT.$urlComCs)){
                        @mkdir ( PATH_BASE_ROOT.$urlComCs, 0777 );
                    }
                    $urlComJs = $urlCom.'/js';
                    if(!is_dir(PATH_BASE_ROOT.$urlComJs)){
                        @mkdir ( PATH_BASE_ROOT.$urlComJs, 0777 );
                    }
                    $config = file_get_contents($url_config);
                    $config = json_decode($config, true);
                    
                    $listCssFileRunTime = array();
                    $listCssFile = array();
                    $listCss = array();
                    $listCssOnly = array();
                    if(!empty($config['css'])){
                        foreach ($config['css'] as $kcss => $iCss) {
                            if( !$this->isUrlHttp($iCss['href']) ){
                                $iCss['href'] = 'https://static.coz.vn/'.$name_folder.'/'.trim($iCss['href'],'/');
                            }
                            if( empty($iCss['only']) ){
                                $href = $iCss['href'];
                                $listCssFileRunTime[] = $href;
                            }else{
                                if( !empty($listCssFileRunTime) ){
                                    if( count($listCssFileRunTime)>1 ){
                                        $urlComCs = $this->compressCss($listCssFileRunTime);
                                        $listCssFile[] = array('href'=>$urlComCs, 'rel' => 'stylesheet', 'type' => ' text/css');
                                    }else{
                                        $listCssFile[] = array('href'=>$listCssFileRunTime[0], 'rel' => 'stylesheet', 'type' => ' text/css');
                                    }
                                    $listCssFileRunTime = array();
                                }
                                $listCssFile[] = $iCss;
                            }

                            /*if( empty($iCss['only'])
                                && (strpos( $iCss['href'], FOLDERWEB ) !== false 
                                || strpos( $iCss['href'], 'static.coz.vn' ) !== false 
                                || strpos( $iCss['href'], 'cdn.coz.vn' ) !== false) ){
                                $href = $iCss['href'];
                                $listCssFileRunTime[] = $href;
                                continue;
                            }else {
                                if( !empty($listCssFileRunTime) ){
                                    if( count($listCssFileRunTime)>1 ){
                                        $urlComCs = $this->compressCss($listCssFileRunTime);
                                        $listCssFile[] = array('href'=>$urlComCs, 'rel' => 'stylesheet', 'type' => ' text/css');
                                    }else{
                                        $listCssFile[] = array('href'=>str_replace(FOLDERWEB.'/templates/Websites/'.$name_folder, '', $listCssFileRunTime[0]), 'rel' => 'stylesheet', 'type' => ' text/css');
                                    }
                                    $listCssFileRunTime = array();
                                }
                                $listCssFile[] = $iCss;
                            }*/
                            
                            /*$only = array();
                            if( !empty($iCss['only']) ){
                                $only = explode(',', $iCss['only']);
                            }
                            if( !empty($iCss) && !empty($iCss['href']) ){
                                $href = '';
                                if( !$this->isUrlHttp($iCss['href']) ){
                                    $href = FOLDERWEB.'/templates/Websites/'.$name_folder.'/'.trim($iCss['href'],'/');
                                }else{
                                    $href = $iCss['href'];
                                }
                                if( empty($only) ){
                                    $listCss[] = $href;
                                    foreach ($listCssOnly as $lok => $lco) {
                                        $lco[] = $href;
                                    }
                                }else{
                                    foreach ($only as $lk => $ol) {
                                        if( empty($listCssOnly[$ol]) ){
                                            $listCssOnly[$ol] = array();
                                        }
                                        $listCssOnly[$ol][] = $href;
                                    }
                                }
                            }*/
                        }

                        if( !empty($listCssFileRunTime) ){
                            if( count($listCssFileRunTime)>1 ){
                                $urlComCs = $this->compressCss($listCssFileRunTime);
                                $listCssFile[] = array('href'=>$urlComCs, 'rel' => 'stylesheet', 'type' => 'text/css');
                            }else{
                                $listCssFile[] = array('href'=>$listCssFileRunTime[0], 'rel' => 'stylesheet', 'type' => 'text/css');
                            }
                            $listCssFileRunTime = array();
                        }
                    }

                    $listJSFileRunTime = array();
                    $listJSFile = array();
                    $listJs = array();
                    $listJsOnly = array();
                    if(!empty($config['js'])){
                        foreach ($config['js'] as $kjs => $iJs) {
                            if( !$this->isUrlHttp($iJs['src']) ){
                                $iJs['src'] = 'https://static.coz.vn/'.$name_folder.'/'.trim($iJs['src'],'/');
                            }
                            if( empty($iJs['only']) ){
                                $src = $iJs['src'];
                                $listJSFileRunTime[] = $src;
                            }else {
                                if( !empty($listJSFileRunTime) ){
                                    if( count($listJSFileRunTime)>1 ){
                                        $urlComJS = $this->compressJS($listJSFileRunTime);
                                        $listJSFile[] = array('src'=>$urlComJS, 'type' => ' text/javascript');
                                    }else{
                                        $listJSFile[] = array('src'=>$listJSFileRunTime[0], 'type' => ' text/javascript');
                                    }
                                    $listJSFileRunTime = array();
                                }
                                $listJSFile[] = $iJs;
                            }
                            /*if( empty($iJs['only'])
                                && (strpos( $iJs['src'], FOLDERWEB ) !== false 
                                || strpos( $iJs['src'], 'static.coz.vn' ) !== false 
                                || strpos( $iJs['src'], 'cdn.coz.vn' ) !== false) ){
                                $src = $iJs['src'];
                                $listJSFileRunTime[] = $src;
                                continue;
                            }else {
                                if( !empty($listJSFileRunTime) ){
                                    if( count($listJSFileRunTime)>1 ){
                                        $urlComJS = $this->compressJS($listJSFileRunTime);
                                        $listJSFile[] = array('src'=>$urlComJS, 'type' => ' text/javascript');
                                    }else{
                                        $listJSFile[] = array('src'=>$listJSFileRunTime[0]['src'], 'type' => ' text/javascript');
                                    }
                                    $listJSFileRunTime = array();
                                }
                                $listJSFile[] = $iJs;
                            }*/
                        }

                        if( !empty($listJSFileRunTime) ){
                            if( count($listJSFileRunTime)>1 ){
                                $urlComJS = $this->compressJS($listJSFileRunTime);
                                $listJSFile[] = array('src'=>$urlComJS, 'type' => 'text/javascript');
                            }else{
                                $listJSFile[] = array('src'=>$listJSFileRunTime[0], 'type' => 'text/javascript');
                            }
                            $listJSFileRunTime = array();
                        }
                    }

                    if( !empty($config['minify'])
                        && !empty($config['minify']['css']) ){
                        foreach ($config['minify']['css'] as $kcsm => $csMin) {
                            if( !$this->isUrlHttp($csMin['href']) ){
                                $pathCss = PATH_BASE_ROOT.'/templates/Websites/'.$name_folder.'/'.trim($csMin['href'],'/');
                                unset($pathCss);
                            }
                        }
                    }

                    if( !empty($config['minify'])
                        && !empty($config['minify']['js']) ){
                        foreach ($config['minify']['js'] as $kjsm => $jsMin) {
                            if( !$this->isUrlHttp($jsMin['src']) ){
                                $pathJs = PATH_BASE_ROOT.'/templates/Websites/'.$name_folder.'/'.trim($jsMin['src'],'/');
                                unset($pathJs);
                            }
                        }
                    }
                    $config['isMinify'] = TRUE;
                    $config['minify'] = array('css'=>$listCssFile, 'js' => $listJSFile);
                    $str = "{".$this->getStringFomatJsonFriently($config, "\t", TRUE)."}";
                    $fp = fopen($url_config, 'w+');
                    fwrite($fp, $str);
                    fclose($fp);
                    $result = array('flag' => TRUE, 'msg' => 'Thành công');

                }
            }catch(\Exception $ex){
                print_r($ex->getMessage());die();
                $result = array('flag' => FALSE, 'msg' => $ex->getMessage());
            }
        }
        echo json_encode($result);die();
    }

    public function toggleCompressAction()
    {
        $result = array('flag' => FALSE, 'msg' => '');
        if(!$this->isMasterPage()){
            try{
                $ListFileAsset = array();
                $name_folder = $this->website['websites_folder'];
                $url_config = PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/config.json';
                if(is_file($url_config)){
                    $config = file_get_contents($url_config);
                    $config = json_decode($config, true);
                    if( !empty($config['minify'])
                        && (!empty($config['minify']['css']) || !empty($config['minify']['js'])) ){
                        if( empty($config['isMinify']) ){
                            $config['isMinify'] = TRUE;
                        }else{
                            $config['isMinify'] = FALSE;
                        }
                        $str = "{".$this->getStringFomatJsonFriently($config, "\t", TRUE)."}";
                        $fp = fopen($url_config, 'w+');
                        fwrite($fp, $str);
                        fclose($fp);
                        $result = array('flag' => TRUE, 'msg' => 'Thành công');
                    }
                }
            }catch(\Exception $ex){
                $result = array('flag' => FALSE, 'msg' => $ex->getMessage());
            }
        }
        echo json_encode($result);die();
    }

}