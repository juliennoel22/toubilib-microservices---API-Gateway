<?php

namespace toubilib\gateway\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class GatewayAuthMiddleware implements MiddlewareInterface
{
    private ClientInterface $authClient;

    public function __construct(ClientInterface $authClient)
    {
        $this->authClient = $authClient;
    }

    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        // 1. Vérifier la présence du header Authorization
        if (!$request->hasHeader('Authorization')) {
            throw new HttpUnauthorizedException($request, "Authorization header missing");
        }

        $authHeader = $request->getHeaderLine('Authorization');

        // 2. Valider le token auprès du service Auth
        try {
            $response = $this->authClient->request('GET', '/tokens/validate', [
                'headers' => [
                    'Authorization' => $authHeader
                ],
                'http_errors' => false
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new HttpUnauthorizedException($request, "Invalid token");
            }

            // 3. Passer la requête si valide
            return $handler->handle($request);

        } catch (ClientException $e) {
            // Auth service returned 401 or similar
            throw new HttpUnauthorizedException($request, "Token validation failed", $e);
        } catch (\Exception $e) {
            throw new HttpUnauthorizedException($request, "Authentication service unavailable", $e);
        }
    }
}
