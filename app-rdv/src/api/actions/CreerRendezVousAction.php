<?php

namespace toubilib\api\actions;

use Exception;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;

class CreerRendezVousAction
{
    private ServiceRendezVousInterface $serviceRendezVous;

    public function __construct(ServiceRendezVousInterface $serviceRendezVous)
    {
        $this->serviceRendezVous = $serviceRendezVous;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            // récup dto créer par middleware
            $dto = $request->getAttribute('inputRendezVousDTO');

            $rdvDTO = $this->serviceRendezVous->creerRendezVous($dto);

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