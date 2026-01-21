<?php
namespace toubilib\infra\repositories;

use PDO;
use Ramsey\Uuid\Uuid;
use toubilib\core\application\ports\spi\repositoryInterfaces\PatientRepositoryInterface;
use toubilib\core\domain\entities\praticien\Patient;

class PDOPatientRepository implements PatientRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(Patient $patient): Patient {
        $id = Uuid::uuid4()->toString();
        
        $stmt = $this->pdo->prepare("
            INSERT INTO patient (id, nom, prenom, date_naissance, adresse, code_postal, ville, email, telephone) 
            VALUES (:id, :nom, :prenom, :date_naissance, :adresse, :code_postal, :ville, :email, :telephone)
        ");
        
        $stmt->execute([
            ':id' => $id,
            ':nom' => $patient->getNom(),
            ':prenom' => $patient->getPrenom(),
            ':date_naissance' => $patient->getDateNaissance(),
            ':adresse' => $patient->getAdresse(),
            ':code_postal' => $patient->getCodePostal(),
            ':ville' => $patient->getVille(),
            ':email' => $patient->getEmail(),
            ':telephone' => $patient->getTelephone()
        ]);
        
        return new Patient(
            $id,
            $patient->getNom(),
            $patient->getPrenom(),
            $patient->getDateNaissance(),
            $patient->getAdresse(),
            $patient->getCodePostal(),
            $patient->getVille(),
            $patient->getEmail(),
            $patient->getTelephone()
        );
    }

    public function findById(string $id): ?Patient {
        $stmt = $this->pdo->prepare("
            SELECT id, nom, prenom, date_naissance, adresse, code_postal, ville, email, telephone 
            FROM patient 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new Patient(
            $row['id'],
            $row['nom'],
            $row['prenom'],
            $row['date_naissance'],
            $row['adresse'],
            $row['code_postal'],
            $row['ville'],
            $row['email'],
            $row['telephone']
        );
    }

    public function findByEmail(string $email): ?Patient {
        $stmt = $this->pdo->prepare("
            SELECT id, nom, prenom, date_naissance, adresse, code_postal, ville, email, telephone 
            FROM patient 
            WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new Patient(
            $row['id'],
            $row['nom'],
            $row['prenom'],
            $row['date_naissance'],
            $row['adresse'],
            $row['code_postal'],
            $row['ville'],
            $row['email'],
            $row['telephone']
        );
    }
}