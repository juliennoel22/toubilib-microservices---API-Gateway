<?php
namespace toubilib\core\application\ports\spi\repositoryInterfaces;

use toubilib\core\domain\entities\auth\User;

interface UserRepositoryInterface {
    public function FindById(string $id): ?User;
    
    public function FindByEmail(string $email): ?User;
    
    public function save(User $user): User;
}