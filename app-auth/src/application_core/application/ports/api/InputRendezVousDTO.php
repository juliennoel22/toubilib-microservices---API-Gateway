<?php

namespace toubilib\core\application\ports\api;

class InputRendezVousDTO
{
    public string $praticien_id;
    public string $patient_id;
    public string $date_heure;
    public string $motif_visite;
    public int $duree; 

    public function __construct(
        string $praticien_id,
        string $patient_id,
        string $date_heure,
        string $motif_visite,
        int $duree
    ) {
        $this->praticien_id = $praticien_id;
        $this->patient_id = $patient_id;
        $this->date_heure = $date_heure;
        $this->motif_visite = $motif_visite;
        $this->duree = $duree;
    }
}