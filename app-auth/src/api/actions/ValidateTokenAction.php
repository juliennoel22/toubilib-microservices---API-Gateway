<?php
namespace toubilib\api\actions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use toubilib\api\provider\AuthProviderInterface;
use Exception;

class ValidateTokenAction {
    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        if (!$request->hasHeader('Authorization')) {
             $response->getBody()->write(json_encode(['error' => 'Missing authorization header']));
             return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $authHeader = $request->getHeaderLine('Authorization');
        $token = sscanf($authHeader, "Bearer %s")[0] ?? null;

        if (!$token) {
             $response->getBody()->write(json_encode(['error' => 'Invalid authorization format']));
             return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        try {
            $userProfile = $this->authProvider->getSignedInUser($token);
            $response->getBody()->write(json_encode($userProfile));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }
}
