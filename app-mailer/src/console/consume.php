<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$host = 'rabbitmq';
$port = 5672;
$user = 'toubi';
$pass = 'toubi';
$queue = 'toubi.mails';

echo "Démarrage du consommateur de mails...\n";

$connection = null;
while (true) {
    try {
        $connection = new AMQPStreamConnection($host, $port, $user, $pass);
        break;
    } catch (\Exception $e) {
        echo "En attente de RabbitMQ...\n";
        sleep(5);
    }
}

$channel = $connection->channel();

// S'assurer que la queue existe (au cas où le setup n'a pas tourné)
$channel->queue_declare($queue, false, true, false, false);

// Initialisation unique de PHPMailer pour réutilisation
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = getenv('SMTP_HOST') ?: 'mail.toubi';
    $mail->Port = getenv('SMTP_PORT') ?: 1025;
    $mail->SMTPAuth = false;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('no-reply@toubilib.fr', 'Toubilib Notif');
    // Garder la connexion SMTP ouverte
    $mail->SMTPKeepAlive = true;
} catch (Exception $e) {
    echo "Erreur config mailer: " . $e->getMessage() . "\n";
}

echo " [*] En attente de messages dans '$queue'.\n";

$callback = function ($msg) use ($mail) {
    echo " [x] Reçu : " . $msg->body . "\n";

    $data = json_decode($msg->body, true);
    $event = $data['event'] ?? 'unknown';
    $payload = $data['data'] ?? [];

    switch ($event) {
        case 'rdv.created':
            sendEmail($mail, $payload, 'created');
            break;
        case 'rdv.cancelled':
            sendEmail($mail, $payload, 'cancelled');
            break;
        default:
            echo " [!] Type d'événement inconnu : $event\n";
    }

    $msg->ack();
};

$channel->basic_qos(0, 1, false);
$channel->basic_consume($queue, '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
$mail->smtpClose();

function sendEmail(PHPMailer $mail, $data, $type)
{
    try {
        // Nettoyage des destinataires précédents
        $mail->clearAddresses();

        $recipient = !empty($data['email_patient']) ? $data['email_patient'] : 'patient@example.com';
        $mail->addAddress($recipient);

        // Contenu
        $styleBody = "font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;";
        $styleH1 = "color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;";
        $styleFooter = "margin-top: 20px; font-size: 0.8em; color: #777; border-top: 1px solid #eee; padding-top: 10px;";

        if ($type === 'created') {
            $mail->Subject = 'Confirmation de RDV - Toubilib';
            $mail->Body = "
                <div style='$styleBody'>
                    <h1 style='$styleH1'>Rendez-vous Confirmé</h1>
                    <p>Bonjour,</p>
                    <p>Votre rendez-vous a bien été enregistré.</p>
                    <ul>
                        <li><strong>Date et Heure :</strong> {$data['date_heure']}</li>
                        <li><strong>Motif :</strong> {$data['motif']}</li>
                    </ul>
                    <p>Merci de votre confiance.</p>
                    <div style='$styleFooter'>L'équipe Toubilib</div>
                </div>
            ";
            $mail->AltBody = "Rendez-vous confirmé.\nDate: {$data['date_heure']}\nMotif: {$data['motif']}";
        } elseif ($type === 'cancelled') {
            $mail->Subject = 'Annulation de RDV - Toubilib';
            $mail->Body = "
                <div style='$styleBody'>
                    <h1 style='$styleH1; color: #e74c3c; border-bottom-color: #e74c3c;'>Rendez-vous Annulé</h1>
                    <p>Bonjour,</p>
                    <p>Le rendez-vous suivant a été annulé :</p>
                    <ul>
                        <li><strong>Date et Heure :</strong> {$data['date_heure']}</li>
                    </ul>
                    <p>Nous sommes désolés pour ce désagrément.</p>
                    <div style='$styleFooter'>L'équipe Toubilib</div>
                </div>
            ";
            $mail->AltBody = "Rendez-vous annulé.\nDate: {$data['date_heure']}";
        }

        $mail->send();
        echo " [v] Email ($type) envoyé à $recipient\n";
    } catch (Exception $e) {
        echo " [x] Erreur lors de l'envoi du mail: {$mail->ErrorInfo}\n";
    }
}
