<?php

namespace App\MessageHandler;

use App\Entity\LiveChatConnection;
use App\Entity\Message;
use App\Enum\WebsocketMessage;
use App\Enum\WebsocketType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class WebsocketHandler implements MessageComponentInterface
{
    private SplObjectStorage $clients;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->clients = new SplObjectStorage();
    }

    function onOpen(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);
        $message = new Message();
        $message
            ->setMessage(WebsocketMessage::CONNECT->value)
            ->setAvatar('202828531791560704/7bff21cb0bccf2b417727e8234e43c01')
            ->setType(WebsocketType::CONNECTION->value)
            ->setUsername('System')
            ->setConnectionCount($this->clients->count())
            ->setResourceId($conn->resourceId);
        $conn->send(json_encode($message));
    }

    function onClose(ConnectionInterface $conn): void
    {
        $connRepository = $this->entityManager->getRepository(LiveChatConnection::class);

        $userConnection = $connRepository->findOneBy(['connection' => $conn->resourceId]);
        if ($userConnection) {
            $connRepository->remove($userConnection, true);
        }

        $this->clients->detach($conn);
    }

    function onError(ConnectionInterface $conn, Exception $e): void
    {
        $conn->close();
    }

    function onMessage(ConnectionInterface $from, $msg): void
    {
        $data = json_decode($msg, true);
        if (!isset($data['chatId']) || !isset($data['message'])) {
            return;
        }

        $conn = $this->entityManager->getRepository(LiveChatConnection::class)->findOneBy(['connection' => $from->resourceId, 'episode' => $data['chatId']]);
        if (!$conn) {
            $message = (new Message())
                ->setMessage('Aby pisać na czacie musisz się zalogować')
                ->setType(WebsocketType::SYSTEM->value)
                ->setUsername('System')
                ->setAvatar('202828531791560704/7bff21cb0bccf2b417727e8234e43c01')
                ->setResourceId($from->resourceId)
                ->setChatId($data['chatId']);

            $from->send(json_encode($message));
            return;
        };
        $conn = $conn->jsonSerialize();

        $data['username'] = $conn['username'];
        $data['avatar'] = $conn['avatar'];
        $data['connectionCount'] = $this->clients->count();

        $message = Message::fromJson($data);

        if (!$message->getChatId() || !$message->getMessage()) {
            return;
        }

        foreach ($this->clients as $client) {

            $clientConn = $this->entityManager->getRepository(LiveChatConnection::class)->findOneBy(['connection' => $client->resourceId]);
            if (!$clientConn || $clientConn->getEpisode()->getId() !== $message->getChatId()) {
                continue;
            }

            $client->send(json_encode($message));
        }
    }
}