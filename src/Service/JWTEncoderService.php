<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class JWTEncoderService
{
    private JWTTokenManagerInterface $jwtManager;
    private UserRepository $userRepository;

    public function __construct(JWTTokenManagerInterface $jwtManager, UserRepository $userRepository)
    {
        $this->jwtManager = $jwtManager;
        $this->userRepository = $userRepository;
    }

    public function createToken(User $user): string
    {
        $payload = [
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'exp' => (new DateTime())->modify('+1 day')->getTimestamp(),
        ];

        return $this->jwtManager->createFromPayload($user, $payload);
    }
}