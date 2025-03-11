<?php

namespace App\Redis;

use Exception;
use Predis\Client as PredisClient;

class RedisConnections
{
    private $host;
    private $port;
    private $redis;
    
    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        
        $this->redis = new PredisClient([
            'scheme' => 'tcp',
            'host'   => $this->host,
            'port'   => $this->port,
        ]);

        try {
            $this->redis->ping();
            echo "Conectado ao Redis com sucesso!";
        } catch (Exception $e) {
            echo "Conectado ao Redis com erro!";
            throw new Exception('Redis connection failed');
        }
    }

    public function send($key, $value)
    {
        return $this->redis->set($key, $value);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }
}