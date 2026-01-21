<?php
namespace toubilib\api\actions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use toubilib\core\application\ports\api\ServiceIndisponibiliteInterface;

class ListerIndisponibilitesAction {
    private ServiceIndisponibiliteInterface $serviceIndisponibilite;

    public function __construct(ServiceIndisponibiliteInterface $serviceIndisponibilite) {
        $this->serviceIndisponibilite = $serviceIndisponibilite;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $praticienId = $request->getAttribute('id');
        
        try {
            $indisponibilites = $this->serviceIndisponibilite->listerIndisponibilitesPraticien($praticienId);
            
            $result = [
                'type' => 'resources',
                'count' => count($indisponibilites),
                'indisponibilites' => $indisponibilites,
                'links' => [
                    'self' => ['href' => "/praticiens/{$praticienId}/indisponibilites"],
                    'praticien' => ['href' => "/praticiens/{$praticienId}"],
                    'create' => ['href' => "/praticiens/{$praticienId}/indisponibilites"]
                ]
            ];
            
            $response->getBody()->write(json_encode($result));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
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