<?php

namespace App\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class WebhookClient
{
    public function __construct(private readonly Client $client = new Client())
    {
    }

    public function post(array $data, string $url): void
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $request = new Request('POST', $url, $headers, json_encode($data));
        $this->client->sendAsync($request)->wait();
    }
}