<?php
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            'kh-lien-he' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/lien-he-mua-ngay(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Product',
                        'action' => 'contactkh',
                        'format' => 'jp',
                    ),
                    'spec' => 'lien-he-mua-ngay.%format%'
                )
            ),
            'landingpage' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)(\.(?<format>(ladi)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Landingpage',
                        'action' => 'index',
                        'format' => 'ladi',
                    ),
                    'spec' => '%slug%.%format%'
                )
            ),

            'search-ajax' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/search/ajax',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Search',
                        'action' => 'ajaxSearch',
                    ),
                ),
            ),

            'review-product-detail' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/review\/(?<slug>[a-zA-Z0-9-]+)-sp-(?<id>[0-9-]+)(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Product',
                        'action' => 'reviewproduct',
                        'format' => 'jp',
                    ),
                    'spec' => 'review\/%slug%-sp-%id%.%format%'
                )
            ),
            'comment' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/comment[/:action]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Comment',
                        'action' => 'index',
                    ),
                ),
            ),
            'checkouttemp' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/checkouttemp[/:action]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\CheckoutTemp',
                        'action' => 'index',
                    ),
                ),
            ),
            'category' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/category[/:action]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Category',
                        'action' => 'index',
                    ),
                ),
            ),
            'order' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/order[/:action]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Order',
                        'action' => 'index',
                    ),
                ),
            ),
            'payment' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/payment[/:action]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Payment',
                        'action' => 'index',
                    ),
                ),
            ),
            'version' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/version[/:action]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Version',
                        'action' => 'index',
                    ),
                ),
            ),
            'event' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)-event(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Event',
                        'action' => 'index',
                        'format' => 'jp',
                    ),
                    'spec' => '%slug%-event.%format%'
                )
            ),

            'event-pagination' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)-event(\.(?<format>(jp)))\/p\=(?<page>[0-9-]+)?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Event',
                        'action' => 'index',
                        'page' => '[0-9]+',
                        'format' => 'jp',
                    ),
                    'spec' => '%slug%-event.%format%\/p\=%page%'
                )
            ),

            'event_1' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/event\/(?<slug>[a-zA-Z0-9-]+)',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Event',
                        'action' => 'index'
                    ),
                    'spec' => 'event\/%slug%'
                )
            ),

            'Tags' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)-tags(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Tags',
                        'action' => 'index',
                        'format' => 'jp',
                    ),
                    'spec' => '%slug%-tags.%format%'
                )
            ),

            'thongbao' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/thongbao(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\User',
                        'action' => 'messinfo',
                        'format' => 'jp',
                    ),
                    'spec' => 'thongbao.%format%'
                )
            ),
            'updatepwdtonew' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/lay-lai-mat-khau(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\User',
                        'action' => 'updatepwdtonew',
                        'format' => 'jp',
                    ),
                    'spec' => 'lay-lai-mat-khau.%format%'
                )
            ),
            'consulting' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/phone-consulting(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Product',
                        'action' => 'consulting',
                        'format' => 'jp',
                    ),
                    'spec' => 'phone-consulting.%format%'
                )
            ),
            'contact' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/lien-he(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Contact',
                        'action' => 'index',
                        'format' => 'jp',
                    ),
                    'spec' => 'lien-he.%format%'
                )
            ),
            'checkmobile' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/kiem-tra-so-dien-thoai(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Cart',
                        'action' => 'checkmobile',
                        'format' => 'jp',
                    ),
                    'spec' => 'kiem-tra-so-dien-thoai.%format%'
                )
            ),

            'staticpages' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)-static-(?<id>[0-9-]+)(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Static',
                        'action' => 'index',
                        'format' => 'jp',
                    ),
                    'spec' => '%slug%-static-%id%.%format%'
                )
            ),

            'static' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/static(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Static',
                        'action' => 'static',
                        'format' => 'jp',
                    ),
                    'spec' => 'static.%format%'
                )
            ),

            'search' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/search(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Search',
                        'action' => 'index',
                        'format' => 'jp',
                    ),
                    'spec' => 'search.%format%'
                )
            ),

            'search-pagination' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/search\.jp\/p\=(?<page>[0-9-]+)?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Search',
                        'action' => 'index',
                        'page' => '[0-9]+',
                    ),
                    'spec' => 'search\.jp\/p\=%page%'
                )
            ),

            'checkpwdcur' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/kiem-tra-mat-khau-hien-tai(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\User',
                        'action' => 'checkpwdcur',
                        'format' => 'jp',
                    ),
                    'spec' => 'kiem-tra-mat-khau-hien-tai.%format%'
                )
            ),
            'updatepwd' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/cap-nhat-mat-khau(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\User',
                        'action' => 'updatepwd',
                        'format' => 'jp',
                    ),
                    'spec' => 'cap-nhat-mat-khau.%format%'
                )
            ),
            'userprofiles' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/thong-tin-tai-khoan(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\User',
                        'action' => 'userprofiles',
                        'format' => 'jp',
                    ),
                    'spec' => 'thong-tin-tai-khoan.%format%'
                )
            ),
            'reviewcart' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/theo-doi-don-hang(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Cart',
                        'action' => 'reviewcart',
                        'format' => 'jp',
                    ),
                    'spec' => 'theo-doi-don-hang.%format%'
                )
            ),
            'giohang' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/gio-hang(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Cart',
                        'action' => 'index',
                        'format' => 'jp',
                    ),
                    'spec' => 'gio-hang.%format%'
                )
            ),
            'checkout' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/checkout(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Cart',
                        'action' => 'checkout',
                        'format' => 'jp',
                    ),
                    'spec' => 'checkout.%format%'
                )
            ),
            'thank' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/thank(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Cart',
                        'action' => 'thank',
                        'format' => 'jp',
                    ),
                    'spec' => 'thank.%format%'
                )
            ),
            'userprofiles' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/thong-tin-tai-khoan(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\User',
                        'action' => 'userprofiles',
                        'format' => 'jp',
                    ),
                    'spec' => 'thong-tin-tai-khoan.%format%'
                )
            ),



            'product-detail1' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)-sp-(?<id>[0-9-]+)(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Product',
                        'action' => 'index',
                        'format' => 'jp',
                    ),
                    'spec' => '%slug%-sp-%id%.%format%'
                )
            ),


            'product-detail' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)-sp-(?<id>[0-9-]+)?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Product',
                        'action' => 'index',
                        'format' => 'jp_t',
                    ),
                    'spec' => '%slug%-sp-%id%'
                )
            ),

            'product-category' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)\/',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Productcategory',
                        'action' => 'index'
                    ),
                    'spec' => '%slug%'
                )
            ),
            'product-category-pagination' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)\/p\=(?<page>[0-9-]+)?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Productcategory',
                        'action' => 'index',
                        'page' => '[0-9]+',
                    ),
                    'spec' => '%slug%\/p\=%page%'
                )
            ),
            'brand-index' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/thuong-hieu(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Brand',
                        'action' => 'index'
                    ),
                    'spec' => 'thuong-hieu.%format%'
                )
            ),
            'brand' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)-brand.jp',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Brand',
                        'action' => 'list'
                    ),
                    'spec' => '%slug%-brand.jp'
                )
            ),
            'brand1' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/brand\/(?<slug>[a-zA-Z0-9-]+)',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Brand',
                        'action' => 'list'
                    ),
                    'spec' => 'brand\/%slug%'
                )
            ),
            'brand-pagination' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)-brand.jp\/p\=(?<page>[0-9-]+)?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Brand',
                        'action' => 'list',
                        'page' => '[0-9]+',
                    ),
                    'spec' => '%slug%-brand.jp\/p\=%page%'
                )
            ),

            /* Link Trang */
            'newscategory' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/news(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\News',
                        'action' => 'index',
                        'format' => 'jp',
                    ),
                    'spec' => 'news.%format%'
                )
            ),
            'newscategory-pagination' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/news\/p\=(?<page>[0-9-]+)?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\News',
                        'action' => 'index',
                        'page' => '[0-9]+',
                    ),
                    'spec' => 'news\/p\=%page%'
                )
            ),
            'newsdetail' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)-news-(?<id>[0-9-]+)(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\News',
                        'action' => 'detail',
                        'format' => 'jp',
                    ),
                    'spec' => '%slug%-news-%id%.%format%'
                )
            ),
            'newsdetailreview' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/review\/(?<slug>[a-zA-Z0-9-]+)-news-(?<id>[0-9-]+)(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\News',
                        'action' => 'reviewdetail',
                        'format' => 'jp',
                    ),
                    'spec' => 'review\/%slug%-news-%id%.%format%'
                )
            ),
            'newslist' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)-list-(?<id>[0-9-]+)(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\News',
                        'action' => 'list',
                        'format' => 'jp',
                    ),
                    'spec' => '%slug%-list-%id%.%format%'
                )
            ),
            'newslist-padding' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/(?<slug>[a-zA-Z0-9-]+)-list-(?<id>[0-9-]+)(\.(?<format>(jp))\/p\=(?<page>[0-9-]+)?)?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\News',
                        'action' => 'list',
                        'format' => 'jp',
                    ),
                    'spec' => '%slug%-list-%id%.%format%\/p\=%page%'
                )
            ),
            'storageall' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/barcode-in-file-all(\.(?<format>(json)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Barcode',
                        'action' => 'storageall',
                        'format' => 'json',
                    ),
                    'spec' => 'barcode-in-file-all.%format%'
                )
            ),
            'barcode' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/barcode-in-file(\.(?<format>(json)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Barcode',
                        'action' => 'index',
                        'format' => 'json',
                    ),
                    'spec' => 'barcode-in-file.%format%'
                )
            ),
            'qrLink' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/qr-(?<id>[0-9-]+)(\.(?<format>(jp)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Barcode',
                        'action' => 'qrlink',
                        'format' => 'jp',
                    ),
                    'spec' => 'qr-%id%.%format%'
                )
            ),
            'bill' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/printbill-in-file(\.(?<format>(json)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Barcode',
                        'action' => 'bill',
                        'format' => 'json',
                    ),
                    'spec' => 'printbill-in-file.%format%'
                )
            ),
            'tem' => array(
                'type' => 'regex',
                'options' => array(
                    'regex' => '/printtem-in-file(\.(?<format>(json)))?',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Barcode',
                        'action' => 'tem',
                        'format' => 'json',
                    ),
                    'spec' => 'printtem-in-file.%format%'
                )
            ),
            'api' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/api',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(),
                        ),
                    ),
                ),
            ),

            'product-detail-new' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/product-detail-new[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Api\Controller\HomeNew',
                        'action' => 'viewProductDetail',
                    ),
                ),
            ),

            'product-category-new' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/product-category-new[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Api\Controller\HomeNew',
                        'action' => 'viewProductCategory',
                    ),
                ),
            ),

            'product-cart' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/product-cart[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Api\Controller\HomeNew',
                        'action' => 'viewCart',
                    ),
                ),
            ),

            'product-checkout' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/product-checkout[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Api\Controller\HomeNew',
                        'action' => 'viewCheckout',
                    ),
                ),
            ),

            'load-product' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/load-product[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Api\Controller\LoadProduct',
                        'action' => 'index',
                    ),
                ),
            ),

            'load-product-news' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/load-product-news[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Api\Controller\LoadProductNews',
                        'action' => 'index',
                    ),
                ),
            ),

            'export-product' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/export-product[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Api\Controller\ExportProduct',
                        'action' => 'index',
                    ),
                ),
            ),

            'customer-history' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/customer-history[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Api\Controller\CustomerHistory',
                        'action' => 'index',
                    ),
                ),
            ),

            'statistic-customer' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/statistic-customer[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Api\Controller\StatisticCustomer',
                        'action' => 'index',
                    ),
                ),
            ),

            'callback-status' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/callback-status[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Api\Controller\Callback',
                        'action' => 'updateStatus',
                    ),
                ),
            ),
            'address' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/address[/:action]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Address',
                        'action' => 'index',
                    ),
                ),
            ),
            'notification' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/notification[/:action]',
                    'defaults' => array(
                        'controller' => 'Api\Controller\Notification',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
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
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Api\Controller\Index' => 'Api\Controller\IndexController',
            'Api\Controller\Cart' => 'Api\Controller\CartController',
            'Api\Controller\Productcategory' => 'Api\Controller\ProductcategoryController',
            'Api\Controller\Product' => 'Api\Controller\ProductController',
            'Api\Controller\Checkout' => 'Api\Controller\CheckoutController',
            'Api\Controller\Brand' => 'Api\Controller\BrandController',
            'Api\Controller\User' => 'Api\Controller\UserController',
            'Api\Controller\News' => 'Api\Controller\NewsController',
            'Api\Controller\Search' => 'Api\Controller\SearchController',
            'Api\Controller\Static' => 'Api\Controller\StaticController',
            'Api\Controller\Contact' => 'Api\Controller\ContactController',
            'Api\Controller\Event' => 'Api\Controller\EventController',
            'Api\Controller\Tags' => 'Api\Controller\TagsController',
            'Api\Controller\Emailregisted' => 'Api\Controller\EmailregistedController',
            'Api\Controller\Ajaxs' => 'Api\Controller\AjaxsController',
            'Api\Controller\Webhook' => 'Api\Controller\WebhookController',
            'Api\Controller\Barcode' => 'Api\Controller\BarcodeController',
            'Api\Controller\Landingpage' => 'Api\Controller\LandingpageController',
            'Api\Controller\HomeNew' => 'Api\Controller\HomeNewController',
            'Api\Controller\LoadProduct' => 'Api\Controller\LoadProductController',
            'Api\Controller\LoadProductNews' => 'Api\Controller\LoadProductNewsController',
            'Api\Controller\ExportProduct' => 'Api\Controller\ExportProductController',
            'Api\Controller\Callback' => 'Api\Controller\CallbackController',
            'Api\Controller\StatisticCustomer' => 'Api\Controller\StatisticCustomerController',
            'Api\Controller\CustomerHistory' => 'Api\Controller\CustomerHistoryController',
            'Api\Controller\Comment' => 'Api\Controller\CommentController',
            'Api\Controller\Apiorder' => 'Api\Controller\ApiorderController',
            'Api\Controller\Address' => 'Api\Controller\AddressController',
            'Api\Controller\CheckoutTemp' => 'Api\Controller\CheckoutTempController',
            'Api\Controller\Version' => 'Api\Controller\VersionController',
            'Api\Controller\Category' => 'Api\Controller\CategoryController',
            'Api\Controller\Payment' => 'Api\Controller\PaymentController',
            'Api\Controller\Order' => 'Api\Controller\OrderController',
            'Api\Controller\Notification' => 'Api\Controller\NotificationController',
            'Api\Controller\Otp' => 'Api\Controller\OtpController',
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(),
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'product_full_width' => 'Api\Block\Product\ProductFullWidth',
            'product_3_column' => 'Api\Block\Product\Product3Column',
            'banner_pro3' => 'Api\Block\Product\BannerPro3',

            'product_time1' => 'Api\Block\Product\ProductTime1',
            'product_time2' => 'Api\Block\Product\ProductTime2',
            'product_filter1' => 'Api\Block\Product\ProductFilter1',
            'product_detail' => 'Api\Block\Product\ProductDetail',
            'product_item' => 'Api\Block\Product\ProductItem',
            'product1_column' => 'Api\Block\Product\Product1Column',
            'product1_column1' => 'Api\Block\Product\Product1Column1',
            'product_6item' => 'Api\Block\Product\Product6Item',
            'product_tags' => 'Api\Block\Product\ProductTags',

            'banner1' => 'Api\Block\Banner\Banner1',
            'banner2' => 'Api\Block\Banner\Banner2',
            'banner3' => 'Api\Block\Banner\Banner3',
            'banner4' => 'Api\Block\Banner\Banner4',
            "banner_full_width" => 'Api\Block\Banner\BannerFullWidth',

            'countdown1' => 'Api\Block\CountDown\CountDown1',
            'countdown2' => 'Api\Block\CountDown\CountDown2',

            'slider_brand' => 'Api\Block\Slider\SliderBrand',
            'slider_home' => 'Api\Block\Slider\SliderHome',
            'slider_product' => 'Api\Block\Slider\SliderProduct',
            'slider_product_km' => 'Api\Block\Slider\SliderProductKm',
            'slider_product_sale_top' => 'Api\Block\Slider\SliderProductSaleTop',

            'login_register' => 'Api\Block\Member\LoginRegister',
            'checkout' => 'Api\Block\Cart\Checkout',
            'reviewcart' => 'Api\Block\Cart\Reviewcart',
            'cart_index' => 'Api\Block\Cart\Cartindex',

            'user_left' => 'Api\Block\User\Userleft',
            'userlogin' => 'Api\Block\User\Userlogin',
            'cutString' => 'Api\View\Helper\CutString',
            'news_slide' => 'Api\Block\News\NewsSlide',
            'news_slider_item' => 'Api\Block\News\NewsSliderItem',
            'NewsList' => 'Api\Block\News\NewsList',
            'NewsIndex' => 'Api\Block\News\NewsIndex',
            'SideNews' => 'Api\Block\News\SideNews',
            'NewsDetail' => 'Api\Block\News\NewsDetail',
            'BestSeller' => 'Api\Block\News\BestSeller',
            'header' => 'Api\Block\Header',
            'footer' => 'Api\Block\Footer',
            'blockfull' => 'Api\Block\BlockFull'
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        
        
        
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'frontend/index/index'    => __DIR__ . '/../view/api/index/index.phtml',
            'frontend/productcategory/index'    => __DIR__ . '/../view/api/productcategory/index.phtml',
            'frontend/product/index'    => __DIR__ . '/../view/api/product/index.phtml',
            'frontend/product/detail'    => __DIR__ . '/../view/api/product/detail.phtml',
            'frontend/cart/index'    => __DIR__ . '/../view/api/cart/index.phtml',
            'frontend/contact/index'    => __DIR__ . '/../view/api/contact/index.phtml',
            'frontend/checkout/index'    => __DIR__ . '/../view/api/checkout/index.phtml',
            'block/product/block1'    => __DIR__ . '/../view/api/block/product/block1/block1.phtml',
            'frontend/user/userprofiles'    => __DIR__ . '/../view/api/user/userprofiles.phtml',
            'frontend/user/updatepwd'    => __DIR__ . '/../view/api/user/updatepwd.phtml',
            'frontend/user/updatepwdtonew'    => __DIR__ . '/../view/api/user/updatepwdtonew.phtml',
            'frontend/cart/reviewcart'    => __DIR__ . '/../view/api/cart/reviewcart.phtml',
            'frontend/news/index'    => __DIR__ . '/../view/api/news/index.phtml',
            'frontend/news/detail'    => __DIR__ . '/../view/api/news/detail.phtml',
            'frontend/news/list'    => __DIR__ . '/../view/api/news/list.phtml',
            'frontend/comment/list' => __DIR__ . '/../view/api/comment/list.phtml',
            'frontend/comment/load_comment' => __DIR__ . '/../view/api/comment/load_comment.phtml',
            'frontend/comment/load_comment_ajax' => __DIR__ . '/../view/api/comment/load_comment_ajax.phtml',
            'frontend/comment/load_comment_image' => __DIR__ . '/../view/api/comment/load_comment_image.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'view/api/pagination'           	=> __DIR__ . '/../view/api/pagination.phtml',
            
            /* Banner */
            "banner/banner4" => __DIR__."/../view/api/block/banner/banner4/banner4.phtml",
            "banner/banner3" => __DIR__."/../view/api/block/banner/banner3/banner3.phtml",
            "banner/banner2" => __DIR__."/../view/api/block/banner/banner2/banner2.phtml",
            "banner/banner1" => __DIR__."/../view/api/block/banner/banner1/banner1.phtml",
            "banner/banner_full_width" => __DIR__."/../view/api/block/banner/banner_full_width/banner_full_width.phtml",
            
            /* Countdown */
            'countdown/countdown1' => __DIR__."/../view/api/block/count_down/count_down1/count_down1.phtml",
            'countdown/countdown2' => __DIR__."/../view/api/block/count_down/count_down2/count_down2.phtml",
            
            /* News */
            'news/news'         => __DIR__."/../view/api/block/news/news.phtml",
            'news/news_slide'    => __DIR__."/../view/api/block/news/news_slide.phtml",
            'news/news_slider_item'    => __DIR__."/../view/api/block/news/news_slider_item.phtml",
            'news/news_list'    => __DIR__."/../view/api/block/news/news_list.phtml",
            'news/news_index'    => __DIR__."/../view/api/block/news/news_index.phtml",
            'news/news_detail'    => __DIR__."/../view/api/block/news/news_detail.phtml",
            'news/side_news'    => __DIR__."/../view/api/block/news/side_news.phtml",
            
            /* Product */
            "product/product1_column" => __DIR__."/../view/api/block/product/product1_column/product1_column.phtml",
            //            "product/product1_column1" => __DIR__."/../view/api/block/product/product1_column1/product1_column1.phtml",
            "product/product1_column1" => __DIR__."/../view/api/block/product/product1_column1/product1_column1_new.phtml",
            "product/product3_column" => __DIR__."/../view/api/block/product/product3_column/product3_column.phtml",
            //            "product/product_detail" => __DIR__."/../view/api/block/product/product_detail/product_detail.phtml",
            "product/product_detail" => __DIR__."/../view/api/block/product/product_detail/product_detail_new.phtml",
            
            "product/product_item" => __DIR__."/../view/api/block/product/product_item/product_item.phtml",
            
            "product/product_filter1" => __DIR__."/../view/api/block/product/product_filter1/product_filter1.phtml",
            "product/product_full_width" => __DIR__."/../view/api/block/product/product_full_width/product_full_width.phtml",
            "product/product_time1" => __DIR__."/../view/api/block/product/product_time1/product_time1.phtml",
            "product/product_time2" => __DIR__."/../view/api/block/product/product_time2/product_time2.phtml",
            "product/product_tags" => __DIR__."/../view/api/block/product/product_tags/product_tags.phtml",
            "product/banner_pro3" => __DIR__."/../view/api/block/product/banner_pro3/banner_pro3.phtml",
            "product/product_6item" => __DIR__."/../view/api/block/product/product_6item/product_6item.phtml",
            /* Slider */
            'slider/slider_home' => __DIR__ . "/../view/api/block/slider/slider_home/slider_home.phtml",
            'slider/slider_brand' => __DIR__ . "/../view/api/block/slider/slider_brand/slider_brand.phtml",
            //            'slider/slider_product' => __DIR__ . "/../view/api/block/slider/slider_product/slider_product.phtml",
            'slider/slider_product' => __DIR__ . "/../view/api/block/slider/slider_product/slider_product_new.phtml",
            'slider/slider_product_km' => __DIR__ . "/../view/api/block/slider/slider_product/slider_product_km.phtml",
            'slider/slider_product_sale_top' => __DIR__ . "/../view/api/block/slider/slider_product/slider_product_sale_top.phtml",
            
            /* User */
            'user/userleft' => __DIR__ . "/../view/api/block/user/userleft.phtml",
            'user/userlogin' => __DIR__ . "/../view/api/block/user/userlogin.phtml",
            
            'block/header'            => __DIR__ . '/../view/api/block/header/header.phtml',
            'block/footer'            => __DIR__ . '/../view/api/block/footer/footer.phtml',
            
            'block/blockfull' => __DIR__ . "/../view/api/block/block_full.phtml",
            
            /* Cart */
            "cart/checkout" => __DIR__ . '/../view/api/block/cart/checkout.phtml',
            "cart/reviewcart" => __DIR__ . '/../view/api/block/cart/reviewcart.phtml',
            "cart/cartindex" => __DIR__ . '/../view/api/block/cart/cartindex.phtml',
            
            /* layout new */
            'frontend/home-new/view-product-detail'  => __DIR__ . "/../view/api/block/product/product_detail/product_detail_new.phtml",
            'frontend/home-new/view-product-category'  => __DIR__ . "/../view/api/productcategory/index_new.phtml",
            'frontend/home-new/view-cart'  => __DIR__ . "/../view/api/cart/index_new.phtml",
            'frontend/home-new/view-checkout'  => __DIR__ . "/../view/api/cart/checkout_new.phtml",
            //            'frontend/home-new/view-home'  => __DIR__ . "/../view/api/index/index_new.phtml",
            
            'frontend/load-product/load-data'  => __DIR__ . "/../view/api/loadproduct/load-data.phtml",
            'frontend/load-product/load-data-together'  => __DIR__ . "/../view/api/loadproduct/load-data-together.phtml",
            'frontend/load-product/load-data-content-product'  => __DIR__ . "/../view/api/loadproduct/load-data-content.phtml",
            'frontend/load-product-news/load-data'  => __DIR__ . "/../view/api/loadproductnews/load-data.phtml",
        
        ),
        'template_path_stack' => array(
            'application' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);