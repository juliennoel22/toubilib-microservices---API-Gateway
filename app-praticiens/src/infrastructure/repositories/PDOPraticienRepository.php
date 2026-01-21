<?php

namespace toubilib\infra\repositories;

use Exception;
use toubilib\core\application\ports\spi\repositoryinterfaces\PraticienRepositoryInterface;
use toubilib\core\domain\entities\praticien\Praticien;
use toubilib\core\domain\entities\praticien\Specialite;
use toubilib\core\domain\entities\praticien\Structure;
use toubilib\core\domain\entities\praticien\MoyenPaiement;
use toubilib\core\domain\entities\praticien\MotifVisite;

class PDOPraticienRepository implements PraticienRepositoryInterface
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }


    private function buildPraticien(array $row): Praticien
    {
        $structure = null;
        if (!empty($row['structure_id'])) {
            $stmtStruct = $this->pdo->prepare("
                SELECT id, nom, adresse, ville, code_postal, telephone 
                FROM structure 
                WHERE id = :structure_id
            ");
            $stmtStruct->execute([':structure_id' => $row['structure_id']]);
            $structData = $stmtStruct->fetch(\PDO::FETCH_ASSOC);
            
            if ($structData) {
                $structure = new Structure(
                    $structData['id'],
                    $structData['nom'],
                    $structData['adresse'],
                    $structData['ville'],
                    $structData['code_postal'],
                    $structData['telephone']
                );
            }
        }

        $stmtMoyens = $this->pdo->prepare("
            SELECT mp.id, mp.libelle 
            FROM moyen_paiement mp
            INNER JOIN praticien2moyen p2m ON mp.id = p2m.moyen_id
            WHERE p2m.praticien_id = :praticien_id
        ");
        $stmtMoyens->execute([':praticien_id' => $row['id']]);
        $moyensData = $stmtMoyens->fetchAll(\PDO::FETCH_ASSOC);
        $moyensPaiement = [];
        foreach ($moyensData as $moyen) {
            $moyensPaiement[] = new MoyenPaiement($moyen['id'], $moyen['libelle']);
        }

        $stmtMotifs = $this->pdo->prepare("
            SELECT mv.id, mv.specialite_id, mv.libelle 
            FROM motif_visite mv
            INNER JOIN praticien2motif p2m ON mv.id = p2m.motif_id
            WHERE p2m.praticien_id = :praticien_id
        ");
        $stmtMotifs->execute([':praticien_id' => $row['id']]);
        $motifsData = $stmtMotifs->fetchAll(\PDO::FETCH_ASSOC);
        $motifsVisite = [];
        foreach ($motifsData as $motif) {
            $motifsVisite[] = new MotifVisite(
                $motif['id'], 
                $motif['specialite_id'], 
                $motif['libelle']
            );
        }

        return new Praticien(
            $row['id'],
            $row['nom'],
            $row['prenom'],
            $row['ville'],
            $row['email'],
            $row['telephone'],
            new Specialite(
                $row['sp_id'],
                $row['sp_libelle'],
                $row['sp_description']
            ),
            $structure,
            $row['rpps_id'],
            $row['organisation'] === '1' || $row['organisation'] === true,
            $row['nouveau_patient'] === '1' || $row['nouveau_patient'] === true,
            $row['titre'],
            $moyensPaiement,
            $motifsVisite
        );
    }

    public function getAllPraticiens(): array
    {
        $statement = $this->pdo->prepare("
            SELECT p.id, p.nom, p.prenom, p.ville, p.email, p.telephone, 
                   p.rpps_id, p.organisation, p.nouveau_patient, p.titre, p.structure_id,
                   s.id as sp_id, s.libelle as sp_libelle, s.description as sp_description 
            FROM praticien p
            JOIN specialite s ON p.specialite_id = s.id
        ");
        $statement->execute();
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $praticiens = [];
        foreach ($results as $res) {
            $praticiens[] = $this->buildPraticien($res);
        }
        return $praticiens;
    }
    public function findPraticien(string $id_p): Praticien
    {
        $statement = $this->pdo->prepare("
            SELECT p.id, p.nom, p.prenom, p.ville, p.email, p.telephone,
                   p.rpps_id, p.organisation, p.nouveau_patient, p.titre, p.structure_id,
                   s.id as sp_id, s.libelle as sp_libelle, s.description as sp_description 
            FROM praticien p
            JOIN specialite s ON p.specialite_id = s.id
            WHERE p.nom = :pid
        ");
        
        $statement->execute([":pid" => $id_p]);
        $results = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!$results) {
            throw new Exception("Praticien not found");
        }        
        
        return $this->buildPraticien($results);
    }
    public function findPraticienId(string $id_p): Praticien
    {
        $statement = $this->pdo->prepare("
            SELECT p.id, p.nom, p.prenom, p.ville, p.email, p.telephone,
                   p.rpps_id, p.organisation, p.nouveau_patient, p.titre, p.structure_id,
                   s.id as sp_id, s.libelle as sp_libelle, s.description as sp_description 
            FROM praticien p
            JOIN specialite s ON p.specialite_id = s.id
            WHERE p.id = :pid
        ");
        
        $statement->execute([":pid" => $id_p]);
        $results = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!$results) {
            throw new Exception("Praticien not found");
        }        
        
        return $this->buildPraticien($results);
    }
    public function findPraticienBy(string $type, string $value): array
    {
        if ($type == "ville") {
            $statement = $this->pdo->prepare("
                SELECT p.id, p.nom, p.prenom, p.ville, p.email, p.telephone,
                       p.rpps_id, p.organisation, p.nouveau_patient, p.titre, p.structure_id,
                       s.id as sp_id, s.libelle as sp_libelle, s.description as sp_description 
                FROM praticien p
                JOIN specialite s ON p.specialite_id = s.id
                WHERE p.ville = :pville
            ");
            
            $statement->execute([":pville" => $value]);
            $results_a = $statement->fetchAll(\PDO::FETCH_ASSOC);
            
            $praticiens = [];
            foreach ($results_a as $results) {
                $praticiens[] = $this->buildPraticien($results);
            } 
            return $praticiens;
        }
        else if ($type == "specialite") {
            $statement = $this->pdo->prepare("
                SELECT p.id, p.nom, p.prenom, p.ville, p.email, p.telephone,
                       p.rpps_id, p.organisation, p.nouveau_patient, p.titre, p.structure_id,
                       s.id as sp_id, s.libelle as sp_libelle, s.description as sp_description 
                FROM praticien p
                JOIN specialite s ON p.specialite_id = s.id
                WHERE s.libelle = :libelle
            ");
            
            $statement->execute([":libelle" => $value]);
            $results_a = $statement->fetchAll(\PDO::FETCH_ASSOC);
            
            $praticiens = [];
            foreach ($results_a as $results) {
                $praticiens[] = $this->buildPraticien($results);
            }
            return $praticiens;
        }
        else {
            throw new Exception("Erreur");
        }
    }
 
}