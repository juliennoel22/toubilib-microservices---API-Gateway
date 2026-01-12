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
    // $app->post('/auth/signin', SigninAction::class);
    // $app->post('/auth/refresh', RefreshTokenAction::class);
    // $app->post('/patient/register', RegisterPatientAction::class);

    // Page d'accueil
    $app->get('/', function ($request, $response, $args) {
        $html = "<h1>Welcome to Toubilib API!</h1>";
        $html .= "<p>Read the README.md file for more information.</p>";

        $html .= "<h2>État des Fonctionnalités :</h2>";
        $html .= "<ul>";
        
        $html .= "<li>✅ 1. Lister les praticiens<br>";
        $html .= "<a href='/praticiens'>/praticiens</a></li>";
        
        $html .= "<li>✅ 2. Afficher le détail d’un praticien<br>";
        $html .= "<a href='/praticiens/592692c8-4a8c-3f91-967b-fde67ebea54d'>/praticiens/{id}</a></li>";
        
        $html .= "<li>✅ 3. Lister les créneaux de rdvs déjà occupés<br>";
        $html .= "<a href='/praticiens/592692c8-4a8c-3f91-967b-fde67ebea54d/creneaux?date_debut=2010-01-01&date_fin=2025-12-31'>/praticiens/{id}/creneaux</a></li>";
        
        $html .= "<li>✅ 4. Consulter un rendez-vous<br>";
        $html .= "GET /rdvs/{id} (Auth requise)</li>";

        $html .= "<li>✅ 5. Réserver un rendez-vous<br>";
        $html .= "POST /rdvs (Auth Body requis)</li>";

        $html .= "<li>✅ 6. Annuler un rendez-vous<br>";
        $html .= "PATCH /rdvs/{id}/annuler (Auth requise)</li>";

        $html .= "<li>✅ 7. Afficher l’agenda d’un praticien<br>";
        $html .= "GET /praticiens/{id}/agenda (Auth requise)</li>";

        $html .= "<li>✅ 8. S’authentifier (Patient/Praticien)<br>";
        $html .= "POST /auth/signin</li>";

        $html .= "<li>✅ 9. Rechercher un praticien (Spécialité/Ville)<br>";
        $html .= "<a href='/praticiens/villes/Nancy'>/praticiens/villes/Nancy</a> | ";
        $html .= "<a href='/praticiens/specialites/Dentiste'>/praticiens/specialites/Dentiste</a></li>";

        $html .= "<li>✅ 10. Gérer le cycle de vie (Honoré/Non honoré)<br>";
        $html .= "PATCH /rdvs/{id}/honorer</li>";        
        $html .= "<li>✅ 11. Historique des consultations d’un patient<br>";
        $html .= "GET /patients/{id}/historique (Auth requise)</li>";
        $html .= "<a href='/patients/d975aca7-50c5-3d16-b211-cf7d302cba50/historique'>/patients/{id}/historique</a></li>";

        $html .= "<li>❌ 12. S’inscrire en tant que patient<br>";

        $html .= "<li>❌ 13. Gérer les indisponibilités temporaires<br>";        
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
    $app->get('/praticiens/{id}/indisponibilites', ListerIndisponibilitesAction::class);
    // ->add(AuthzPraticienMiddleware::class)
    // ->add(AuthnMiddleware::class);

    $app->post('/praticiens/{id}/indisponibilites', CreerIndisponibiliteAction::class);
    // ->add(AuthzPraticienMiddleware::class)
    // ->add(AuthnMiddleware::class);

    $app->delete('/praticiens/{id}/indisponibilites/{indispo_id}', SupprimerIndisponibiliteAction::class);
    // ->add(AuthzPraticienMiddleware::class)
    // ->add(AuthnMiddleware::class);

    
    return $app;
};
