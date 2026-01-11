<?php
declare(strict_types=1);

namespace toubilib\gateway\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetPraticiensAction extends AbstractGatewayAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $apiResponse = $this->remote_service->request('GET', '/praticiens');
        $body = $apiResponse->getBody();

        $response->getBody()->write((string)$body);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($apiResponse->getStatusCode());
    }
}
