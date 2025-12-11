<?php
declare(strict_types=1);

use toubilib\api\actions\AnnulerRendezVousAction;
use toubilib\api\actions\ConsulterAgendaAction;
use toubilib\api\actions\ConsulterRendezVousAction;
use toubilib\api\actions\CreerRendezVousAction;
use toubilib\api\actions\HonorerRendezVousAction;
use toubilib\api\actions\ListerCreneauxOccAction;
use toubilib\api\actions\ListerPraticienIdAction;
use toubilib\api\actions\ListerPraticiensAction;
use toubilib\api\actions\ListerRendezVousAction;
use toubilib\api\actions\ListerRendezVousActionID;  
use toubilib\api\actions\NePasHonorerRendezVousAction;
use toubilib\api\actions\RecherchePraticiensActionSpecialite;
use toubilib\api\actions\RecherchePraticiensActionVille;
use toubilib\api\middlewares\ValidationRendezVousMiddleware;
use toubilib\api\actions\SigninAction;
use toubilib\api\actions\RefreshTokenAction;
use toubilib\api\actions\RegisterPatientAction;
use toubilib\api\middlewares\AuthnMiddleware;
use toubilib\api\middlewares\AuthzPraticienMiddleware;
use toubilib\api\middlewares\AuthzRendezVousMiddleware;
use toubilib\api\actions\CreerIndisponibiliteAction;
use toubilib\api\actions\ListerIndisponibilitesAction;
use toubilib\api\actions\SupprimerIndisponibiliteAction;

return function(\Slim\App $app): \Slim\App {

    // Auth routes
    $app->post('/auth/signin', SigninAction::class);
    $app->post('/auth/refresh', RefreshTokenAction::class);
    $app->post('/patient/register', RegisterPatientAction::class);

    // Page d'accueil
    $app->get('/', function ($request, $response, $args) {
        $response->getBody()->write("Welcome to Toubilib API!\nRead the README.md file for more information.");
        return $response;
    });
    
    // Praticiens
    $app->get('/praticiens', ListerPraticiensAction::class);
    $app->get('/praticiens/{id}', ListerPraticienIdAction::class);
    $app->get('/praticiens/villes/{ville}', RecherchePraticiensActionVille::class);
    $app->get('/praticiens/specialites/{specialite}', RecherchePraticiensActionSpecialite::class);

    // Indisponibilites
    $app->get('/praticiens/{id}/indisponibilites', ListerIndisponibilitesAction::class)
    ->add(AuthzPraticienMiddleware::class)
    ->add(AuthnMiddleware::class);

    $app->post('/praticiens/{id}/indisponibilites', CreerIndisponibiliteAction::class)
    ->add(AuthzPraticienMiddleware::class)
    ->add(AuthnMiddleware::class);

    $app->delete('/praticiens/{id}/indisponibilites/{indispo_id}', SupprimerIndisponibiliteAction::class)
    ->add(AuthzPraticienMiddleware::class)
    ->add(AuthnMiddleware::class);

    // Agenda
    $app->get('/praticiens/{id}/agenda', ConsulterAgendaAction::class)
        ->add(AuthzPraticienMiddleware::class)
        ->add(AuthnMiddleware::class);

    // Rdvs

    // $app->get('/rdvs', ListerRendezVousAction::class)

    $app->get('/rdvs/{id}', ConsulterRendezVousAction::class)
       ->add(AuthzRendezVousMiddleware::class)
       ->add(AuthnMiddleware::class);

    $app->get('/praticiens/{id}/rdvs', ListerRendezVousActionID::class);  
    
    $app->post('/rdvs', CreerRendezVousAction::class)
        ->add(ValidationRendezVousMiddleware::class)
        ->add(AuthnMiddleware::class);
    
    $app->patch('/rdvs/{id}/annuler', AnnulerRendezVousAction::class)
         ->add(AuthzRendezVousMiddleware::class)
        ->add(AuthnMiddleware::class);

    $app->patch('/rdvs/{id}/honorer', HonorerRendezVousAction::class)
        ->add(AuthzRendezVousMiddleware::class)
        ->add(AuthnMiddleware::class);

    $app->patch('/rdvs/{id}/ne-pas-honorer', NePasHonorerRendezVousAction::class)
        ->add(AuthzRendezVousMiddleware::class)
        ->add(AuthnMiddleware::class);


    // Creneaux Praticien
    $app->get('/praticiens/{id}/creneaux', ListerCreneauxOccAction::class); 
    
    return $app;
};
