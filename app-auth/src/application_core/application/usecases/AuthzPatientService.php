<?php
namespace toubilib\core\application\usecases;
use Exception;
use toubilib\core\application\ports\api\AuthzPatientServiceInterface;

class AuthzPatientService implements AuthzPatientServiceInterface {
    public int $OPERATION_READ = 1;
    public int $OPERATION_UPDATE = 2;
    public int $OPERATION_DELETE = 3;
    public int $OPERATION_CREATE = 4;
    public int $OPERATION_LIST = 5;
    
    public int $ROLE_PATIENT = 5;
    public int $ROLE_PRATICIEN = 10;
    public int $ROLE_ADMIN = 100;
    
    public function isGranted(string $user_id, string $role, string $ressource_id, int $operation=1): bool {
        $roleInt = (int)$role;
        
        if ($roleInt < $this->ROLE_PRATICIEN) {
            if ($user_id !== $ressource_id) {
                throw new Exception("Erreur autorisation: Seul le patient ou un praticien peut accéder à cette ressource");
            }
        }
        
        if ($operation === $this->OPERATION_DELETE && $roleInt < $this->ROLE_ADMIN) {
            throw new Exception("Erreur autorisation: Droits insuffisants pour la suppression");
        }
        
        return true;
    }
}