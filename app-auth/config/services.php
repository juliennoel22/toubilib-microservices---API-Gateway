<?php

use toubilib\api\middlewares\CorsMiddleware;
use toubilib\api\provider\AuthProviderInterface;
use toubilib\api\provider\jwt\JwtAuthProvider;
use toubilib\api\provider\jwt\JwtManager;
use toubilib\api\provider\jwt\JwtManagerInterface;
use toubilib\core\application\ports\api\AuthnServiceInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;
use toubilib\core\application\usecases\AuthnService;
use toubilib\infra\repositories\UserRepository;


return [   
    'toubiauth_db' => static function ($c): PDO {
        $dbaConfig = $c->get('settings')['db_auth'];
        $driver  = $dbaConfig['driver'] ?? 'pgsql';
        $host    = $dbaConfig['host'] ?? 'localhost';
        $dbname  = $dbaConfig['dbname'] ?? 'toubiauth';
        $user    = $dbaConfig['username'] ?? 'toubiauth';
        $pass    = $dbaConfig['password'] ?? 'toubiauth';
        $charset = $dbaConfig['charset'] ?? 'utf8mb4';

        $dsn = $driver === 'mysql'
            ? "mysql:host={$host};dbname={$dbname};charset={$charset}"
            : "pgsql:host={$host};dbname={$dbname}";

        return new PDO($dsn, $user, $pass);
    },    
    UserRepositoryInterface::class => function ($c) {
        return new UserRepository($c->get('toubiauth_db'));
    },
    
    // JWT
    JwtManagerInterface::class => function () {
        //Pas de valeur par défaut, obligatoire, on peut ni coder ni décoder sans cette clé 
        $jwtSecret = $_ENV['JWT_SECRET'];
        $accessExpiration = 3600; 
        $refreshExpiration = 86400;
        
        $jwtManager = new JwtManager($jwtSecret, $accessExpiration, $refreshExpiration);
        $jwtManager->setIssuer('toubilib');
        
        return $jwtManager;
    },
    AuthnServiceInterface::class => function ($c) {
        return new AuthnService(
            $c->get(UserRepositoryInterface::class)
        );
    },
    
    // Auth Provider
    AuthProviderInterface::class => function ($c) {
        return new JwtAuthProvider(
            $c->get(AuthnServiceInterface::class),
            $c->get(JwtManagerInterface::class)
        );
    },
 
    // Middleware CORS
    CorsMiddleware::class => function ($c) {
        return new CorsMiddleware();
    },
];