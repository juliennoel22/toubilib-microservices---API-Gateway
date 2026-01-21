<?php
namespace toubilib\core\domain\entities\praticien;

class Indisponibilite {
    private string $id;
    private string $praticien_id;
    private string $date_debut;
    private string $date_fin;
    private string $motif;
    private string $type;
    private string $date_creation;

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

    public function getId(): string { return $this->id; }
    public function getPraticienId(): string { return $this->praticien_id; }
    public function getDateDebut(): string { return $this->date_debut; }
    public function getDateFin(): string { return $this->date_fin; }
    public function getMotif(): string { return $this->motif; }
    public function getType(): string { return $this->type; }
    public function getDateCreation(): string { return $this->date_creation; }
}