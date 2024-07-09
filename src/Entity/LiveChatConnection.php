<?php

namespace App\Entity;

use App\Repository\LiveChatConnectionRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: LiveChatConnectionRepository::class)]
class LiveChatConnection implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $connection = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?Episode $episode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConnection(): ?int
    {
        return $this->connection;
    }

    public function setConnection(int $connection): self
    {
        $this->connection = $connection;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEpisode(): ?Episode
    {
        return $this->episode;
    }

    public function setEpisode(?Episode $episode): self
    {
        $this->episode = $episode;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'connection' => $this->getConnection(),
            'userId' => $this->getUser()->getId(),
            'username' => $this->getUser()->getUsername(),
            'avatar' => $this->getUser()->getAvatarUri()
        ];
    }
}
