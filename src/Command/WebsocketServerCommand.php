<?php

namespace App\Command;

use App\Adapter\WebsocketServerAdapter;
use App\MessageHandler\WebsocketHandler;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('run:websocket-server')]
class WebsocketServerCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $port = 3002;
        $output->writeln('Starting server on port ' . $port);
        $server = IoServer::factory(
            new HttpServer(
                new WebsocketServerAdapter(
                    new WsServer(
                        new WebsocketHandler($this->entityManager)
                    )
                )
            ),
            $port
        );

        $server->run();

        return Command::SUCCESS;
    }
}