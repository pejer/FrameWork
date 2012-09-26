<?php
declare( encoding = "UTF8" ) ;
date_default_timezone_set('Europe/Stockholm'); # OR @date_default_timezone_set(date_default_timezone_get());
include_once __DIR__.DIRECTORY_SEPARATOR.'strap.php';

try {
    if ( !isset( $routes ) && file_exists(APP_ROOT . 'routes.php') ) {
        $routes = require_once( APP_ROOT . 'routes.php' );
    }
    $loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
    $loader->register(TRUE);
    $loader->registerNamespace('undertow', UNDERTOW_ROOT);
    $loader->registerNamespaceFallback(substr(APP_ROOT, 0, -1));
    $DI = new undertow\dependencyinjection\DependencyInjection( new undertow\dependencyinjection\ContainerOfObjects() );

    $DI->register('Event', 'undertow\\event\\Event');
    $DI->register('Router', 'undertow\\router\\Router');
    $DI->register('Response', 'undertow\\response\\Response');
    $DI
      ->register('Kernel', 'undertow\\kernel\HttpKernel')
      ->setArguments(array($DI));
    $k = $DI->get('Kernel');
    $k->setRoutes($routes);
}
catch (\Exception $e) {
    var_dump($e->getMessage());
}

try{
    $event = $DI->get('Event');
    $event->trigger('system.load');
    $event->trigger('system.preboot');
    $k->boot();
    $event->trigger('system.postboot');
    $event->trigger('system.end');
}catch(RuntimeException $e){  #4xx error go here...?
    var_dump($e->getCode().':'.$e->getMessage());
}catch(ErrorException $e){    #5xx error go here...?
    var_dump($e->getCode().':'.$e->getMessage());
}