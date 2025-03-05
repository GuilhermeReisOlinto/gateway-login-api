<?php

use RdKafka\Conf;
use RdKafka\KafkaConsumer;

class ConsumerKafka
{   
    private $kafkaConf;
    
    public function __construct($key, $host, $groupId, $group, $auto, $earlist)
    {
        $this->kafkaConf = new Conf();
        $this->kafkaConf->set($groupId, $group);
        $this->kafkaConf->set($key, $host);
        $this->kafkaConf->set($auto, $earlist);
    }

    public function consumer()
    {
        $consumer = new KafkaConsumer($this->kafkaConf);
        $consumer->subscribe(['Customer-created']);
        
        return $consumer;
    }    
}