<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserToken;
use App\Service\JWTEncoderService;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[OA\Tag(name: 'Discord')]
class DiscordController extends AbstractController
{
    private HttpClientInterface $client;
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->clientId = $_ENV['DISCORD_CLIENT_ID'];
        $this->clientSecret = $_ENV['DISCORD_CLIENT_SECRET'];
        $this->redirectUri = $_ENV['DISCORD_REDIRECT_URI'];
    }

    #[Route('/discord/login', methods: ['GET'])]
    #[OA\Tag(name: 'Discord')]
    #[OA\Response(response: 200, description: 'Get the auth url to redirect to discord')]
    public function login(): JsonResponse
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'identify email',
        ];
        $authUrl = 'https://discord.com/api/oauth2/authorize?' . http_build_query($params);

        return new JsonResponse([
            'auth_url' => $authUrl,
        ]);
    }

    #[Route('/discord/callback', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'User successfully authenticated. Redirect to page with token in query',)]
    #[OA\Parameter(name: 'code', description: 'Discord code', in: 'query', schema: new OA\Schema(type: 'string'), example: 'NhhvTDYsFcdgNLnnLijcl7Ku7bEEeee')]
    public function callback(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder, JWTEncoderService $jwtAuthenticator): RedirectResponse
    {
        $code = $request->query->get('code');

        $response = $this->client->request('POST', 'https://discord.com/api/oauth2/token', [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'scope' => 'identify email',
            ],
        ]);

        $data = json_decode($response->getContent(), true);
        $accessToken = $data['access_token'];

        $response = $this->client->request('GET', 'https://discord.com/api/users/@me', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        $userData = json_decode($response->getContent(), true);

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userData['email']]) ?? new User();

        $user->setDiscordId($userData['id']);
        $user->setUsername($userData['username']);
        $user->setEmail($userData['email']);
        $user->setAvatar($userData['avatar']);
        $user->setNickname($userData['username']);
        if(!$user->getPassword()) {
            $user->setPassword($passwordEncoder->hashPassword($user, bin2hex(random_bytes(10))));
        }
        $entityManager->persist($user);
        $entityManager->flush();

        $jwtToken = $jwtAuthenticator->createToken($user);

        $token = $entityManager->getRepository(UserToken::class)->findOneBy(['user' => $user]) ?: new UserToken();

        $token->setUser($user);
        $token->setToken($jwtToken);
        $entityManager->persist($token);
        $entityManager->flush();

        return $this->redirect($_ENV['WEB_APP_URI'] . "?token=" . $jwtToken);
    }
}
