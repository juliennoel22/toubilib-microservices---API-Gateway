<?php


use toubilib\api\actions\SigninAction;
use toubilib\api\actions\RefreshTokenAction;
use toubilib\api\actions\ValidateTokenAction;
use toubilib\api\actions\RegisterAction;
use toubilib\api\provider\AuthProviderInterface;
use toubilib\api\provider\jwt\JwtManagerInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;


return [    
    RegisterAction::class => function ($c) {
        return new RegisterAction(
            $c->get(UserRepositoryInterface::class)
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
    
    ValidateTokenAction::class => function ($c) {
        return new ValidateTokenAction(
            $c->get(JwtManagerInterface::class)
        );
    },
];