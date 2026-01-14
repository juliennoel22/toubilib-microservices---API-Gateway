<?php

namespace toubilib\infra\repositories;

use Exception;
use toubilib\core\application\ports\spi\repositoryInterfaces\PraticienRepositoryInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\RendezVousRepositoryInterface;
use toubilib\core\domain\entities\praticien\Praticien;
use toubilib\core\domain\entities\praticien\RendezVous;
use toubilib\core\domain\entities\praticien\Specialite;

class PDORendezVousRepository implements RendezVousRepositoryInterface
{
    private \PDO $pdo;
    private PraticienRepositoryInterface $praticien_interface;

    public function __construct(\PDO $pdo, PraticienRepositoryInterface $praticien_interface)
    {
        $this->pdo = $pdo;
        $this->praticien_interface = $praticien_interface;
    }

    public function getAllRendezVous(): array
    {
        $stmt = $this->pdo->prepare("SELECT r.id, r.praticien_id, r.patient_id, r.patient_email, r.date_heure_debut,
        r.status, r.duree, r.date_heure_fin, r.date_creation, r.motif_visite
         from rdv r");
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $rdvs = [];
        foreach ($results as $rt) {
            try {
                $pdopraticien = $this->praticien_interface->findPraticienId($rt['praticien_id']);
            } catch (Exception $e) {
                $pdopraticien = new Praticien('', '', '', '', '', new Specialite('', '', ''));
            }

            $rdvs[] = new RendezVous(
                $rt['id'],
                $pdopraticien,
                $rt['patient_id'],
                $rt['patient_email'] ?? ' ',
                $rt['date_heure_debut'],
                $rt['status'],
                $rt['duree'],
                $rt['date_heure_fin'],
                $rt['date_creation'],
                $rt['motif_visite']
            );
        }
        return $rdvs;
    }

    public function GetRdvsbyPrat(string $pid): array
    {
        $stmt = $this->pdo->prepare("SELECT r.id, r.praticien_id, r.patient_id, r.patient_email, r.date_heure_debut,
        r.status, r.duree, r.date_heure_fin, r.date_creation, r.motif_visite
         from rdv r where r.praticien_id = :pid");
        $stmt->execute(["pid" => $pid]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $rdvs = [];
        foreach ($results as $rt) {
            if (empty($rt['praticien_id'])) {
                continue;
            }
            try {
                $pdopraticien = $this->praticien_interface->findPraticienId($rt['praticien_id']);
            } catch (Exception $e) {
                continue;
            }
            $rdvs[] = new RendezVous(
                $rt['id'],
                $pdopraticien,
                $rt['patient_id'],
                $rt['patient_email'] ?? ' ',
                $rt['date_heure_debut'],
                $rt['status'],
                $rt['duree'],
                $rt['date_heure_fin'],
                $rt['date_creation'],
                $rt['motif_visite']
            );
        }
        return $rdvs;
    }

    public function save(RendezVous $rdv): RendezVous
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO rdv (id, praticien_id, patient_id, patient_email, date_heure_debut, status, duree, date_heure_fin, date_creation, motif_visite)
             VALUES (:id, :praticien_id, :patient_id, :patient_email, :date_heure_debut, :status, :duree, :date_heure_fin, :date_creation, :motif_visite)"
        );

        $stmt->execute([
            'id' => $rdv->getId(),
            'praticien_id' => $rdv->getPraticien()->getId(),
            'patient_id' => $rdv->getPatientId(),
            'patient_email' => $rdv->getPatientEmail(),
            'date_heure_debut' => $rdv->getDateHD(),
            'status' => $rdv->getStatus(),
            'duree' => $rdv->getDuree(),
            'date_heure_fin' => $rdv->getDateHF(),
            'date_creation' => $rdv->getDateC(),
            'motif_visite' => $rdv->getMotifVisite()
        ]);

        return $rdv;
    }

    public function findById(string $id): RendezVous
    {
        $stmt = $this->pdo->prepare(
            "SELECT r.id, r.praticien_id, r.patient_id, r.patient_email, r.date_heure_debut,
            r.status, r.duree, r.date_heure_fin, r.date_creation, r.motif_visite
            FROM rdv r WHERE r.id = :id"
        );
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            throw new \Exception("Rendez-vous not found with id: " . $id);
        }

        $praticien = $this->praticien_interface->findPraticienId($result['praticien_id']);

        return new RendezVous(
            $result['id'],
            $praticien,
            $result['patient_id'],
            $result['patient_email'] ?? '',
            $result['date_heure_debut'],
            $result['status'],
            $result['duree'],
            $result['date_heure_fin'],
            $result['date_creation'],
            $result['motif_visite']
        );
    }

