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
    public function newClient(Request $request,
                              SerializerInterface $serializer,
                              EntityManagerInterface $manager,
                              AdminKeyService $adminKeyService): Response
    {
        $json = $request->getContent();
        $body = json_decode($json, true);

        $hasAccess = $adminKeyService->checkAdminKey($body["adminKey"]);

        if($hasAccess){
            $client = $serializer->deserialize($json, Client::class, 'json');

            $faker = \Faker\Factory::create();
            $apiKey = substr(str_shuffle(str_repeat($faker->lexify('??????') . $faker->numerify('###'), 10)), 0, 20);

            $client->setNbUsedRequests(0);
            $client->setFromPlatform($adminKeyService->getPlatformWithAdminKey($apiKey));

            $hashedAPIKey = hash('sha256', $apiKey);
            $client->setApiKey($hashedAPIKey);
            $client->setActive(true);

            $manager->persist($client);
            $manager->flush();

            $response =  [
                "email"=>$client->getEmail(),
                "apiKey"=>$apiKey
            ];

            return $this->json($response);
        }
        return $this->json("your admin key does not give you access to this api");


    }

    #[Route('/client/used/requests', name: 'get_nb_', methods: ['POST'])]
    public function getNumberOfUsedRequests(
        Request $request,
        AdminKeyService $adminKeyService,
        ClientRepository $clientRepository,
    ): Response
    {
        $json = $request->getContent();
        $body = json_decode($json, true);

        $hasAccess = $adminKeyService->checkAdminKey($body["adminKey"]);

        if ($hasAccess === true) {

            $platformId = $adminKeyService->getIdOfPlatformWithAdminKey($body["apiKey"]);

            $clientRepository->findByEmailAndPlatform($body["email"], $platformId);

            $response = [
                "nbUsedRequests" => "",
                "nbOfAvailableRequests" => ""
            ];
            return $this->json($response);
        }



    }
}
