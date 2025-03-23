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

}
