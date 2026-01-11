<?php
declare(strict_types=1);

namespace toubilib\gateway\actions;

use GuzzleHttp\Client;

abstract class AbstractGatewayAction
{
    protected Client $remote_service;

    public function __construct(Client $remote_service)
    {
        $this->remote_service = $remote_service;
    }
}
