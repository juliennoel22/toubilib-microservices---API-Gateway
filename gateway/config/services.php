<?php

use Psr\Container\ContainerInterface;
use GuzzleHttp\Client;

return [
    'praticiens.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => 'http://api.praticiens/',
        ]);
    },
    
    'rdv.client' => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => 'http://api.rdv/',
        ]);
    },
    
    Client::class => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => 'http://api.toubilib/',
        ]);
    },
        Client::class => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => 'http://api.auth/',
        ]);
    },
];
