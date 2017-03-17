<?php

namespace Sandbox;

use Dez\DependencyInjection\Container;
use Dez\EventDispatcher\Dispatcher;
use Dez\Http\Request;
use Dez\Router\Router;
use Dez\Url\Builder;
use Dez\Url\Url;

error_reporting(1);
ini_set('display_errors', 1);
include_once '../vendor/autoload.php';

$di = Container::instance();

$di->set('eventDispatcher', new Dispatcher());

$di->set('router', function () {
  return new Router();
});

$di->set('request', new Request());

$di->set('url', function () {
  $url = new Url();
  $url->setBasePath('/dez-url/sandbox/');
  $url->setStaticPath('/dez-url/sandbox/media/');
  return $url;
});

/**
 * @var $url Url
 * @var $router Router
 */
$router = $di->get('router');
$url = $di->get('url');

$router->add('/:hash/:format/stat_download.html', [
  'controller' => 'stat',
  'action' => 'download_file'
]);


$router->add('/admin_panel/:hash/:page.html', [
  'controller' => 'admin',
  'action' => 'index'
]);

$router->add('/:action/:id/product.html', [
  'controller' => 'products',
]);

$router->add('/:controller/:action.:format/:module-:do/:p/:statusCode');
$router->add('/:controller/:auth_driver-:action/:format/:id/:back_url');
$router->add('/:controller');
$router->add('/:controller/:action');
$router->add('/:module/:controller/:action');
$router->add('/:controller/:action/:id' );
$router->add('/:controller/:action/:token');
$router->add('/:controller/:action.:format/:module-:do/:params/:statusCode')->regex('format', 'html|json');

$router->handle('/products/order');

header('content-type: text/plain');

echo $url->create('backend:users:api', 'format=json&do=doDeletedUsers&p=700145&statusCode=OK') . PHP_EOL;

echo $url->create('backend:users:list-api', [
    'format' => 'json',
    'do' => 'export_data',
    'p' => 53,
    'statusCode' => 500
  ]) . PHP_EOL;
// /users/list-api.json/backend-export_data/53/500

echo $url->create('user:login', [
    'auth_driver' => 'oauth2',
    'format' => 'json',
    'id' => 9871,
    'back_url' => 'google'
  ]) . PHP_EOL;
// /dez-url/sandbox/user/oauth2-login/json/9871/google

echo (new Builder('stat:download_file', [
    'format' => 'csv',
    'hash' => md5(time())
  ], $router))->make() . PHP_EOL; // /219e6c3c6e6c2d015bfb09d749fd5082/csv/stat_download.html

echo (new Builder('admin:index', [
    'page' => 'dashboard',
    'hash' => md5(time())
  ], $router))->make() . PHP_EOL; // /admin_panel/28726b1ce51b5cb4d500c3ad70e9054e/dashboard.html

echo (new Builder('products', [], $router))->make() . PHP_EOL;
// /products

echo (new Builder('products:order', [], $router))->make() . PHP_EOL;
// /products/order

echo (new Builder('products:order', [
    'id' => 53
  ], $router))->make() . PHP_EOL;
// /products/order/53

echo $url->create('products:item', ['id' => 53,], ['order_id' => 341,]) . PHP_EOL;
// /dez-url/sandbox/products/item/53?order_id=341

echo $url->create('stat:download_file', [
    'format' => 'csv',
    'hash' => md5(time())
  ], ['go' => 'dashboard',]) . PHP_EOL;
// /dez-url/sandbox/3784bb74f03cf1ca05600e5a7ddb8103/csv/stat_download.html?go=dashboard

echo $url->staticPath('js/jquery.min.js') . PHP_EOL;
// /dez-url/sandbox/media/js/jquery.min.js

echo $url->full('product/5378', ['customer_id' => 8451], 'tab-order') . PHP_EOL;
// http://my.local/dez-url/sandbox/product/5378?customer_id=8451#tab-order

echo $url->create('product:view') . PHP_EOL;
// http://my.local/dez-url/sandbox/product/5378?customer_id=8451#tab-order

