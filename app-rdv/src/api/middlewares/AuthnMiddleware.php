<?php
namespace toubilib\api\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use toubilib\api\provider\jwt\JwtManagerInterface;
use toubilib\core\application\ports\api\UserProfileDTO;

class AuthnMiddleware {
    private JwtManagerInterface $jwtManager;

    public function __construct(JwtManagerInterface $jwtManager) {
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            $response = new Response();
            $response->getBody()->write(json_encode(['type' => 'error', 'error' => 401, 'message' => 'Missing authorization header']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $token = sscanf($authHeader, "Bearer %s")[0] ?? null;
        
        if (!$token) {
            $response = new Response();
            $response->getBody()->write(json_encode(['type' => 'error', 'error' => 401, 'message' => 'Invalid authorization format']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        try {
            $payload = $this->jwtManager->validate($token);
            $userProfile = new UserProfileDTO($payload['id'], $payload['email'], $payload['role']);
            $request = $request->withAttribute('authenticated_user', $userProfile);
        } catch (\Exception $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['type' => 'error', 'error' => 401, 'message' => 'Authentication failed']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        
        return $handler->handle($request);
    }
}