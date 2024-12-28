<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/api/loginas', name: 'app_loginas', methods: ['POST'])]
    public function index(Security $security, JWTTokenManagerInterface $jwtManager): JsonResponse
    {

        $user = new User();
        $user->setEmail("one@one.com");
        $user->setPassword("one");
        $user->setRoles(["ROLE_USER"]);

// If no user is logged in, return an error response
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

// Generate JWT token for the user
        $token = $jwtManager->create($user);

        return new JsonResponse([
            'username' => $user->getUsername(),
            'token' => $token,
        ]);
    }
}
