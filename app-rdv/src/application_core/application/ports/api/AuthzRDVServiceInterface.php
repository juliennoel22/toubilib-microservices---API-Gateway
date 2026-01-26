<?php
namespace toubilib\core\application\ports\api;

interface AuthzRDVServiceInterface
{
    const OPERATION_READ = 1;
    const OPERATION_UPDATE = 2;
    const OPERATION_DELETE = 3;
    const OPERATION_CREATE = 4;
    const OPERATION_LIST = 5;

    public function isGranted(string $user_id, string $role, string $ressource_id, int $operation = self::OPERATION_READ): bool;
}