<?php
namespace toubilib\core\application\usecases;
use Exception;
use toubilib\core\application\ports\api\AuthzRDVServiceInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\RendezVousRepositoryInterface;

class AuthzRendezVousService implements AuthzRDVServiceInterface{
    public int $OPERATION_READ = 1;
    public int $OPERATION_UPDATE = 2;
    public int $OPERATION_DELETE = 3;
    public int $OPERATION_CREATE = 4;
    public int $OPERATION_LIST = 5;
    
    public int $ROLE_PATIENT = 5;
    public int $ROLE_PRATICIEN = 10;
    public int $ROLE_ADMIN = 100;
    
    private $rdvRepository;
    
    public function __construct(RendezVousRepositoryInterface $rdvRepository) {
        $this->rdvRepository = $rdvRepository;
    }
    
    public function isGranted(string $user_id, string $role, string $ressource_id, int $operation=1): bool {
        $roleInt = (int)$role;
        
        $rdv = $this->rdvRepository->findById($ressource_id);
        if (!$rdv) {
            throw new Exception("Erreur: Ressource introuvable");
        }
        
        if ($roleInt >= $this->ROLE_ADMIN) {
            return true;
        } else if ($roleInt >= $this->ROLE_PRATICIEN) {
            if ($user_id !== $rdv->getPraticien()->getId()) {
                throw new Exception("Erreur autorisation: Vous n'êtes pas le praticien de ce rendez-vous");
            }
        } else if ($roleInt >= $this->ROLE_PATIENT) {
            if ($user_id !== $rdv->getPatientId()) {
                throw new Exception("Erreur autorisation: Vous n'êtes pas le patient de ce rendez-vous");
            }
            
            if ($operation === $this->OPERATION_DELETE && $rdv->getStatus() === 3) {
                throw new Exception("Erreur autorisation: Impossible d'annuler un rendez-vous déjà honoré");
            }
        } else {
            throw new Exception("Erreur autorisation: Rôle invalide");
        }
        
        return true;
    }
}