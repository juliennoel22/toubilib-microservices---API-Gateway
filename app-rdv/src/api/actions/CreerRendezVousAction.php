<?php

namespace toubilib\api\actions;

use Exception;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;
use toubilib\infra\messaging\EventPublisher;

class CreerRendezVousAction
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
            $dto = $request->getAttribute('inputRendezVousDTO');
            $rdvDTO = $this->serviceRendezVous->creerRendezVous($dto);

            $eventData = [
                'event_type' => 'CREATE',
                'rdv_id' => $rdvDTO->id,
                'praticien_id' => $dto->praticien_id,
                'patient_id' => $dto->patient_id,
                'date_heure' => $dto->date_heure,
                'duree' => $dto->duree,
                'destinataires' => [
                    ['type' => 'praticien', 'id' => $dto->praticien_id],
                    ['type' => 'patient', 'id' => $dto->patient_id]
                ]
            ];

            $this->eventPublisher->publish($eventData, 'rdv.create');

            $response->getBody()->write(json_encode($rdvDTO));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
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