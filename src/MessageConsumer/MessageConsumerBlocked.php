<?php

use RdKafka;

class MessageConsumer
{
    public function createConsumer(){
        $conf = new RdKafka\Conf();
        $conf->set('customer-create', 'customer-group');
        $conf->set('metadata.broker.list', '192.168.100.3:9092');

        $consumer->subscribe(['Customer-created']);

        while (true) {
            $message = $consumer->consume(1000);

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    echo "Message recebida: " . $message->payload . "\n";
                    echo "Partição: " . $message->partition . "\n";
                    echo "Offset: " . $message->offset . "\n";
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "Fim da partição, aguardando novas mensagens...\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timeout, aguardando.../n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }
   

}