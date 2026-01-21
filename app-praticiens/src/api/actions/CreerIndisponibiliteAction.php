<?php
namespace toubilib\api\actions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use toubilib\core\application\ports\api\ServiceIndisponibiliteInterface;

class CreerIndisponibiliteAction {
    private ServiceIndisponibiliteInterface $serviceIndisponibilite;

    public function __construct(ServiceIndisponibiliteInterface $serviceIndisponibilite) {
        $this->serviceIndisponibilite = $serviceIndisponibilite;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $praticienId = $request->getAttribute('id');
        $data = $request->getParsedBody();
        
        $required = ['date_debut', 'date_fin', 'motif', 'type'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $error = [
                    'type' => 'error',
                    'error' => 400,
                    'message' => "Field {$field} is required"
                ];
                $response->getBody()->write(json_encode($error));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        }

        try {
            $indisponibilite = $this->serviceIndisponibilite->creerIndisponibilite(
                $praticienId,
                $data['date_debut'],
                $data['date_fin'],
                $data['motif'],
                $data['type']
            );
            
            $result = [
                'type' => 'success',
                'indisponibilite' => [
                    'id' => $indisponibilite->id,
                    'praticien_id' => $indisponibilite->praticien_id,
                    'date_debut' => $indisponibilite->date_debut,
                    'date_fin' => $indisponibilite->date_fin,
                    'motif' => $indisponibilite->motif,
                    'type' => $indisponibilite->type
                ],
                'links' => [
                    'self' => ['href' => "/praticiens/{$praticienId}/indisponibilites/{$indisponibilite->id}"],
                    'praticien' => ['href' => "/praticiens/{$praticienId}"]
                ]
            ];
            
            $response->getBody()->write(json_encode($result));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $error = [
                'type' => 'error',
                'error' => 400,
                'message' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }
}