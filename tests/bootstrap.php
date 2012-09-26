<?php
declare( encoding = "UTF8" ) ;
/**
 *
 * Created by: Henrik Pejer, mr@henrikpejer.com
 * Date: 2012-07-24 00:18
 *
 */
define('APP_ROOT',NULL);
date_default_timezone_set('Europe/Stockholm'); # OR @date_default_timezone_set(date_default_timezone_get());
$routes = NULL;
include_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'strap.php';
$loader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->register(TRUE);
$loader->registerNamespace('undertow', UNDERTOW_ROOT);
$loader->registerNamespaceFallback(substr(APP_ROOT, 0, -1));
