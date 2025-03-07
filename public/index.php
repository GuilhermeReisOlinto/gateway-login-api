<?php

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/DependenciesFactory.php";

use Swoole\Http\Server;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Swoole\Coroutine;
use GuzzleHttp\Psr7\ServerRequest;
use Slim\Factory\AppFactory;
use GuzzleHttp\Psr7\Response;

AppFactory::setResponseFactory(new \GuzzleHttp\Psr7\HttpFactory());
AppFactory::setStreamFactory(new \GuzzleHttp\Psr7\HttpFactory());

$dependence = DependenciesFactory::create();

$app = $dependence['app'];
$consumer = $dependence['consumer'];

$server = new Server('0.0.0.0', 9501);

$server->on('start', function (Server $server) {
    echo "Servidor HTTP rodando em http://0.0.0.0:9501\n";
});

$server->on('request', function (SwooleRequest $swooleRequest, SwooleResponse $swooleResponse) use ($app) {
    $request = ServerRequest::fromGlobals();

    $response = $app->handle($request);

    foreach ($response->getHeaders() as $name => $values) {
        $swooleResponse->header($name, implode(', ', $values));
    }
    $swooleResponse->status($response->getStatusCode());
    $swooleResponse->end($response->getBody());
});

$server->on('workerStart', function (Server $server, int $workerId) use ($consumer) {
    echo "Worker $workerId iniciado\n";

    if ($workerId === 0) {

        Coroutine::create(function () use ($consumer) {
            while (true) {
                $message = $consumer->consume(100);

                if ($message->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
                    echo "Mensagem recebida: " . $message->payload . "\n";
                }

                Coroutine::sleep(0.01);
            }
        });
    }
});

$server->start();