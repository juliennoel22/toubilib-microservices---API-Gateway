<?php
namespace toubilib\core\application\usecases;
use Exception;
use toubilib\core\application\ports\api\AuthzPraticienServiceInterface;
use toubilib\core\application\ports\spi\repositoryInterfaces\PraticienRepositoryInterface;

class AuthzPraticienService implements AuthzPraticienServiceInterface {
    //Déclaration des opérations
    public int $OPERATION_READ = 1;
    public int $OPERATION_UPDATE = 2;
    public int $OPERATION_DELETE = 3;
    public int $OPERATION_CREATE = 4;
    public int $OPERATION_LIST = 5;

    //Déclaration des Roles
    public int $ROLE_PATIENT = 5;
    public int $ROLE_PRATICIEN = 10;
    public int $ROLE_ADMIN = 100;
   
    private PraticienRepositoryInterface $praticienRepository;
   
    public function __construct(PraticienRepositoryInterface $praticienRepository) {
        $this->praticienRepository = $praticienRepository;
    }
   
    public function isGranted(string $user_id, string $role, string $ressource_id, int $operation=1): bool {
        $roleInt = (int)$role;
       
        if ($roleInt < $this->ROLE_PRATICIEN) {
            throw new Exception("Erreur autorisation: Rôle invalide");
        }
       
        if ($operation !== $this->OPERATION_LIST && $roleInt < $this->ROLE_ADMIN) {
            if ($user_id !== $ressource_id) {
                throw new Exception("Erreur autorisation: Vous n'êtes pas propriétaire de cette ressource");
            }
        }
       
        if ($operation === $this->OPERATION_DELETE && $roleInt < $this->ROLE_ADMIN) {
            throw new Exception("Erreur autorisation: Droits insuffisants pour la suppression");
        }
       
        return true;
    }
}