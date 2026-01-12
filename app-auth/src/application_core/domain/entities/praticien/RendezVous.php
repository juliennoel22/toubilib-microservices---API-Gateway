<?php

namespace toubilib\core\domain\entities\praticien;

use Exception;

class RendezVous
{
    private string $id;
    private Praticien $praticien;
    private string $patient_id;
    private string $patient_email;
    private int $status;
    private string $duree;
    private string $date_heure_debut;
    private string $date_heure_fin;
    private string $date_creation;
    private string $motif_visite;

    public function __construct(
        string $id,
        Praticien $praticien,
        string $patient_id,
        string $patient_email,
        string $date_heure_debut,
        int $status,
        string $duree,
        string $date_heure_fin,
        string $date_creation,
        string $motif_visite
    ) {
        $this->id = $id;
        $this->praticien = $praticien;
        $this->patient_id = $patient_id;
        $this->patient_email = $patient_email;
        $this->status = $status;
        $this->date_heure_debut = $date_heure_debut;
        $this->duree = $duree;
        $this->date_heure_fin = $date_heure_fin;
        $this->date_creation = $date_creation;
        $this->motif_visite = $motif_visite;
    }

    public function annuler(): void
    {
        if ($this->status === 5) {
            throw new Exception("Le rendez-vous est déjà annulé");
        }

        $dateRdv = new \DateTime($this->date_heure_debut);
        $maintenant = new \DateTime();
        
        if ($dateRdv <= $maintenant) {
            throw new Exception("Impossible d'annuler un rendez-vous passé");
        }

        $this->status = 5;
    }
    public function honorer(): void
    {
        if ($this->status === 5) {
            throw new Exception("Le rendez-vous est déjà annulé");
        }
        else if ($this->status === 3) {
            throw new Exception("Le rendez-vous est déjà honoré");
        }

        $dateRdv = new \DateTime($this->date_heure_debut);
        $maintenant = new \DateTime();
        
        if ($dateRdv <= $maintenant) {
            throw new Exception("Impossible d'annuler un rendez-vous passé");
        }

        $this->status = 3;
    }
    public function nepashonorer(): void
    {
        if ($this->status === 5) {
            throw new Exception("Le rendez-vous est déjà annulé");
        }

        $dateRdv = new \DateTime($this->date_heure_debut);
        $maintenant = new \DateTime();
        
        if ($dateRdv <= $maintenant) {
            throw new Exception("Impossible d'annuler un rendez-vous passé");
        }

        $this->status = 4;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getPraticien(): Praticien
    {
        return $this->praticien;
    }

    public function getPatientId(): string
    {
        return $this->patient_id;
    }

    public function getPatientEmail(): string
    {
        return $this->patient_email;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getDuree(): string
    {
        return $this->duree;
    }

    public function getDateHF(): string
    {
        return $this->date_heure_fin;
    }

    public function getDateHD(): string
    {
        return $this->date_heure_debut;
    }

    public function getDateC(): string
    {
        return $this->date_creation;
    }

    public function getMotifVisite(): string
    {
        return $this->motif_visite;
    }
}