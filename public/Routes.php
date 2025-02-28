<?php

use App\Authentication\HandlerTokenJwt;
use App\Authentication\MiddlewareToken;
use App\HandlerCustomer\HandlerCustomerController;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

$app = AppFactory::create();

$jwtHandler = new HandlerTokenJwt();
$app->add(new MiddlewareToken($jwtHandler));

$app->post("/", function(Request $request, Response $response, $arg) {
    return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
});

$app->post("/customer", HandlerCustomerController::class . ':test');
$app->get("/customer", [HandlerCustomerController::class, 'test']);


$app->get('/customer/{documentNumber}', [HandlerCustomerController::class, 'test']);

$app->run();