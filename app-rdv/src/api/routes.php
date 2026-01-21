<?php
declare(strict_types=1);

use toubilib\api\actions\AnnulerRendezVousAction;
use toubilib\api\actions\ConsulterAgendaAction;
use toubilib\api\actions\ConsulterRendezVousAction;
use toubilib\api\actions\CreerRendezVousAction;
use toubilib\api\actions\HonorerRendezVousAction;
use toubilib\api\actions\ListerCreneauxOccAction;
use toubilib\api\actions\ListerRendezVousAction;
use toubilib\api\actions\ListerRendezVousActionID;
use toubilib\api\actions\NePasHonorerRendezVousAction;
use toubilib\api\middlewares\ValidationRendezVousMiddleware;
use toubilib\api\middlewares\AuthnMiddleware;
use toubilib\api\middlewares\AuthzRendezVousMiddleware;

return function(\Slim\App $app): \Slim\App {

    // Page d'accueil
    $app->get('/', function ($request, $response, $args) {
        $html = "<h1>Welcome to Toubilib API (Rendez-Vous Service)!</h1>";
        $html .= "<p>Microservice dédié à la gestion des rendez-vous.</p>";
        $response->getBody()->write($html);
        return $response;
    });

    // Agenda
    $app->get('/praticiens/{id}/agenda', ConsulterAgendaAction::class);
    
    // Creneaux
    $app->get('/praticiens/{id}/creneaux', ListerCreneauxOccAction::class);
    
    // Listing des RDV d'un praticien
    $app->get('/praticiens/{id}/rdvs', ListerRendezVousActionID::class);

    // Rdvs
    $app->get('/rdvs', ListerRendezVousAction::class);

    $app->get('/rdvs/{id}', ConsulterRendezVousAction::class);
       // ->add(AuthzRendezVousMiddleware::class);
       // ->add(AuthnMiddleware::class); // Middleware Auth désactivé pour simplifier ou à configurer si auth service externe

    $app->post('/rdvs', CreerRendezVousAction::class)
        ->add(ValidationRendezVousMiddleware::class);
    
    $app->patch('/rdvs/{id}/annuler', AnnulerRendezVousAction::class);
         //->add(AuthzRendezVousMiddleware::class);

    $app->patch('/rdvs/{id}/honorer', HonorerRendezVousAction::class);
        //->add(AuthzRendezVousMiddleware::class);

    $app->patch('/rdvs/{id}/ne-pas-honorer', NePasHonorerRendezVousAction::class);
        //->add(AuthzRendezVousMiddleware::class);
    
    return $app;
};
