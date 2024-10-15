<?php

namespace App\Controller;

use App\Entity\Vegetable;
use App\Repository\ColorRepository;
use App\Repository\SeasonRepository;
use App\Repository\VegetableRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/vegetable/{id}', methods: ['GET'])]
    public function show(Vegetable $vegetable): Response
    {
        return $this->json($vegetable, 200, [], ['groups' => ['vegetable:read']]);
    }

    #[Route('/vegetable/new', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $manager,
        ColorRepository $colorRepository,
        SeasonRepository $seasonRepository
    ): Response
    {
        $json = $request->getContent();
        $requestBody = json_decode($json, true);

        $vegetable = new Vegetable();
        $vegetable->setName($requestBody['name']);


        foreach($requestBody['colors'] as $givenColor) {
            $color = $colorRepository->findOneBy(['name' => $givenColor]);
            if(!$color) {
                return $this->json($givenColor.' not found');
            }
            $vegetable->addColor($color);
        }

        // ajout plusieurs saisons a ajouter comme pour couleurs
        $season = $seasonRepository->findOneBy(['name' => $requestBody['season']]);
        if($season){
            $vegetable->setCreatedBy($this->getUser());
            $vegetable->addSeason($season);

            $manager->persist($vegetable);
            $manager->flush();

            return $this->json($vegetable, 200,[], ['groups' => ['vegetable:read']]);
        }

        return $this->json("pas content", 200); //mettre message correct

    }

    #[Route('/vegetable/{id}/edit', methods: ['PUT'])]
    public function edit(
        SerializerInterface $serializer,
        Request $request,
        Vegetable $vegetable,
        EntityManagerInterface $manager,
        ColorRepository $colorRepository,
        SeasonRepository $seasonRepository
    )
    {
        if($this->getUser() !== $vegetable->getCreatedBy()){
            return $this->json("you can only edit your vegetables");
        }
        $json = $request->getContent();
        $requestBody = json_decode($json, true);

        $vegetable->setName($requestBody["name"]);

        foreach($requestBody['colors'] as $givenColor) {
            $color = $colorRepository->findOneBy(['name' => $givenColor]);
            if(!$color) {
                return $this->json($givenColor.' not found');
            }
            $vegetable->addColor($color);
        }

        // ajout plusieurs saisons a ajouter comme pour couleurs
        $season = $seasonRepository->findOneBy(['name' => $requestBody['season']]);
        $vegetable->addSeason($season);


        $manager->persist($vegetable);
        $manager->flush();
        return $this->json($vegetable, 200, [], ['groups' => ['vegetable:read']]);

    }

    #[Route('/vegetable/{id}/delete', methods: ['DELETE'])]
    public function delete(Vegetable $vegetable, EntityManagerInterface $manager)
    {
        if($this->getUser() !== $vegetable->getCreatedBy()){
            return $this->json("you can only delete your vegetables");
        }
        $manager->remove($vegetable);
        $manager->flush();
        return $this->json("vegetable deleted successfully", );
    }

}
