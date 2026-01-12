<?php
declare(strict_types=1);


use toubilib\api\actions\SigninAction;
use toubilib\api\actions\RefreshTokenAction;
use toubilib\api\actions\ValidateTokenAction;

return function(\Slim\App $app): \Slim\App {

    // Auth routes
    $app->post('/auth/signin', SigninAction::class);
    $app->post('/auth/refresh', RefreshTokenAction::class);
    $app->post('/tokens/validate', ValidateTokenAction::class);
    
    // $app->post('/patient/register', RegisterPatientAction::class);
    return $app;
};
