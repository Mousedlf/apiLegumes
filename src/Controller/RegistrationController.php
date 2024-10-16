<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $manager,UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $json = $request->getContent();
        $user = $serializer->deserialize($json, User::class, 'json');

        // encode the plain password
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $taken = $userRepository->findOneBy(['email'=> $user->getEmail()]);
        if (!$taken){

            // generate uuid ? juste ça ?
            $user->setUuid(Uuid::v4());
            $user->setActive(true);

            $manager->persist($user);
            $manager->flush();

            return $this->json("welcome", 200);
        } else {
            return $this->json("email taken", 401);
        }

    }



}
