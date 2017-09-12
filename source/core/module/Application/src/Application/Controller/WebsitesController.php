<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\Products;
use Zend\View\Model\ViewModel;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class WebsitesController extends FrontEndController
{
    private $backlist = array('photos.coz.vn', 'cdn.coz.vn', 'static.coz.vn', 'static2.coz.vn');

    protected $fcKeyword = "if (!function_exists('hasPermission')) {
                            function hasPermission(\$module, \$controller,\$action){
                                if(isset(\$_SESSION['CMSMEMBER'])){
                                    if(\$_SESSION['CMSMEMBER']['type'] == 'admin' && \$_SESSION['CMSMEMBER']['website_id'] == ID_MASTERPAGE){
                                        return TRUE;
                                    }
                                    if(isset(\$_SESSION['CMSMEMBER']['permissions'])){
                                        if(isset(\$_SESSION['CMSMEMBER']['permissions'][\$module][\$controller][\$action])){
                                            return TRUE;
                                        }
                                    }
                                }
                                return FALSE;
                            };
                        }
                        if (!function_exists('swapTranslateForAdmin')) {
                            function swapTranslateForAdmin(\$langs){
                              if(hasPermission('cms','language', 'manage-keywords')){
                                \$results = array();
                                foreach (\$langs as \$key => \$lang) {
                                  \$results[\$key] = '<lang class=\"editer-lang\" data-key=\"'.\$key.'\" >'.\$lang.'</lang>';
                                }
                                return \$results;
                              }
                              return \$langs;
                            };
                        }";

    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
    	if($this->domain != MASTERPAGE){
    		return $this->redirect()->toRoute($this->getUrlRouterLang().'index');
    	}
    	die();
    }

    public function validDom($domain) {
	    if(!preg_match("/^([-a-z0-9]{2,100})\.([a-z\.]{2,8})$/i", $domain)) {
	    	return false;
	    }
		return $domain;
	}

	public function filter_var_domain($domain){
	    if(stripos($domain, 'http://') === 0)
	    {
	        $domain = substr($domain, 7); 
	    }

	    if(stripos($domain, 'https://') === 0)
	    {
	        $domain = substr($domain, 8);
	    }
	     
	    if(!substr_count($domain, '.') || substr_count($domain, '//'))
	    {
	        return false;
	    }


	     
	    if(stripos($domain, 'www.') === 0)
	    {
	        $domain = substr($domain, 4); 
	    }
	     
	    $again = 'http://' . $domain;
	    return filter_var ($again, FILTER_VALIDATE_URL);
	}

	public function trimDomain($domain){
		if(stripos($domain, 'http://') === 0)
	    {
	        $domain = substr($domain, 7); 
	    }
	     
	    if(stripos($domain, 'https://') === 0)
	    {
	        $domain = substr($domain, 8);
	    }
	     
	    if(!substr_count($domain, '.') || substr_count($domain, '//'))
	    {
	        return false;
	    }
	     
	    if(stripos($domain, 'www.') === 0)
	    {
	        $domain = substr($domain, 4); 
	    }
	     
	    $url = 'http://' . $domain;
		$parse = parse_url($url);
		return $parse['host'];
	}

    public function createAction()
    {   
    	if($this->domain != MASTERPAGE){
    		echo json_encode(array('flag'=>FALSE, 'msg'=>'Khong ton tai'));
        	die();
    	}

        $request = $this->getRequest ();
        if ($request->isPost ()) {
        	$StoreName = $request->getPost ( 'StoreName', null );
            $BusinessTypeId = $request->getPost ( 'BusinessTypeId', null );
            $FullName = $request->getPost ( 'FullName', null );
            $PhoneNumber = $request->getPost ( 'PhoneNumber', null );
            $Address = $request->getPost ( 'Address', null );
            $Email = $request->getPost ( 'Email', null );
            $Password = $request->getPost ( 'Password', null );
            $pack_id = $request->getPost ( 'pack_id', 1 );
            $template_id = $request->getPost ( 'template_id', 1 );
            if(!empty($StoreName) 
                && !empty($BusinessTypeId)
            	&& !empty($FullName) && !empty($PhoneNumber)
            	&& !empty($Address)  && !empty($Email) && filter_var($Email, FILTER_VALIDATE_EMAIL)
            	&& !empty($Password) && is_numeric($PhoneNumber) ){
		        //$StoreName = $this->trimDomain($StoreName);
            	$sub_domain = trim($this->toAlias($StoreName).'.'.MASTERPAGE);
                if( !in_array( $sub_domain , $this->backlist) ){
                	$website = $this->getModelTable('WebsitesTable')->getWebsite($sub_domain);
                	if( empty($website) ){
                        $template = array();
                        if( !empty($template_id) ){
                            $template = $this->getModelTable('TemplatesTable')->getTemplateById($template_id);
                        }
                        if( empty($template) ){
                            $category = $this->getModelTable('CategoriesTable')->getOneCategoryNoWebsite($BusinessTypeId);
                            if( !empty($category) 
                                && !empty($category->template_id) ){
                                $template_id = $category->template_id;
                            }else{
                                $template_id = 1;
                            }
                        }

                		$row = array('pack_id' => $pack_id,
                                    'template_id' => $template_id,
                					'business_type' => $BusinessTypeId,
                					'store_name' => $StoreName,
                					'alias' => $this->toAlias($StoreName),
                					'email' => $Email,
                					'phone' => $PhoneNumber,
                					'address' => $Address,
                					'full_name' => $FullName,
                					'alias_user' => $this->toAlias($FullName,'.'),
                					'password' => $Password,
                					'sub_domain'=>$sub_domain);
                		$result = $this->getModelTable('WebsitesTable')->createWebsite($row);
                		if($result){
    						echo json_encode(array('flag'=>TRUE, 'msg'=> 'Dang ki thanh cong', 'domain'=>'http://'.$sub_domain, 'cms'=>'http://'.$sub_domain.'/cms', 'row' => $row, 'result' => $result));
    			        	die();
    			        }else{
    			        	echo json_encode(array('flag'=>FALSE, 'msg'=> 'Co loi xay ra, ban vui long thu lai'));
    		        		die();
    			        }
    				}else{
    					echo json_encode(array('flag'=>FALSE, 'msg'=> 'Website da co nguoi dang ki'));
    		        	die();
    				}
                }else{
                    echo json_encode(array('flag'=>FALSE, 'msg'=> 'Domain đã tồn tại trên hệ thống'));
                    die();
                }
			}else{
				echo json_encode(array('flag'=>FALSE, 'msg'=> 'Nhap chua dung'));
		        die();
			}
		}
        echo json_encode(array('flag'=>FALSE, 'msg'=>'Khong ton tai'));
        die();
    }

    public function demoAction()
    {
        $translator = $this->getServiceLocator()->get('translator'); 
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle($this->website['seo_title']);
        $renderer->headMeta()->appendName('description', $this->website['seo_keywords']);
        $renderer->headMeta()->appendName('keywords', $this->website['seo_description']);
        
        if($this->domain != MASTERPAGE){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'index');
        }
        $alias = $this->params()->fromRoute('alias', '');
        if(!empty($alias)){
            $website_demo = $this->getModelTable('WebsitesTable')->getWebsiteDemoByAliasCoz($alias);

            if( empty($website_demo) ){
                return $this->redirect()->toRoute($this->getUrlRouterLang().'error');
            }else{
                $this->data_view['domain_demo'] = $alias;
                $this->data_view['url'] = 'https://'.$website_demo['website_domain'];
                $this->data_view['website_demo'] = $website_demo;

                $this->has_header = false;
                $this->has_footer = false;
                $this->data_view['has_header'] = $this->has_header;
                $this->data_view['has_footer'] = $this->has_footer;
                return $this->data_view;
            }
        }
        return $this->redirect()->toRoute($this->getUrlRouterLang().'index');
    }

    public function syncDataAction()
    {
        if($this->domain != MASTERPAGE){
            return $this->redirect()->toRoute($this->getUrlRouterLang().'index');
        }
        $website_id = 184;
        $result = $this->getModelTable('WebsitesTable')->fixUrlImageForWebsite($website_id);
        die('create 3');
        //$helper = $this->getServiceLocator()->get('viewhelpermanager')->get('Images');
        //echo $helper->getUrlImage('/custom/domain_1/cd3ae6cac1e3d9506c03fbfe7bce30fe/mau durocolour moi (2).jpg', 181, 181);
        die();
        /*$cats = $this->getModelTable('CategoriesArticlesTable')->fetchAll();
        foreach ($cats as $key => $cat) {
            $lan = $this->getModelTable('CategoriesArticlesLanguagesTable')->getRow($cat['categories_articles_id'], 3);
            $row = array('categories_articles_id' => $cat['categories_articles_id'],
                                'languages_id' => 3,
                                'categories_articles_title' => $cat['categories_articles_title'],
                                'categories_articles_alias' => $cat['categories_articles_alias']);
            if(empty($lan)){
                $last_ = $this->getModelTable('CategoriesArticlesLanguagesTable')->insert($row);
            }else{
                $this->getModelTable('CategoriesArticlesLanguagesTable')->updateCategory($row, array('categories_articles_id' => $cat['categories_articles_id'],
                                'languages_id' => 3));
            }
        }
        echo count($cats);
        $articles = $this->getModelTable('ArticlesTable')->fetchAll();
        foreach ($articles as $key => $article) {
            $lan = $this->getModelTable('ArticlesLanguagesTable')->getRow($article['articles_id'], 3);
            $row = array('languages_id' => 3,
                                 'articles_id' => $article['articles_id'],
                                 'articles_title' => (!empty($article['articles_title']) ? $article['articles_title'] : '' ),
                                 'articles_alias' => (!empty($article['articles_alias']) ? $article['articles_alias'] : '' ),
                                 'articles_sub_content' => (!empty($article['articles_sub_content']) ? $article['articles_sub_content'] : '' ),
                                 'articles_content' => (!empty($article['articles_content']) ? $article['articles_content'] : '' ));
            if(empty($lan)){
                $last_ = $this->getModelTable('ArticlesLanguagesTable')->insert($row);
            }else{
                $this->getModelTable('ArticlesLanguagesTable')->updateArticles($row, array('languages_id' => 3,
                                 'articles_id' => $article['articles_id']));
            }
        }
        echo count($articles);*/
        /*$from = 214;
        $to = 266;
        $result = $this->getModelTable('WebsitesTable')->syncDataWebsite($from, $to, true, true, true,
                                                                        true, true, true,true, true, true, true, true);*/
        /*$from = 266;
        $result = $this->getModelTable('WebsitesTable')->fixUrlImageForWebsite($from);
        die('create 1');*/
    }

    public function testAction()
    {
        die('');
    }

    public function updateTempleteAction()
    {   
    	if($this->domain != MASTERPAGE){
    		echo json_encode(array('flag'=>FALSE, 'msg'=>'Không tồn tại'));
        	die();
    	}

        $request = $this->getRequest ();
        if ($request->isPost ()) {
        	$template_id = $request->getPost ( 'template_id', null );
        	$domain = $request->getPost ( 'domain', null );
            $email = $request->getPost ( 'email', null );
            $password = $request->getPost ( 'password', null );
            $domain = $this->trimDomain($domain);
            if(!empty($domain) && !empty($template_id) && !empty($password)
            	&& !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)){

            	$website = $this->getModelTable('WebsitesTable')->getWebsite($domain);
            	$template = $this->getModelTable('TemplatesTable')->getTemplateById($template_id);
            	if(!empty($website) && !empty($template)){
                    if($website['template_id'] != $template['template_id']){
                        $user = $this->getModelTable('UserTable')->loginWithWebsite($email, $password, $website['website_id']);
                        if(!empty($user) && $user['type'] == 'admin'){
                            if($user['is_administrator'] == 1){
                                $group_permissions = $this->getModelTable('PermissionsTable')->getGroupPermissionsWebsiteForAdmin($website['website_id']);
                            }else{
                                $group_permissions = $this->getModelTable('PermissionsTable')->getGroupPermissionsWebsiteForUser($user['groups_id'], $website['website_id']);
                            }
                            $data_permissions = array();
                            foreach($group_permissions as $permit){
                                $data_permissions[$permit['module']][$permit['controller']][$permit['action']] = 1;
                            }
                            if(!empty($data_permissions['cms']['themes']['buy'])){
                                /*if($website['website_id'] == 195){
                                    $Default= PATH_BASE_ROOT . '/templates/Sources/'.$template['template_folder'];
                                    $name_folder = $website['websites_folder'];
                                    $view_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/view';
                                    //xoa source cu di
                                    exec('rm -rf '.escapeshellarg($view_new));
                                }
                                echo json_encode(array('flag'=>FALSE, 'msg'=> 'Website dang su dung giao dien nay','view_new'=>$view_new));
                                die();*/
                                $Default= PATH_BASE_ROOT . '/templates/Sources/'.$template['template_folder'];
                                $name_folder = $website['websites_folder'];
                                
                                //copy view
                                $view_source = $Default.'/view';
                                $view_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/view';
                                
                                //xoa source cu di
                                exec('rm -rf '.escapeshellarg($view_new));
                                //chep source sang
                                exec("cp -r $view_source $view_new");

                                //copy style
                                $styles_source = $Default.'/styles';
                                $styles_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/styles';
                                //xoa style cu di
                                exec('rm -rf '.escapeshellarg($styles_new));
                                //chep style sang
                                exec("cp -r $styles_source $styles_new");


                                if(!$this->isMasterPage()){
                                    $languages_ = $this->getModelTable('LanguagesTable')->fetchAll('','', 0, 100);
                                    foreach($languages_ as $lang){
                                        if(is_file(PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php')){
                                            require_once PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file'].'.php';
                                            $array_lang = $$lang['languages_file'];
                                            if(is_file($Default.'/lang/'.$lang['languages_file'].'.php')){
                                                require_once $Default.'/lang/'.$lang['languages_file'].'.php';
                                                $langs_compare = $$lang['languages_file'];
                                                foreach ($langs_compare as $key => $value) {
                                                    if(!isset($array_lang[$key])){
                                                        $array_lang[$key] = $value;
                                                    }
                                                }
                                            }
                                            $path=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/lang/'.$lang['languages_file']. '.php';
                                            $data = "<?php\r\n {$this->fcKeyword} \r\n \${$lang['languages_file']} = ". var_export($array_lang,true). "; \r\n return swapTranslateForAdmin(\${$lang['languages_file']});";
                                            @file_put_contents($path, $data);
                                        }
                                    }

                                    $config_source = $Default.'/config.json';
                                    $url_config = PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/config.json';
                                    //modify css, js
                                    if(is_file($config_source)){
                                        if(is_file($url_config)){
                                            try{
                                                $config_s = file_get_contents($config_source);
                                                $config_s = json_decode($config_s, true);
                                                $config = file_get_contents($url_config);
                                                $config = json_decode($config, true);
                                                $css = array();
                                                $js = array();
                                                if(!empty($config_s['css'])){
                                                    $css = $config_s['css'];
                                                }
                                                if(!empty($config_s['js'])){
                                                    $js = $config_s['js'];
                                                }
                                                $config['css'] = $css;
                                                $config['js'] = $js;

                                                @file_put_contents($url_config, json_encode($config));

                                            }catch(\Exception $ex){
                                                echo json_encode(array('flag'=>FALSE, 'msg'=> 'có lỗi xảy ra, vui lòng thử lại'));
                                                die();
                                            }
                                        }else{
                                            $config_new=PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/config.json';
                                            //chep config sang
                                            exec("cp -r $config_source $config_new");
                                        }
                                    }
                                }

                                $templete_buy = trim($website['templete_buy'], ',');
                                $list_templete_by = explode(',',$templete_buy);
                                if(!in_array($template_id, $list_templete_by)){
                                    $list_templete_by[] = $template_id;
                                }
                                $data = array('template_id'=>$template_id, 'templete_buy' => implode(',',$list_templete_by));
                                $this->getModelTable('WebsitesTable')->updateWebsite($data, $website);
                                echo json_encode(array(
                                    'flag' => TRUE,
                                    'website' => $website,
                                    'template' => $template,
                                    'user' => $user,
                                    'permissions' => $data_permissions,
                                    'domain' => $domain,
                                ));
                                die();
                            }

                        }
                    }else{
                        echo json_encode(array('flag'=>FALSE, 'msg'=> 'Website đang sử dụng giao diện này'));
                        die();
                    }
		        	echo json_encode(array('flag'=>FALSE, 'msg'=> 'có lỗi xảy ra, vui lòng thử lại'));
	        		die();
				}else{
					echo json_encode(array('flag'=>FALSE, 'msg'=> 'Website hoac theme không tồn tại'));
		        	die();
				}
            }else{
				echo json_encode(array('flag'=>FALSE, 'msg'=> 'Nhập chưa đúng'));
		        die();
			}
        }
        echo json_encode(array('flag'=>FALSE, 'msg'=>'Không tồn tại'));
        die();
    }

	
}

