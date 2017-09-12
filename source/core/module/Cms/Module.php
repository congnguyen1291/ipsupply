<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Cms;

use Cms\Model\Articles;
use Cms\Model\ArticlesTable;
use Cms\Model\Banks;
use Cms\Model\BanksTable;
use Cms\Model\Banners;
use Cms\Model\BannersTable;
use Cms\Model\Branches;
use Cms\Model\BranchesTable;
use Cms\Model\Category;
use Cms\Model\CategoryArticles;
use Cms\Model\CategoryArticlesTable;
use Cms\Model\CategoryTable;
use Cms\Model\Coupons;
use Cms\Model\CouponsTable;
use Cms\Model\Extension;
use Cms\Model\ExtensionTable;
use Cms\Model\Feature;
use Cms\Model\FeatureTable;
use Cms\Model\GeneralTable;
use Cms\Model\GoldTimer;
use Cms\Model\GoldTimerTable;
use Cms\Model\Invoice;
use Cms\Model\InvoiceTable;
use Cms\Model\Languagescms;
use Cms\Model\LanguagescmsTable;
use Cms\Model\Languages;
use Cms\Model\LanguagesTable;
use Cms\Model\Payment;
use Cms\Model\PaymentTable;
use Cms\Model\Permissions;
use Cms\Model\PermissionsTable;
use Cms\Model\Product;
use Cms\Model\ProductTable;
use Cms\Model\Manufacturers;
use Cms\Model\ManufacturersTable;
use Cms\Model\Setting;
use Cms\Model\SettingTable;
use Cms\Model\Transportation;
use Cms\Model\TransportationTable;
use Cms\Model\User;
use Cms\Model\UserTable;
use Cms\Model\Websites;
use Cms\Model\WebsitesTable;
use Cms\Model\Templates;
use Cms\Model\TemplatesTable;
use Cms\Model\Modules;
use Cms\Model\ModulesTable;
use Cms\Model\Menus;
use Cms\Model\MenusTable;
use Cms\Model\Pack;
use Cms\Model\PackTable;
use Cms\Model\Api;
use Cms\Model\ApiTable;
use Cms\Model\BannerPosition;
use Cms\Model\BannerPositionTable;
use Cms\Model\BannerSize;
use Cms\Model\BannerSizeTable;
use Cms\Model\Country;
use Cms\Model\CountryTable;
use Cms\Model\CategoryTemplate;
use Cms\Model\CategoryTemplateTable;
use Cms\Model\Domain;
use Cms\Model\DomainTable;
use Cms\Model\Tags;
use Cms\Model\TagsTable;
use Cms\Model\Area;
use Cms\Model\AreaTable;
use Cms\Model\Picture;
use Cms\Model\PictureTable;
use Cms\Model\ArticlesLanguage;
use Cms\Model\ArticlesLanguageTable;
use Cms\Model\CategoriesArticlesLanguage;
use Cms\Model\CategoriesArticlesLanguageTable;
use Cms\Model\GroupsRegions;
use Cms\Model\GroupsRegionsTable;
use Cms\Model\District;
use Cms\Model\DistrictTable;
use Cms\Model\Shipping;
use Cms\Model\ShippingTable;
use Cms\Model\City;
use Cms\Model\CityTable;
use Cms\Model\WholesaleInvoice;
use Cms\Model\WholesaleInvoiceTable;
use Cms\Model\ShippingWard;
use Cms\Model\ShippingWardTable;
use Cms\Model\Contact;
use Cms\Model\ContactTable;
use Cms\Model\Assign;
use Cms\Model\AssignTable;
use Cms\Model\AssignUser;
use Cms\Model\AssignUserTable;
use Cms\Model\ProductType;
use Cms\Model\ProductTypeTable;
use Cms\Model\ProductExtension;
use Cms\Model\ProductExtensionTable;
use Cms\Model\Commission;
use Cms\Model\CommissionTable;
use Cms\Model\Merchant;
use Cms\Model\MerchantTable;
use Cms\Model\Traffic;
use Cms\Model\TrafficTable;

