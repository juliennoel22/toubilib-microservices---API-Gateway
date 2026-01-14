<?php
declare(strict_types=1);

namespace toubilib\infra\adapters;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use toubilib\core\application\ports\spi\repositoryInterfaces\PraticienRepositoryInterface;
use toubilib\core\domain\entities\praticien\Praticien;
use toubilib\core\domain\entities\praticien\Specialite;


class RemotePraticienRepository implements PraticienRepositoryInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function GetAllPraticiens(): array
    {
        try {
            $response = $this->client->request('GET', '/praticiens');
            $data = json_decode((string)$response->getBody(), true);
            
            $praticiens = [];
            foreach ($data as $item) {
                $praticiens[] = $this->hydratePraticien($item);
            }
            return $praticiens;
        } catch (ClientException $e) {
            return [];
        }
    }

    public function findPraticien(string $id_p): Praticien
    {
        try {
            $response = $this->client->request('GET', '/praticiens/' . $id_p);
            $data = json_decode((string)$response->getBody(), true);
            return $this->hydratePraticien($data);
        } catch (ClientException $e) {
            throw new \Exception("Praticien not found: " . $id_p);
        }
    }

    public function findPraticienId(string $id_p): Praticien
    {
        return $this->findPraticien($id_p);
    }

    public function findPraticienBy(string $type, string $value): array
    {
        try {
            $endpoint = $type === 'specialite' 
                ? '/praticiens/specialites/' . urlencode($value)
                : '/praticiens/villes/' . urlencode($value);
                
            $response = $this->client->request('GET', $endpoint);
            $data = json_decode((string)$response->getBody(), true);
            
            $praticiens = [];
            foreach ($data as $item) {
                $praticiens[] = $this->hydratePraticien($item);
            }
            return $praticiens;
        } catch (ClientException $e) {
            return [];
        }
    }


    private function hydratePraticien(array $data): Praticien
    {
        $specialite = new Specialite(
            $data['specialite']['id'] ?? '',
            $data['specialite']['label'] ?? $data['specialite']['libelle'] ?? '',
            $data['specialite']['description'] ?? ''
        );
        
        $praticien = new Praticien(
            $data['id'] ?? '',
            $data['nom'] ?? '',
            $data['prenom'] ?? '',
            $data['ville'] ?? $data['adresse'] ?? '',
            $data['email'] ?? '',
            $specialite
        );
        
        return $praticien;
    }
}