    public function update(RendezVous $rdv): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE rdv SET 
                status = :status,
                patient_email = :patient_email,
                date_heure_debut = :date_heure_debut,
                duree = :duree,
                date_heure_fin = :date_heure_fin,
                motif_visite = :motif_visite
            WHERE id = :id"
        );

        $stmt->execute([
            'id' => $rdv->getId(),
            'status' => $rdv->getStatus(),
            'patient_email' => $rdv->getPatientEmail(),
            'date_heure_debut' => $rdv->getDateHD(),
            'duree' => $rdv->getDuree(),
            'date_heure_fin' => $rdv->getDateHF(),
            'motif_visite' => $rdv->getMotifVisite()
        ]);
    }

    public function findByPraticienAndPeriode(string $praticienId, string $dateDebut, string $dateFin): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT r.id, r.praticien_id, r.patient_id, r.patient_email, r.date_heure_debut,
            r.status, r.duree, r.date_heure_fin, r.date_creation, r.motif_visite
            FROM rdv r 
            WHERE r.praticien_id = :praticien_id 
            AND r.status != '5'
            AND (
                (r.date_heure_debut BETWEEN :date_debut AND :date_fin)
                OR (r.date_heure_fin BETWEEN :date_debut AND :date_fin)
                OR (r.date_heure_debut <= :date_debut AND r.date_heure_fin >= :date_fin)
            )"
        );

        $stmt->execute([
            'praticien_id' => $praticienId,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin
        ]);

        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $rdvs = [];

        foreach ($results as $rt) {
            if (empty($rt['praticien_id'])) {
                continue;
            }
            try {
                $praticien = $this->praticien_interface->findPraticienId($rt['praticien_id']);
            } catch (Exception $e) {
                continue;
            }
            $rdvs[] = new RendezVous(
                $rt['id'],
                $praticien,
                $rt['patient_id'],
                $rt['patient_email'] ?? '',
                $rt['date_heure_debut'],
                $rt['status'],
                $rt['duree'],
                $rt['date_heure_fin'],
                $rt['date_creation'],
                $rt['motif_visite']
            );
        }

        return $rdvs;
    }

    public function getRdvsByPatient(string $patientId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT r.id, r.praticien_id, r.patient_id, r.patient_email, r.date_heure_debut,
            r.status, r.duree, r.date_heure_fin, r.date_creation, r.motif_visite
            FROM rdv r 
            WHERE r.patient_id = :patient_id
            ORDER BY r.date_heure_debut DESC"
        );

        $stmt->execute(['patient_id' => $patientId]);
        
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $rdvs = [];

        foreach ($results as $rt) {
            try {
                $praticien = $this->praticien_interface->findPraticienId($rt['praticien_id']);
            } catch (Exception $e) {
                $praticien = new Praticien($rt['praticien_id'], 'Inconnu', '', '', '', new Specialite('', '', ''));
            }

            $rdvs[] = new RendezVous(
                $rt['id'],
                $praticien,
                $rt['patient_id'],
                $rt['patient_email'] ?? '',
                $rt['date_heure_debut'],
                $rt['status'],
                $rt['duree'],
                $rt['date_heure_fin'],
                $rt['date_creation'],
                $rt['motif_visite']
            );
        }

        return $rdvs;
    }
}