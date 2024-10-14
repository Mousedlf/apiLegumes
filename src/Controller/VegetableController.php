<?php

namespace App\Controller;

use App\Entity\Vegetable;
use App\Repository\VegetableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class VegetableController extends AbstractController
{
    #[Route('/vegetables', methods: ['GET'])]
    public function index(VegetableRepository $vegetableRepository): Response
    {
        $vegetables = $vegetableRepository->findAll();
        return $this->json($vegetables, 200, [], ['groups' => ['vegetables:read']]);
    }

    #[Route('/vegetable/new', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer): Response
    {
        $json = $request->getContent();
        $vegetable = $serializer->deserialize($json, Vegetable::class, 'json');

        return $this->json($vegetable, 200);
    }
}
