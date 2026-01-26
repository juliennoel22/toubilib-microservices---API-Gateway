<?php
namespace toubilib\infra\repositories;

use PDO;
use Ramsey\Uuid\Uuid;
use toubilib\core\application\ports\spi\repositoryInterfaces\UserRepositoryInterface;
use toubilib\core\domain\entities\auth\User;

class UserRepository implements UserRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function FindById(string $id): ?User {
        $stmt = $this->pdo->prepare("
            SELECT id, email, password, role 
            FROM users 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new User(
            $row['id'],
            $row['email'],
            $row['password'],
            $row['role']
        );
    }

    public function FindByEmail(string $email): ?User {
        $stmt = $this->pdo->prepare("
            SELECT id, email, password, role 
            FROM users 
            WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new User(
            $row['id'],
            $row['email'],
            $row['password'],
            $row['role']
        );
    }

    public function save(User $user): User {
        $id = Uuid::uuid4()->toString();
        
        $stmt = $this->pdo->prepare("
            INSERT INTO users (id, email, password, role) 
            VALUES (:id, :email, :password, :role)
        ");
        
        $stmt->execute([
            ':id' => $id,
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword(),
            ':role' => $user->getRole()
        ]);
        
        return new User(
            $id,
            $user->getEmail(),
            $user->getPassword(),
            $user->getRole()
        );
    }
}