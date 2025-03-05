<?php

use App\HandlerCustomer\HandlerCustomerController;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class RoutesApplication
{
    public function routes($app)
    {
        $app->post("/", function(Request $request, Response $response, $arg) {

            $response->getBody()->write('enviado');
            return $response->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
        });
        
        $app->post("/customer", HandlerCustomerController::class . ':test');
        
        $app->get("/customer", [HandlerCustomerController::class, 'test']);
        
        
        $app->get('/customer/{documentNumber}', [HandlerCustomerController::class, 'test']);
    }
}
