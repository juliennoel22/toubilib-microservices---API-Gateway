<?php
namespace toubilib\core\application\usecases;

use Exception;
use toubilib\core\application\ports\api\AuthNServiceInterface;
use toubilib\core\application\ports\api\UserProfileDTO;
use toubilib\core\application\ports\api\CredentialsDTO;
use toubilib\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;
use toubilib\core\domain\entities\auth\User;

class AuthnService implements AuthNServiceInterface {
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function register(CredentialsDTO $credentials, int $role): UserProfileDTO {
        // Vérifier si l'utilisateur existe déjà
        $existingUser = $this->userRepository->FindByEmail($credentials->email);
        if ($existingUser !== null) {
            throw new Exception("User with this email already exists");
        }

        // Hasher le mot de passe
        $hashedPassword = password_hash($credentials->password, PASSWORD_DEFAULT);
        
        // Créer l'utilisateur
        $user = new User(
            '',
            $credentials->email,
            $hashedPassword,
            (string)$role
        );
        
        $savedUser = $this->userRepository->save($user);
        
        return new UserProfileDTO(
            $savedUser->getId(),
            $savedUser->getEmail(),
            $savedUser->getRole()
        );
    }

    public function byCredentials(CredentialsDTO $credentials): UserProfileDTO {
        $user = $this->userRepository->FindByEmail($credentials->email);
        
        if ($user === null) {
            throw new Exception("Invalid credentials");
        }
        
        if (!$user->verifyPassword($credentials->password)) {
            throw new Exception("Invalid credentials");
        }
        
        return new UserProfileDTO(
            $user->getId(),
            $user->getEmail(),
            $user->getRole()
        );
    }
}