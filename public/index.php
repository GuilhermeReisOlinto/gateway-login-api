<?php

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/DependenciesFactory.php";


use Slim\Factory\AppFactory;
use GuzzleHttp\Psr7\Response;

AppFactory::setResponseFactory(new \GuzzleHttp\Psr7\HttpFactory());
AppFactory::setStreamFactory(new \GuzzleHttp\Psr7\HttpFactory());

$dependence = DependenciesFactory::create();

$app = $dependence['app'];
$consumer = $dependence['consumer'];

$server = DependenciesFactory::server($app, $consumer);

$server->start();