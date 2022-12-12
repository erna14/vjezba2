<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    #[Route('/api/signup',methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $username = $request->request->get("username");
        $password = $request->request->get("password");
        $email = $request->request->get("email");

        $user = new User();
        $hashedPassword = $hasher->hashPassword($user,$password);
        $user->setPassword($hashedPassword)->setUsername($username)->setSalt("123")->setEmail($email);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            $user
        );
    }
}