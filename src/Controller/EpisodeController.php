<?php

namespace App\Controller;

use App\Repository\EpisodeRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Episode')]
class EpisodeController extends AbstractController
{
    #[Route('/episode', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Get episode details',)]
    #[OA\Parameter(name: 'id', description: 'An episode id', in: 'query', required: true, schema: new OA\Schema(type: 'integer'), example: 1)]
    public function get(Request $request, EpisodeRepository $episodeRepository): JsonResponse
    {
        if (!$request->query->has('id')) {
            return $this->json(['status' => 'Brak parametru query ID']);
        }

        return $this->json($episodeRepository->find($request->query->get('id')) ?: ['status' => 'Brak odcinka o podanym id']);
    }

    #[Route('/episode/list', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'An anime episodes list',)]
    #[OA\Parameter(name: 'id', description: 'An anime id', in: 'query', required: true, schema: new OA\Schema(type: 'integer'), example: 1)]
    #[OA\Parameter(name: 'quantity', description: 'Get quantity of episodes', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 1)]
    public function list(Request $request, EpisodeRepository $episodeRepository): JsonResponse
    {
        if (!$request->query->has('id')) {
            return $this->json(['status' => 'Brak parametru query ID']);
        }

        return $this->json($episodeRepository->findBy(
            ['anime' => $request->query->get('id')], ['number' => 'DESC'], $request->query->get('quantity'))
            ?: ['status' => 'Brak odcinków dla podanego anime']
        );
    }

    #[Route('/episode/latest', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'An latest episodes list',)]
    #[OA\Parameter(name: 'quantity', description: 'Get quantity of episodes', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 1)]
    public function latest(Request $request, EpisodeRepository $episodeRepository): JsonResponse
    {
        return $this->json($episodeRepository->findBy(
            [], ['id' => 'DESC'], $request->query->get('quantity'))
            ?: ['status' => 'Brak odcinków dla podanego anime']
        );
    }
}