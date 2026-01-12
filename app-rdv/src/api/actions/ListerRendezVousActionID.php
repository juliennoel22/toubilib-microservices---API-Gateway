<?php

namespace toubilib\api\actions;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;

class ListerRendezVousActionID
{

private ServiceRendezVousInterface $service_rendez_vous;

public function __construct(ServiceRendezVousInterface $service_rendez_vous)
{
    $this->service_rendez_vous = $service_rendez_vous;
}
public function __invoke(Request $request, Response $response): Response
{
    $id = $request->getAttribute('id');
    $rdvs = $this->service_rendez_vous->ListerRDVID($id);
    
    // Ajout HATEOAS
    $data = [
        'type' => 'ressource',
        'rendez_vous' => $rdvs,
        'links' => [
            'self' => ['href' => "http://localhost:6080/rdvs/id/$id"],
            'praticien' => ['href' => "http://localhost:6080/prat/id/$id"]
            // 'patient' => ['href' => "http://localhost:6080/prat/id/$id"],
            // 'annuler' => ['href' => "http://localhost:6080/prat/id/$id"],
            // 'honorer' => ['href' => "http://localhost:6080/prat/id/$id"]
        ]
    ];
    
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type','application/json');
}




}