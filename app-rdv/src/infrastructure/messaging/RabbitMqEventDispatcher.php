<?php

namespace toubilib\infra\messaging;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use toubilib\core\application\ports\spi\EventDispatcherInterface;

class RabbitMqEventDispatcher implements EventDispatcherInterface
{
    private string $host = 'rabbitmq';
    private int $port = 5672;
    private string $user = 'toubi';
    private string $pass = 'toubi';
    private string $exchange = 'toubilib.events';

    public function dispatch(string $eventName, array $data): void
    {
        try {
            $connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->pass);
            $channel = $connection->channel();

            // S'assurer que l'exchange existe (idempotent)
            $channel->exchange_declare($this->exchange, 'direct', false, true, false);

            $msgBody = json_encode([
                'event' => $eventName,
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            $msg = new AMQPMessage($msgBody, ['content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

            $channel->basic_publish($msg, $this->exchange, $eventName);

            $channel->close();
            $connection->close();
        } catch (\Exception $e) {
            // Log error but don't block the application
            error_log("Erreur RabbitMQ: " . $e->getMessage());
        }
    }
}
