<?php

namespace toubilib\api\actions;

use Exception;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;

class ListerCreneauxOccAction {
    private ServiceRendezVousInterface $serviceRendezVous;
    
    public function __construct(ServiceRendezVousInterface $serviceRendezVous) {
        $this->serviceRendezVous = $serviceRendezVous;
    }
    
    public function __invoke(Request $request, Response $response): Response {
        $id = $request->getAttribute('id');
        $queryParams = $request->getQueryParams();
        $dateDebut = $queryParams['date_debut'];
        $dateFin = $queryParams['date_fin'];
        
        
        $creneaux = $this->serviceRendezVous->listerCreneauxOccupes($id, $dateDebut, $dateFin);
        
        $response->getBody()->write(json_encode($creneaux));
        return $response->withHeader('Content-Type', 'application/json');
    }
}