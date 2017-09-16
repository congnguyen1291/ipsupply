<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
$router = array(
    'categoriesVn' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '/danh-muc',
            'defaults' => array(
                'controller' => 'Application\Controller\Categories',
                'action' => 'index',
            ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
            'default' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/[:alias]-[:id]',
                    'constraints' => array(
                      'alias' => '[a-zA-Z][a-zA-Z0-9_-]*',
                      'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Categories',
                        'action' => 'detail',
                    ),
                ),
            ),
            'hots' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/hots',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Categories',
                        'action' => 'hots',
                    ),
                ),
            ),
            'news' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/news',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Categories',
                        'action' => 'news',
                    ),
                ),
            ),
            'deals' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/deals',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Categories',
                        'action' => 'deals',
                    ),
                ),
            ),
        ),
    ),
    'categories' => array(
        'type' => 'Segment',
        'priority' => 108, 
        'options' => array(
            'route' => '/category',
            'defaults' => array(
                'controller' => 'Application\Controller\Categories',
                'action' => 'index',
            ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
            'default' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/[:alias]-[:id]',
                    'constraints' => array(
                      'alias' => '[a-zA-Z][a-zA-Z0-9_-]*',
                      'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Categories',
                        'action' => 'detail',
                    ),
                ),
            ),
            'hots' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/hots',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Categories',
                        'action' => 'hots',
                    ),
                ),
            ),
            'news' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/news',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Categories',
                        'action' => 'news',
                    ),
                ),
            ),
            'deals' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/deals',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Categories',
                        'action' => 'deals',
                    ),
                ),
            ),
        ),
    ),

    'categoriesdetail' => array(
        'type'    => 'Segment',
        'options' => array(
            'route' => '/[:title]-[:id]s.html',
            'constraints' => array(
                'title' => '[0-9a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Categories',
                'action' => 'detail',
            ),
        ),
    ),

    'international' => array(
        'type'    => 'Segment',
        'options' => array(
            'route' => '/international[[/][:id]].html',
            'constraints' => array(
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Categories',
                'action' => 'international',
            ),
        ),
    ),

    'banggia' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '/bang-gia[/][:action[/:id]]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Price',
                'action' => 'index'
            ),
        ),
    ),
    'hangsapve' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
            'route' => '/hang-sap-ve',
            'defaults' => array(
                'controller' => 'Application\Controller\Categories',
                'action' => 'hang-sap-ve',
            ),
        ),
    ),
    'BaiVietVn' => array(
        'type' => 'Segment',
        'priority' => 109,
        'options' => array(
            'route' => '/bai-viet',
            'defaults' => array(
                'controller' => 'Application\Controller\Articles',
                'action' => 'index'
            ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
            'default' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/[:title]-[:id]',
                    'constraints' => array(
                        'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Articles',
                        'action' => 'listing',
                    ),
                ),
            ),
            'detail' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/[:title]-[:id].html',
                    'constraints' => array(
                        'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Articles',
                        'action' => 'detail',
                    ),
                ),
            ),
        ),
    ),
    'baiviet2' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '/bai-viet[/][:action[/:id]]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Articles',
                'action' => 'index'
            ),
        ),
    ),
    'BaiViet' => array(
        'type' => 'Segment',
        'priority'=>110,
        'options' => array(
            'route' => '/articles',
            'defaults' => array(
                'controller' => 'Application\Controller\Articles',
                'action' => 'index'
            ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
            'default' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/[:title]-[:id]',
                    'constraints' => array(
                        'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Articles',
                        'action' => 'listing',
                    ),
                ),
            ),
            'detail' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/[:title]-[:id].html',
                    'constraints' => array(
                        'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Articles',
                        'action' => 'detail',
                    ),
                ),
            ),
        ),
    ),
    'detailarticlewr' => array(
        'type'    => 'Segment',
        'options' => array(
            'route' => '/[:title]-[:id].html',
            'constraints' => array(
                'title' => '[0-9a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Articles',
                'action' => 'detail',
            ),
        ),
    ),
    'rss' => array(
        'type' => 'Literal',
        'options' => array(
            'route' => '/rss',
            'defaults' => array(
                'controller' => 'Application\Controller\Index',
                'action' => 'rss',
            ),
        ),
    ),
    'cron' => array(
        'type' => 'Literal',
        'options' => array(
            'route' => '/cron-send-email',
            'defaults' => array(
                'controller' => 'Application\Controller\Index',
                'action' => 'cron-send-email',
            ),
        ),
    ),
    'manu' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '/nha-san-xuat[/][:alias]-[:id]',
            'constraints' => array(
                'alias' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Manu',
                'action' => 'index',
            ),
        ),
    ),
    'allmanu' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '/tat-ca-nha-san-xuat[/page[/:page]]',
            'constraints' => array(
                'page' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Manu',
                'action' => 'showall',
                'page' => 0,
            ),
        ),
    ),
    'manu_warranty' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '/dieu-kien-bao-hanh[/][:title]-[:id]',
            'constraints' => array(
                'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Manu',
                'action' => 'show-warranty',
                'page' => 0,
            ),
        ),
    ),
    'manu_listing_all' => array(
        'type' => 'Literal',
        'options' => array(
            'route' => '/listing/all',
            'defaults' => array(
                'controller' => 'Application\Controller\Manu',
                'action' => 'listing',
            ),
        ),
    ),
    'manu_listing_detail' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '/listing[/][:alias]-[:id]',
            'constraints' => array(
                'alias' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Manu',
                'action' => 'listing',
            ),
        ),
    ),
    'manu_listing_detail_cat' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '[/][:cat]-[:idcat][/][:alias]-[:id]',
            'constraints' => array(
                'alias' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
                'cat' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'idcat' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Manu',
                'action' => 'listing',
            ),
        ),
    ),
    'manu_listing_detail_cat_all' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '[/][:cat]-[:idcat]/tat-ca',
            'constraints' => array(
                'cat' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'idcat' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Manu',
                'action' => 'listing',
            ),
        ),
    ),
    
    'subscription' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
            'route' => '/subscription',
            'defaults' => array(
                'controller' => 'Application\Controller\Index',
                'action' => 'subscription',
            ),
        ),
    ),
    'product_front' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '/san-pham[/][:alias]-[:id]',
            'constraints' => array( 
             'alias' => '[a-zA-Z0-9_-]*',
              'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Product',
                'action' => 'detail',
            ),
        ),
    ),
    'product_front-detail' => array(
        'type' => 'Segment',
        'priority'=>111,
        'options' => array(
            'route' => '[/][:alias]-[:id]',
            'constraints' => array( 
             'alias' => '[a-zA-Z0-9_-]*',
              'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Product',
                'action' => 'detail',
            ),
        ),
    ),
    'buyByEmail_products' => array(
        'type' => 'Segment',
        'priority'=>112,
        'options' => array(
            'route' => '/buyByEmail[/][:alias]-[:id]',
            'constraints' => array( 
             'alias' => '[a-zA-Z0-9_-]*',
              'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Product',
                'action' => 'buyByEmail',
            ),
        ),
    ),
    'popWholesale_products' => array(
        'type' => 'Segment',
        'priority'=>114,
        'options' => array(
            'route' => '/popWholesale[/][:alias]-[:id]',
            'constraints' => array( 
             'alias' => '[a-zA-Z0-9_-]*',
              'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Product',
                'action' => 'popWholesale',
            ),
        ),
    ),
    'quickview_products' => array(
        'type' => 'Segment',
        'priority'=>115,
        'options' => array(
            'route' => '/quickview[/][:alias]-[:id]',
            'constraints' => array( 
             'alias' => '[a-zA-Z0-9_-]*',
              'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Product',
                'action' => 'quickview',
            ),
        ),
    ),
    'heart_products' => array(
        'type' => 'Segment',
        'priority'=>116,
        'options' => array(
            'route' => '/heart[/][:alias]-[:id]',
            'constraints' => array( 
             'alias' => '[a-zA-Z0-9_-]*',
              'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Product',
                'action' => 'heart',
            ),
        ),
    ),
    'tra_gop' => array(
        'type' => 'Segment',
        'priority'=>118,
        'options' => array(
            'route' => '/san-pham/tra-gop/[:alias]-[:id]',
            'constraints' => array(
                'alias' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Product',
                'action' => 'tra-gop',
            ),
        ),
    ),
    'deals' => array(
        'type' => 'Segment',
        'options' => array(
            'route' => '/deals[/[:alias]-[:id]]',
            'constraints' => array(
                'alias' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Product',
                'action' => 'deals',
            ),
        ),
    ),
    'product_front1' => array(
        'type' => 'Segment',
        'priority'=>115,
        'options' => array(
            'route' => '/product[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Product',
                'action' => 'detail',
            ),
        ),
    ),
    'cart' => array(
        'type' => 'Segment',
        'priority'=>116,
        'options' => array(
            'route' => '/cart[/][:action[/:id]]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Cart',
                'action' => 'index',
            ),
        ),
    ),
    'checkout' => array(
        'type' => 'Segment',
        'priority'=>116,
        'options' => array(
            'route' => '/checkout[/][:action[/:id]]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Checkout',
                'action' => 'index',
            ),
        ),
    ),
    'paypal' => array(
        'type' => 'Segment',
        'priority'=>118,
        'options' => array(
            'route' => '/paypal[/][:action[/:id]]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Paypal',
                'action' => 'index',
            ),
        ),
    ),
    'vnpay' => array(
        'type' => 'Segment',
        'priority'=>118,
        'options' => array(
            'route' => '/vnpay[/][:action[/:id]]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Vnpay',
                'action' => 'index',
            ),
        ),
    ),
    'onepay' => array(
        'type' => 'Segment',
        'priority'=>118,
        'options' => array(
            'route' => '/onepay[/][:action[/:id]]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Onepay',
                'action' => 'index',
            ),
        ),
    ),
    'sign-in' => array(
        'type' => 'Segment',
        'priority'=>119,
        'options' => array(
            'route' => '/sign-in[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Login',
                'action' => 'index',
            ),
        ),
    ),
    'login' => array(
        'type' => 'Segment',
        'priority'=>120,
        'options' => array(
            'route' => '/login[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Login',
                'action' => 'index',
            ),
        ),
    ),
    'logout' => array(
        'type' => 'Segment',
        'priority' => 107, 
        'options' => array(
            'route' => '/logout',
            'defaults' => array(
                'controller' => 'Application\Controller\Login',
                'action' => 'logout',
            ),
        ),
    ),
    'sign-up' => array(
        'type' => 'Segment',
        'priority' => 108, 
        'options' => array(
            'route' => '/sign-up',
            'defaults' => array(
                'controller' => 'Application\Controller\Login',
                'action' => 'register',
            ),
        ),
    ),
    
    'profile' => array(
        'type' => 'Segment',
        'priority' => 106, 
        'options' => array(
            'route' => '/profile[/][:action][/:param][/]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'param'      => '(.*)',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Profile',
                'action' => 'index',
            ),
        ),                
    ),

    'faq' => array(
        'type' => 'Segment',
        'priority'=>121,
        'options' => array(
            'route' => '/faq',
            'defaults' => array(
                'controller' => 'Application\Controller\Articles',
                'action' => 'faq'
                //'type' => 'faq',
            ),
        ),
    ),


    'article_news' => array(
        'type' => 'Segment',
        'priority'=>122,
        'options' => array(
            'route' => '/tin-tuc[/][/:id]',
            'constraints' => array(
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Articles',
                'action' => 'tin-tuc'
            ),
        ),
    ),
    'article_r' => array(
        'type' => 'Segment',
        'priority'=>123,
        'options' => array(
            'route' => '/kinh-nghiem/[:title]-[:id]',
            'constraints' => array(
                'action' => 'detail',
                'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Articles',
                'action' => 'detail'
            ),
        ),
    ),
    'helpcenter' => array(
        'type' => 'Segment',
        'priority'=>124,
        'options' => array(
            'route' => '/helpcenter/[:title]-[:id]',
            'constraints' => array(
                'action' => 'detail',
                'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Articles',
                'action' => 'detail'
            ),
        ),
    ),
    'carticle' => array(
        'type' => 'Segment',
        'priority' => 104, 
        'options' => array(
            'route' => '/chuyen-muc/[:title]-[:id]',
            'constraints' => array(
                'action' => 'by-category',
                'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Articles',
                'action' => 'by-category'
            ),
        ),
    ),
    'contact' => array(
        'type' => 'Segment',
        'priority' => 100, 
        'options' => array(
            'route' => '/contact[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Contact',
                'action' => 'index',
            ),
        ),
    ),
    'search' => array(
        'type' => 'Literal',
        'priority' => 1, 
        'options' => array(
            'route' => '/search',
            'defaults' => array(
                'controller' => 'Application\Controller\Search',
                'action' => 'index',
            ),
        ),
    ),

    'tags' => array(
        'type' => 'Segment',
        'priority'=>126,
        'options' => array(
            'route' => '/tags[/][:tag]',
            'constraints' => array(
                'tag' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Search',
                'action' => 'tags',
            ),
        ),
    ),
    'keywords' => array(
        'type' => 'Literal',
        'priority' => 102, 
        'options' => array(
            'route' => '/keywords',
            'defaults' => array(
                'controller' => 'Application\Controller\Keywords',
                'action' => 'index',
            ),
        ),
    ),
    'product_detail_alias' => array(
        'type' => 'segment',
        'options' => array(
            'route' => '[/][:alias]',
            'constraints' => array( 
             'alias' => '[a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Product',
                'action' => 'detail',
            ),
        ),
    ),

    'home' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'priority' => 101, 
        'options' => array(
            'route' => '/',
            'defaults' => array(
                'controller' => 'Application\Controller\Index',
                'action' => 'index',
            ),
        ),
    ),

    'home_rss' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'priority' => 101, 
        'options' => array(
            'route' => '/sitemap.xml',
            'defaults' => array(
                'controller' => 'Application\Controller\Index',
                'action' => 'sitemap',
            ),
        ),
    ),
    
    'index' => array(
        'type' => 'Segment',
        'priority' => 122, 
        'options' => array(
            'route' => '/index[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Index',
                'action' => 'index',
            ),
        ),
    ),
    'language' => array(
        'type' => 'Segment',
        'priority' => 123, 
        'options' => array(
            'route' => '/language[/][:id]',
            'constraints' => array(
                'id' => '[a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Language',
                'action' => 'index',
            ),
        ),
    ),
    
    'service' => array(
        'type' => 'Segment',
        'priority' => 124, 
        'options' => array(
            'route' => '/service[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Service',
                'action' => 'index',
            ),
        ),
    ),

    'websites' => array(
        'type' => 'Segment',
        'priority' => 125, 
        'options' => array(
            'route' => '/websites[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Websites',
                'action' => 'index',
            ),
        ),
    ),

    'domain' => array(
        'type' => 'Segment',
        'priority' => 126, 
        'options' => array(
            'route' => '/domain[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Domain',
                'action' => 'index',
            ),
        ),
    ),
    'image' => array(
        'type' => 'Segment',
        'priority' => 120, 
        'options' => array(
            'route' => '/image',
            'defaults' => array(
                'controller' => 'Application\Controller\Images',
                'action' => 'index',
            ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
            'full' => array(
                'type'    => 'Segment',
                'priority' => 127, 
                'options' => array(
                    'route' => '/[:y]/[:m]/[:d]/[:params].[:ex]',
                    'constraints' => array(
                      'y' => '[0-9]+',
                      'm' => '[0-9]+',
                      'd' => '[0-9]+',
                      'params' => '[a-zA-Z][a-zA-Z0-9_-]*',
                      'ex' => '(jpg|png|gif|pdf|JPG|PNG|GIF|PDF)*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Images',
                        'action' => 'index',
                    ),
                ),
            ),
            'sm' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/[:params].[:ex]',
                    'constraints' => array(
                      'params' => '[a-zA-Z][a-zA-Z0-9_-]*',
                      'ex' => '(jpg|png|gif|pdf|JPG|PNG|GIF|PDF)*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Images',
                        'action' => 'index',
                    ),
                ),
            ),
            'placeholder' => array(
                'type'    => 'Segment',
                'priority' => 128, 
                'options' => array(
                    'route' => '/placeholder[/][:title]-[:dimensions].[:ex]',
                    'constraints' => array(
                      'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                      'dimensions' => '[0-9x]+',
                      'ex' => '[a-z]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Images',
                        'action' => 'placeholder',
                    ),
                ),
            ),
            'capture' => array(
                'type'    => 'Segment',
                'priority' => 129, 
                'options' => array(
                    'route' => '/capture[/][:title]-[:dimensions].[:ex]',
                    'constraints' => array(
                      'title' => '[a-zA-Z][a-zA-Z0-9_-]*',
                      'dimensions' => '[0-9x]+',
                      'ex' => '[a-z]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Images',
                        'action' => 'capture',
                    ),
                ),
            ),
        ),
    ),

    'websites_demo' => array(
        'type' => 'Segment',
        'priority' => 128, 
        'options' => array(
            'route' => '/demo/[:alias]',
            'constraints' => array(
                'action' => 'demo',
                'alias' => '[a-zA-Z0-9_-]*'
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Websites',
                'action' => 'demo'
            ),
        ),
    ),

    'project' => array(
        'type' => 'Segment',
        'priority' => 129, 
        'options' => array(
            'route' => '/project[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Project',
                'action' => 'index',
            ),
        ),
    ),

    'js' => array(
        'type' => 'Segment',
        'priority' => 130, 
        'options' => array(
            'route' => '/js/[:name].js',
            'constraints' => array(
                'action' => 'index',
                'name' => '[a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Js',
                'action' => 'index'
            ),
        ),
    ),

    'css' => array(
        'type' => 'Segment',
        'priority' => 131, 
        'options' => array(
            'route' => '/css/[:name].css',
            'constraints' => array(
                'action' => 'index',
                'name' => '[a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Css',
                'action' => 'index'
            ),
        ),
    ),
    'error' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'priority' => 133, 
        'options' => array(
            'route' => '/error',
            'defaults' => array(
                'controller' => 'Application\Controller\Error',
                'action' => 'index',
            ),
        )
    ),
    'theme' => array(
        'type' => 'Segment',
        'priority' => 132, 
        'options' => array(
            'route' => '/theme',
            'defaults' => array(
                'controller' => 'Application\Controller\Theme',
                'action' => 'index',
            ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
            'default' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/[:alias]-[:id]',
                    'constraints' => array(
                      'alias' => '[a-zA-Z0-9_-]*',
                      'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Theme',
                        'action' => 'detail',
                    ),
                ),
            ),
        ),
    ),
    
    'statistical' => array(
        'type' => 'Segment',
        'priority' => 134, 
        'options' => array(
            'route' => '/statistical[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Statistical',
                'action' => 'index',
            ),
        ),
    ),

    'facebook' => array(
        'type' => 'Segment',
        'priority' => 134, 
        'options' => array(
            'route' => '/facebook[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Facebook',
                'action' => 'index',
            ),
        ),
    ),

    'google' => array(
        'type' => 'Segment',
        'priority' => 134, 
        'options' => array(
            'route' => '/google[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Google',
                'action' => 'index',
            ),
        ),
    ),

    'location' => array(
        'type' => 'Segment',
        'priority' => 134, 
        'options' => array(
            'route' => '/location[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Location',
                'action' => 'index',
            ),
        ),
    ),

    'country' => array(
        'type' => 'Segment',
        'priority' => 134, 
        'options' => array(
            'route' => '/country[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Country',
                'action' => 'index',
            ),
        ),
    ),

    'cities' => array(
        'type' => 'Segment',
        'priority' => 134, 
        'options' => array(
            'route' => '/cities[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Cities',
                'action' => 'index',
            ),
        ),
    ),

    'district' => array(
        'type' => 'Segment',
        'priority' => 134, 
        'options' => array(
            'route' => '/district[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\District',
                'action' => 'index',
            ),
        ),
    ),

    'ward' => array(
        'type' => 'Segment',
        'priority' => 134, 
        'options' => array(
            'route' => '/ward[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Ward',
                'action' => 'index',
            ),
        ),
    ),

    'features' => array(
        'type' => 'Segment',
        'priority' => 108, 
        'options' => array(
            'route' => '/features',
            'defaults' => array(
                'controller' => 'Application\Controller\Features',
                'action' => 'index',
            ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
            'default' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route' => '/[:id]',
                    'constraints' => array(
                      'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Application\Controller\Features',
                        'action' => 'detail',
                    ),
                ),
            ),
        ),
    ),

    'assign' => array(
        'type' => 'Segment',
        'priority' => 134, 
        'options' => array(
            'route' => '/assign[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Assign',
                'action' => 'index',
            ),
        ),
    ),

    'invoice' => array(
        'type' => 'Segment',
        'priority' => 134, 
        'options' => array(
            'route' => '/invoice[/][:action]',
            'constraints' => array(
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
            ),
            'defaults' => array(
                'controller' => 'Application\Controller\Invoice',
                'action' => 'index',
            ),
        ),
    )
);
$all_router = $router;
$all_router['router_vi']  = array(
    'type' => 'Literal',
    'options' => array(
        'route' => '/au',
        'defaults' => array(
            '__NAMESPACE__' => 'Application\Controller',
            'controller' => 'Index',
            'action' => 'index',
        ),
    ),
    'defaults' => array(
        'controller' => 'Application\Controller\Index',
        'action' => 'index',
    ),
    'may_terminate' => true,
    'child_routes' => $router
);
$all_router['router_en']  = array(
    'type' => 'Literal',
    'options' => array(
        'route' => '/en',
        'defaults' => array(
            '__NAMESPACE__' => 'Application\Controller',
            'controller' => 'Index',
            'action' => 'index',
        ),
    ),
    'defaults' => array(
        'controller' => 'Application\Controller\Index',
        'action' => 'index',
    ),
    'may_terminate' => true,
    'child_routes' => $router
);
$all_router['application']  = array(
    'type' => 'Literal',
    'options' => array(
        'route' => '/application',
        'defaults' => array(
            '__NAMESPACE__' => 'Application\Controller',
            'controller' => 'Index',
            'action' => 'index',
        ),
    ),
    'may_terminate' => true,
    'child_routes' => array(
        'default' => array(
            'type'    => 'Segment',
            'options' => array(
                'route'    => '/[:controller[/:action][/:param]][/]',
                //'route'    => '/[:controller[/:action]',
                'constraints' => array(
                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'param'      => '(.*)',
                ),
                'defaults' => array(
                ),
            ),
        ),
    ),
);

