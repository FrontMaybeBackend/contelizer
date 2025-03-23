<?php

namespace App\Controller;

use App\Service\GoRestApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $apiService;

    public function __construct(GoRestApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @Route("/users", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    /**
     * @Route("/api/users", name="api_users_list", methods={"GET"})
     */
    public function getUsers(Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $search = $request->query->get('search', '');

        $users = $this->apiService->getUsers($page, $search);

        return new JsonResponse($users);
    }

    /**
     * @Route("/users/{id}", name="user_edit", methods={"GET"})
     */
    public function edit(int $id): Response
    {
        $user = $this->apiService->getUser($id);

        if (!$user) {
            throw $this->createNotFoundException('Użytkownik nie został znaleziony');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/api/users/{id}", name="api_user_update", methods={"PUT"})
     */
    public function updateUser(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Walidacja danych
        if (!isset($data['name']) || !isset($data['email']) || !isset($data['gender']) || !isset($data['status'])) {
            return new JsonResponse(['error' => 'Brakujące dane'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->apiService->updateUser($id, $data);

        if (isset($result['error'])) {
            return new JsonResponse($result, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($result);
    }
}
