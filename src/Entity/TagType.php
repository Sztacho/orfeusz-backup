<?php

namespace App\Entity;

use App\Repository\TagTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TagTypeRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'This tag type already exists', errorPath: 'name')]
class TagType implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'tagType', targetEntity: Tag::class)]
    private Collection $tagsList;

    public function __construct()
    {
        $this->tagsList = new ArrayCollection();
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

    public function getTagsList(): Collection
    {
        return $this->tagsList;
    }

    public function addTagsList(Tag $tagsList): self
    {
        if (!$this->tagsList->contains($tagsList)) {
            $this->tagsList->add($tagsList);
            $tagsList->setTagType($this);
        }

        return $this;
    }

    public function removeTagsList(Tag $tagsList): self
    {
        if ($this->tagsList->removeElement($tagsList)) {
            // set the owning side to null (unless already changed)
            if ($tagsList->getTagType() === $this) {
                $tagsList->setTagType(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }


}
