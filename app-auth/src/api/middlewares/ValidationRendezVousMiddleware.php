<?php

namespace toubilib\api\middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\InputRendezVousDTO;

class ValidationRendezVousMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $data = $request->getParsedBody();


        $requiredFields = ['praticien_id', 'patient_id', 'date_heure', 'motif_visite', 'duree'];
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'error' => 'Champs manquants',
                'missing_fields' => $missingFields
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }


        $dateHeure = \DateTime::createFromFormat('Y-m-d H:i:s', $data['date_heure']);
        if (!$dateHeure) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'error' => 'Format de date invalide. Attendu: Y-m-d H:i:s'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }


        if (!is_numeric($data['duree']) || (int)$data['duree'] <= 0) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                'error' => 'La durée doit être un entier positif'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $dto = new InputRendezVousDTO(
            $data['praticien_id'],
            $data['patient_id'],
            $data['date_heure'],
            $data['motif_visite'],
            (int)$data['duree']
        );

        // Ajouter le DTO à la requête pour le récupérer dans l'action
        $request = $request->withAttribute('inputRendezVousDTO', $dto);

        return $handler->handle($request);
    }
}