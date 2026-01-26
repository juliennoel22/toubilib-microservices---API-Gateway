<?php

use toubilib\api\actions\AnnulerRendezVousAction;
use toubilib\api\actions\ConsulterAgendaAction;
use toubilib\api\actions\ConsulterRendezVousAction;
use toubilib\api\actions\CreerRendezVousAction;
use toubilib\api\actions\HonorerRendezVousAction;
use toubilib\api\actions\ListerCreneauxOccAction;
use toubilib\api\actions\ListerPraticienAction;
use toubilib\api\actions\ListerPraticienIdAction;
use toubilib\api\actions\ListerPraticiensAction;
use toubilib\api\actions\ListerRendezVousAction;
use toubilib\api\actions\ListerRendezVousActionID;
use toubilib\api\actions\NePasHonorerRendezVousAction;
use toubilib\api\actions\RecherchePraticiensActionSpecialite;
use toubilib\api\actions\RecherchePraticiensActionVille;
use toubilib\core\application\ports\api\ServicePraticienInterface;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;
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

    \toubilib\api\actions\ValidateTokenAction::class => function ($c) {
        return new \toubilib\api\actions\ValidateTokenAction(
            $c->get(AuthProviderInterface::class)
        );
    },
    
    RegisterPatientAction::class => function ($c) {
        return new RegisterPatientAction(
            $c->get(ServicePatientInterface::class)
        );
    },
];