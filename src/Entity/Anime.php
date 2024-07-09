<?php

namespace App\Entity;

use App\Repository\AnimeRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AnimeRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE', region: 'anime')]
#[UniqueEntity(fields: ['name'], message: 'This studio already exists', errorPath: 'name')]
class Anime implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column()]
    private string $ageRatingSystem;

    #[ORM\ManyToMany(targetEntity: Tag::class, fetch: 'EAGER')]
    private Collection $tags;

    #[ORM\ManyToOne(inversedBy: 'animes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Season $season = null;

    #[ORM\ManyToOne(inversedBy: 'animes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $translateBy = null;

    #[ORM\OneToMany(mappedBy: 'anime', targetEntity: Episode::class, fetch: 'EAGER')]
    private Collection $episodes;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $releaseDate = null;

    #[ORM\ManyToMany(targetEntity: Studio::class, inversedBy: 'animes')]
    private Collection $studios;

    #[ORM\OneToMany(mappedBy: 'anime', targetEntity: Rating::class, orphanRemoval: true)]
    private Collection $ratings;

    #[ORM\OneToMany(mappedBy: 'anime', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->episodes = new ArrayCollection();
        $this->studios = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): self
    {
        $this->season = $season;

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

    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): self
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes->add($episode);
            $episode->setAnime($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): self
    {
        if ($this->episodes->removeElement($episode)) {
            if ($episode->getAnime() === $this) {
                $episode->setAnime(null);
            }
        }

        return $this;
    }

    public function getReleaseDate(): ?DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getStudios(): Collection
    {
        return $this->studios;
    }

    public function addStudio(Studio $studio): self
    {
        if (!$this->studios->contains($studio)) {
            $this->studios->add($studio);
        }

        return $this;
    }

    public function removeStudio(Studio $studio): self
    {
        $this->studios->removeElement($studio);

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): Anime
    {
        $this->image = $image;
        return $this;
    }

    public function getAgeRatingSystem(): string
    {
        return $this->ageRatingSystem;
    }

    public function setAgeRatingSystem(string $ageRatingSystem): Anime
    {
        $this->ageRatingSystem = $ageRatingSystem;
        return $this;
    }

    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setAnime($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->removeElement($rating)) {
            if ($rating->getAnime() === $this) {
                $rating->setAnime(null);
            }
        }

        return $this;
    }

    public function getAverageRating(): ?float
    {
        return $this->ratings->reduce(function (float $carry, Rating $rating) {
            return $carry + $rating->getRating();
        }, 0) / (count($this->ratings) ?: 1);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'image' => $this->getImage(),
            'ageRatingSystem' => $this->getAgeRatingSystem(),
            'episodeAmount' => count($this->getEpisodes()),
            'translateBy' => $this->getTranslateBy()->getNickname(),
            'tags' => $this->getTags(),
            'studios' => $this->getStudios(),
            'season' => $this->getSeason(),
            'rating' => $this->getAverageRating() ?: null,
            'releaseDate' => $this->getReleaseDate()->format('Y-m-d'),
        ];
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAnime($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAnime() === $this) {
                $comment->setAnime(null);
            }
        }

        return $this;
    }
}
