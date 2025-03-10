<?php

require __DIR__ . "/RoutesApplication.php";
require __DIR__ . "/ConsumerKafka.php";

use App\Authentication\HandlerTokenJwt;
use App\Authentication\MiddlewareToken;
use App\Redis\RedisConnections;
use Slim\Factory\AppFactory;
use Swoole\Http\Server;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Swoole\Coroutine;
use GuzzleHttp\Psr7\ServerRequest;

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
        $consumer = $kafka->consumer();

        $app = AppFactory::create();

        $jwtHandler = new HandlerTokenJwt();
        $app->add(new MiddlewareToken($jwtHandler));

        $routes = new RoutesApplication();
        $routes->routes($app);

        return $dependence = [ 
            'app'      => $app,
            'kafka' => $kafka,
            'consumer' => $consumer
        ];
    }

    public static function server($app, $consumer)
    {

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
                        $message = $consumer->consume(1000);
                        
                        if ($message->err === RD_KAFKA_RESP_ERR_NO_ERROR) {
                            echo "Mensagem recebida: " . $message->payload . "\n";

                            $redis = new RedisConnections("127.0.0.1", 6379);
                            $redis->send("kafka", $message->payload);
                        }
                        
                        Coroutine::sleep(0.01);
                    }
                });
            }
        });
        
        return $server;
    }
}