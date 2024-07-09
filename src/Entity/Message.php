<?php

namespace App\Entity;

use App\Enum\WebsocketType;
use JsonSerializable;

class Message implements JsonSerializable
{
    private ?string $message;
    private string $username;
    private string $avatar;
    private ?string $resourceId = null;
    private ?string $type = null;
    private ?int $chatId;
    private int $connectionCount;

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }

    public static function fromJson(array $data): Message
    {
        $obj = new self();
        $obj->message = $data['message']  ?? null;
        $obj->chatId = $data['chatId'] ?? null;
        $obj->username = $data['username'];
        $obj->avatar = $data['avatar'];
        $obj->type = $data['type'] ?? WebsocketType::DEFAULT->value;
        $obj->connectionCount = $data['connectionCount'];

        return $obj;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): Message
    {
        $this->message = $message;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): Message
    {
        $this->username = $username;
        return $this;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): Message
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getResourceId(): ?string
    {
        return $this->resourceId;
    }

    public function setResourceId(string $resourceId): Message
    {
        $this->resourceId = $resourceId;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Message
    {
        $this->type = $type;
        return $this;
    }

    public function getConnectionCount(): int
    {
        return $this->connectionCount;
    }

    public function setConnectionCount(int $connectionCount): Message
    {
        $this->connectionCount = $connectionCount;
        return $this;
    }

    public function getChatId(): ?int
    {
        return $this->chatId;
    }

    public function setChatId(int $chatId): Message
    {
        $this->chatId = $chatId;
        return $this;
    }
}