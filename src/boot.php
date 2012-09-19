<?php
declare( encoding = "UTF8" ) ;
date_default_timezone_set('Europe/Stockholm'); # OR @date_default_timezone_set(date_default_timezone_get());
require_once( __DIR__ . DIRECTORY_SEPARATOR . 'constants.php' );
require_once ( __DIR__ . D_S . 'Symfony' . D_S . 'Component' . D_S . 'ClassLoader' . D_S . 'UniversalClassLoader.php' );

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
$e = $DI->get('Event');

#$DI->get('undertow\\response\\Response');

$e->register('start', function(&$one) {
    $one = 'ändrat ett';
    return 'This is the text';
});
$e->register('start', function(&$one) {
    $one = 'ändrat twå!';
    return \undertow\event\EVENT_ABORT;
});
$e->register('start', function(&$one) {
    $one = 'ändrat tre!';
    return 'This is not gonna get called';
});

/*$e->register('router.redirect',function(&$redirectUrl){
    $redirectUrl = str_replace('/page/1','/page/22',$redirectUrl);
});
*/
$t = 'test';
var_dump($t);
echo "This is returned : " . $e->trigger('start', $t);
var_dump($t);
var_dump($k->DIgetParameter('henrik', 'Fan, inte satt'));
$k->DIsetParameter('henrik', 'Pejer');
var_dump($k->DIgetParameter('henrik', 'Fan, inte satt'));

$k->boot();
$e->trigger('system.end');

echo ( microtime(TRUE) - BENCHMARK_START_TIME ) . "s<br>";
echo ( ( ( memory_get_peak_usage() ) / 1024 ) / 1024 ) . "Mb";
?>
