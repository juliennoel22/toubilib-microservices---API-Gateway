<?php

namespace toubilib\core\application\usecases;

use Exception;
use toubilib\core\application\ports\api\InputRendezVousDTO;
use toubilib\core\application\ports\api\RendezVousDTO;
use toubilib\core\application\ports\api\RendezVousDTOID;
use toubilib\core\application\ports\api\ServiceRendezVousInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\PraticienRepositoryInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\RendezVousRepositoryInterface;
use toubilib\core\domain\entities\praticien\RendezVous;
use Ramsey\Uuid\Uuid;


class ServiceRendezVous implements ServiceRendezVousInterface
{
    private RendezVousRepositoryInterface $rendezVousRepository;
    private PraticienRepositoryInterface $praticienRepository;

    public function __construct(
        RendezVousRepositoryInterface $rendezVousRepository,
        PraticienRepositoryInterface $praticienRepository
    ) {
        $this->rendezVousRepository = $rendezVousRepository;
        $this->praticienRepository = $praticienRepository;
    }

    public function ListerRendezVous(): array
    {
        $lien = "http://localhost:6080/prat/id";
        $rdvs = $this->rendezVousRepository->getAllRendezVous();
        $rdv = [];
        foreach ($rdvs as $element) {
            $rdv[] = new RendezVousDTO(
                $element->getId(),
                $element->getPraticien()->getNom(),
                $lien . "/" . $element->getPraticien()->getId(),
                $element->getPatientId(),
                $element->getPatientEmail(),
                $element->getStatus(),
                $element->getDuree(),
                $element->getDateHF(),
                $element->getDateC(),
                $element->getMotifVisite()
            );
        }
        return $rdv;
    }

   public function ListerRDVID(string $pid): array
{
    $rdvs = $this->rendezVousRepository->GetRdvsbyPrat($pid);
    $rdv = [];
    foreach ($rdvs as $element) {
        $rdv[] = new RendezVousDTOID(
            $element->getId(),
            $element->getPraticien()->getId(),
            $element->getPatientId(),
            $element->getPatientEmail(),
            $element->getStatus(),
            (int)$element->getDuree(),        
            $element->getDateHD(),           
            $element->getDateHF(),
            $element->getDateC(),
            $element->getMotifVisite()
        );
    }
    return $rdv;
}

    public function creerRendezVous(InputRendezVousDTO $dto): RendezVousDTO
    {

        try {
            $praticien = $this->praticienRepository->findPraticienId($dto->praticien_id);
        } catch (Exception $e) {
            throw new Exception("Praticien introuvable");
        }


        $this->validerCreneauHoraire($dto->date_heure);


        $dateDebut = new \DateTime($dto->date_heure);
        $dateFin = clone $dateDebut;
        $dateFin->modify("+{$dto->duree} minutes");


        $this->verifierDisponibilite($dto->praticien_id, $dateDebut, $dateFin);

        $uuid = Uuid::uuid4();

        $rdv = new RendezVous(
            $uuid,
            $praticien,
            $dto->patient_id,
            '', 
            $dateDebut->format('Y-m-d H:i:s'),
            1,
            (string)$dto->duree,
            $dateFin->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s'),
            $dto->motif_visite
        );


        $rdvSaved = $this->rendezVousRepository->save($rdv);


        return new RendezVousDTO(
            $rdvSaved->getId(),
            $rdvSaved->getPraticien()->getNom(),
            "http://localhost:6080/prat/id/" . $rdvSaved->getPraticien()->getId(),
            $rdvSaved->getPatientId(),
            $rdvSaved->getStatus(),
            $rdvSaved->getDuree(),
            $rdvSaved->getDateHF(),
            $rdvSaved->getDateC(),
            $rdvSaved->getMotifVisite()
        );
    }

    private function validerCreneauHoraire(string $dateHeure): void
    {
        $date = new \DateTime($dateHeure);
        $jourSemaine = (int)$date->format('N');
        $heure = (int)$date->format('H');

        if ($jourSemaine < 1 || $jourSemaine > 5) {
            throw new Exception("Le rendez-vous doit être pris en semaine (lundi-vendredi)");
        }

        if ($heure < 8 || $heure >= 19) {
            throw new Exception("Le rendez-vous doit être entre 8h et 19h");
        }

        if ($date <= new \DateTime()) {
            throw new Exception("Le rendez-vous doit être dans le futur");
        }
    }

    private function verifierDisponibilite(string $praticienId, \DateTime $debut, \DateTime $fin): void
    {
        $rdvs = $this->rendezVousRepository->findByPraticienAndPeriode(
            $praticienId,
            $debut->format('Y-m-d H:i:s'),
            $fin->format('Y-m-d H:i:s')
        );

        if (!empty($rdvs)) {
            throw new Exception("Le praticien n'est pas disponible pour ce créneau");
        }
    }

