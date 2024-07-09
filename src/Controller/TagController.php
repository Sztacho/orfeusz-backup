<?php

namespace App\Controller;

use App\Repository\TagRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Tag')]
class TagController extends AbstractController
{
    #[Route('/tag/list', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns the tags list')]
    public function get(TagRepository $repository): JsonResponse
    {
        return $this->json($repository->findAll());
    }
}