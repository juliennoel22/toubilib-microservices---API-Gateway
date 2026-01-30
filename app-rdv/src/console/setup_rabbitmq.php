<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;

$host = 'rabbitmq';
$port = 5672;
$user = 'toubi';
$pass = 'toubi';

echo "Tentative de connexion à RabbitMQ...\n";

// Attendre que RabbitMQ soit prêt
$maxRetries = 10;
$connection = null;

for ($i = 0; $i < $maxRetries; $i++) {
    try {
        $connection = new AMQPStreamConnection($host, $port, $user, $pass);
        break;
    } catch (\Exception $e) {
        echo "RabbitMQ non prêt, nouvel essai dans 5s...\n";
        sleep(5);
    }
}

if (!$connection) {
    die("Impossible de se connecter à RabbitMQ après plusieurs essais.\n");
}

$channel = $connection->channel();

// 1. Déclaration de l'Exchange
$exchange = 'toubilib.events';
$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
echo "Exchange '$exchange' déclaré.\n";

// 2. Déclaration de la Queue
$queue = 'toubi.mails';
$channel->queue_declare($queue, false, true, false, false);
echo "Queue '$queue' déclarée.\n";

// 3. Binding (Liaison)
$routingKey = 'rdv.created';
$channel->queue_bind($queue, $exchange, 'rdv.created');
echo "Binding '$exchange' -> '$queue' (clé: 'rdv.created') créé.\n";

// Binding pour l'annulation
$channel->queue_bind($queue, $exchange, 'rdv.cancelled');
echo "Binding '$exchange' -> '$queue' (clé: 'rdv.cancelled') créé.\n";

$channel->close();
$connection->close();

echo "Configuration RabbitMQ terminée avec succès.\n";
