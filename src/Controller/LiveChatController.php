<?php

namespace App\Controller;

use App\Entity\LiveChatConnection;
use App\Form\LiveChatConnectionType;
use App\Repository\LiveChatConnectionRepository;
use App\Validation\FormErrorHandler;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'LiveChat')]
class LiveChatController extends AbstractController
{
    #[Route('/liveChat', methods: ['POST'])]
    #[Security(name: 'Token')]
    #[OA\Response(response: 201, description: 'Create live chat connection',)]
    #[OA\RequestBody(content: new OA\JsonContent(properties: [new OA\Property(property: 'episode', description: 'Episode ID', type: 'integer', minItems: 1, example: 1), new OA\Property(property: 'connection', description: 'Connection ID', type: 'integer', minItems: 1, example: 1)]))]
    public function __invoke(Request $request, FormErrorHandler $errorHandler, LiveChatConnectionRepository $repository): JsonResponse
    {
        $form = $this->createForm(LiveChatConnectionType::class);
        $errors = $errorHandler->handleForm($form, $request);
        if ($errors) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        /** @var LiveChatConnection $liveChatConnection */
        $liveChatConnection = $form->getData();
        $liveChatConnection->setUser($this->getUser());
        $repository->save($liveChatConnection, true);

        return $this->json($liveChatConnection, Response::HTTP_CREATED);
    }

    #[Route('/liveChat', methods: ['GET'])]
    public function get(Request $request, LiveChatConnectionRepository $repository): JsonResponse
    {
        return $this->json($repository->findOneBy($request->query->all()));
    }
}