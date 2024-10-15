<?php

namespace App\Controller;

use App\Entity\Color;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ColorController extends AbstractController
{
    #[Route('/colors', methods: ['GET'])]
    public function index(ColorRepository $colorRepository): Response
    {
        $colors = $colorRepository->findAll();
        return $this->json($colors, 200, [], ['groups' => ['colors:read']]);
    }

    #[Route('/color/{id}', methods: ['GET'])]
    public function show(Color $color): Response
    {
        return $this->json($color, 200, [], ['groups' => ['color:read']]);
    }

    #[Route('/color/new', methods: ['POST'])]
    public function create(ColorRepository $colorRepository,Request $request, SerializerInterface $serializer, EntityManagerInterface $manager): Response
    {
        $json = $request->getContent();
        $color = $serializer->deserialize($json, Color::class, 'json');

        $colorAlreadyInDB = $colorRepository->findOneBy(['name' => $color->getName()]);
        if($colorAlreadyInDB) {
            return $this->json("color already in database");
        }

        $manager->persist($color);
        $manager->flush();

        return $this->json($color, 200);
    }

    #[Route('/color/{id}/delete', methods: ['DELETE'])]
    public function delete(Color $color, EntityManagerInterface $manager): Response
    {
        $manager->remove($color);
        $manager->flush();
        return $this->json("color deleted successfully", 200);
    }

    #[Route('/color/{id}/edit', methods: ['PUT'])]
    public function edit(Color $color, Request $request, EntityManagerInterface $manager, SerializerInterface $serializer): Response
    {
        $editedColor = $serializer->deserialize($request->getContent(), Color::class, 'json');
        $color->setName($editedColor->getName());

        $manager->persist($color);
        $manager->flush();
        return $this->json($color, 200);
    }
}
