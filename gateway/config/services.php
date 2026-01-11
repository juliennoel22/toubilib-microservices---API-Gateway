<?php

use Psr\Container\ContainerInterface;
use GuzzleHttp\Client;

return [
    Client::class => function (ContainerInterface $c) {
        return new Client([
            'base_uri' => 'http://api.toubilib/',
            'timeout'  => 5.0,
        ]);
    },
];
