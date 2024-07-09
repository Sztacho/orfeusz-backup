<?php

namespace App\Controller;

use App\Repository\StudioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Studios')]
class StudioController extends AbstractController
{
    #[Route('/studio/list', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns the list of studios',)]
    public function __invoke(StudioRepository $repository): JsonResponse
    {
        return $this->json($repository->findAll() ?? []);
    }
}