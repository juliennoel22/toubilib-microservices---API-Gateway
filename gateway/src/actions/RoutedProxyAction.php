<?php
declare(strict_types=1);

namespace toubilib\gateway\actions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpInternalServerErrorException;

class RoutedProxyAction
{
    private ContainerInterface $container;
    private string $serviceClientKey;

    public function __construct(ContainerInterface $container, string $serviceClientKey)
    {
        $this->container = $container;
        $this->serviceClientKey = $serviceClientKey;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $client = $this->container->get($this->serviceClientKey);
        
        $options = [
            'query' => $request->getQueryParams(),
            'headers' => [],
        ];
        
        $body = (string) $request->getBody();
        if ($body) {
            $options['body'] = $body;
            $options['headers']['Content-Type'] = $request->getHeaderLine('Content-Type') ?: 'application/json';
        }
        
        if ($auth = $request->getHeaderLine('Authorization')) {
            $options['headers']['Authorization'] = $auth;
        }

        try {
            $apiResponse = $client->request(
                $request->getMethod(),
                $request->getUri()->getPath(),
                $options
            );

            $response->getBody()->write((string) $apiResponse->getBody());
            return $response
                ->withHeader('Content-Type', $apiResponse->getHeaderLine('Content-Type') ?: 'application/json')
                ->withStatus($apiResponse->getStatusCode());
                
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new HttpNotFoundException($request, "Resource not found");
            }
            
            $response->getBody()->write((string) $e->getResponse()->getBody());
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($e->getResponse()->getStatusCode());
                
        } catch (ServerException $e) {
            throw new HttpInternalServerErrorException($request, "Internal server error from upstream service");
        }
    }
}
