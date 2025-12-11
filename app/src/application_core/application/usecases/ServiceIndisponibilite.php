<?php
namespace toubilib\core\application\usecases;

use Exception;
use Ramsey\Uuid\Uuid;
use toubilib\core\application\ports\api\IndisponibiliteDTO;
use toubilib\core\application\ports\api\ServiceIndisponibiliteInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\IndisponibiliteRepositoryInterface;
use toubilib\core\domain\entities\praticien\Indisponibilite;

class ServiceIndisponibilite implements ServiceIndisponibiliteInterface {
    private IndisponibiliteRepositoryInterface $indisponibiliteRepository;

    public function __construct(IndisponibiliteRepositoryInterface $indisponibiliteRepository) {
        $this->indisponibiliteRepository = $indisponibiliteRepository;
    }

    public function creerIndisponibilite(string $praticienId, string $dateDebut, string $dateFin, string $motif, string $type): IndisponibiliteDTO {
        $this->validerPeriode($dateDebut, $dateFin);
        
        $conflits = $this->indisponibiliteRepository->findByPraticienAndPeriode($praticienId, $dateDebut, $dateFin);
        if (!empty($conflits)) {
            throw new Exception("Une indisponibilité existe déjà pour cette période");
        }

        $id = Uuid::uuid4()->toString();
        $dateCreation = (new \DateTime())->format('Y-m-d H:i:s');

        $indisponibilite = new Indisponibilite(
            $id,
            $praticienId,
            $dateDebut,
            $dateFin,
            $motif,
            $type,
            $dateCreation
        );

        $saved = $this->indisponibiliteRepository->save($indisponibilite);

        return new IndisponibiliteDTO(
            $saved->getId(),
            $saved->getPraticienId(),
            $saved->getDateDebut(),
            $saved->getDateFin(),
            $saved->getMotif(),
            $saved->getType(),
            $saved->getDateCreation()
        );
    }

    public function listerIndisponibilitesPraticien(string $praticienId): array {
        $indisponibilites = $this->indisponibiliteRepository->findByPraticienId($praticienId);
        
        $dtos = [];
        foreach ($indisponibilites as $indispo) {
            $dtos[] = new IndisponibiliteDTO(
                $indispo->getId(),
                $indispo->getPraticienId(),
                $indispo->getDateDebut(),
                $indispo->getDateFin(),
                $indispo->getMotif(),
                $indispo->getType(),
                $indispo->getDateCreation()
            );
        }

        return $dtos;
    }

    public function supprimerIndisponibilite(string $id): void {
        $indispo = $this->indisponibiliteRepository->findById($id);
        if (!$indispo) {
            throw new Exception("Indisponibilité introuvable");
        }

        $this->indisponibiliteRepository->delete($id);
    }

    public function verifierDisponibilite(string $praticienId, string $dateDebut, string $dateFin): bool {
        $indisponibilites = $this->indisponibiliteRepository->findByPraticienAndPeriode($praticienId, $dateDebut, $dateFin);
        return empty($indisponibilites);
    }

    private function validerPeriode(string $dateDebut, string $dateFin): void {
        $debut = new \DateTime($dateDebut);
        $fin = new \DateTime($dateFin);

        if ($debut >= $fin) {
            throw new Exception("La date de début doit être antérieure à la date de fin");
        }

        if ($debut < new \DateTime()) {
            throw new Exception("La date de début ne peut pas être dans le passé");
        }
    }
}