    public function annulerRendezVous(string $idRdv): void
    {
        try {
            $rdv = $this->rendezVousRepository->findById($idRdv);
        } catch (Exception $e) {
            throw new Exception("Rendez-vous introuvable");
        }

        if ($rdv === null) {
            throw new Exception("Rendez-vous introuvable");
        }

        $rdv->annuler();

        $this->rendezVousRepository->update($rdv);
    }
    public function HonorerRDV(string $idRdv): void
    {
        try {
            $rdv = $this->rendezVousRepository->findById($idRdv);
        } catch (Exception $e) {
            throw new Exception("Rendez-vous introuvable");
        }

        if ($rdv === null) {
            throw new Exception("Rendez-vous introuvable");
        }

        $rdv->honorer();

        $this->rendezVousRepository->update($rdv);
    }
    public function NePasHonorerRDV(string $idRdv): void
    {
        try {
            $rdv = $this->rendezVousRepository->findById($idRdv);
        } catch (Exception $e) {
            throw new Exception("Rendez-vous introuvable");
        }

        if ($rdv === null) {
            throw new Exception("Rendez-vous introuvable");
        }

        $rdv->nepashonorer();

        $this->rendezVousRepository->update($rdv);
    }

    public function consulterAgenda(string $praticienId, string $dateDebut, string $dateFin): array
{
    if ($dateDebut === null) {
        $dateDebut = (new \DateTime())->format('Y-m-d 00:00:00');
    }
    if ($dateFin === null) {
        $dateFin = (new \DateTime())->format('Y-m-d 23:59:59');
    }

    $rdvs = $this->rendezVousRepository->findByPraticienAndPeriode($praticienId, $dateDebut, $dateFin);

    $result = [];
    foreach ($rdvs as $rdv) {
        $result[] = new RendezVousDTOID(
            $rdv->getId(),
            $rdv->getPraticien()->getId(),
            $rdv->getPatientId(),
            $rdv->getPatientEmail(),
            $rdv->getStatus(),
            (int)$rdv->getDuree(),        
            $rdv->getDateHD(),          
            $rdv->getDateHF(),
            $rdv->getDateC(),
            $rdv->getMotifVisite()
        );
    }

    return $result;
}

    public function consulterRendezVous(string $idRdv): ?RendezVousDTOID
{
    try {
        $rdv = $this->rendezVousRepository->findById($idRdv);
    } catch (Exception $e) {
        return null;
    }
    
    if ($rdv === null) {
        return null;
    }
    
    return new RendezVousDTOID(
        $rdv->getId(),
        $rdv->getPraticien()->getId(),
        $rdv->getPatientId(),
        $rdv->getPatientEmail(),
        $rdv->getStatus(),                    
        (int)$rdv->getDuree(),                
        $rdv->getDateHD(),                   
        $rdv->getDateHF(),
        $rdv->getDateC(),
        $rdv->getMotifVisite()
    );
}
 public function listerCreneauxOccupes(string $praticienId, ?string $dateDebut = null, ?string $dateFin = null): array
    {
        if ($dateDebut === null) {
            $dateDebut = (new \DateTime())->format('Y-m-d 00:00:00');
        }
        if ($dateFin === null) {
            $dateFin = (new \DateTime('+1 month'))->format('Y-m-d 23:59:59');
        }

        $rdvs = $this->rendezVousRepository->findByPraticienAndPeriode($praticienId, $dateDebut, $dateFin);

        $creneauxOccupes = [];
        foreach ($rdvs as $rdv) {
            if ($rdv->getStatus() !== 5) {
                $creneauxOccupes[] = [
                    'date_debut' => $rdv->getDateHD(),
                    'date_fin' => $rdv->getDateHF(),
                    'disponible' => false
                ];
            }
        }

        return $creneauxOccupes;
    }

    public function getHistoriquePatient(string $patientId): array
    {
        $rdvs = $this->rendezVousRepository->getRdvsByPatient($patientId);
        $result = [];
        foreach ($rdvs as $rdv) {
            $result[] = new RendezVousDTOID(
                $rdv->getId(),
                $rdv->getPraticien()->getId(),
                $rdv->getPatientId(),
                $rdv->getPatientEmail(),
                $rdv->getStatus(),
                (int)$rdv->getDuree(),        
                $rdv->getDateHD(),          
                $rdv->getDateHF(),
                $rdv->getDateC(),
                $rdv->getMotifVisite()
            );
        }
        return $result;
    }
}