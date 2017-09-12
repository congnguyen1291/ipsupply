<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

use Application\Model\AppTable;
use Application\Model\TraGop;
use Application\Model\TraGopTable;
use Application\Model\Banks;
use Application\Model\BanksTable;
use Application\Model\EmailNewLetter;
use Application\Model\EmailNewLetterTable;
use Application\Model\RegisterMailProduct;
use Application\Model\RegisterMailProductTable;
use Application\Model\Fqa;
use Application\Model\FqaTable;
use Application\Model\Comments;
use Application\Model\CommentsTable;
use Application\Model\Invoice;
use Application\Model\InvoiceTable;
use Application\Model\User;
use Application\Model\UserTable;
use Application\Model\Cities;
use Application\Model\CitiesTable;
use Application\Model\Districts;
use Application\Model\DistrictsTable;
use Application\Model\Wards;
use Application\Model\WardsTable;
use Application\Model\Contact;
use Application\Model\ContactTable;
use Zend\Cache\Storage\Adapter\Memcached;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Resolver\TemplateMapResolver;
use Zend\View\Resolver\TemplatePathStack;
use Application\Model\Categories;
use Application\Model\CategoriesTable;
use Application\Model\Feature;
use Application\Model\FeatureTable;
use Application\Model\Articles;
use Application\Model\ArticlesTable;
use Application\Model\CategoriesArticles;
use Application\Model\CategoriesArticlesTable;
use Application\Model\Products;
use Application\Model\ProductsTable;
use Application\Model\Manufacturers;
use Application\Model\ManufacturersTable;
use Application\Model\Banners;
use Application\Model\BannersTable;
use Application\Model\BannerPosition;
use Application\Model\BannerPositionTable;
use Application\Model\BannerSize;
use Application\Model\BannerSizeTable;
use Application\Model\Keywords;
use Application\Model\KeywordsTable;
use Application\Model\Websites;
use Application\Model\WebsitesTable;
use Application\Model\Menus;
use Application\Model\MenusTable;
use Application\Model\Modules;
use Application\Model\ModulesTable;
use Application\Model\Country;
use Application\Model\CountryTable;
use Application\Model\Payment;
use Application\Model\PaymentTable;
use Application\Model\Transportation;
use Application\Model\TransportationTable;
use Application\Model\Domain;
use Application\Model\DomainTable;
use Application\Model\Pack;
use Application\Model\PackTable;
use Application\Model\Permissions;
use Application\Model\PermissionsTable;
use Application\Model\Templates;
use Application\Model\TemplatesTable;
use Application\Model\Languages;
use Application\Model\LanguagesTable;
use Application\Model\Branches;
use Application\Model\BranchesTable;
use Application\Model\Wholesale;
use Application\Model\WholesaleTable;
use Application\Model\WholesaleProducts;
use Application\Model\WholesaleProductsTable;
use Application\Model\ArticlesLanguages;
use Application\Model\ArticlesLanguagesTable;
use Application\Model\CategoriesArticlesLanguages;
use Application\Model\CategoriesArticlesLanguagesTable;
use Application\Model\CategoryTemplates;
use Application\Model\CategoryTemplatesTable;
use Application\Model\Tags;
use Application\Model\TagsTable;
use Application\Model\Coupons;
use Application\Model\CouponsTable;
use Application\Model\Shipping;
use Application\Model\ShippingTable;
use Application\Model\ShippingWard;
use Application\Model\ShippingWardTable;
use Application\Model\Google;
use Application\Model\GoogleTable;
use Application\Model\Facebook;
use Application\Model\FacebookTable;
use Application\Model\AppFacebook;
use Application\Model\AppFacebookTable;
use Application\Model\FBComment;
use Application\Model\FBCommentTable;
use Application\Model\FBFeed;
use Application\Model\FBFeedTable;
use Application\Model\FBUser;
use Application\Model\FBUserTable;
use Application\Model\Assign;
use Application\Model\AssignTable;
use Application\Model\AssignUser;
use Application\Model\AssignUserTable;
use Application\Model\Commission;
use Application\Model\CommissionTable;
use Application\Model\Traffic;
use Application\Model\TrafficTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Session\Config\SessionConfig;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\EventManager\EventInterface;
use Zend\I18n\Translator\Translator;
use Openid;
use Zend\Mvc\Application;

use zz\Html\HTMLMinify;
use Ubench\Ubench;

class Module {

	protected function getModelTable($name, $sm) {
        if (! isset ( $this->{$name} )) {
            $this->{$name} = NULL;
        }
        if (! $this->{$name}) {
            $this->{$name} = $sm->get ( 'Application\Model\\' . $name );
        }
        return $this->{$name};
    }

