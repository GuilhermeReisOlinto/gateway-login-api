<?php

namespace  App\HandlerCustomer;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class HandlerCustomerController
{
    public function test(Request $request, Response $response) {
        var_dump('aqui');
        return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
    }
}