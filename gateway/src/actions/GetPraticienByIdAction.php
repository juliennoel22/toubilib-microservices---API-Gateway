<?php
declare(strict_types=1);

namespace toubilib\gateway\actions;

use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;

class GetPraticienByIdAction extends AbstractGatewayAction
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'];

        try {
            $apiResponse = $this->remote_service->request('GET', '/praticiens/' . $id);

            $body = $apiResponse->getBody();

            $response->getBody()->write((string)$body);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($apiResponse->getStatusCode());
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new HttpNotFoundException($request, "Praticien not found");
            }
            throw $e;
        }
    }
}
