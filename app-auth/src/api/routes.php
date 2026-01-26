<?php
declare(strict_types=1);


use toubilib\api\actions\SigninAction;
use toubilib\api\actions\RefreshTokenAction;
use toubilib\api\actions\RegisterPatientAction;
use toubilib\api\actions\ValidateTokenAction;

return function (\Slim\App $app): \Slim\App {

    // Routes d'authentification
    $app->post('/auth/signin', SigninAction::class);
    $app->post('/auth/refresh', RefreshTokenAction::class);
    $app->get('/tokens/validate', ValidateTokenAction::class);

    $app->post('/patient/register', RegisterPatientAction::class);
    $app->post('/auth/register', RegisterPatientAction::class);

    return $app;
};
