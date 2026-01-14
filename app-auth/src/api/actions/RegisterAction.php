<?php
namespace toubilib\api\actions;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;
use toubilib\core\domain\entities\auth\User;

class RegisterAction {
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response
    ): ResponseInterface {
        
        $data = $request->getParsedBody();
        
        $required = ['email', 'password'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $error = [
                    'type' => 'error',
                    'error' => 400,
                    'message' => "Field {$field} is required"
                ];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withStatus(400)
                    ->withHeader('Content-Type', 'application/json');
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = [
                'type' => 'error',
                'error' => 400,
                'message' => 'Invalid email format'
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }

        if (strlen($data['password']) < 6) {
            $error = [
                'type' => 'error',
                'error' => 400,
                'message' => 'Password must be at least 6 characters long'
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
        
        try {
            $existingUser = $this->userRepository->FindByEmail($data['email']);
            if ($existingUser !== null) {
                $error = [
                    'type' => 'error',
                    'error' => 409,
                    'message' => 'User already exists with this email'
                ];
                $response->getBody()->write(json_encode($error));
                return $response
                    ->withStatus(409)
                    ->withHeader('Content-Type', 'application/json');
            }
            
            $role = $data['role'] ?? 0;
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            
            $user = new User(
                '',
                $data['email'],
                $hashedPassword,
                $role
            );
            
            $savedUser = $this->userRepository->save($user);
            
            $result = [
                'type' => 'success',
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $savedUser->getId(),
                    'email' => $savedUser->getEmail(),
                    'role' => $savedUser->getRole()
                ],
                'links' => [
                    'signin' => [
                        'href' => '/auth/signin'
                    ]
                ]
            ];
            
            $response->getBody()->write(json_encode($result));
            return $response
                ->withStatus(201)
                ->withHeader('Content-Type', 'application/json');
                
        } catch (\Exception $e) {
            $error = [
                'type' => 'error',
                'error' => 500,
                'message' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
        }
    }
}