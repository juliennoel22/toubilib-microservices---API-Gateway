<?php

namespace toubilib\infra\messaging;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class EventPublisher
{
    private string $host;
    private int $port;
    private string $user;
    private string $password;
    private string $exchange;

    public function __construct(string $host, int $port, string $user, string $password, string $exchange)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->exchange = $exchange;
    }

    public function publish(array $eventData, string $routingKey): void
    {
        $connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
        $channel = $connection->channel();

        $channel->exchange_declare(
            $this->exchange,  
            'topic',          
            false,            
            true,            
            false            
        );

        $msg = new AMQPMessage(
            json_encode($eventData),
            ['delivery_mode' => 2] 
        );
        
        $channel->basic_publish($msg, $this->exchange, $routingKey);

        $channel->close();
        $connection->close();
    }
}
