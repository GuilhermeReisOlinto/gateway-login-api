<?php

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/DependenciesFactory.php";

use Swoole\Http\Server;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

$dependence = DependenciesFactory::create();

$app = $dependence['app'];
$consumer = $dependence['consumer'];

$server = new Server('0.0.0.0', 9001);

$server->on('start', function (Server $server) {
    echo "Servidor HTTP rodando em http://0.0.0.0:9501\n";
});

$server->on('request', function (SwooleRequest $swooleRequest, SwooleResponse $swooleResponse) use ($app) {
    $request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
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
        while (true) {
            $message = $consumer->consume(1000);

            if ($message->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
                echo "Mensagem recebida: " . $message->payload . "\n";
            }
        }
    }
});

$server->start();