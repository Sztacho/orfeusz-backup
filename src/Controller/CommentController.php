<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Validation\FormErrorHandler;
use DateTimeImmutable;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Comment')]
class CommentController extends AbstractController
{
    #[Route('/comment', methods: ['POST'])]
    #[Security(name: 'Token')]
    #[OA\Response(response: 201, description: 'Successfully created comment',)]
    #[OA\RequestBody(content: new OA\JsonContent(properties: [
        new OA\Property(property: 'nickname', description: 'Nickname of the user. Not required if logged in', type: 'string', minItems: 0, example: 'John'),
        new OA\Property(property: 'email', description: 'Email of the user. Not required if logged in', type: 'string', minItems: 0, example: 'test@test.com'),
        new OA\Property(property: 'content', description: 'Comment content', type: 'string', minItems: 1, example: 'Test comment'),
        new OA\Property(property: 'comment', description: 'Parent comment id', type: 'integer', minItems: 0, example: 1),
        new OA\Property(property: 'anime', description: 'Anime id', type: 'integer', minItems: 1, example: 1),
        new OA\Property(property: 'captcha', description: 'Captcha', type: 'string', minItems: 1, example: '123456'),
    ], type: 'object'))]
    public function __invoke(Request $request, FormErrorHandler $errorHandler, CommentRepository $repository): Response
    {
        if ($this->getUser()) {
            $request->request->set('nickname', $this->getUser()->getNickname());
            $request->request->set('email', $this->getUser()->getUserIdentifier());
        }

        $form = $this->createForm(CommentType::class);
        $errors = $errorHandler->handleForm($form, $request);

        /** @var Comment $comment */
        $comment = $form->getData();
        $comment->setCreatedAt(new DateTimeImmutable('now'));
        $comment->setUser($this->getUser() ?: null);
        $repository->save($comment, true);

        return $this->json($errors, $errors ? Response::HTTP_BAD_REQUEST : Response::HTTP_CREATED);
    }

    #[Route('/comment/list', methods: ['GET'])]
    #[OA\Response(response: 201, description: 'Successfully created comment',)]
    #[OA\Parameter(name: 'page', description: 'Page number starting from 0', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'limit', description: 'Max elements per page', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'filters', description: 'Filters for comment', in: 'query', schema: new OA\Schema(properties: [
        new OA\Property(property: 'id', description: 'Anime id', type: 'string', minItems: 0, example: 1),
        new OA\Property(property: 'dateFrom', description: 'Date from', type: 'date', minItems: 0),
        new OA\Property(property: 'dateTo', description: 'Date to', type: 'date', minItems: 0),
    ], type: 'object'), style: 'deepObject')]
    public function get(Request $request, CommentRepository $commentRepository): Response
    {
        $page = $request->query->getInt('page');
        $limit = $request->query->getInt('limit', 10);
        $filters = $request->query->all()['filters'] ?? [];
        $count = $commentRepository->countCommentsByFilters($filters);

        return $this->json([
            'data' => $commentRepository->findCommentsByFilters($filters, $limit, $page * $limit),
            'pagination' => [
                'page' => $page,
                'items_per_page' => $limit,
                'last_page' => ceil($count / $limit),
                'all_items_count' => $count
            ]
        ]);
    }
}