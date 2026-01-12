<?php
namespace toubilib\api\provider\jwt;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JwtManager implements JwtManagerInterface {
    private string $secret;
    private int $access_expiration_time;
    private int $refresh_expiration_time;
    private string $issuer = 'toubilib';

    public function __construct(
        string $secret, 
        int $accessExpirationTime = 3600,
        int $refreshExpirationTime = 86400
    ) {
        $this->secret = $secret;
        $this->access_expiration_time = $accessExpirationTime;
        $this->refresh_expiration_time = $refreshExpirationTime;
    }

    public function setIssuer(string $issuer): void {
        $this->issuer = $issuer;
    }

    public function create(array $payload, int $type): string {
        if ($type === JwtManagerInterface::ACCESS_TOKEN) {
            $expirationTime = time() + $this->access_expiration_time;
        } else {
            $expirationTime = time() + $this->refresh_expiration_time;
        }

        $token = JWT::encode([
            'iss' => $this->issuer,
            'sub' => $payload['id'],
            'iat' => time(),
            'exp' => $expirationTime,
            'upr' => $payload
        ], $this->secret, 'HS512');

        return $token;
    }

    public function validate(string $jwtToken): array {
        try {
            $decoded = JWT::decode($jwtToken, new Key($this->secret, 'HS512'));
            return (array) $decoded->upr;
        } catch (ExpiredException $e) {
            throw new \Exception("Expired JWT token: " . $e->getMessage());
        } catch (SignatureInvalidException | \UnexpectedValueException | \DomainException $e) {
            throw new \Exception("Invalid JWT token: " . $e->getMessage());
        }
    }
}