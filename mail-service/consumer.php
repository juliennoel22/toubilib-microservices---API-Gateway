<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/vendor/autoload.php';

$queue = 'rdv_notifications';

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'toubi', 'toubi');
$channel = $connection->channel();

echo "[*] En attente de messages. CTRL+C pour quitter.\n";

$callback = function(AMQPMessage $msg) {
    $data = json_decode($msg->getBody(), true);
    
    if (!$data) {
        echo "[X] Invalid message format\n";
        $msg->getChannel()->basic_nack($msg->getDeliveryTag(), false, false);
        return;
    }
    
    echo "\n[x] {$data['event_type']} - RDV {$data['rdv_id']} - {$data['date_heure']}\n";
    
    $msg->getChannel()->basic_ack($msg->getDeliveryTag());
    echo "[>] Traité\n";
};

$channel->basic_consume($queue, '', false, false, false, false, $callback);

try {
    $channel->consume();
} catch (Exception $e) {
    echo $e->getMessage();
}

$channel->close();
$connection->close();
