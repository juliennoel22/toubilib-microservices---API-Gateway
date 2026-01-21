<?php

namespace toubilib\core\application\usecases;

use toubilib\core\application\ports\api\PraticienDTO;
use toubilib\core\application\ports\api\MoyenPaiementDTO;
use toubilib\core\application\ports\api\MotifVisiteDTO;
use toubilib\core\application\ports\api\StructureDTO;
use toubilib\core\application\ports\api\ServicePraticienInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\PraticienRepositoryInterface;
use toubilib\core\domain\entities\praticien\Praticien;

class ServicePraticien implements ServicePraticienInterface
{
    private PraticienRepositoryInterface $praticienRepository;

    public function __construct(PraticienRepositoryInterface $praticienRepository)
    {
        $this->praticienRepository = $praticienRepository;
    }

  
    private function toPraticienDTO(Praticien $praticien): PraticienDTO
    {
        $structureDTO = null;
        if ($praticien->getStructure() !== null) {
            $struct = $praticien->getStructure();
            $structureDTO = new StructureDTO(
                $struct->getId(),
                $struct->getNom(),
                $struct->getAdresse(),
                $struct->getVille(),
                $struct->getCodePostal(),
                $struct->getTelephone()
            );
        }

        $moyensPaiementDTO = [];
        foreach ($praticien->getMoyensPaiement() as $moyen) {
            $moyensPaiementDTO[] = new MoyenPaiementDTO(
                $moyen->getId(),
                $moyen->getLibelle()
            );
        }

        $motifsVisiteDTO = [];
        foreach ($praticien->getMotifsVisite() as $motif) {
            $motifsVisiteDTO[] = new MotifVisiteDTO(
                $motif->getId(),
                $motif->getSpecialiteId(),
                $motif->getLibelle()
            );
        }

        return new PraticienDTO(
            $praticien->getId(),
            $praticien->getNom(),
            $praticien->getPrenom(),
            $praticien->getVille(),
            $praticien->getEmail(),
            $praticien->getTelephone(),
            $praticien->getSpecialite()->getLibelle(),
            $praticien->getSpecialite()->getDescription(),
            $structureDTO,
            $praticien->getRppsId(),
            $praticien->isOrganisation(),
            $praticien->accepteNouveauPatient(),
            $praticien->getTitre(),
            $moyensPaiementDTO,
            $motifsVisiteDTO
        );
    }

    public function ListerPraticiens(): array
    {
        $praticiensrepos = $this->praticienRepository->GetAllPraticiens();
        $praticiens = [];
        foreach ($praticiensrepos as $prep) {
            $praticiens[] = $this->toPraticienDTO($prep);
        }
        return $praticiens;
    }

    public function RecherchePraticiens(string $type, string $value): array
    {
        $praticiens = [];
        $praticienrepos = $this->praticienRepository->findPraticienBy($type, $value);
        foreach ($praticienrepos as $prep) {
            $praticiens[] = $this->toPraticienDTO($prep);
        }
        return $praticiens;
    }

    public function ListerPraticien(string $nom): PraticienDTO
    {
        $prep = $this->praticienRepository->findPraticien($nom);
        return $this->toPraticienDTO($prep);
    }

    public function ListerPraticienId(string $id): PraticienDTO
    {
        $prep = $this->praticienRepository->findPraticienId($id);
        return $this->toPraticienDTO($prep);
    }
}