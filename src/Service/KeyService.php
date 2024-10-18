<?php

namespace App\Service;

use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;

class KeyService
{

    private $clientRepository;

    private $entityManager;

    public function __construct(ClientRepository $clientRepository, EntityManagerInterface $entityManager)
    {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
    }

    public function isValidApiKey(string $apiKey): bool
    {
        $client = $this->clientRepository->findOneBy(['apiKey' => hash('sha256', $apiKey)]);

        if (!$client || !$client->isActive()) {
            return false;
        }

        $client->setNbUsedRequests($client->getNbUsedRequests() + 1);
        if ($client->getNbUsedRequests() === $client->getNbOfAvailableRequests()) {
            $client->setActive(false);
        }

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return true;
    }


}