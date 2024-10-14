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

    #[Route('/color/new', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager): Response
    {
        $json = $request->getContent();
        $color = $serializer->deserialize($json, Color::class, 'json');

        $manager->persist($color);
        $manager->flush();

        return $this->json($color, 200);
    }
}
