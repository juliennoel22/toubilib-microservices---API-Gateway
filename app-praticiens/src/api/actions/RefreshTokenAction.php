<?php
namespace toubilib\api\actions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use toubilib\api\provider\AuthProviderInterface;

class RefreshTokenAction {
    private AuthProviderInterface $authProvider;

    public function __construct(AuthProviderInterface $authProvider) {
        $this->authProvider = $authProvider;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response
    ): ResponseInterface {
        
        $data = $request->getParsedBody();
        
        if (!isset($data['refresh_token'])) {
            $error = [
                'type' => 'error',
                'error' => 400,
                'message' => 'Refresh token is required'
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
        
        try {
            $authDTO = $this->authProvider->refresh($data['refresh_token']);
            
            $result = [
                'type' => 'success',
                'user' => [
                    'id' => $authDTO->ID,
                    'email' => $authDTO->email,
                    'role' => $authDTO->role
                ],
                'access_token' => $authDTO->access_token,
                'refresh_token' => $authDTO->refresh_token
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