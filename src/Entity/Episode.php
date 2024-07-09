<?php

namespace App\Entity;

use App\Repository\EpisodeRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EpisodeRepository::class)]
#[UniqueEntity(fields: ['number', 'anime'], message: 'This episode already exists in this anime', errorPath: 'number')]
class Episode implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'episodes')]
    private ?Anime $anime = null;

    #[ORM\OneToMany(mappedBy: 'episode', targetEntity: VideoPlayer::class, fetch: 'EAGER')]
    private Collection $videoPlayers;

    #[ORM\Column]
    private ?int $number = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'episodes')]
    private ?User $translateBy = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToOne(mappedBy: 'episode', cascade: ['persist', 'remove'])]
    private ?LiveChatConnection $liveChatConnection = null;

    #[ORM\Column]
    private ?bool $premiere = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $premiereDate = null;

    public function __construct()
    {
        $this->videoPlayers = new ArrayCollection();
    }

    public function __toString(): string
    {
        return substr($this->anime->getName(), 0, 25) . "... | Odc " . $this->number;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnime(): ?Anime
    {
        return $this->anime;
    }

    public function setAnime(?Anime $anime): self
    {
        $this->anime = $anime;

        return $this;
    }

    /**
     * @return Collection<int, VideoPlayer>
     */
    public function getVideoPlayers(): Collection
    {
        return $this->videoPlayers;
    }

    public function addVideoPlayer(VideoPlayer $videoPlayer): self
    {
        if (!$this->videoPlayers->contains($videoPlayer)) {
            $this->videoPlayers->add($videoPlayer);
            $videoPlayer->setEpisode($this);
        }

        return $this;
    }

    public function removeVideoPlayer(VideoPlayer $videoPlayer): self
    {
        if ($this->videoPlayers->removeElement($videoPlayer)) {
            if ($videoPlayer->getEpisode() === $this) {
                $videoPlayer->setEpisode(null);
            }
        }

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getTranslateBy(): ?User
    {
        return $this->translateBy;
    }

    public function setTranslateBy(?User $translateBy): self
    {
        $this->translateBy = $translateBy;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLiveChatConnection(): ?LiveChatConnection
    {
        return $this->liveChatConnection;
    }

    public function setLiveChatConnection(?LiveChatConnection $liveChatConnection): self
    {
        // unset the owning side of the relation if necessary
        if ($liveChatConnection === null && $this->liveChatConnection !== null) {
            $this->liveChatConnection->setEpisode(null);
        }

        // set the owning side of the relation if necessary
        if ($liveChatConnection !== null && $liveChatConnection->getEpisode() !== $this) {
            $liveChatConnection->setEpisode($this);
        }

        $this->liveChatConnection = $liveChatConnection;

        return $this;
    }

    public function isPremiere(): ?bool
    {
        return $this->premiere;
    }

    public function setPremiere(bool $premiere): self
    {
        $this->premiere = $premiere;

        return $this;
    }

    public function getPremiereDate(): ?DateTimeInterface
    {
        return $this->premiereDate;
    }

    public function setPremiereDate(?DateTimeInterface $premiereDate): self
    {
        $this->premiereDate = $premiereDate;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'anime' => $this->getAnime()->getId(),
            'title' => $this->getTitle(),
            'number' => $this->getNumber(),
            'description' => $this->getDescription(),
            'image' => $this->getImage(),
            'premiere' => $this->isPremiere(),
            'premiereDate' => $this->getPremiereDate(),
            'translatedBy' => [
                'nickname' => $this->getTranslateBy()->getNickname(),
                'avatar' => $this->getTranslateBy()->getDiscordId() . '/' . $this->getTranslateBy()->getAvatar(),
                'roles' => $this->getTranslateBy()->getCrewRole(),
            ]
        ];
    }
}
