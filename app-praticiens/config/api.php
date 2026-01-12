<?php

use toubilib\api\actions\ListerPraticienAction;
use toubilib\api\actions\ListerPraticienIdAction;
use toubilib\api\actions\ListerPraticiensAction;
use toubilib\api\actions\RecherchePraticiensActionSpecialite;
use toubilib\api\actions\RecherchePraticiensActionVille;
use toubilib\core\application\ports\api\ServicePraticienInterface;
use toubilib\api\actions\SigninAction;
use toubilib\api\actions\RefreshTokenAction;
use toubilib\api\actions\RegisterPatientAction;
use toubilib\api\provider\AuthProviderInterface;
use toubilib\core\application\ports\api\ServicePatientInterface;
use toubilib\api\actions\CreerIndisponibiliteAction;
use toubilib\api\actions\ListerIndisponibilitesAction;
use toubilib\api\actions\SupprimerIndisponibiliteAction;
use toubilib\core\application\ports\api\ServiceIndisponibiliteInterface;


return [
    ListerPraticiensAction::class => function ($c) {
        return new ListerPraticiensAction(
            $c->get(ServicePraticienInterface::class)
        );
    },
    
    ListerPraticienAction::class => function ($c) {
        return new ListerPraticienAction( 
            $c->get(ServicePraticienInterface::class)
        );
    },
    
    ListerPraticienIdAction::class => function ($c) {
        return new ListerPraticienIdAction( 
            $c->get(ServicePraticienInterface::class)
        );
    },
    
    SigninAction::class => function ($c) {
        return new SigninAction(
            $c->get(AuthProviderInterface::class)
        );
    },
    
    RefreshTokenAction::class => function ($c) {
        return new RefreshTokenAction(
            $c->get(AuthProviderInterface::class)
        );
    },
    
    RecherchePraticiensActionVille::class => function ($c) {
        return new RecherchePraticiensActionVille(
            $c->get(ServicePraticienInterface::class)
        );
    },
    RecherchePraticiensActionSpecialite::class => function ($c) {
        return new RecherchePraticiensActionSpecialite(
            $c->get(ServicePraticienInterface::class)
        );
    },
    RegisterPatientAction::class => function ($c) {
        return new RegisterPatientAction(
            $c->get(ServicePatientInterface::class)
        );
    },
    CreerIndisponibiliteAction::class => function ($c) {
        return new CreerIndisponibiliteAction(
            $c->get(ServiceIndisponibiliteInterface::class)
        );
    },

    ListerIndisponibilitesAction::class => function ($c) {
        return new ListerIndisponibilitesAction(
            $c->get(ServiceIndisponibiliteInterface::class)
        );
    },

    SupprimerIndisponibiliteAction::class => function ($c) {
        return new SupprimerIndisponibiliteAction(
            $c->get(ServiceIndisponibiliteInterface::class)
        );
    },
];