<?php
use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (App $app) {
    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write("Gateway is running!");
        return $response;
    });


    // Routes Authentification
    $app->post('/auth/signin', \toubilib\gateway\Action\Auth\AuthAction::class);
    $app->post('/auth/register', \toubilib\gateway\Action\Auth\AuthAction::class);
    $app->post('/auth/refresh', \toubilib\gateway\Action\Auth\AuthAction::class);

    $app->get('/praticiens', \toubilib\gateway\Action\Praticien\PraticienAction::class);
    $app->get('/praticiens/{id}', \toubilib\gateway\Action\Praticien\PraticienAction::class);

    // Routes Rendez-vous avec Middleware d'Authentification
    $app->group('', function ($group) {
        $group->get('/praticiens/{id}/rdvs', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
        $group->get('/rdvs', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
        $group->get('/rdvs/{id}', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
        $group->post('/rdvs', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
        $group->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/rdvs/{routes:.+}', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
        $group->get('/praticiens/{id}/agenda', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
        $group->get('/praticiens/{id}/creneaux', \toubilib\gateway\Action\RendezVous\RendezVousAction::class);
    })->add(\toubilib\gateway\Middleware\GatewayAuthMiddleware::class);
};
