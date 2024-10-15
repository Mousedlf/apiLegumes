<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/change/status', name: 'user_change_status')]
    public function changeStatus(User $user, UserRepository $userRepository, EntityManagerInterface $manager): Response
    {
        if($user->isActive() === false){
            $user->setActive(true);
        } else {
            $user->setActive(false);
        }

        $manager->persist($user);
        $manager->flush();

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);    }
}
