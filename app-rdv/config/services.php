<?php

use toubilib\api\middlewares\AuthzRendezVousMiddleware;
use toubilib\api\middlewares\CorsMiddleware;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\PraticienRepositoryInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\RendezVousRepositoryInterface;
use toubilib\core\application\usecases\AuthzRendezVousService;
use toubilib\core\application\ports\api\AuthzRDVServiceInterface;
use toubilib\core\application\usecases\ServiceRendezVous;
use toubilib\infra\repositories\PDORendezVousRepository;
use toubilib\infra\repositories\PraticienClientAdapter;
use GuzzleHttp\Client;

return [
    // Connexion PDO
    'rdv_db' => static function ($c): PDO {
        $dbrConfig = $c->get('settings')['db_rdv'];
        $driver  = $dbrConfig['driver'] ?? 'pgsql';
        $host    = $dbrConfig['host'] ?? 'localhost';
        $dbname  = $dbrConfig['dbname'] ?? 'toubiprat';
        $user    = $dbrConfig['username'] ?? 'toubiprat';
        $pass    = $dbrConfig['password'] ?? 'toubiprat';
        $charset = $dbrConfig['charset'] ?? 'utf8mb4';

        $dsn = $driver === 'mysql'
            ? "mysql:host={$host};dbname={$dbname};charset={$charset}"
            : "pgsql:host={$host};dbname={$dbname}";

        return new PDO($dsn, $user, $pass);
    },
    
    // Config Praticien API Client
    'client.praticiens' => function () {
        return new Client([
            'base_uri' => 'http://api.praticiens', 
            'timeout'  => 5.0,
        ]);
    },

    // Repositories
    PraticienRepositoryInterface::class => function ($c) {
        return new PraticienClientAdapter($c->get('client.praticiens'));
    },
    
    RendezVousRepositoryInterface::class => function ($a) {
        return new PDORendezVousRepository(
            $a->get('rdv_db'),
            $a->get(PraticienRepositoryInterface::class)
        );
    },

    // Messaging
    \toubilib\core\application\ports\spi\EventDispatcherInterface::class => function ($c) {
        return new \toubilib\infra\messaging\RabbitMqEventDispatcher();
    },

        // Services
    ServiceRendezVousInterface::class => function ($c) {
        return new ServiceRendezVous(
            $c->get(RendezVousRepositoryInterface::class),
            $c->get(PraticienRepositoryInterface::class),
            $c->get(\toubilib\core\application\ports\spi\EventDispatcherInterface::class)
        );
    },
    
    AuthzRDVServiceInterface::class => function ($c) {
        return new AuthzRendezVousService(
            $c->get(RendezVousRepositoryInterface::class)
        );
    },
    
    // Middlewares d'autorisation
    AuthzRendezVousMiddleware::class => function ($c) {
        return new AuthzRendezVousMiddleware(
            $c->get(AuthzRDVServiceInterface::class)
        );
    },
    
    // Actions
    \toubilib\api\actions\ListerRendezVousActionID::class => function ($c) {
        return new \toubilib\api\actions\ListerRendezVousActionID(
            $c->get(ServiceRendezVousInterface::class)
        );
    },

    \toubilib\api\actions\ConsulterRendezVousAction::class => function ($c) {
        return new \toubilib\api\actions\ConsulterRendezVousAction(
            $c->get(ServiceRendezVousInterface::class)
        );
    },

    // Middleware CORS
    CorsMiddleware::class => function ($c) {
        return new CorsMiddleware();
    },
];