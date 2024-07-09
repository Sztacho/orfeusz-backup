<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\AnimeRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Anime')]
class AnimeController extends AbstractController
{
    #[Route('/anime/list', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns the list of anime',)]
    #[OA\Parameter(name: 'page', description: 'Page number starting from 0', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'limit', description: 'Max elements per page', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'filters', description: 'Filters for anime search', in: 'query', schema: new OA\Schema(properties: [
        new OA\Property(property: 'dateFrom', description: 'Date from', type: 'date', minItems: 0),
        new OA\Property(property: 'dateTo', description: 'Date to', type: 'date', minItems: 0),
        new OA\Property(property: 'name', description: 'Anime name like', type: 'string', minItems: 0,  example: 'Naruto'),
        new OA\Property(property: 'tags', description: 'Tags array', type: 'array', items: new OA\Items(type: 'string'), minItems: 0, example: ['action', 'adventure']),
        new OA\Property(property: 'studios', description: 'Studios array', type: 'array', items: new OA\Items(type: 'string'), minItems: 0, example: ['Studio 1', 'Studio 2']),
        new OA\Property(property: 'season', description: 'Selected season', type: 'string', minItems: 0, example: 'winter 2022'),
        new OA\Property(property: 'ageRatingSystem', description: 'Age rating system', type: 'string', minItems: 0, example: 'General Audiences'),
    ], type: 'object'), style: 'deepObject')]
    public function list(Request $request, AnimeRepository $animeRepository): JsonResponse
    {
        $page = $request->query->getInt('page');
        $limit = $request->query->getInt('limit', 10);
        $filters = $request->query->all()['filters'] ?? [];

        $count = $animeRepository->countAnimeByFilters($filters);

        return $this->json([
            'data' => $animeRepository->findAnimeByFilters($filters, $limit, $page * $limit),
            'pagination' => [
                'page' => $page,
                'itemsPerPage' => $limit,
                'lastPage' => ceil($count / $limit),
                'allItemsCount' => $count
            ]
        ]);
    }

    #[Route('/anime', methods: ['GET'])]
    #[OA\Response(response: 200, description: 'Returns selected anime',)]
    #[OA\Parameter(name: 'id', description: 'id of anime', in: 'query', required: true, schema: new OA\Schema(type: 'integer'))]
    public function get(Request $request, AnimeRepository $animeRepository): JsonResponse
    {
        $anime = $animeRepository->findOneBy(['id' => $request->query->get('id')]);

        if (!$anime) {
            return $this->json(['status' => 'Brak anime o podanym ID']);
        }

        return $this->json($anime->jsonSerialize());
    }
}