use Zend\Cache\Storage\Adapter\Memcache;
use Zend\Cache\Storage\Adapter\MemcachedOptions;
use Zend\Cache\Storage\Adapter\MemcachedResourceManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;


class Module implements AutoloaderProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication ()->getEventManager ();
        $e->getApplication()->getEventManager()->getSharedManager()->attach(__NAMESPACE__, 'dispatch', function ($e) {
            $e->getTarget()->layout('cms/layout');
        });
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }


    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'CMSCommon' => function ($sm) {
                        $helper = new Helper\CMSCommon ();
                        return $helper;
                    },
            )
        );
    }

    // Add this method:
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Cms\Model\CategoryTable' => function ($sm) {
                        $tableGateway = $sm->get('CategoryTableGateway');
                        $table = new CategoryTable($tableGateway);
                        return $table;
                    },
                'CategoryTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Category());
                        return new TableGateway('categories', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\FeatureTable' => function ($sm) {
                        $tableGateway = $sm->get('FeatureTableGateway');
                        $table = new FeatureTable($tableGateway);
                        return $table;
                    },
                'FeatureTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Feature());
                        return new TableGateway('feature', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\ManufacturersTable' => function ($sm) {
                        $tableGateway = $sm->get('ManufacturersTableGateway');
                        $table = new ManufacturersTable($tableGateway);
                        return $table;
                    },
                'ManufacturersTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Manufacturers());
                        return new TableGateway('manufacturers', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\ProductTable' => function ($sm) {
                        $tableGateway = $sm->get('ProductTableGateway');
                        $table = new ProductTable($tableGateway);
                        return $table;
                    },
                'ProductTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Product());
                        return new TableGateway('products', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\ArticlesTable' => function ($sm) {
                        $tableGateway = $sm->get('ArticlesTableGateway');
                        $table = new ArticlesTable($tableGateway);
                        return $table;
                    },
                'ArticlesTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Articles());
                        return new TableGateway('articles', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\CategoryArticlesTable' => function ($sm) {
                        $tableGateway = $sm->get('CategoryArticlesTableGateway');
                        $table = new CategoryArticlesTable($tableGateway);
                        return $table;
                    },
                'CategoryArticlesTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new CategoryArticles());
                        return new TableGateway('categories_articles', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\TransportationTable' => function ($sm) {
                        $tableGateway = $sm->get('TransportationTableGateway');
                        $table = new TransportationTable($tableGateway);
                        return $table;
                    },
                'TransportationTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Transportation());
                        return new TableGateway('transportation', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\InvoiceTable' => function ($sm) {
                        $tableGateway = $sm->get('InvoiceTableGateway');
                        $table = new InvoiceTable($tableGateway);
                        return $table;
                    },
                'InvoiceTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Invoice());
                        return new TableGateway('invoice', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\SettingTable' => function ($sm) {
                        $tableGateway = $sm->get('SettingTableGateway');
                        $table = new SettingTable($tableGateway);
                        return $table;
                    },
                'SettingTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Setting());
                        return new TableGateway('settings', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\UserTable' => function ($sm) {
                        $tableGateway = $sm->get('UserTableGateway');
                        $table = new UserTable($tableGateway);
                        return $table;
                    },
                'UserTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new User());
                        return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\PaymentTable' => function ($sm) {
                        $tableGateway = $sm->get('PaymentTableGateway');
                        $table = new PaymentTable($tableGateway);
                        return $table;
                    },
                'PaymentTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Payment());
                        return new TableGateway('payment_method', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\ExtensionTable' => function ($sm) {
                        $tableGateway = $sm->get('ExtensionTableGateway');
                        $table = new ExtensionTable($tableGateway);
                        return $table;
                    },
                'ExtensionTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Extension());
                        return new TableGateway('extensions', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\BranchesTable' => function ($sm) {
                        $tableGateway = $sm->get('BranchesTableGateway');
                        $table = new BranchesTable($tableGateway);
                        return $table;
                    },
                'BranchesTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Branches());
                        return new TableGateway('branches', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\LanguagesTable' => function ($sm) {
                        $tableGateway = $sm->get('LanguagesTableGateway');
                        $table = new LanguagesTable($tableGateway);
                        return $table;
                    },
                'LanguagesTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Languages());
                        return new TableGateway('languages', $dbAdapter, null, $resultSetPrototype);
                    },
				 'Cms\Model\LanguagescmsTable' => function ($sm) {
                        $tableGateway = $sm->get('LanguagescmsTableGateway');
                        $table = new LanguagescmsTable($tableGateway);
                        return $table;
                    },
                'LanguagescmsTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Languagescms());
                        return new TableGateway('languages', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\BannersTable' => function ($sm) {
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
                'Cms\Model\PermissionsTable' => function ($sm) {
                        $tableGateway = $sm->get('PermissionsTableGateway');
                        $table = new PermissionsTable($tableGateway);
                        return $table;
                    },
                'PermissionsTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new Permissions());
                        return new TableGateway('permissions', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\GoldTimerTable' => function ($sm) {
                        $tableGateway = $sm->get('GoldTimerTableGateway');
                        $table = new GoldTimerTable($tableGateway);
                        return $table;
                    },
                'GoldTimerTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new GoldTimer());
                        return new TableGateway('gold_timer', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\BanksTable' => function ($sm) {
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
                'Cms\Model\CouponsTable' => function ($sm) {
                    $tableGateway = $sm->get('CouponsTableGateway');
                    $table = new CouponsTable($tableGateway);
                    return $table;
                },
                'CouponsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Coupons());
                    return new TableGateway('coupons', $dbAdapter, null, $resultSetPrototype);
                },
                'Cms\Model\WebsitesTable' => function ($sm) {
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
                'Cms\Model\TemplatesTable' => function ($sm) {
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
                'Cms\Model\ModulesTable' => function ($sm) {
                    $tableGateway = $sm->get ( 'ModulesTableGateway' );
                    $table = new ModulesTable ( $tableGateway );
                    return $table;
                },
                'ModulesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
                    $resultSetPrototype = new ResultSet ();
                    $resultSetPrototype->setArrayObjectPrototype ( new Modules());
                    return new TableGateway ( 'module', $dbAdapter, null, $resultSetPrototype );
                },
                'Cms\Model\MenusTable' => function ($sm) {
                    $tableGateway = $sm->get ( 'MenusTableGateway' );
                    $table = new MenusTable ( $tableGateway );
                    return $table;
                },
                'MenusTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
                    $resultSetPrototype = new ResultSet ();
                    $resultSetPrototype->setArrayObjectPrototype ( new Menus());
                    return new TableGateway ( 'menus', $dbAdapter, null, $resultSetPrototype );
                },
                'Cms\Model\PackTable' => function ($sm) {
                    $tableGateway = $sm->get ( 'PackTableGateway' );
                    $table = new PackTable ( $tableGateway );
                    return $table;
                },
                'PackTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
                    $resultSetPrototype = new ResultSet ();
                    $resultSetPrototype->setArrayObjectPrototype ( new Pack());
                    return new TableGateway ( 'pack', $dbAdapter, null, $resultSetPrototype );
                },
                'Cms\Model\ApiTable' => function ($sm) {
                    $tableGateway = $sm->get ( 'ApiTableGateway' );
                    $table = new ApiTable ( $tableGateway );
                    return $table;
                },
                'ApiTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
                    $resultSetPrototype = new ResultSet ();
                    $resultSetPrototype->setArrayObjectPrototype ( new Api());
                    return new TableGateway ( 'api', $dbAdapter, null, $resultSetPrototype );
                },
                'Cms\Model\BannerPositionTable' => function ($sm) {
                    $tableGateway = $sm->get ( 'BannerPositionTableGateway' );
                    $table = new BannerPositionTable ( $tableGateway );
                    return $table;
                },
                'BannerPositionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
                    $resultSetPrototype = new ResultSet ();
                    $resultSetPrototype->setArrayObjectPrototype ( new BannerPosition());
                    return new TableGateway ( 'banners_position', $dbAdapter, null, $resultSetPrototype );
                },
                'Cms\Model\BannerSizeTable' => function ($sm) {
                    $tableGateway = $sm->get ( 'BannerSizeTableGateway' );
                    $table = new BannerSizeTable ( $tableGateway );
                    return $table;
                },
                'BannerSizeTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
                    $resultSetPrototype = new ResultSet ();
                    $resultSetPrototype->setArrayObjectPrototype ( new BannerSize());
                    return new TableGateway ( 'banners_size', $dbAdapter, null, $resultSetPrototype );
                },
                'Cms\Model\CountryTable' => function ($sm) {
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
                'Cms\Model\CategoryTemplateTable' => function ($sm) {
                    $tableGateway = $sm->get ( 'CategoryTemplateTableGateway' );
                    $table = new CategoryTemplateTable ( $tableGateway );
                    return $table;
                },
                'CategoryTemplateTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
                    $resultSetPrototype = new ResultSet ();
                    $resultSetPrototype->setArrayObjectPrototype ( new CategoryTemplate () );
                    return new TableGateway ( 'categories_template', $dbAdapter, null, $resultSetPrototype );
                },
                'Cms\Model\DomainTable' => function ($sm) {
                    $tableGateway = $sm->get ( 'DomainTableGateway' );
                    $table = new DomainTable ( $tableGateway );
                    return $table;
                },
                'DomainTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
                    $resultSetPrototype = new ResultSet ();
                    $resultSetPrototype->setArrayObjectPrototype ( new Domain());
                    return new TableGateway ( 'domain', $dbAdapter, null, $resultSetPrototype );
                },
                'Cms\Model\TagsTable' => function ($sm) {
                    $tableGateway = $sm->get ( 'TagsTableGateway' );
                    $table = new TagsTable ( $tableGateway );
                    return $table;
                },
                'TagsTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
                    $resultSetPrototype = new ResultSet ();
                    $resultSetPrototype->setArrayObjectPrototype ( new Tags());
                    return new TableGateway ( 'tags', $dbAdapter, null, $resultSetPrototype );
                },
                'Cms\Model\AreaTable' => function ($sm) {
                    $tableGateway = $sm->get ( 'AreaTableGateway' );
                    $table = new AreaTable ( $tableGateway );
                    return $table;
                },
                'AreaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
                    $resultSetPrototype = new ResultSet ();
                    $resultSetPrototype->setArrayObjectPrototype ( new Area());
                    return new TableGateway ( 'area', $dbAdapter, null, $resultSetPrototype );
                },
                'Cms\Model\PictureTable' => function ($sm) {
                    $tableGateway = $sm->get ( 'PictureTableGateway' );
                    $table = new PictureTable ( $tableGateway );
                    return $table;
                },
                'PictureTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
                    $resultSetPrototype = new ResultSet ();
                    $resultSetPrototype->setArrayObjectPrototype ( new Picture());
                    return new TableGateway ( 'picture', $dbAdapter, null, $resultSetPrototype );
                },
                'Cms\Model\CategoriesArticlesLanguageTable' => function ($sm) {
                        $tableGateway = $sm->get('CategoriesArticlesLanguageTableGateway');
                        $table = new CategoriesArticlesLanguageTable($tableGateway);
                        return $table;
                    },
                'CategoriesArticlesLanguageTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new CategoriesArticlesLanguage());
                        return new TableGateway('categories_articles_languages', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\ArticlesLanguageTable' => function ($sm) {
                        $tableGateway = $sm->get('ArticlesLanguageTableGateway');
                        $table = new ArticlesLanguageTable($tableGateway);
                        return $table;
                    },
                'ArticlesLanguageTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new ArticlesLanguage());
                        return new TableGateway('articles_languages', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\GroupsRegionsTable' => function ($sm) {
                        $tableGateway = $sm->get('GroupsRegionsTableGateway');
                        $table = new GroupsRegionsTable($tableGateway);
                        return $table;
                    },
                'GroupsRegionsTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new GroupsRegions());
                        return new TableGateway('group_regions', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\DistrictTable' => function ($sm) {
                        $tableGateway = $sm->get('DistrictTableGateway');
                        $table = new DistrictTable($tableGateway);
                        return $table;
                    },
                'DistrictTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new District());
                        return new TableGateway('districts', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\ShippingTable' => function ($sm) {
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
                'Cms\Model\CityTable' => function ($sm) {
                        $tableGateway = $sm->get('CityTableGateway');
                        $table = new CityTable($tableGateway);
                        return $table;
                    },
                'CityTableGateway' => function ($sm) {
                        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                        $resultSetPrototype = new ResultSet();
                        $resultSetPrototype->setArrayObjectPrototype(new City());
                        return new TableGateway('cities', $dbAdapter, null, $resultSetPrototype);
                    },
                'Cms\Model\WholesaleInvoiceTable' => function ($sm) {
                    $tableGateway = $sm->get('WholesaleInvoiceTableGateway');
                    $table = new WholesaleInvoiceTable($tableGateway);
                    return $table;
                },
                'WholesaleInvoiceTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new WholesaleInvoice());
                    return new TableGateway('wholesale_invoice', $dbAdapter, null, $resultSetPrototype);
                },
                'Cms\Model\ShippingWardTable' => function ($sm) {
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

                'Cms\Model\ContactTable' => function ($sm) {
                    $tableGateway = $sm->get('ContactTableGateway');
                    $table = new ContactTable($tableGateway);
                    return $table;
                },
                'ContactTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Contact());
                    return new TableGateway('website_contact', $dbAdapter, null, $resultSetPrototype);
                },

                'Cms\Model\AssignTable' => function ($sm) {
                    $tableGateway = $sm->get('CmAssignTableGateway');
                    $table = new AssignTable($tableGateway);
                    return $table;
                },
                'CmAssignTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Assign());
                    return new TableGateway('assign', $dbAdapter, null, $resultSetPrototype);
                },

                'Cms\Model\AssignUserTable' => function ($sm) {
                    $tableGateway = $sm->get('CmAssignUserTableGateway');
                    $table = new AssignUserTable($tableGateway);
                    return $table;
                },
                'CmAssignUserTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new AssignUser());
                    return new TableGateway('assign_user', $dbAdapter, null, $resultSetPrototype);
                },

                'Cms\Model\ProductTypeTable' => function ($sm) {
                    $tableGateway = $sm->get('CmProductTypeTableGateway');
                    $table = new ProductTypeTable($tableGateway);
                    return $table;
                },
                'CmProductTypeTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ProductType());
                    return new TableGateway('products_type', $dbAdapter, null, $resultSetPrototype);
                },

                'Cms\Model\ProductExtensionTable' => function ($sm) {
                    $tableGateway = $sm->get('CmProductExtensionTableGateway');
                    $table = new ProductExtensionTable($tableGateway);
                    return $table;
                },
                'CmProductExtensionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ProductExtension());
                    return new TableGateway('products_extensions', $dbAdapter, null, $resultSetPrototype);
                },

                'Cms\Model\CommissionTable' => function ($sm) {
                    $tableGateway = $sm->get('CmCommissionTableGateway');
                    $table = new CommissionTable($tableGateway);
                    return $table;
                },
                'CmCommissionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Commission());
                    return new TableGateway('merchant_commission', $dbAdapter, null, $resultSetPrototype);
                },

                'Cms\Model\MerchantTable' => function ($sm) {
                    $tableGateway = $sm->get('CmMerchantTableGateway');
                    $table = new MerchantTable($tableGateway);
                    return $table;
                },
                'CmMerchantTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Merchant());
                    return new TableGateway('merchant', $dbAdapter, null, $resultSetPrototype);
                },

                'Cms\Model\TrafficTable' => function ($sm) {
                    $tableGateway = $sm->get('CmTrafficTableGateway');
                    $table = new TrafficTable($tableGateway);
                    return $table;
                },
                'CmTrafficTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Traffic());
                    return new TableGateway('traffic', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
