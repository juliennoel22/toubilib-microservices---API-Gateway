<?php

namespace toubilib\infra\repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use toubilib\core\application\ports\spi\repositoryInterfaces\PraticienRepositoryInterface;
use toubilib\core\domain\entities\praticien\Praticien;
use toubilib\core\domain\entities\praticien\Specialite;
use Exception;

class PraticienClientAdapter implements PraticienRepositoryInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function GetAllPraticiens(): array
    {
        try {
            $response = $this->client->get('/praticiens');
            $data = json_decode($response->getBody()->getContents(), true);
            $praticiens = [];
            foreach ($data as $p) {
                // Assuming the API returns similar structure to entity fields
                $specialite = new Specialite($p['specialite_id'] ?? '', $p['specialite_lib'] ?? '', $p['specialite_desc'] ?? '');
                $praticiens[] = new Praticien(
                    $p['id'],
                    $p['nom'],
                    $p['prenom'],
                    $p['ville'],
                    $p['email'],
                    $specialite
                );
            }
            return $praticiens;
        } catch (GuzzleException $e) {
            return [];
        }
    }

    public function findPraticien(string $id_p): Praticien
    {
        return $this->findPraticienId($id_p);
    }

    private array $cache = [];

    public function findPraticienId(string $id_p): Praticien
    {
        if (isset($this->cache[$id_p])) {
            return $this->cache[$id_p];
        }

        try {
            $response = $this->client->get('/praticiens/' . $id_p);
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Adapter: JSON decode error: " . json_last_error_msg());
                throw new Exception("Erreur decoding JSON praticien");
            }

            if (!is_array($data)) {
                 error_log("Adapter: Data is not array");
                 throw new Exception("Data invalid");
            }

            try {
                $specialite = new Specialite(
                    $data['specialite_id'] ?? 'unknown',
                    $data['specialite_lib'] ?? '',
                    $data['specialite_desc'] ?? ''
                );

                $praticien = new Praticien(
                    $data['id'],
                    $data['nom'],
                    $data['prenom'],
                    $data['ville'],
                    $data['email'],
                    $specialite
                );
                $this->cache[$id_p] = $praticien;
                return $praticien;
            } catch (\Throwable $e) { // Catch Error and Exception
                error_log("Adapter: Error creating objects: " . $e->getMessage());
                throw $e;
            }
        } catch (GuzzleException $e) {
            throw new Exception("Praticien introuvable");
        }
    }

    public function findPraticienBy(string $type, string $value): array
    {
        // Not used by ServiceRendezVous directly, but required by interface.
        // Could implement if Praticien API supports searching by criteria.
        // For now, return empty or implement basic search via all.
        return [];
    }
}
