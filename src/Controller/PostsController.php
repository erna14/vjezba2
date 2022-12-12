<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PostsController extends AbstractController {


    public function __construct(private PostRepository $postRepository, private Security $security)
    {
    }

    #CREATE
    #[Route('/api/posts', methods: 'POST')]
    public function create(Request $request, PostRepository $postRepository): JsonResponse
    {
        $content = $request->request->get("content");
        $user = $this->security->getUser();
        $post = new Post();
        $post->setContent($content)->setUser($user);

        $postRepository->save($post, true);

//      $entityManager->persist($post);
//      $entityManager->flush();

        return $this->json(
            $post,
            200,
            [],
            ["groups" => ["read:post", "read:user"]]
        );
    }

    #READ ALL
    #[Route('/api/posts', methods: 'GET')]
    public function readAll(): JsonResponse
    {
        $posts = $this->postRepository->findAll();

        return $this->json(
            $posts,
            200,
            [],
            ["groups" => ["read:post", "read:user"]]
        );
    }

    #READ ONE
    #[Route('/api/posts/{id}', methods: 'GET')]
    public function readOne(string $id): JsonResponse
    {
        $post = $this->postRepository->find($id);

        if ($post === null) {
            return new JsonResponse("POST NOT FOUND", Response::HTTP_BAD_REQUEST);
        }

        return $this->json(
            $post,
            200,
            [],
            ["groups" => ["read:post", "read:user"]]
        );
    }


    #UPDATE
    #[Route('/api/posts/{id}', methods: 'PUT')]
    public function update(Request $request, string $id): JsonResponse
    {
        $post = $this->postRepository->find($id);

        if($post === null) {
            return new JsonResponse("POST_NOT_FOUND", Response::HTTP_BAD_REQUEST);
        }

        $content = $request->request->get("content");

        $post->setContent($content);


        $this->postRepository->save($post, true);

        return $this->json(
            $post,
            200,
            [],
            ["groups" => ["read:post", "read:user"]]
        );
    }

    #DELETE
    #[Route('/api/posts/{id}', methods: 'DELETE')]
    public function delete(string $id): Response
    {
        $post = $this->postRepository->find($id);

        if ($post === null) {
            return new Response("BAD_REQUEST_COULD_NOT_FIND_POST",Response::HTTP_BAD_REQUEST);
        }
        $this->postRepository->remove($post, true);

        return new Response(null,Response::HTTP_NO_CONTENT);
    }

}