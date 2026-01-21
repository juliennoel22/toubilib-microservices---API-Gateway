<?php
namespace toubilib\core\application\ports\api;

interface AuthNServiceInterface {
    public function register(CredentialsDTO $credentials, int $role): UserProfileDTO;
    public function byCredentials(CredentialsDTO $credentials): UserProfileDTO;
}