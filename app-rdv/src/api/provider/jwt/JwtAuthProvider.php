<?php
namespace toubilib\api\provider\jwt;

use toubilib\api\provider\AuthProviderInterface;
use toubilib\core\application\ports\api\AuthNServiceInterface;
use toubilib\core\application\ports\api\CredentialsDTO;
use toubilib\core\application\ports\api\AuthDTO;
use toubilib\core\application\ports\api\UserProfileDTO;

class JwtAuthProvider implements AuthProviderInterface {
    private AuthNServiceInterface $authnService;
    private JwtManagerInterface $jwtManager;

    public function __construct(
        AuthNServiceInterface $authnService, 
        JwtManagerInterface $jwtManager
    ) {
        $this->authnService = $authnService;
        $this->jwtManager = $jwtManager;
    }

    // Fonctionnalité en cours, ne marche pas encore !
    public function register(CredentialsDTO $credentials, int $role): UserProfileDTO {
        return $this->authnService->register($credentials, $role);
    }

    public function signin(CredentialsDTO $credentials): AuthDTO {
        $profile = $this->authnService->byCredentials($credentials);
        
        $access_token = $this->jwtManager->create([
            'id' => $profile->id,
            'email' => $profile->email,
            'role' => $profile->role
        ], JwtManagerInterface::ACCESS_TOKEN);
        
        $refresh_token = $this->jwtManager->create([
            'id' => $profile->id,
            'email' => $profile->email,
            'role' => $profile->role
        ], JwtManagerInterface::REFRESH_TOKEN);
        
        return new AuthDTO($profile, $access_token, $refresh_token);
    }

    public function getSignedInUser(string $token): UserProfileDTO {
        // Décodage pour tester si le token est crée par toubilib
        $payload = $this->jwtManager->validate($token);
        
        return new UserProfileDTO(
            $payload['id'], 
            $payload['email'], 
            $payload['role']
        );
    }

    public function refresh(string $refreshToken): AuthDTO {
        $payload = $this->jwtManager->validate($refreshToken);
        
        $profile = new UserProfileDTO(
            $payload['id'],
            $payload['email'],
            $payload['role']
        );
        
        $access_token = $this->jwtManager->create([
            'id' => $profile->id,
            'email' => $profile->email,
            'role' => $profile->role
        ], JwtManagerInterface::ACCESS_TOKEN);
        
        $new_refresh_token = $this->jwtManager->create([
            'id' => $profile->id,
            'email' => $profile->email,
            'role' => $profile->role
        ], JwtManagerInterface::REFRESH_TOKEN);
        
        return new AuthDTO($profile, $access_token, $new_refresh_token);
    }
}