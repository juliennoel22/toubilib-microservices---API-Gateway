<?php
namespace toubilib\api\provider\jwt;

interface JwtManagerInterface {
    const ACCESS_TOKEN = 1;
    const REFRESH_TOKEN = 2;

    public function create(array $payload, int $type): string;
    
    public function validate(string $token): array;
    
    public function setIssuer(string $issuer): void;
}