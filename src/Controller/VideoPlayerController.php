<?php

namespace App\Controller;

use App\Repository\EpisodeRepository;
use App\Repository\VideoPlayerRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'VideoPlayer')]
class VideoPlayerController extends AbstractController
{
    #[Route('/player/list', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns the list of players for an episode')]
    #[OA\Parameter(name: 'id', description: 'An episode id', in: 'query', schema: new OA\Schema(type: 'integer'), example: 1)]
    public function getPlayersForEpisode(Request $request, VideoPlayerRepository $playerRepository): JsonResponse
    {
        $players = $playerRepository->getPlayersForEpisode($request->query->getInt('id'));

        return $this->json($players ?: ['status' => 'Brak odtwarzaczy dla podanego odcinka'], $players ? 200 : 404);
    }
}