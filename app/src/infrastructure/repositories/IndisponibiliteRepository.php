<?php
namespace toubilib\infra\repositories;

use PDO;
use toubilib\core\application\ports\spi\repositoryInterfaces\IndisponibiliteRepositoryInterface;
use toubilib\core\domain\entities\praticien\Indisponibilite;

class PDOIndisponibiliteRepository implements IndisponibiliteRepositoryInterface {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function save(Indisponibilite $indisponibilite): Indisponibilite {
        $stmt = $this->pdo->prepare("
            INSERT INTO indisponibilite (id, praticien_id, date_debut, date_fin, motif, type, date_creation) 
            VALUES (:id, :praticien_id, :date_debut, :date_fin, :motif, :type, :date_creation)
        ");
        
        $stmt->execute([
            ':id' => $indisponibilite->getId(),
            ':praticien_id' => $indisponibilite->getPraticienId(),
            ':date_debut' => $indisponibilite->getDateDebut(),
            ':date_fin' => $indisponibilite->getDateFin(),
            ':motif' => $indisponibilite->getMotif(),
            ':type' => $indisponibilite->getType(),
            ':date_creation' => $indisponibilite->getDateCreation()
        ]);
        
        return $indisponibilite;
    }

    public function findByPraticienId(string $praticienId): array {
        $stmt = $this->pdo->prepare("
            SELECT id, praticien_id, date_debut, date_fin, motif, type, date_creation 
            FROM indisponibilite 
            WHERE praticien_id = :praticien_id 
            ORDER BY date_debut ASC
        ");
        $stmt->execute([':praticien_id' => $praticienId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $indisponibilites = [];
        foreach ($results as $row) {
            $indisponibilites[] = new Indisponibilite(
                $row['id'],
                $row['praticien_id'],
                $row['date_debut'],
                $row['date_fin'],
                $row['motif'],
                $row['type'],
                $row['date_creation']
            );
        }
        
        return $indisponibilites;
    }

    public function findById(string $id): ?Indisponibilite {
        $stmt = $this->pdo->prepare("
            SELECT id, praticien_id, date_debut, date_fin, motif, type, date_creation 
            FROM indisponibilite 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }
        
        return new Indisponibilite(
            $row['id'],
            $row['praticien_id'],
            $row['date_debut'],
            $row['date_fin'],
            $row['motif'],
            $row['type'],
            $row['date_creation']
        );
    }

    public function delete(string $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM indisponibilite WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function findByPraticienAndPeriode(string $praticienId, string $dateDebut, string $dateFin): array {
        $stmt = $this->pdo->prepare("
            SELECT id, praticien_id, date_debut, date_fin, motif, type, date_creation 
            FROM indisponibilite 
            WHERE praticien_id = :praticien_id 
            AND (
                (date_debut BETWEEN :date_debut AND :date_fin)
                OR (date_fin BETWEEN :date_debut AND :date_fin)
                OR (date_debut <= :date_debut AND date_fin >= :date_fin)
            )
            ORDER BY date_debut ASC
        ");
        
        $stmt->execute([
            ':praticien_id' => $praticienId,
            ':date_debut' => $dateDebut,
            ':date_fin' => $dateFin
        ]);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $indisponibilites = [];
        foreach ($results as $row) {
            $indisponibilites[] = new Indisponibilite(
                $row['id'],
                $row['praticien_id'],
                $row['date_debut'],
                $row['date_fin'],
                $row['motif'],
                $row['type'],
                $row['date_creation']
            );
        }
        
        return $indisponibilites;
    }
}