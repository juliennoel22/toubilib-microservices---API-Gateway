<?php

namespace toubilib\api\actions;

use Exception;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;
use toubilib\infra\messaging\EventPublisher;

class AnnulerRendezVousAction
{
    private ServiceRendezVousInterface $serviceRendezVous;
    private EventPublisher $eventPublisher;

    public function __construct(ServiceRendezVousInterface $serviceRendezVous, EventPublisher $eventPublisher)
    {
        $this->serviceRendezVous = $serviceRendezVous;
        $this->eventPublisher = $eventPublisher;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $id = $request->getAttribute('id');

            $rdv = $this->serviceRendezVous->consulterRendezVous($id);
            
            $this->serviceRendezVous->annulerRendezVous($id);

            $eventData = [
                'event_type' => 'CANCEL',
                'rdv_id' => $id,
                'praticien_id' => $rdv->praticien_id,
                'patient_id' => $rdv->patient_id,
                'date_heure' => $rdv->date_heure_debut,
                'duree' => $rdv->duree,
                'destinataires' => [
                    ['type' => 'praticien', 'id' => $rdv->praticien_id],
                    ['type' => 'patient', 'id' => $rdv->patient_id]
                ]
            ];

            $this->eventPublisher->publish($eventData, 'rdv.cancel');

            $response->getBody()->write(json_encode([
                'message' => 'Rendez-vous annulé avec succès'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }
}