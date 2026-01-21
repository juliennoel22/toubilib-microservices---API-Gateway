<?php
namespace toubilib\core\application\ports\spi\repositoryInterfaces;

use toubilib\core\domain\entities\praticien\Indisponibilite;

interface IndisponibiliteRepositoryInterface {
    public function save(Indisponibilite $indisponibilite): Indisponibilite;
    public function findByPraticienId(string $praticienId): array;
    public function findById(string $id): ?Indisponibilite;
    public function delete(string $id): void;
    public function findByPraticienAndPeriode(string $praticienId, string $dateDebut, string $dateFin): array;
}