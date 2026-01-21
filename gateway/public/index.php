<?php
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../config/settings.php');
$containerBuilder->addDefinitions(__DIR__ . '/../config/dependencies.php');
$container = $containerBuilder->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->add(new \toubilib\gateway\Middleware\CorsMiddleware());
$app->addErrorMiddleware(true, true, true);

(require __DIR__ . '/../config/routes.php')($app);

$app->run();