	public function onBootstrap(MvcEvent $e) {
		$eventManager = $e->getApplication ()->getEventManager ();
		
		$eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR,function(MvcEvent $e) {

			$response = $e->getResponse(); 
		    $response->setStatusCode(302); 
		    $response->getHeaders()->addHeaderLine(
                                    'Location',
                                    $e->getRouter()->assemble(
                                            array(),
                                            array('name' => 'error')
                                    )
                            ); 
		      
		    $e->stopPropagation();
		},100);

		$e->getApplication()->getEventManager()->attach(
                \Zend\Mvc\MvcEvent::EVENT_ROUTE,
                function($e){
                    $current_controller = $e->getRouteMatch()->getParam('controller');
                    $str_controller = explode('\\', $current_controller);
                    if(count($str_controller) == 3){
                        $str_module = strtolower($str_controller[0]);
                        $str_controller = strtolower($str_controller[2]);
                        $str_action = strtolower($e->getRouteMatch()->getParam('action'));

                        if($str_module == 'cms'
                            && ( $str_controller != 'login' || $str_action != 'index' ) 
                            && empty($_SESSION['CMSMEMBER'])){
                            unset($_SESSION['CMSMEMBER']);
                            $response = $e->getResponse();
                            $response->getHeaders()->addHeaderLine(
                                    'Location',
                                    $e->getRouter()->assemble(
                                            array(),
                                            array('name' => 'cms/login')
                                    )
                            );
                            $response->setStatusCode(302);
                            return $response;
                        }
                    }else{
                        $response = $e->getResponse();
                        $response->getHeaders()->addHeaderLine(
                                'Location',
                                $e->getRouter()->assemble(
                                        array(),
                                        array('name' => 'home')
                                )
                        );
                        $response->setStatusCode(302);
                        return $response;
                    }
                    return;
                },
        -100);

		//$eventManager = $e->getApplication ()->getEventManager ();
		$e->getApplication ()->getEventManager ()->getSharedManager ()->attach ( __NAMESPACE__, 'dispatch', function ($e) {
			$sm = $e->getParam('application')->getServiceManager();

			$baseUrl = $_SERVER['HTTP_HOST'];
	        if (substr($baseUrl, 0, 4) == "www.")
	            $baseUrl = substr($baseUrl, 4);
	        if(!empty($baseUrl) && $baseUrl != MASTERPAGE){
	            $website = $this->getModelTable('WebsitesTable',$sm)->getWebsite($baseUrl);
	            if(!empty($website)){
	            	$websites_dir = $website['websites_dir'];
	            	$websites_folder = $website['websites_folder'];
	            	$url_website = trim($websites_dir, '/').'/'.trim($websites_folder, '/');
	            	$url_website = getUrlFolderWebsite($website);
	            	$viewResolverMap = $sm->get('ViewTemplateMapResolver');
			        $viewResolverMap->setMap(array(
			            'app/layout' => PATH_BASE_ROOT .'/'.$url_website. '/view/app/layout.phtml',
			            'application/index/index' => PATH_BASE_ROOT .'/'.$url_website. '/view/application/index/index.phtml',
			            'error/404' => PATH_BASE_ROOT .'/'.$url_website. '/view/error/404.phtml',
			            'error/index' => PATH_BASE_ROOT  .'/'.$url_website. '/view/error/index.phtml',
			        ));

			        
			        $viewResolverPathStack = $sm->get('ViewTemplatePathStack');
			        $viewResolverPathStack->setPaths(array(PATH_BASE_ROOT .'/'.$url_website. '/view'));
	            }
	        }

	        $e->getTarget ()->layout ( 'app/layout' );

		}, 10);

		//$eventManager->getSharedManager()->attach('Zend\Mvc\Application', 'finish', array($this, 'compressHtml'), 1002);
		$moduleRouteListener = new ModuleRouteListener ();
		$moduleRouteListener->attach ( $eventManager );
		$translator = $e->getApplication ()->getServiceManager ()->get ( 'translator' );
		if (isset ($_SESSION ['language'] ['code'] )) {
			$translator->setFallbackLocale ( $_SESSION ['language'] ['code'] );
		} else {
			$translator->setFallbackLocale ( 'vi_VN' );
			$_SESSION ['language'] ['code'] = 'vi_VN';
		}
		
