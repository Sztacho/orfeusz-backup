<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Security')]
#[Security(name: 'Token')]
class UserController extends AbstractController
{
    #[Route('/user', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Return the user data',)]
    public function get(UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        return new JsonResponse([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'email' => $user->getUserIdentifier(),
            'discordId' => $user->getDiscordId(),
            'avatar' => $user->getAvatar(),
            'nickname' => $user->getNickname(),
        ]);
    }

    #[Route('/user/crew', methods: ['GET'])]
    #[OA\Tag(name: 'Public user')]
    #[OA\Response(response: 200, description: 'Return the crew',)]
    public function getCrewUsers(UserRepository $userRepository): JsonResponse
    {
        return $this->json($userRepository->getUsersByRole('ROLE_CREW'));
    }
}