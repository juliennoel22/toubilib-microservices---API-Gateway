<?php
use Slim\App;
use toubilib\gateway\actions\RoutedProxyAction;
use toubilib\gateway\actions\AuthenticatedProxyAction;
use toubilib\gateway\middlewares\AuthMiddleware;
use Psr\Container\ContainerInterface;

return function (App $app): App {
    $container = $app->getContainer();
    

    $app->get('/praticiens', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'praticiens.client');
        return $action($request, $response, $args);
    });
    
    $app->get('/praticiens/{id}', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'praticiens.client');
        return $action($request, $response, $args);
    });
    
    $app->get('/praticiens/{id}/rdvs', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'praticiens.client');
        return $action($request, $response, $args);
    });
    
    $app->get('/praticiens/{id}/agenda', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'praticiens.client');
        return $action($request, $response, $args);
    })->add(new AuthMiddleware($container->get('auth.client')));
    
    $app->get('/praticiens/{id}/creneaux', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'praticiens.client');
        return $action($request, $response, $args);
    });
    
    $app->get('/praticiens/villes/{ville}', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'praticiens.client');
        return $action($request, $response, $args);
    });
    
    $app->get('/praticiens/specialites/{specialite}', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'praticiens.client');
        return $action($request, $response, $args);
    });
    
    $app->post('/praticiens', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'praticiens.client');
        return $action($request, $response, $args);
    });
    
    $app->put('/praticiens/{id}', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'praticiens.client');
        return $action($request, $response, $args);
    });
    
    $app->patch('/praticiens/{id}', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'praticiens.client');
        return $action($request, $response, $args);
    });
    
    $app->delete('/praticiens/{id}', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'praticiens.client');
        return $action($request, $response, $args);
    });
    

    $app->get('/rdvs', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'rdv.client');
        return $action($request, $response, $args);
    });
    
    $app->get('/rdvs/{id}', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'rdv.client');
        return $action($request, $response, $args);
    })->add(new AuthMiddleware($container->get('auth.client')));
    
    $app->post('/rdvs', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'rdv.client');
        return $action($request, $response, $args);
    })->add(new AuthMiddleware($container->get('auth.client')));

    
    $app->patch('/rdvs/{id}', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'rdv.client');
        return $action($request, $response, $args);
    });
    
    $app->patch('/rdvs/{id}/annuler', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'rdv.client');
        return $action($request, $response, $args);
    });
    
    $app->patch('/rdvs/{id}/honorer', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'rdv.client');
        return $action($request, $response, $args);
    });
    
    $app->patch('/rdvs/{id}/ne-pas-honorer', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'rdv.client');
        return $action($request, $response, $args);
    });

    $app->post('/auth/signin', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'auth.client');
        return $action($request, $response, $args);
    });
    
    $app->post('/auth/signup', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'auth.client');
        return $action($request, $response, $args);
    });
    
    $app->post('/auth/refresh', function ($request, $response, $args) use ($container) {
        $action = new RoutedProxyAction($container, 'auth.client');
        return $action($request, $response, $args);
    });

    return $app;
};
