<?php


use toubilib\api\actions\SigninAction;
use toubilib\api\actions\RefreshTokenAction;
use toubilib\api\actions\ValidateTokenAction;
use toubilib\api\provider\AuthProviderInterface;
use toubilib\api\provider\jwt\JwtManagerInterface;


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
    
    ValidateTokenAction::class => function ($c) {
        return new ValidateTokenAction(
            $c->get(JwtManagerInterface::class)
        );
    },
];