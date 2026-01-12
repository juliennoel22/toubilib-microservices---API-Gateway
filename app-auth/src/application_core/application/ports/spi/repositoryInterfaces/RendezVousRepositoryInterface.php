<?php

namespace toubilib\core\application\ports\spi\repositoryInterfaces;

use toubilib\core\domain\entities\praticien\RendezVous;

interface RendezVousRepositoryInterface
{
    public function getAllRendezVous(): array;
    public function GetRdvsbyPrat(string $pid): array;
    public function save(RendezVous $rdv): RendezVous;
    public function findById(string $id): RendezVous;
    public function update(RendezVous $rdv): void;
    public function findByPraticienAndPeriode(string $praticienId, string $dateDebut, string $dateFin): array;
    public function getRdvsByPatient(string $patientId): array;
}