return array(
    'router' => array(
        'routes' => $all_router
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
                'text_domain' => __NAMESPACE__,
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Categories' => 'Application\Controller\CategoriesController',
            'Application\Controller\Product' => 'Application\Controller\ProductController',
            'Application\Controller\Cart' => 'Application\Controller\CartController',
            'Application\Controller\Login' => 'Application\Controller\LoginController',
            'Application\Controller\Language' => 'Application\Controller\LanguageController',
            'Application\Controller\Search' => 'Application\Controller\SearchController',
            'Application\Controller\Articles' => 'Application\Controller\ArticlesController',
            'Application\Controller\Profile' => 'Application\Controller\ProfileController',
            'Application\Controller\Contact' => 'Application\Controller\ContactController',
            'Application\Controller\Manu' => 'Application\Controller\ManuController',
            'Application\Controller\Keywords' => 'Application\Controller\KeywordsController',
            'Application\Controller\Service' => 'Application\Controller\ServiceController',
            'Application\Controller\Websites' => 'Application\Controller\WebsitesController',
            'Application\Controller\Price' => 'Application\Controller\PriceController',
            'Application\Controller\Images' => 'Application\Controller\ImagesController',
            'Application\Controller\Project' => 'Application\Controller\ProjectController',
            'Application\Controller\Domain' => 'Application\Controller\DomainController',
            'Application\Controller\Js' => 'Application\Controller\JsController',
            'Application\Controller\Css' => 'Application\Controller\CssController',
            'Application\Controller\Error' => 'Application\Controller\ErrorController',
            'Application\Controller\Paypal' => 'Application\Controller\PaypalController',
            'Application\Controller\Onepay' => 'Application\Controller\OnepayController',
            'Application\Controller\Vnpay' => 'Application\Controller\VnpayController',
            'Application\Controller\Theme' => 'Application\Controller\ThemeController',
            'Application\Controller\Statistical' => 'Application\Controller\StatisticalController',
            'Application\Controller\Checkout' => 'Application\Controller\CheckoutController',
            'Application\Controller\Facebook' => 'Application\Controller\FacebookController',
            'Application\Controller\Google' => 'Application\Controller\GoogleController',
            'Application\Controller\Location' => 'Application\Controller\LocationController',
            'Application\Controller\Country' => 'Application\Controller\CountryController',
            'Application\Controller\Cities' => 'Application\Controller\CitiesController',
            'Application\Controller\District' => 'Application\Controller\DistrictController',
            'Application\Controller\Ward' => 'Application\Controller\WardController',
            'Application\Controller\Features' => 'Application\Controller\FeaturesController',
            'Application\Controller\Assign' => 'Application\Controller\AssignController',
            'Application\Controller\Invoice' => 'Application\Controller\InvoiceController',
        ),
    ),
     'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_layout' => 'app/layout',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'app/layout' => __DIR__ . '/../view/Default/app/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/Default/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/Default/error/404.phtml',
            'error/index' => __DIR__ . '/../view/Default/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view/Default',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        )
    ),
    
    'console' => array(
        'router' => array(
            'routes' => array(),
        ),
    ),
    'session' => array(
        'remember_me_seconds' => 1200,
        'use_cookies' => true,
        'cookie_httponly' => true,
        'cookie_domain' => 'aromatix.fr',
    ),
);