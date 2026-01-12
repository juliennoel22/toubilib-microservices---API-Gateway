<?php

namespace toubilib\core\application\ports\api;

class RendezVousDTOID{
    public string $id;
    // private Praticien $praticien;
    public string $praticien_id;
    public string $patient_id;
    public string $patient_email;
    public string $status;
    public string $duree;
    public string $date_heure_debut; 
    public string $date_heure_fin;
    public string $date_creation;
    public string $motif_visite;
    

    public function __construct(
        string $id,
        string $praticien_id,
        string $patient_id,
        string $patient_email,
        string $status,
        string $duree,
        string $date_heure_debut,
        string $date_heure_fin,
        string $date_creation,
        string $motif_visite,
    )
    {
    $this->id=$id;
    $this->praticien_id=$praticien_id;
    $this->patient_id=$patient_id;
    $this->patient_email=$patient_email;
    $this->status=$status;
    $this->duree=$duree;
    $this->date_heure_debut=$date_heure_debut;
    $this->date_heure_fin=$date_heure_fin;
    $this->date_creation=$date_creation;
    $this->motif_visite=$motif_visite;
    }


}