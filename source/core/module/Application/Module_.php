<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

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
use Application\Model\ContactTable;
use Zend\Cache\Storage\Adapter\Memcached;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Application\Model\Categories;
use Application\Model\CategoriesTable;
use Application\Model\Products;
use Application\Model\ProductsTable;
use Application\Model\Manufacturers;
use Application\Model\ManufacturersTable;
use Application\Model\Banner;
use Application\Model\BannerTable;
use Application\Model\Keywords;
use Application\Model\KeywordsTable;
use Application\Model\Websites;
use Application\Model\WebsitesTable;
use Application\Model\Languages;
use Application\Model\LanguagesTable;

use Application\Model\Airport;
use Application\Model\AirportTable;
use Application\Model\Booking;
use Application\Model\BookingTable;
use Application\Model\RoomType;
use Application\Model\RoomTypeTable;

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

class Module {
	public function onBootstrap(MvcEvent $e) {
		$eventManager = $e->getApplication ()->getEventManager ();
		$e->getApplication ()->getEventManager ()->getSharedManager ()->attach ( __NAMESPACE__, 'dispatch', function ($e) {
			$e->getTarget ()->layout ( 'app/layout' );
		});
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
        // compress HTML output 
        $response->setContent($this->compress($response->getContent())); 
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
						'DataFactory' => function ($sm) {
							$helper = new View\Helper\DataFactory ($sm);
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
						'Application\Model\BannerTable' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$table = new Model\BannerTable ( $dbAdapter );
							return $table;
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
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$table = new Model\ArticlesTable ( $dbAdapter );
							return $table;
						},
						'Application\Model\CitiesTable' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$table = new Model\CitiesTable ( $dbAdapter );
							return $table;
						},
						'Application\Model\DistrictsTable' => function ($sm) {
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
						},
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
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$table = new Model\CategoriesArticlesTable ( $dbAdapter );
							return $table;
						},
						'Application\Model\BranchesTable' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$table = new Model\BranchesTable ( $dbAdapter );
							return $table;
						},
						'Application\Model\PaymentTable' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$table = new Model\PaymentTable ( $dbAdapter );
							return $table;
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

						'Application\Model\AirportTable' => function ($sm) {
							$tableGateway = $sm->get ( 'AirportTableGateway' );
							$table = new AirportTable ( $tableGateway );
							return $table;
						},
						'AirportTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Airport () );
							return new TableGateway ( 'airport', $dbAdapter, null, $resultSetPrototype );
						},
						'Application\Model\BookingTable' => function ($sm) {
							$tableGateway = $sm->get ( 'BookingTableGateway' );
							$table = new BookingTable ( $tableGateway );
							return $table;
						},
						
						'BookingTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new Booking () );
							return new TableGateway ( 'booking_form', $dbAdapter, null, $resultSetPrototype );
						},

						'Application\Model\RoomTypeTable' => function ($sm) {
							$tableGateway = $sm->get ( 'RoomTypeTableGateway' );
							$table = new RoomTypeTable ( $tableGateway );
							return $table;
						},
						
						'RoomTypeTableGateway' => function ($sm) {
							$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
							$resultSetPrototype = new ResultSet ();
							$resultSetPrototype->setArrayObjectPrototype ( new RoomType () );
							return new TableGateway ( 'room_type_detail', $dbAdapter, null, $resultSetPrototype );
						},
						
				) 
		);
	
    }
}
?>