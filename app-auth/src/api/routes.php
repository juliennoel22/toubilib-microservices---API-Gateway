<?php
declare(strict_types=1);


use toubilib\api\actions\SigninAction;
use toubilib\api\actions\RefreshTokenAction;
use toubilib\api\actions\ValidateTokenAction;
use toubilib\api\actions\RegisterAction;

return function(\Slim\App $app): \Slim\App {

    $app->post('/auth/signup', RegisterAction::class);
    $app->post('/auth/signin', SigninAction::class);
    $app->post('/auth/refresh', RefreshTokenAction::class);
    $app->post('/tokens/validate', ValidateTokenAction::class);
    
    return $app;
};
