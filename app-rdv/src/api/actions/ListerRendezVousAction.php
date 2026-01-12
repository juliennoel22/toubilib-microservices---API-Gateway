<?php
namespace toubilib\api\actions;

use Exception;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;

class ListerRendezVousAction
{
    private ServiceRendezVousInterface $service_rendez_vous;

    public function __construct(ServiceRendezVousInterface $service_rendez_vous)
    {
        $this->service_rendez_vous = $service_rendez_vous;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $rdvs = $this->service_rendez_vous->ListerRendezVous();
            $data = [
                'type' => 'resources',
                // 'count' => count($rdvs),
                'rdvs' => $rdvs,
                'links' => [
                    'self' => ['href' => 'http://localhost:6080/rdvs']
                ]
            ];
            
            $response->getBody()->write(json_encode($data));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
                
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'message' => '500 Internal Server Error',
                'error' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }
}