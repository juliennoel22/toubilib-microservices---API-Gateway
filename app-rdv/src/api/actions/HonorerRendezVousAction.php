<?php

namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;

class HonorerRendezVousAction
{
    private ServiceRendezVousInterface $service_rendez_vous;

    public function __construct(ServiceRendezVousInterface $service_rendez_vous)
    {
        $this->service_rendez_vous = $service_rendez_vous;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $rdv_id = $request->getAttribute('id');
        
        try {
            $this->service_rendez_vous->HonorerRDV($rdv_id);
            
            $rdv = $this->service_rendez_vous->consulterRendezVous($rdv_id);
            
            if ($rdv === null) {
                $error = [
                    'type' => 'error',
                    'error' => 404,
                    'message' => "Rendez-vous $rdv_id not found"
                ];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withStatus(404)
                    ->withHeader('Content-Type', 'application/json');
            }
            
            $dateDebut = new \DateTime($rdv->date_heure_debut);
            $dateCreation = new \DateTime($rdv->date_creation);
            
            $data = [
                'type' => 'ressource',
                'rendez_vous' => [
                    'id' => $rdv->id,
                    'praticien_id' => $rdv->praticien_id,
                    'patient_id' => $rdv->patient_id,
                    'date_debut' => $dateDebut->format('d/m/Y H:i'),
                    'date_creation' => $dateCreation->format('d/m/Y'),
                    'duree' => $rdv->duree,
                    'motif_visite' => $rdv->motif_visite,
                    'etat' => $rdv->status  
                ],
                'links' => [
                    'self' => [
                        'href' => "/rdvs/{$rdv->id}/"
                    ],
                    'praticien' => [
                        'href' => "/praticiens/{$rdv->praticien_id}/"
                    ],
                    'patient' => [
                        'href' => "/patients/{$rdv->patient_id}/"
                    ]
                ]
            ];
            
            $response->getBody()->write(json_encode($data));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (\Exception $e) {
            $error = [
                'type' => 'error',
                'error' => 400,
                'message' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}