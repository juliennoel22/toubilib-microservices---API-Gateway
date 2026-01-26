<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require_once __DIR__ . '/vendor/autoload.php';

$smtpHost = getenv('SMTP_HOST') ?: 'mail.toubi';
$smtpPort = getenv('SMTP_PORT') ?: '1025';
$dsn = "smtp://{$smtpHost}:{$smtpPort}";

$transport = Transport::fromDsn($dsn);
$mailer = new Mailer($transport);

$exchange = 'toubilib_events';
$queue = 'rdv_notifications';

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'toubi', 'toubi');
$channel = $connection->channel();

// exchange
$channel->exchange_declare(
    $exchange,    
    'topic',      
    false,      
    true,       
    false        
);

//  queue
$channel->queue_declare(
    $queue,      
    false,       
    true,       
    false,      
    false     
);

$channel->queue_bind($queue, $exchange, 'rdv.create');
$channel->queue_bind($queue, $exchange, 'rdv.cancel');

echo "[*] Exchange '$exchange' et queue '$queue' configurés\n";
echo "[*] En attente de messages. CTRL+C pour quitter.\n";

$callback = function(AMQPMessage $msg) use ($mailer) {
    $data = json_decode($msg->getBody(), true);
    
    if (!$data) {
        echo "[X] Format de message invalide\n";
        $msg->getChannel()->basic_nack($msg->getDeliveryTag(), false, false);
        return;
    }
    
    echo "\n[x] {$data['event_type']} - RDV {$data['rdv_id']} - {$data['date_heure']}\n";
    
    foreach ($data['destinataires'] as $dest) {
        $email = (new Email())
            ->from('noreply@toubilib.fr')
            ->subject("RDV {$data['event_type']} - {$data['rdv_id']}");
        
        if ($dest['type'] === 'praticien') {
            $email->to("praticien.{$dest['id']}@toubilib.fr")
                ->text("Un rendez-vous a été {$data['event_type']} le {$data['date_heure']} (Durée: {$data['duree']} min)");
        } elseif ($dest['type'] === 'patient') {
            $email->to("patient.{$dest['id']}@toubilib.fr")
                ->text("Votre rendez-vous a été {$data['event_type']} le {$data['date_heure']} (Durée: {$data['duree']} min)");
        }
        
        try {
            $mailer->send($email);
            echo "[✓] Mail envoyé à {$dest['type']} {$dest['id']}\n";
        } catch (Exception $e) {
            echo "[X] Erreur envoi mail: {$e->getMessage()}\n";
        }
    }
    
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
