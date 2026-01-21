<?php
namespace toubilib\api\middlewares;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use toubilib\api\provider\AuthProviderInterface;


class AuthnMiddleware {
    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        RequestHandlerInterface $handler
    ): ResponseInterface {
        
        if (!$request->hasHeader('Authorization')) {
            throw new Exception("Missing authorization header");
        }

        $authHeader = $request->getHeaderLine('Authorization');
        
        $token = sscanf($authHeader, "Bearer %s")[0] ?? null;
        
        if (!$token) {
            throw new Exception("Invalid authorization format");
        }

        try {
            $userProfile = $this->authProvider->getSignedInUser($token);
            
            $request = $request->withAttribute('authenticated_user', $userProfile);
            
        } catch (Exception $e) {
             $response = new Response();
             $response->getBody()->write(json_encode([
                'type' => 'error',
                'error' => 401,
                'message' => "Authentication failed: " . $e->getMessage()
            ]));
            
            return $response
                ->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
        }
        return $handler->handle($request);
    }
}