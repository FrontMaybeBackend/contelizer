<?php

namespace App\Controller;

use App\Service\GoRestApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    private $apiService;

    public function __construct(GoRestApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @Route("/posts", name="post_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('post/index.html.twig');
    }

    /**
     * @Route("/api/posts", name="api_posts_list", methods={"GET"})
     */
    public function getPosts(Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $userId = $request->query->get('user_id');

        $posts = $this->apiService->getPosts($userId, $page);

        return new JsonResponse($posts);
    }

    /**
     * @Route("/posts/{id}", name="post_edit", methods={"GET"})
     */
    public function edit(int $id): Response
    {
        $post = $this->apiService->getPost($id);

        if (!$post) {
            throw $this->createNotFoundException('Post nie został znaleziony');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/api/posts/{id}", name="api_post_update", methods={"PUT"})
     */
    public function updatePost(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title']) || !isset($data['body'])) {
            return new JsonResponse(['error' => 'Brakujące dane'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->apiService->updatePost($id, $data);

        if (isset($result['error'])) {
            return new JsonResponse($result, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($result);
    }

}
