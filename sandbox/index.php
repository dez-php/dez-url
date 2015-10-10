<?php

    namespace Sandbox;

    use Dez\DependencyInjection\Container;
    use Dez\EventDispatcher\Dispatcher;
    use Dez\Http\Request;
    use Dez\Router\Router;
    use Dez\Url\Uri;
    use Dez\Url\Url;

    error_reporting(1); ini_set('display_errors', 1);
    include_once '../vendor/autoload.php';

    $di = Container::instance();

    $di->set( 'eventDispatcher', new Dispatcher() );

    $di->set( 'router', function() {
        return new Router();
    } );

    $di->set( 'request', new Request() );

    $di->set( 'url', new Url() );

    /**
     * @var $url Url
     * @var $router Router
     */
    $router     = $di->get( 'router' );
    $url        = $di->get( 'url' );
    $url->setBasePath( '/dez-url/sandbox/' );

    $router->add( '/:controller/:auth_driver-:action/:format/:id' );
    $router->add( '/:controller' );
    $router->add( '/:controller/:action' );
    $router->add( '/:controller/:action/:id' );
    $router->add( '/:controller/:action/:token' );
    $router->add( '/:controller/:action.:format/:module-:do/:params/:statusCode' )->regex( 'format', 'html|json' );

    $route = $router->handle()->getMatchedRoute();

//    $uri    = new Uri( 'https://user:pass@github.com:8888/dez-php/dez-url?var=1&test=123qwe#test-anchor' );

//    $uri    = new Uri( '/user/123' );

    var_dump( $url->path( 'product/53' ) );