<?php
namespace toubilib\api\provider;

use toubilib\core\application\ports\api\CredentialsDTO;
use toubilib\core\application\ports\api\AuthDTO;
use toubilib\core\application\ports\api\UserProfileDTO;

interface AuthProviderInterface {
    public function register(CredentialsDTO $credentials, int $role): UserProfileDTO;
    
    public function signin(CredentialsDTO $credentials): AuthDTO;
    
    public function getSignedInUser(string $token): UserProfileDTO;
    
    public function refresh(string $refreshToken): AuthDTO;
}