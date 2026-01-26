<?php

use Psr\Container\ContainerInterface;
use GuzzleHttp\Client;

return [
    Client::class => function (ContainerInterface $c) {
        $settings = $c->get('settings');
        return new Client([
            'base_uri' => $settings['toubilib_api'],
            'timeout'  => 5.0,
        ]);
    },

    'client.praticiens' => function (ContainerInterface $c) {
        $settings = $c->get('settings');
        return new Client([
            'base_uri' => $settings['praticien_api'],
            'timeout'  => 5.0,
        ]);
    },

    \toubilib\gateway\Action\Praticien\PraticienAction::class => function (ContainerInterface $c) {
        return new \toubilib\gateway\Action\Praticien\PraticienAction(
            $c->get('client.praticiens')
        );
    },

    'client.rdv' => function (ContainerInterface $c) {
        $settings = $c->get('settings');
        return new Client([
            'base_uri' => $settings['rdv_api'],
            'timeout'  => 60.0,
            'headers' => ['Connection' => 'close'],
        ]);
    },

    \toubilib\gateway\Action\RendezVous\RendezVousAction::class => function (ContainerInterface $c) {
        return new \toubilib\gateway\Action\RendezVous\RendezVousAction(
            $c->get('client.rdv')
        );
    },

    'client.auth' => function (ContainerInterface $c) {
        $settings = $c->get('settings');
        return new Client([
            'base_uri' => $settings['auth_api'],
            'timeout'  => 5.0,
        ]);
    },

    \toubilib\gateway\Action\Auth\AuthAction::class => function (ContainerInterface $c) {
        return new \toubilib\gateway\Action\Auth\AuthAction(
            $c->get('client.auth')
        );
    },

    \toubilib\gateway\Middleware\GatewayAuthMiddleware::class => function (ContainerInterface $c) {
        return new \toubilib\gateway\Middleware\GatewayAuthMiddleware(
            $c->get('client.auth')
        );
    },
];
