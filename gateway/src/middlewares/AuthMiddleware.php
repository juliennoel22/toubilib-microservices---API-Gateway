<?php
declare(strict_types=1);

namespace toubilib\gateway\middlewares;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AuthMiddleware 
{
    private Client $authClient;

    public function __construct(Client $authClient)
    {
        $this->authClient = $authClient;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            $response = new Response();
            $response->getBody()->write(json_encode(['type' => 'error', 'error' => 401, 'message' => 'Authorization header is missing']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        
        try {
            $validationResponse = $this->authClient->request('POST', '/tokens/validate', [
                'headers' => ['Authorization' => $authHeader]
            ]);
            
            return $handler->handle($request);
            
        } catch (ClientException $e) {
            $response = new Response();
            $response->getBody()->write((string) $e->getResponse()->getBody());
            return $response->withStatus($e->getResponse()->getStatusCode())->withHeader('Content-Type', 'application/json');
        } catch (ServerException $e) {
            $response = new Response();
            $response->getBody()->write((string) $e->getResponse()->getBody());
            return $response->withStatus($e->getResponse()->getStatusCode())->withHeader('Content-Type', 'application/json');
        }
    }
}
