<?php
namespace toubilib\api\actions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use toubilib\api\provider\jwt\JwtManagerInterface;

class ValidateTokenAction {
    private JwtManagerInterface $jwtManager;

    public function __construct(JwtManagerInterface $jwtManager) {
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response
    ): ResponseInterface {
        
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            $error = [
                'type' => 'error',
                'error' => 401,
                'message' => 'Authorization header is missing'
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
        }
        
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $error = [
                'type' => 'error',
                'error' => 401,
                'message' => 'Invalid Authorization header format. Expected: Bearer <token>'
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
        }
        
        $token = $matches[1];
        
        try {
            $payload = $this->jwtManager->validate($token);
            
            $result = [
                'type' => 'success',
                'message' => 'Token is valid',
                'user' => [
                    'id' => $payload['id'] ?? null,
                    'email' => $payload['email'] ?? null,
                    'role' => $payload['role'] ?? null
                ]
            ];
            
            $response->getBody()->write(json_encode($result));
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (\Exception $e) {
            $error = [
                'type' => 'error',
                'error' => 401,
                'message' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(401)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}
