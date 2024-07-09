<?php

namespace App\Adapter;

use Psr\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServerInterface;

readonly class WebsocketServerAdapter implements HttpServerInterface
{

    public function __construct(private HttpServerInterface $httpServer)
    {

    }

    function onClose(ConnectionInterface $conn): void
    {
        $this->httpServer->onClose($conn);
    }

    function onError(ConnectionInterface $conn, \Exception $e): void
    {
        $this->httpServer->onError($conn, $e);
    }

    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null): void
    {
        $this->httpServer->onOpen($conn, $request);
    }

    function onMessage(ConnectionInterface $from, $msg): void
    {
        $this->httpServer->onMessage($from, $msg);
    }
}