<?php

namespace App\Controller;

use App\Repository\SeasonRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Season')]
class SeasonController extends AbstractController
{
    #[Route('/season/list', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns the list of season',)]
    public function __invoke(Request $request, SeasonRepository $repository): JsonResponse
    {
        return $this->json($repository->getLast4ActiveSeasons() ?? []);
    }
}