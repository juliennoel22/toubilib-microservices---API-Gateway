<?php
use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write("Gateway is running!");
        return $response;
    });

    $app->get('/praticiens', \toubilib\gateway\Action\Praticien\PraticienAction::class);
    $app->get('/praticiens/{id}', \toubilib\gateway\Action\Praticien\PraticienAction::class);
    $app->get('/praticiens/{id}/rdvs', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
    
    // Autres routes RDV
    $app->get('/rdvs', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
    $app->get('/rdvs/{id}', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
    $app->post('/rdvs', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/rdvs/{routes:.+}', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);

    // Routes mixtes (Praticien -> RDV)
    $app->get('/praticiens/{id}/agenda', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
    $app->get('/praticiens/{id}/creneaux', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
};
