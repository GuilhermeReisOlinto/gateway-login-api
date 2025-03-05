<?php

require __DIR__ . "/RoutesApplication.php";

use App\Authentication\HandlerTokenJwt;
use App\Authentication\MiddlewareToken;
use Slim\App;
use Slim\Factory\AppFactory;

class DependenciesFactory 
{
    public static function create(): array
    {
        $key  = 'metadata.broker.list';
        $host = '192.168.100.3:9092';
        $groupId = 'group.id'; 
        $group = 'myConsumerGroup';
        $auto = 'auto.offset.reset';
        $earlist = 'earliest';

        $kafka = new ConsumerKafka($key, $host, $groupId, $group, $auto, $earlist);
        $kafka->consumer();

        $app = AppFactory::create();

        $jwtHandler = new HandlerTokenJwt();
        $app->add(new MiddlewareToken($jwtHandler));
     
        $routes = new RoutesApplication();
        $routes->routes($app);

        return $dependence = [ 
            'app'      => $app,
            'consumer' => $kafka
        ];
    }
}