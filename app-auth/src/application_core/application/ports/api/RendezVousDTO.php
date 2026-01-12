<?php

namespace toubilib\core\application\ports\api;

class RendezVousDTO{
    public string $id;
    // private Praticien $praticien;
    public string $praticien_id;
    public string $praticien_url;
    public string $patient_id;
    public string $status;
    public string $duree;
    public string $date_heure_fin;
    public string $date_creation;
    public string $motif_visite;
    

    public function __construct(
        string $id,
        string $praticien_id,
        string $praticien_url, 
        string $patient_id,
        string $status,
        string $duree,
        string $date_heure_fin,
        string $date_creation,
        string $motif_visite,
    )
    {
    $this->id=$id;
    $this->praticien_id=$praticien_id;
    $this->praticien_url=$praticien_url;
    $this->patient_id=$patient_id;
    $this->status=$status;
    $this->duree=$duree;
    $this->date_heure_fin=$date_heure_fin;
    $this->date_creation=$date_creation;
    $this->motif_visite=$motif_visite;
    }


}