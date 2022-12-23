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
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hashed): JsonResponse
    {
        $decoded = json_decode($request->getContent());

        $username = $decoded->username;
        $password = $decoded->password;
        $email = $decoded->email;

        $user = new User();
        $hashedPassword = $hashed->hashPassword($user, $password);
        $user->setPassword($hashedPassword)
            ->setUsername($username)
            ->setEmail($email);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            $user
        );
    }
}