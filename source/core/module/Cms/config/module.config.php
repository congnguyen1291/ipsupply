<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'cms' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/cms',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Cms\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'defaults' => array(
                    'controller' => 'Cms\Controller\Index',
                    'action' => 'index',
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'category' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/category[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Category',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'feature' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/feature[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Feature',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'manufacturers' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/manufacturers[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Manufacturers',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'product' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/product[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Product',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'setting' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/setting[/][:action[/:name][/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'name' => '[a-zA-Z][a-zA-Z0-9_-]*',
								'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Setting',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    /**
                     * Action là Add có ID là add cho sản phẩm có ID đó
                     * Action là List có ID là danh sách bài viết của sản phẩm đó
                     * Action là Index là danh sách tất cả bài viết
                     */
                    'articles' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/articles[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Articles',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'carticles' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/category-articles[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\CategoryArticles',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'trans' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/trans[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Transportation',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'invoice' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/invoice[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Invoice',
                                'action' => 'index',
                            ),
                        ),
                    ),
					'invoiceUpdae' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/invoice/update-invoice',
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Invoice',
                                'action' => 'updateInvoice',
                            ),
                        ),
                    ),
                    'login' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/login[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Login',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'user' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/user[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\User',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'goldtimer' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/gold-timer[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\GoldTimer',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'banks' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/banks[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Banks',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'coupons' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/coupons[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Coupons',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'payment' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/payment[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Payment',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'branches' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/branches[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Branches',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => TRUE,
                        'child_routes' => array(
                            'page' => array(
                                'type'    => 'Segment',
                                'options' => array(
                                    'route'    => '/page[/:page]',
                                    'constraints' => array(
                                        'page' => '[0-9]+',
                                    ),
                                    'defaults' => array(
                                        'action' => 'index',
                                    ),
                                ),
                            ),
                        )
                    ),
                    'extension' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/extension[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Extension',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'ext_require' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/extension-require[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\ExtensionRequire',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'language' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/language[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Language',
                                'action' => 'index',
                            ),
                        ),
                    ),
					'languagescms' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/languagescms[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Languagescms',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'user_level' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/user-level[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\UsersLevel',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'banners' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/banners[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Banners',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'permission' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/permission[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Permissions',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'group' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/group[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Groups',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'fqa' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/fqa[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\QuestionAnswer',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'website' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/website[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Website',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'themes' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/themes[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Themes',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'modules' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/modules[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Modules',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'menus' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/menus[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Menus',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'pack' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/pack[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Pack',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'api' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/api[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Api',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'domain' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/domain[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Domain',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'BannerPosition' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/banner-position[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\BannerPosition',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'BannerSize' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/banner-size[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\BannerSize',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'tags' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/tags[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Tags',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'pictures' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/pictures[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Pictures',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'group_regions' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/group-regions[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\GroupsRegions',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'districts' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/districts[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Districts',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'cities' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/cities[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Cities',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'shipping' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/shipping[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Shipping',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'success' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/success',
                            'defaults' => array(
                                '__NAMESPACE__' => 'Cms\Controller',
                                'controller' => 'Success',
                                'action' => 'index',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'default' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/[:action]',
                                    'constraints' => array(
                                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    ),
                                    'defaults' => array(),
                                ),
                            ),
                        ),
                    ),
                    'WholesaleInvoice' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/wholesale-invoice[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\WholesaleInvoice',
                                'action' => 'index',
                            ),
                        ),
                    ),

                    'facebook' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/facebook[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Facebook',
                                'action' => 'index',
                            ),
                        ),
                    ),

                    'contact' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/contact[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Contact',
                                'action' => 'index',
                            ),
                        ),
                    ),

                    'assign' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/assign[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Assign',
                                'action' => 'index',
                            ),
                        ),
                    ),

                    'merchant' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/merchant[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Merchant',
                                'action' => 'index',
                            ),
                        ),
                    ),

                    'country' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/country[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Country',
                                'action' => 'index',
                            ),
                        ),
                    ),

                    'stock' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/stock[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Stock',
                                'action' => 'index',
                            ),
                        ),
                    ),

                    'trash' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/trash[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Trash',
                                'action' => 'index',
                            ),
                        ),
                    ),

                    'siri' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/siri[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Siri',
                                'action' => 'index',
                            ),
                        ),
                    ),

                    'traffic' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/traffic[/][:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Cms\Controller\Traffic',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    
                ),
            ),
            'upload' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/upload[/][:action[/:id]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Cms\Controller\Upload',
                        'action' => 'uploadimage',
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Cms\Controller\Index' => 'Cms\Controller\IndexController',
            'Cms\Controller\Login' => 'Cms\Controller\LoginController',
            'Cms\Controller\Category' => 'Cms\Controller\CategoryController',
            'Cms\Controller\Feature' => 'Cms\Controller\FeatureController',
            'Cms\Controller\Manufacturers' => 'Cms\Controller\ManufacturersController',
            'Cms\Controller\Product' => 'Cms\Controller\ProductController',
            'Cms\Controller\Articles' => 'Cms\Controller\ArticlesController',
            'Cms\Controller\Upload' => 'Cms\Controller\UploadController',
            'Cms\Controller\CategoryArticles' => 'Cms\Controller\CategoryArticlesController',
            'Cms\Controller\Transportation' => 'Cms\Controller\TransportationController',
            'Cms\Controller\Invoice' => 'Cms\Controller\InvoiceController',
            'Cms\Controller\Setting' => 'Cms\Controller\SettingController',
            'Cms\Controller\User' => 'Cms\Controller\UserController',
            'Cms\Controller\UsersLevel' => 'Cms\Controller\UsersLevelController',
            'Cms\Controller\Payment' => 'Cms\Controller\PaymentController',
            'Cms\Controller\Extension' => 'Cms\Controller\ExtensionController',
            'Cms\Controller\Branches' => 'Cms\Controller\BranchesController',
            'Cms\Controller\Language' => 'Cms\Controller\LanguageController',
			'Cms\Controller\Languagescms' => 'Cms\Controller\LanguagescmsController',
            'Cms\Controller\Banners' => 'Cms\Controller\BannersController',
            'Cms\Controller\Permissions' => 'Cms\Controller\PermissionsController',
            'Cms\Controller\Groups' => 'Cms\Controller\GroupsController',
            'Cms\Controller\GoldTimer' => 'Cms\Controller\GoldTimerController',
            'Cms\Controller\Banks' => 'Cms\Controller\BanksController',
            'Cms\Controller\Coupons' => 'Cms\Controller\CouponsController',
            'Cms\Controller\QuestionAnswer' => 'Cms\Controller\QuestionAnswerController',
            'Cms\Controller\ExtensionRequire' => 'Cms\Controller\ExtensionRequireController',
            'Cms\Controller\Website' => 'Cms\Controller\WebsiteController',
            'Cms\Controller\Themes' => 'Cms\Controller\ThemesController',
            'Cms\Controller\Modules' => 'Cms\Controller\ModulesController',
            'Cms\Controller\Menus' => 'Cms\Controller\MenusController',
            'Cms\Controller\Api' => 'Cms\Controller\ApiController',
            'Cms\Controller\Pack' => 'Cms\Controller\PackController',
            'Cms\Controller\BannerPosition' => 'Cms\Controller\BannerPositionController',
            'Cms\Controller\BannerSize' => 'Cms\Controller\BannerSizeController',
            'Cms\Controller\Domain' => 'Cms\Controller\DomainController',
            'Cms\Controller\Tags' => 'Cms\Controller\TagsController',
            'Cms\Controller\Pictures' => 'Cms\Controller\PicturesController',
            'Cms\Controller\GroupsRegions' => 'Cms\Controller\GroupsRegionsController',
            'Cms\Controller\Districts' => 'Cms\Controller\DistrictsController',
            'Cms\Controller\Cities' => 'Cms\Controller\CitiesController',
            'Cms\Controller\Shipping' => 'Cms\Controller\ShippingController',
            'Cms\Controller\WholesaleInvoice' => 'Cms\Controller\WholesaleInvoiceController',
			'Cms\Controller\BackEnd' => 'Cms\Controller\BackEndController',
            'Cms\Controller\Facebook' => 'Cms\Controller\FacebookController',
            'Cms\Controller\Contact' => 'Cms\Controller\ContactController',
            'Cms\Controller\Assign' => 'Cms\Controller\AssignController',
            'Cms\Controller\Merchant' => 'Cms\Controller\MerchantController',
            'Cms\Controller\Country' => 'Cms\Controller\CountryController',
            'Cms\Controller\Stock' => 'Cms\Controller\StockController',
            'Cms\Controller\Trash' => 'Cms\Controller\TrashController',
            'Cms\Controller\Siri' => 'Cms\Controller\SiriController',
            'Cms\Controller\Traffic' => 'Cms\Controller\TrafficController',
        ),
    ),
	'view_helpers' => array(
		'factories' => array(
			'formelementerrors' => function($vhm) {
				$fee = new \Zend\Form\View\Helper\FormElementErrors();
				$fee->setAttributes([
					'style' => 'color:red'
				]);
				return $fee;
			}
		)
	),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'cms/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
