<?php

namespace toubilib\api\actions;

use Exception;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;

class AnnulerRendezVousAction
{
    private ServiceRendezVousInterface $serviceRendezVous;

    public function __construct(ServiceRendezVousInterface $serviceRendezVous)
    {
        $this->serviceRendezVous = $serviceRendezVous;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $id = $request->getAttribute('id');

            $this->serviceRendezVous->annulerRendezVous($id);

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