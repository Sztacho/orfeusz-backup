<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Articles')]
class ArticleController extends AbstractController
{
    #[Route('/article/list', methods: ['GET'])]
    public function __invoke(Request $request, ArticleRepository $articleRepository): JsonResponse
    {
        return $this->json($articleRepository->findAll());
    }

    #[Route('/article/get/{id}', methods: ['GET'])]
    public function getArticle(Request $request, Article $article): JsonResponse
    {
        return $this->json($article);
    }
}