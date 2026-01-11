<?php
declare(strict_types=1);

use Slim\App;
use toubilib\gateway\actions\GetPraticiensAction;
use toubilib\gateway\actions\GetPraticienByIdAction;

return function (App $app): App {
    $app->get('/praticiens', GetPraticiensAction::class);
    $app->get('/praticiens/{id}', GetPraticienByIdAction::class);

    return $app;
};
