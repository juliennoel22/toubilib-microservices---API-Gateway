<?php
namespace toubilib\api\actions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use toubilib\core\application\ports\api\ServiceIndisponibiliteInterface;

class SupprimerIndisponibiliteAction {
    private ServiceIndisponibiliteInterface $serviceIndisponibilite;

    public function __construct(ServiceIndisponibiliteInterface $serviceIndisponibilite) {
        $this->serviceIndisponibilite = $serviceIndisponibilite;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $indisponibiliteId = $request->getAttribute('indispo_id');
        
        try {
            $this->serviceIndisponibilite->supprimerIndisponibilite($indisponibiliteId);
            
            $result = [
                'type' => 'success',
                'message' => 'Indisponibilité supprimée avec succès'
            ];
            
            $response->getBody()->write(json_encode($result));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $error = [
                'type' => 'error',
                'error' => 404,
                'message' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
    }
}