<?php
namespace toubilib\core\application\ports\api;

interface ServiceIndisponibiliteInterface {
    public function creerIndisponibilite(string $praticienId, string $dateDebut, string $dateFin, string $motif, string $type): IndisponibiliteDTO;
    public function listerIndisponibilitesPraticien(string $praticienId): array;
    public function supprimerIndisponibilite(string $id): void;
    public function verifierDisponibilite(string $praticienId, string $dateDebut, string $dateFin): bool;
}