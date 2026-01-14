<?php
declare(strict_types=1);

namespace toubilib\gateway\actions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticatedProxyAction
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
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            $response->getBody()->write(json_encode(['type' => 'error', 'error' => 401, 'message' => 'Authorization header is missing']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        
        $authClient = $this->container->get('auth.client');
        
        try {
            $validationResponse = $authClient->request('POST', '/tokens/validate', [
                'headers' => ['Authorization' => $authHeader]
            ]);
            
            if ($validationResponse->getStatusCode() !== 200) {
                $response->getBody()->write((string) $validationResponse->getBody());
                return $response->withStatus($validationResponse->getStatusCode())->withHeader('Content-Type', 'application/json');
            }
            
        } catch (ClientException $e) {
            $response->getBody()->write((string) $e->getResponse()->getBody());
            return $response->withStatus($e->getResponse()->getStatusCode())->withHeader('Content-Type', 'application/json');
        } catch (ServerException $e) {
            $response->getBody()->write((string) $e->getResponse()->getBody());
            return $response->withStatus($e->getResponse()->getStatusCode())->withHeader('Content-Type', 'application/json');
        }
        
        $client = $this->container->get($this->serviceClientKey);
        
        $options = [
            'query' => $request->getQueryParams(),
            'headers' => ['Authorization' => $authHeader],
        ];
        
        $body = (string) $request->getBody();
        if ($body) {
            $options['body'] = $body;
            $options['headers']['Content-Type'] = $request->getHeaderLine('Content-Type') ?: 'application/json';
        }

        try {
            $apiResponse = $client->request($request->getMethod(), $request->getUri()->getPath(), $options);
            $response->getBody()->write((string) $apiResponse->getBody());
            return $response->withHeader('Content-Type', 'application/json')->withStatus($apiResponse->getStatusCode());
        } catch (ClientException $e) {
            $response->getBody()->write((string) $e->getResponse()->getBody());
            return $response->withHeader('Content-Type', 'application/json')->withStatus($e->getResponse()->getStatusCode());
        } catch (ServerException $e) {
            $response->getBody()->write((string) $e->getResponse()->getBody());
            return $response->withHeader('Content-Type', 'application/json')->withStatus($e->getResponse()->getStatusCode());
        }
    }
}
