<?php

namespace App\Controller;

use App\Entity\Season;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class SeasonController extends AbstractController
{
    #[Route('/seasons', methods: ['GET'])]
    public function index(SeasonRepository $seasonRepository): Response
    {
        $seasons = $seasonRepository->findAll();
        return $this->json($seasons, 200, [], ['groups' => ['seasons:read']]);
    }

    #[Route('/season/{id}', methods: ['GET'])]
    public function show(Season $season): Response
    {
        return $this->json($season, 200, [], ['groups' => ['season:read']]);
    }

    #[Route('/season/new', methods: ['POST'])]
    public function create(SeasonRepository $seasonRepository,Request $request, SerializerInterface $serializer, EntityManagerInterface $manager): Response
    {
        $json = $request->getContent();
        $season = $serializer->deserialize($json, Season::class, 'json');

        $seasonAlreadyInDB = $seasonRepository->findOneBy(['name' => $season->getName()]);
        if($seasonAlreadyInDB) {
            return $this->json("season already in database");
        }

        $manager->persist($season);
        $manager->flush();

        return $this->json($season, 200, [], ['groups' => ['season:read']]);
    }

    #[Route('/season/{id}/delete', methods: ['DELETE'])]
    public function delete(Season $season, EntityManagerInterface $manager): Response
    {
        $manager->remove($season);
        $manager->flush();
        return $this->json("season deleted successfully", 200);
    }

    #[Route('/season/{id}/edit', methods: ['PUT'])]
    public function edit(Season $season, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer): Response
    {
        $editedSeason = $serializer->deserialize($request->getContent(), Season::class, 'json');
        $season->setName($editedSeason->getName());

        $manager->persist($season);
        $manager->flush();
        return $this->json($season, 200);
    }
}
