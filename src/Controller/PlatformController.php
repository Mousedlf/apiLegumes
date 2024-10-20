<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Platform;
use App\Repository\ClientRepository;
use App\Service\AdminKeyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PlatformController extends AbstractController
{
    #[Route('/platform/new', name: 'new_platform', methods: ['POST'])]
    public function newPlatform(Request $request,
                        SerializerInterface $serializer,
                        EntityManagerInterface $manager,
                        MailerInterface $mailer,

    ): Response
    {
        $json = $request->getContent();
        $platform = $serializer->deserialize($json, Platform::class, 'json');
        $adminKey = $platform->getAdminKey();

        $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('Generated platform api key')
            ->text($adminKey);
/*            ->html('<p>a key</p>');*/
        $mailer->send($email);

        $encodedAdminKey = base64_encode($adminKey);
        $platform->setAdminKey($encodedAdminKey);

        $manager->persist($platform);
        $manager->flush();

        return $this->json($platform);
    }

    #[Route('/client/new', name: 'new_client', methods: ['POST'])]
    public function newClientWithKeyCreation(Request $request,
                              SerializerInterface $serializer,
                              EntityManagerInterface $manager,
                              AdminKeyService $adminKeyService): Response
    {
        $json = $request->getContent();
        $body = json_decode($json, true);


        $hasAccess = $adminKeyService->hasPlatformAccess($body["adminKey"]);

        if(!$hasAccess){
            return $this->json("your admin key does not give you access to this api");
        }

        $client = $serializer->deserialize($json, Client::class, 'json');

        $faker = \Faker\Factory::create();
        $apiKey = substr(str_shuffle(str_repeat($faker->lexify('??????') . $faker->numerify('###'), 10)), 0, 20);

        $client->setNbUsedRequests(0);
        $client->setFromPlatform($adminKeyService->getPlatformWithAdminKey($body["adminKey"]));

        $hashedAPIKey = hash('sha256', $apiKey);
        $client->setApiKey($hashedAPIKey);
        $client->setActive(true);

        $manager->persist($client);
        $manager->flush();

        $response =  [
            "email"=>$client->getEmail(),
            "apiKey"=>$apiKey
        ];

        return $this->json($response, 201);
    }

    #[Route('/client/requests', name: 'get_client_request_info', methods: ['POST'])]
    public function getClientRequestInfo(
        Request $request,
        AdminKeyService $adminKeyService,
        ClientRepository $clientRepository,
    ): Response
    {
        $json = $request->getContent();
        $body = json_decode($json, true);  //email,adminKey

        $hasAccess = $adminKeyService->hasPlatformAccess($body["adminKey"]);
        if(!$hasAccess) {
            return $this->json("your admin key does not give you access to this api");
        }

        $requestedClient= $clientRepository->findByEmailAndPlatform($body["email"], $hasAccess['platform']);

        $response = [
            "nbUsedRequests" => $requestedClient->getNbUsedRequests(),
            "nbOfAvailableRequests" => $requestedClient->getNbOfAvailableRequests(),
            "remainingRequests" =>$requestedClient->getNbOfAvailableRequests() - $requestedClient->getNbUsedRequests()
        ];

        return $this->json($response);
    }

    #[Route('/revoke/key', name: 'revoke_key', methods: ['POST'])]
    public function revokeKey(
        Request $request,
        AdminKeyService $adminKeyService,
        ClientRepository $clientRepository,
        EntityManagerInterface $manager,
    ): Response
    {
        $json = $request->getContent();
        $body = json_decode($json, true);  //email,adminKey,destroy(bool)

        $hasAccess = $adminKeyService->hasPlatformAccess($body["adminKey"]);
        if(!$hasAccess) {
            return $this->json("your admin key does not give you access to this api");
        }

        $requestedClient= $clientRepository->findByEmailAndPlatform($body["email"], $hasAccess["platform"]);
        if(!$requestedClient){
            return $this->json("the email does not match to an existing client");
        }

        if($body["mustBeRemoved"]){
            $requestedClient->setApiKey(null);
            $requestedClient->setActive(false);
        } else {
            $faker = \Faker\Factory::create();
            $newApiKey = substr(str_shuffle(str_repeat($faker->lexify('??????') . $faker->numerify('###'), 10)), 0, 20);
            $hashedAPIKey = hash('sha256', $newApiKey);
            $requestedClient->setApiKey($hashedAPIKey);

            $manager->persist($requestedClient);
            $manager->flush();

            $response =  [
                "email"=>$requestedClient->getEmail(),
                "apiKey"=>$newApiKey
            ];

            return $this->json($response);
        }
        return $this->json("key revoked");
    }

    #[Route('/add/requests', name: 'add_requests', methods: ['POST'])]
    public function addRequests(
        Request $request,
        AdminKeyService $adminKeyService,
        EntityManagerInterface $manager,
        ClientRepository $clientRepository,
    )
    {
        $json = $request->getContent();
        $body = json_decode($json, true); //email,adminKey

        $hasAccess = $adminKeyService->hasPlatformAccess($body["adminKey"]);
        if(!$hasAccess) {
            return $this->json("your admin key does not give you access to this api");
        }

        $requestedClient= $clientRepository->findByEmailAndPlatform($body["email"], $hasAccess['platform']);
        if(!$requestedClient){
            return $this->json("the email does not match to an existing client");
        }

        $requestedClient->setNbOfAvailableRequests($requestedClient->getNbOfAvailableRequests()+$body["nbRequest"]);

        $manager->persist($requestedClient);
        $manager->flush();

        $response = [
            "nbUsedRequests" => $requestedClient->getNbUsedRequests(),
            "nbOfAvailableRequests" => $requestedClient->getNbOfAvailableRequests(),
            "remainingRequests" =>$requestedClient->getNbOfAvailableRequests() - $requestedClient->getNbUsedRequests()
        ];

        return $this->json($response);

    }



}
