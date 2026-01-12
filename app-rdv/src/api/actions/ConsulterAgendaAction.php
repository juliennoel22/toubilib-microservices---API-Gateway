<?php
namespace toubilib\api\actions;

use Exception;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;

class ConsulterAgendaAction
{
    private ServiceRendezVousInterface $serviceRendezVous;

    public function __construct(ServiceRendezVousInterface $serviceRendezVous)
    {
        $this->serviceRendezVous = $serviceRendezVous;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $praticienId = $request->getAttribute('id');
            $queryParams = $request->getQueryParams();
            
            $dateDebut = $queryParams['date_debut'] ?? null;
            $dateFin = $queryParams['date_fin'] ?? null;
            
            $agenda = $this->serviceRendezVous->consulterAgenda($praticienId, $dateDebut, $dateFin);
            
            $response->getBody()->write(json_encode($agenda));
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