<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserToken;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Security')]
#[Security(name: 'Token')]
class SecurityController extends AbstractController
{
    #[Route('/user/logout', name: 'logout', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Successful logout',)]
    public function logout(EntityManagerInterface $entityManager): JsonResponse
    {
        $token = $entityManager->getRepository(UserToken::class)->findOneBy(['user' => $this->getUser()]);
        $entityManager->remove($token);

        return $this->json([]);
    }

    #[Route('/user/password', methods: ['PUT'])]
    #[OA\Response(response: 200, description: 'Returns selected anime',)]
    #[OA\RequestBody(content: new OA\JsonContent(properties: [new OA\Property(property: 'password', description: 'New Password', type: 'string', minItems: 1, example: 'P4$$w0rD123'), new OA\Property(property: 'password_confirmation', description: 'Confirmation Password', type: 'string', minItems: 1, example: 'Pa$$worD123'), new OA\Property(property: 'token', description: 'Token', type: 'stringrd'), ], type: 'object'))]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder): JsonResponse
    {
        if (!$request->request->get('password')) {
            return $this->json(['status' => 'Missing parameter password']);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        $user->setPassword($passwordEncoder->hashPassword($user, $request->request->get('password')));
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([]);
    }

}
