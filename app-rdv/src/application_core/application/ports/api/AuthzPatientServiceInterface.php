<?php
namespace toubilib\core\application\ports\api;

interface AuthzPatientServiceInterface {
    public function isGranted(string $user_id, string $role, string $ressource_id, int $operation=1): bool;
}