		$eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_ROUTE, function($e) {
			$route = $e->getRouteMatch();
		
			$param = $route->getParam('param', null);
			$params = array();
			if (!is_null($param)) {
				$paramsItems = explode('/', $param);
				if (count ($paramsItems)) {
					while($paramKey = current($paramsItems)) {
						$paramValue = next($paramsItems);
						if (isset($paramValue)) {
							$route->setParam($paramKey, $paramValue);
							$params[$paramKey] = $paramValue;
						}
						next($paramsItems);
					}
				}
			}
		
			define('URL_PARAM', serialize($params));
		});
		
		$localeValue = "vi";
		$translator = new Translator();
		$translator->addTranslationFile("ini", ROOT_DIR . "/translates/$localeValue.ini", "", 'vi');

	}

	private function compress($html) 
    {
        $HTMLMinify = new HTMLMinify($html, array('optimizationLevel'=>1));
		$minify = $HTMLMinify->process(); 
        return $minify; 
    }

    public function compressHtml(MvcEvent $e) 
    {
        $response = $e->getResponse(); 
        $response->setContent($response->getContent());
    }


	public function initializeSession($em) {
		$config = $em->getApplication ()->getServiceManager ()->get ( 'Config' );
		
		$sessionConfig = new SessionConfig ();
		$sessionConfig->setOptions ( $config ['session'] );
		
		$sessionManager = new SessionManager ( $sessionConfig );
		$sessionManager->start ();
		
		Container::setDefaultManager ( $sessionManager ); // au cas ou on utilise plusieurs SessionManagers
	}
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	public function getAutoloaderConfig() {
		return array (
				'Zend\Loader\ClassMapAutoloader' => array (
						__DIR__ . '/autoload_classmap.php' 
				),
				'Zend\Loader\StandardAutoloader' => array (
						'namespaces' => array (
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
								'GT' => __DIR__ . '/../../vendor/GT/library/GT',
								'Google' => __DIR__ . '/../../vendor/Openid/Google',
								'ApacheSolrphp' => __DIR__ . '/../../vendor/solrphp/SolrPHPClient/Apache/Solr',
								'PHPImageWorkshop' => __DIR__ . '/../../vendor/PHPImageWorkshop',
								'MatthiasMullie\Minify' => __DIR__ . '/../../vendor/MatthiasMullie/src',
								'MatthiasMullie\PathConverter' => __DIR__ . '/../../vendor/MatthiasMullie/PathConverter',
								'Screen' => __DIR__ . '/../../vendor/Screen/src',
								'PHPImageCache' => __DIR__ . '/../../vendor/PHPImageCache',
								'PHPImageIco' => __DIR__ . '/../../vendor/PHPImageIco',
								'zz' => __DIR__ . '/../../vendor/html-minifier/src/zz',
								'PHPSitemap' => __DIR__ . '/../../vendor/PHPSitemap',
								'JasonGrimes' => __DIR__ . '/../../vendor/PHPPaginator/src/JasonGrimes',
								'PHPVnpay' => __DIR__ . '/../../vendor/PHPVnpay',
								'PHPOnepay' => __DIR__ . '/../../vendor/PHPOnepay',
								'PHPPaypal' => __DIR__ . '/../../vendor/PHPPaypal',
								'Facebook' => __DIR__ . '/../../vendor/PHPFacebook/src/Facebook',
								'PHPShipchung' => __DIR__ . '/../../vendor/PHPShipchung',
								'Schema' => __DIR__ . '/../../vendor/PHPSchema',
								'Stringy' => __DIR__ . '/../../vendor/Stringy/src',
								'SliceableStringy' => __DIR__ . '/../../vendor/SliceableStringy/src',
								'SubStringy' => __DIR__ . '/../../vendor/SubStringy/src',
								'Truncate' => __DIR__ . '/../../vendor/Truncate/src',
								'Assetic' => __DIR__ . '/../../vendor/Assetic/src/Assetic',
								'Respect' => __DIR__ . '/../../vendor/Validation/library',
								'Validation' => __DIR__ . '/../../vendor/Validation/library',
								'Ubench' => __DIR__ . '/../../vendor/ubench/src',
								'Location' => __DIR__ . '/../../vendor/phpgeo/src/Location',
								'Goutte' => __DIR__ . '/../../vendor/Goutte/Goutte',
								'Gaufrette' => __DIR__ . '/../../vendor/Gaufrette/src/Gaufrette',
								'MischiefCollective' => __DIR__ . '/../../vendor/ColorJizz/src/MischiefCollective',
								'Carbon' => __DIR__ . '/../../vendor/Carbon/src/Carbon',
								'Buzz' => __DIR__ . '/../../vendor/Buzz/lib/Buzz',
						) 
				)
				 
		);
	}
	public function getViewHelperConfig() {
		return array (
				'factories' => array (
						'Common' => function ($sm) {
							$helper = new View\Helper\Common ();
							return $helper;
						},
						'GenerateUrl' => function ($sm) {
							$helper = new View\Helper\GenerateUrl ();
							return $helper;
						},
						'Datas' => function ($sm) {
							$helper = new View\Helper\Datas($sm);
							return $helper;
						},
						'Products' => function ($sm) {
							$helper = new View\Helper\Products($sm);
							return $helper;
						},
						'Menus' => function ($sm) {
							$helper = new View\Helper\Menus($sm);
							return $helper;
						},
						'Keywords' => function ($sm) {
							$helper = new View\Helper\Keywords($sm);
							return $helper;
						},
						'Categories' => function ($sm) {
							$helper = new View\Helper\Categories($sm);
							return $helper;
						},
						'Manufacturers' => function ($sm) {
							$helper = new View\Helper\Manufacturers($sm);
							return $helper;
						},
						'CategoriesArticles' => function ($sm) {
							$helper = new View\Helper\CategoriesArticles($sm);
							return $helper;
						},
						'Articles' => function ($sm) {
							$helper = new View\Helper\Articles($sm);
							return $helper;
						},
						'Payments' => function ($sm) {
							$helper = new View\Helper\Payments($sm);
							return $helper;
						},
						'User' => function ($sm) {
							$helper = new View\Helper\User($sm);
							return $helper;
						},
						'Country' => function ($sm) {
							$helper = new View\Helper\Country($sm);
							return $helper;
						},
						'Banners' => function ($sm) {
							$helper = new View\Helper\Banners($sm);
							return $helper;
						},
						'Features' => function ($sm) {
							$helper = new View\Helper\Features($sm);
							return $helper;
						},
						'Branches' => function ($sm) {
							$helper = new View\Helper\Branches($sm);
							return $helper;
						},
						'Currency' => function ($sm) {
							$helper = new View\Helper\Currency($sm);
							return $helper;
						},
						'Images' => function ($sm) {
							$helper = new View\Helper\Images($sm);
							return $helper;
						},
						'Websites' => function ($sm) {
							$helper = new View\Helper\Websites($sm);
							return $helper;
						},
						'Banks' => function ($sm) {
							$helper = new View\Helper\Banks($sm);
							return $helper;
						}, 
						'Coupons' => function ($sm) {
							$helper = new View\Helper\Coupons($sm);
							return $helper;
						}, 
						'Extension' => function ($sm) {
							$helper = new View\Helper\Extension($sm);
							return $helper;
						},
						'GoldTimer' => function ($sm) {
							$helper = new View\Helper\GoldTimer($sm);
							return $helper;
						},
						'Invoice' => function ($sm) {
							$helper = new View\Helper\Invoice($sm);
							return $helper;
						}, 
						'Cities' => function ($sm) {
							$helper = new View\Helper\Cities($sm);
							return $helper;
						},
						'Districts' => function ($sm) {
							$helper = new View\Helper\Districts($sm);
							return $helper;
						},
						'Wards' => function ($sm) {
							$helper = new View\Helper\Wards($sm);
							return $helper;
						},
						'Contries' => function ($sm) {
							$helper = new View\Helper\Contries($sm);
							return $helper;
						},
						'Transportations' => function ($sm) {
							$helper = new View\Helper\Transportations($sm);
							return $helper;
						},
						'Html' => function ($sm) {
							$helper = new View\Helper\Html($sm);
							return $helper;
						},
						'JS' => function ($sm) {
							$helper = new View\Helper\JS($sm);
							return $helper;
						},
						'CSS' => function ($sm) {
							$helper = new View\Helper\CSS($sm);
							return $helper;
						},
						'Cart' => function ($sm) {
							$helper = new View\Helper\Cart($sm);
							return $helper;
						},
						'Params' => function ($sm) {
							$helper = new View\Helper\Params($sm);
							return $helper;
						},
						'Tags' => function ($sm) {
							$helper = new View\Helper\Tags($sm);
							return $helper;
						},
						'Link' => function ($sm) {
							$helper = new View\Helper\Link($sm);
							return $helper;
						},
						'Shipping' => function ($sm) {
							$helper = new View\Helper\Shipping($sm);
							return $helper;
						},
						'SEO' => function ($sm) {
							$helper = new View\Helper\SEO($sm);
							return $helper;
						},
						'Neo' => function ($sm) {
							$helper = new View\Helper\Neo($sm);
							return $helper;
						}, 
				) 
		);
	}
	
	// Add this method:
	public function getServiceConfig() {
		return array (
                'aliases' => array(
                    'cache' => 'ZendCacheStorageFactory',
                    'filecache' => 'ZendMemCacheStorageFactory',
                ),
				'factories' => array (

                        'ZendCacheStorageFactory' => function() {
                            return \Zend\Cache\StorageFactory::factory(
                                array(
                                    'adapter' => array(
                                        'name' => 'filesystem',
                                        'lifetime' => 7200,
                                        'options' => array(
                                            'dirLevel' => 2,
                                            'cacheDir' => 'data/cache',
                                            'dirPermission' => 0755,
                                            'filePermission' => 0666,
                                            'namespaceSeparator' => '-db-',
                                            'ttl' => 300,
                                        ),
                                    ),
                                    'plugins' => array('serializer'),
                                )
                            );
                        },

                        'ZendMemCacheStorageFactory' => function() {
							/*
                            return \Zend\Cache\StorageFactory::factory(
                                array(
                                    'adapter' => array(
                                        'name'     =>'memcached',
                                        'lifetime' => 7200,
                                        'options'  => array(
                                            'servers'   => array(
                                                array(
                                                    'localhost',11211
                                                )
                                            ),
                                            'namespace'  => __NAMESPACE__,
                                            'liboptions' => array (
                                                'COMPRESSION' => true,
                                                'binary_protocol' => true,
                                                'no_block' => true,
                                                'connect_timeout' => 100
                                            ),
                                            'ttl' => 300,
                                        )
                                    ),
                                    'plugins' => array(
                                        'exception_handler' => array(
                                            'throw_exceptions' => false
                                        ),
                                    ),
                                )
                            );*/
                            
                            $MemcachedResourceManager = new \Zend\Cache\Storage\Adapter\MemcachedResourceManager();
                            $MemcachedResourceManager->addServer('1', array('localhost', 11211));
                            $memcachedAdapterOptions = new \Zend\Cache\Storage\Adapter\MemcachedOptions(array(
                                'resource_manager' => $MemcachedResourceManager,
                                'resource_id'      => '1',
                                'namespace'        => __NAMESPACE__,
                                'ttl'              => 10,
                            ));
                            $memcache = new Memcached($memcachedAdapterOptions);
                            return $memcache;
                        },
						'Application\Model\CategoriesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'CategoriesTableGateway' );
							$table = new CategoriesTable ( $tableGateway );
							return $table;
						},
						'CategoriesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Categories () );
							return new TableGateway ( 'categories', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\FeatureTable' => function ($sm) {
							$tableGateway = $sm->get ( 'FeatureTableGateway' );
							$table = new FeatureTable ( $tableGateway );
							return $table;
						},
						'FeatureTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Feature () );
							return new TableGateway ( 'feature', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\ProductsTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ProductsTableGateway' );
							$table = new ProductsTable ( $tableGateway );
							return $table;
						},
						'ProductsTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Products () );
							return new TableGateway ( 'products', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\ManufacturersTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ManufacturersTableGateway' );
							$table = new ManufacturersTable ( $tableGateway );
							return $table;
						},
						'ManufacturersTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Manufacturers() );
							return new TableGateway ( 'manufacturers', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\UserTable' => function ($sm) {
							$tableGateway = $sm->get ( 'UserTableGateway' );
							$table = new UserTable ( $tableGateway );
							return $table;
						},
						'UserTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new User () );
							return new TableGateway ( 'users', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\BannersTable' =>  function($sm) {
							$tableGateway = $sm->get('BannersTableGateway');
							$table = new BannersTable($tableGateway);
							return $table;
						},
						'BannersTableGateway' => function ($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new Banners());
							return new TableGateway('banners', $dbAdapter, null, $resultSetPrototype);
						},
						'Application\Model\BannerPositionTable' =>  function($sm) {
							$tableGateway = $sm->get('BannerPositionTableGateway');
							$table = new BannerPositionTable($tableGateway);
							return $table;
						},
						'BannerPositionTableGateway' => function ($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new BannerPosition());
							return new TableGateway('position_id', $dbAdapter, null, $resultSetPrototype);
						},
						'Application\Model\BannerSizeTable' =>  function($sm) {
							$tableGateway = $sm->get('BannerSizeTableGateway');
							$table = new BannerSizeTable($tableGateway);
							return $table;
						},
						'BannerSizeTableGateway' => function ($sm) {
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$resultSetPrototype = new ResultSet();
							$resultSetPrototype->setArrayObjectPrototype(new BannerSize());
							return new TableGateway('banners_size', $dbAdapter, null, $resultSetPrototype);
						},
						
						/*
							'Application\Model\InvoiceTable' =>  function($sm) {
								$tableGateway = $sm->get('InvoiceTableGateway');
								$table = new InvoiceTable($tableGateway);
								return $table;
							},
							'InvoiceTableGateway' => function ($sm) {
								$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
								$resultSetPrototype = new ResultSet();
								$resultSetPrototype->setArrayObjectPrototype(new Invoice());
								return new TableGateway('invoice', $dbAdapter, null, $resultSetPrototype);
							},*/
						'Application\Model\ArticlesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ArticlesTableGateway' );
							$table = new ArticlesTable ( $tableGateway );
							return $table;
						},
						'ArticlesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Articles () );
							return new TableGateway ( 'articles', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\CitiesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'CitiesTableGateway' );
							$table = new CitiesTable ( $tableGateway );
							return $table;
						},
						'CitiesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Cities () );
							return new TableGateway ( 'cities', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\DistrictsTable' => function ($sm) {
							$tableGateway = $sm->get ( 'DistrictsTableGateway' );
							$table = new DistrictsTable ( $tableGateway );
							return $table;
						},
						'DistrictsTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Districts () );
							return new TableGateway ( 'districts', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\WardsTable' => function ($sm) {
							$tableGateway = $sm->get ( 'WardsTableGateway' );
							$table = new WardsTable ( $tableGateway );
							return $table;
						},
						'WardsTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Wards () );
							return new TableGateway ( 'wards', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\ContactTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ContactTableGateway' );
							$table = new ContactTable ( $tableGateway );
							return $table;
						},
						'ContactTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Contact () );
							return new TableGateway ( 'contact', $dbAdapter, null, $resultSetPrototype );
						},

						/*'Application\Model\DistrictsTable' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$table = new Model\DistrictsTable ( $dbAdapter );
							return $table;
						},
						'Application\Model\WardsTable' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$table = new Model\WardsTable ( $dbAdapter );
							return $table;
						},
						'Application\Model\ContactTable' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$table = new Model\ContactTable ( $dbAdapter );
							return $table;
						},*/

						'Application\Model\InvoiceTable' => function ($sm) {
							$tableGateway = $sm->get ( 'InvoiceTableGateway' );
							$table = new InvoiceTable ( $tableGateway );
							return $table;
						},
						'InvoiceTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Invoice () );
							return new TableGateway ( 'invoice', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\CommentsTable' => function ($sm) {
							$tableGateway = $sm->get ( 'CommentsTableGateway' );
							$table = new CommentsTable ( $tableGateway );
							return $table;
						},
						'CommentsTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Comments () );
							return new TableGateway ( 'comments', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\FqaTable' => function ($sm) {
							$tableGateway = $sm->get ( 'FqaTableGateway' );
							$table = new FqaTable ( $tableGateway );
							return $table;
						},
						'FqaTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Fqa () );
							return new TableGateway ( 'fqa', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\RegisterMailProductTable' => function ($sm) {
							$tableGateway = $sm->get ( 'RegisterMailProductTableGateway' );
							$table = new RegisterMailProductTable ( $tableGateway );
							return $table;
						},
						'RegisterMailProductTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new RegisterMailProduct() );
							return new TableGateway ( 'product_register_mail', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\EmailNewLetterTable' => function ($sm) {
							$tableGateway = $sm->get ( 'EmailNewLetterTableGateway' );
							$table = new EmailNewLetterTable ( $tableGateway );
							return $table;
						},
						'EmailNewLetterTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new EmailNewLetter() );
							return new TableGateway ( 'email_new_letter', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\CategoriesArticlesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'CategoriesArticlesTableGateway' );
							$table = new CategoriesArticlesTable ( $tableGateway );
							return $table;
						},
						'CategoriesArticlesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new CategoriesArticles () );
							return new TableGateway ( 'categories_articles', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\BranchesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'BranchesTableGateway' );
							$table = new BranchesTable ( $tableGateway );
							return $table;
						},
						'BranchesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Branches () );
							return new TableGateway ( 'branches', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\PaymentTable' => function ($sm) {
							$tableGateway = $sm->get ( 'PaymentTableGateway' );
							$table = new PaymentTable ( $tableGateway );
							return $table;
						},
						'PaymentTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Payment () );
							return new TableGateway ( 'payment_method', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\TransportationTable' => function ($sm) {
							$tableGateway = $sm->get ( 'TransportationTableGateway' );
							$table = new TransportationTable ( $tableGateway );
							return $table;
						},
						'TransportationTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Transportation () );
							return new TableGateway ( 'transportation', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\BanksTable' => function ($sm) {
								$tableGateway = $sm->get('BanksTableGateway');
								$table = new BanksTable($tableGateway);
								return $table;
							},
						'BanksTableGateway' => function ($sm) {
								$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
								$resultSetPrototype = new ResultSet();
								$resultSetPrototype->setArrayObjectPrototype(new Banks());
								return new TableGateway('banks', $dbAdapter, null, $resultSetPrototype);
							},
						'Application\Model\TraGopTable' => function ($sm) {
								$tableGateway = $sm->get('TraGopTableGateway');
								$table = new TraGopTable($tableGateway);
								return $table;
							},
						'TraGopTableGateway' => function ($sm) {
								$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
								$resultSetPrototype = new ResultSet();
								$resultSetPrototype->setArrayObjectPrototype(new TraGop());
								return new TableGateway('tra_gop', $dbAdapter, null, $resultSetPrototype);
							},

						'Application\Model\KeywordsTable' => function ($sm) {
								$tableGateway = $sm->get('KeywordsTableGateway');
								$table = new KeywordsTable($tableGateway);
								return $table;
							},
						'KeywordsTableGateway' => function ($sm) {
								$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
								$resultSetPrototype = new ResultSet();
								$resultSetPrototype->setArrayObjectPrototype(new Keywords());
								return new TableGateway('keywords', $dbAdapter, null, $resultSetPrototype);
							},
						'Application\Model\WebsitesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'WebsitesTableGateway' );
							$table = new WebsitesTable ( $tableGateway );
							return $table;
						},
						'WebsitesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Websites () );
							return new TableGateway ( 'websites', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\MenusTable' => function ($sm) {
							$tableGateway = $sm->get ( 'MenusTableGateway' );
							$table = new MenusTable ( $tableGateway );
							return $table;
						},
						'MenusTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Menus () );
							return new TableGateway ( 'menus', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\ModulesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ModulesTableGateway' );
							$table = new ModulesTable ( $tableGateway );
							return $table;
						},
						'ModulesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Modules () );
							return new TableGateway ( 'module', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\CountryTable' => function ($sm) {
							$tableGateway = $sm->get ( 'CountryTableGateway' );
							$table = new CountryTable ( $tableGateway );
							return $table;
						},
						'CountryTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Country () );
							return new TableGateway ( 'country', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\DomainTable' => function ($sm) {
							$tableGateway = $sm->get ( 'DomainTableGateway' );
							$table = new DomainTable ( $tableGateway );
							return $table;
						},
						'DomainTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Domain () );
							return new TableGateway ( 'domain', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\PackTable' => function ($sm) {
							$tableGateway = $sm->get ( 'PackTableGateway' );
							$table = new PackTable ( $tableGateway );
							return $table;
						},
						'PackTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Pack () );
							return new TableGateway ( 'pack', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\PermissionsTable' => function ($sm) {
							$tableGateway = $sm->get ( 'PermissionsTableGateway' );
							$table = new PermissionsTable ( $tableGateway );
							return $table;
						},
						'PermissionsTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Permissions () );
							return new TableGateway ( 'permissions', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\TemplatesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'TemplatesTableGateway' );
							$table = new TemplatesTable ( $tableGateway );
							return $table;
						},
						'TemplatesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Templates () );
							return new TableGateway ( 'template', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\LanguagesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'LanguagesTableGateway' );
							$table = new LanguagesTable ( $tableGateway );
							return $table;
						},
						'LanguagesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Languages () );
							return new TableGateway ( 'languages', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\WholesaleTable' => function ($sm) {
							$tableGateway = $sm->get ( 'WholesaleTableGateway' );
							$table = new WholesaleTable ( $tableGateway );
							return $table;
						},
						'WholesaleTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Wholesale () );
							return new TableGateway ( 'wholesale', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\WholesaleProductsTable' => function ($sm) {
							$tableGateway = $sm->get ( 'WholesaleProductsTableGateway' );
							$table = new WholesaleProductsTable ( $tableGateway );
							return $table;
						},
						'WholesaleProductsTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new WholesaleProducts () );
							return new TableGateway ( 'wholesale_products', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\ArticlesLanguagesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'ArticlesLanguagesTableGateway' );
							$table = new ArticlesLanguagesTable ( $tableGateway );
							return $table;
						},
						'ArticlesLanguagesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new ArticlesLanguages () );
							return new TableGateway ( 'articles_languages', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\CategoriesArticlesLanguagesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'CategoriesArticlesLanguagesTableGateway' );
							$table = new CategoriesArticlesLanguagesTable ( $tableGateway );
							return $table;
						},
						'CategoriesArticlesLanguagesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new CategoriesArticlesLanguages () );
							return new TableGateway ( 'categories_articles_languages', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\CategoryTemplatesTable' => function ($sm) {
							$tableGateway = $sm->get ( 'CategoryTemplatesTableGateway' );
							$table = new CategoryTemplatesTable ( $tableGateway );
							return $table;
						},
						'CategoryTemplatesTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new CategoryTemplates () );
							return new TableGateway ( 'categories_template', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\TagsTable' => function ($sm) {
							$tableGateway = $sm->get ( 'TagsTableGateway' );
							$table = new TagsTable ( $tableGateway );
							return $table;
						},
						'TagsTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Tags () );
							return new TableGateway ( 'tags', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\CouponsTable' => function ($sm) {
							$tableGateway = $sm->get ( 'CouponsTableGateway' );
							$table = new CouponsTable ( $tableGateway );
							return $table;
						},
						'CouponsTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Coupons () );
							return new TableGateway ( 'coupons', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\ShippingTable' => function ($sm) {
	                        $tableGateway = $sm->get('ShippingTableGateway');
	                        $table = new ShippingTable($tableGateway);
	                        return $table;
	                    },
	                	'ShippingTableGateway' => function ($sm) {
	                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
	                        $resultSetPrototype = new ResultSet();
	                        $resultSetPrototype->setArrayObjectPrototype(new Shipping());
	                        return new TableGateway('shipping', $dbAdapter, null, $resultSetPrototype);
	                    },

						'Application\Model\ShippingWardTable' => function ($sm) {
		                    $tableGateway = $sm->get('ShippingWardTableGateway');
		                    $table = new ShippingWardTable($tableGateway);
		                    return $table;
		                },
		                'ShippingWardTableGateway' => function ($sm) {
		                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		                    $resultSetPrototype = new ResultSet();
		                    $resultSetPrototype->setArrayObjectPrototype(new ShippingWard());
		                    return new TableGateway('shipping_ward', $dbAdapter, null, $resultSetPrototype);
		                },

		                'Application\Model\GoogleTable' => function ($sm) {
		                    $tableGateway = $sm->get('GoogleTableGateway');
		                    $table = new GoogleTable($tableGateway);
		                    return $table;
		                },
		                'GoogleTableGateway' => function ($sm) {
		                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		                    $resultSetPrototype = new ResultSet();
		                    $resultSetPrototype->setArrayObjectPrototype(new Google());
		                    return new TableGateway('google', $dbAdapter, null, $resultSetPrototype);
		                },

		                'Application\Model\FacebookTable' => function ($sm) {
		                    $tableGateway = $sm->get('FacebookTableGateway');
		                    $table = new FacebookTable($tableGateway);
		                    return $table;
		                },
		                'FacebookTableGateway' => function ($sm) {
		                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		                    $resultSetPrototype = new ResultSet();
		                    $resultSetPrototype->setArrayObjectPrototype(new Facebook());
		                    return new TableGateway('facebook', $dbAdapter, null, $resultSetPrototype);
		                },

		                'Application\Model\AppFacebookTable' => function ($sm) {
		                    $tableGateway = $sm->get('AppFacebookTableGateway');
		                    $table = new AppFacebookTable($tableGateway);
		                    return $table;
		                },
		                'AppFacebookTableGateway' => function ($sm) {
		                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		                    $resultSetPrototype = new ResultSet();
		                    $resultSetPrototype->setArrayObjectPrototype(new AppFacebook());
		                    return new TableGateway('app_facebook', $dbAdapter, null, $resultSetPrototype);
		                },

		                'Application\Model\FBCommentTable' => function ($sm) {
		                    $tableGateway = $sm->get('FBCommentTableGateway');
		                    $table = new FBCommentTable($tableGateway);
		                    return $table;
		                },
		                'FBCommentTableGateway' => function ($sm) {
		                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		                    $resultSetPrototype = new ResultSet();
		                    $resultSetPrototype->setArrayObjectPrototype(new FBComment());
		                    return new TableGateway('fb_comments', $dbAdapter, null, $resultSetPrototype);
		                },

		                'Application\Model\FBFeedTable' => function ($sm) {
		                    $tableGateway = $sm->get('FBFeedTableGateway');
		                    $table = new FBFeedTable($tableGateway);
		                    return $table;
		                },
		                'FBFeedTableGateway' => function ($sm) {
		                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		                    $resultSetPrototype = new ResultSet();
		                    $resultSetPrototype->setArrayObjectPrototype(new FBFeed());
		                    return new TableGateway('fb_feed', $dbAdapter, null, $resultSetPrototype);
		                },

		                'Application\Model\FBUserTable' => function ($sm) {
		                    $tableGateway = $sm->get('FBUserTableGateway');
		                    $table = new FBUserTable($tableGateway);
		                    return $table;
		                },
		                'FBUserTableGateway' => function ($sm) {
		                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		                    $resultSetPrototype = new ResultSet();
		                    $resultSetPrototype->setArrayObjectPrototype(new FBUser());
		                    return new TableGateway('fb_user', $dbAdapter, null, $resultSetPrototype);
		                },

		                'Application\Model\AssignTable' => function ($sm) {
		                    $tableGateway = $sm->get('AssignTableGateway');
		                    $table = new AssignTable($tableGateway);
		                    return $table;
		                },
		                'AssignTableGateway' => function ($sm) {
		                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		                    $resultSetPrototype = new ResultSet();
		                    $resultSetPrototype->setArrayObjectPrototype(new Assign());
		                    return new TableGateway('assign', $dbAdapter, null, $resultSetPrototype);
		                },

		                'Application\Model\AssignUserTable' => function ($sm) {
		                    $tableGateway = $sm->get('AssignUserTableGateway');
		                    $table = new AssignUserTable($tableGateway);
		                    return $table;
		                },
		                'AssignUserTableGateway' => function ($sm) {
		                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		                    $resultSetPrototype = new ResultSet();
		                    $resultSetPrototype->setArrayObjectPrototype(new AssignUser());
		                    return new TableGateway('assign_user', $dbAdapter, null, $resultSetPrototype);
		                },

		                'Application\Model\CommissionTable' => function ($sm) {
		                    $tableGateway = $sm->get('CommissionTableGateway');
		                    $table = new CommissionTable($tableGateway);
		                    return $table;
		                },
		                'CommissionTableGateway' => function ($sm) {
		                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		                    $resultSetPrototype = new ResultSet();
		                    $resultSetPrototype->setArrayObjectPrototype(new Commission());
		                    return new TableGateway('merchant_commission', $dbAdapter, null, $resultSetPrototype);
		                },

		                'Application\Model\TrafficTable' => function ($sm) {
		                    $tableGateway = $sm->get('TrafficTableGateway');
		                    $table = new TrafficTable($tableGateway);
		                    return $table;
		                },
		                'TrafficTableGateway' => function ($sm) {
		                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
		                    $resultSetPrototype = new ResultSet();
		                    $resultSetPrototype->setArrayObjectPrototype(new Traffic());
		                    return new TableGateway('traffic', $dbAdapter, null, $resultSetPrototype);
		                },
						
				) 
		);
	
    }
}
?>