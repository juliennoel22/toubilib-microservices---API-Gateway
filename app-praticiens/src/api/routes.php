<?php
declare(strict_types=1);

use toubilib\api\actions\ListerPraticienIdAction;
use toubilib\api\actions\ListerPraticiensAction;
use toubilib\api\actions\RecherchePraticiensActionSpecialite;
use toubilib\api\actions\RecherchePraticiensActionVille;
use toubilib\api\actions\SigninAction;
use toubilib\api\actions\RefreshTokenAction;
use toubilib\api\actions\RegisterPatientAction;
use toubilib\api\middlewares\AuthnMiddleware;
use toubilib\api\middlewares\AuthzPraticienMiddleware;
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
        $html = "<h1>Welcome to Toubilib API (Praticiens Service)!</h1>";
        $html .= "<p>Microservice dédié à la gestion des praticiens.</p>";

        $html .= "<h2>État des Fonctionnalités :</h2>";
        $html .= "<ul>";
        
        $html .= "<li>✅ 1. Lister les praticiens<br>";
        $html .= "<a href='/praticiens'>/praticiens</a></li>";
        
        $html .= "<li>✅ 2. Afficher le détail d’un praticien<br>";
        $html .= "<a href='/praticiens/592692c8-4a8c-3f91-967b-fde67ebea54d'>/praticiens/{id}</a></li>";
        
        $html .= "<li>✅ 8. S’authentifier (Patient/Praticien)<br>";
        $html .= "POST /auth/signin</li>";

        $html .= "<li>✅ 9. Rechercher un praticien (Spécialité/Ville)<br>";
        $html .= "<a href='/praticiens/villes/Nancy'>/praticiens/villes/Nancy</a> | ";
        $html .= "<a href='/praticiens/specialites/Dentiste'>/praticiens/specialites/Dentiste</a></li>";

        $html .= "<li>✅ 13. Gérer les indisponibilités temporaires<br>";
        $html .= "GET/POST/DELETE /praticiens/{id}/indisponibilites</li>"; 
        
        $html .= "</ul>";
        
        $response->getBody()->write($html);
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
    
    return $app;
};
