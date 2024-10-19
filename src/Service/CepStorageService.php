<?php

namespace App\Service;

use Predis\Client;

class CepStorageService
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    // Armazena os dados do CEP no cache
    public function storeCepData(string $cep, array $data): void
    {
        $key = 'cep_' . $cep;

        // converter o array para uma string JSON para armazenar no Redis
        $jsonData = json_encode($data);

        // armazena o dado no redis com tempo de expiração de 24 horas (86400 segundos)
        $this->client->setex($key, 86400, $jsonData);
    }

    // Busca os dados do CEP no cache
    public function getCepData(string $cep): ?array
    {
        $key = 'cep_' . $cep;

        // busca os dados do redis
        $jsonData = $this->client->get($key);

        if ($jsonData) {
    
            return json_decode($jsonData, true);
        }

        return null;
    }
}