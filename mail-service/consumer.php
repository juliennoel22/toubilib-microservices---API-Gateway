<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require_once __DIR__ . '/vendor/autoload.php';

$queue = 'rdv_notifications';

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'toubi', 'toubi');
$channel = $connection->channel();

echo "[*] En attente de messages. CTRL+C pour quitter.\n";

$callback = function(AMQPMessage $msg) {
    $msg_body = json_decode($msg->body, true);
    
    echo "\n[x] Message reçu:\n";
    echo "Event Type: " . $msg_body['event_type'] . "\n";
    echo "RDV ID: " . $msg_body['rdv_id'] . "\n";
    echo "Praticien ID: " . $msg_body['praticien_id'] . "\n";
    echo "Patient ID: " . $msg_body['patient_id'] . "\n";
    echo "Date: " . $msg_body['date_heure'] . "\n";
    echo "Durée: " . $msg_body['duree'] . " minutes\n";
    echo "Destinataires: " . count($msg_body['destinataires']) . "\n";
    echo json_encode($msg_body, JSON_PRETTY_PRINT) . "\n";
    
    $msg->getChannel()->basic_ack($msg->getDeliveryTag());
    echo "[✓] Message traité\n";
};

$channel->basic_consume($queue, '', false, false, false, false, $callback);

try {
    $channel->consume();
} catch (Exception $e) {
    echo $e->getMessage();
}

$channel->close();
$connection->close();
