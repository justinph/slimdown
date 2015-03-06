<?php

/**
 * Set Application Root to this directory's parent
 * Use this constant when referencing server paths of other files
 */
define('APP_ROOT', realpath(dirname(dirname(__FILE__)) . '/'));

//include composer autolaod 
require APP_ROOT.'/vendor/autoload.php';

//start slim
$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Smarty(),
    'templates.path' => APP_ROOT . '/app/templates'
));


$view = $app->view();
$view->parserCompileDirectory =  '/tmp/compiled';
$view->parserCacheDirectory = '/tmp/cache';

//our only route(s)
$app->get('/', '\SlimDown\SlimdownBase:staticPage');
$app->get('/:slug+', '\SlimDown\SlimdownBase:staticPage');

//run that sucker
$app->run();