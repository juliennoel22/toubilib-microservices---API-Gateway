<?php
namespace toubilib\core\application\ports\api;

class IndisponibiliteDTO {
    public string $id;
    public string $praticien_id;
    public string $date_debut;
    public string $date_fin;
    public string $motif;
    public string $type;
    public string $date_creation;

    public function __construct(
        string $id,
        string $praticien_id,
        string $date_debut,
        string $date_fin,
        string $motif,
        string $type,
        string $date_creation
    ) {
        $this->id = $id;
        $this->praticien_id = $praticien_id;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->motif = $motif;
        $this->type = $type;
        $this->date_creation = $date_creation;
    }
}