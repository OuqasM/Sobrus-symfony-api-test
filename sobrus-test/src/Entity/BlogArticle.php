<?php

namespace App\Entity;

use App\Enum\BlogArticleStatus;
use App\Repository\BlogArticleRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlogArticleRepository::class)]
class BlogArticle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['blogArticle:read'])]
    private int $id;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Groups(['blogArticle:read', 'blogArticle:write'])]
    private int $authorId;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Groups(['blogArticle:read', 'blogArticle:write'])]
    private string $title;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Groups(['blogArticle:read', 'blogArticle:write'])]
    private DateTimeInterface $publicationDate;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Groups(['blogArticle:read', 'blogArticle:write'])]
    private DateTimeInterface $creationDate;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['blogArticle:read', 'blogArticle:write'])]
    private string $content;

    #[ORM\Column(type: 'json')]
    #[Groups(['blogArticle:read', 'blogArticle:write'])]
    private array $keywords = [];

    #[ORM\Column(type: 'string', enumType: BlogArticleStatus::class)]
    #[Assert\NotBlank]
    #[Groups(['blogArticle:read', 'blogArticle:write'])]
    private BlogArticleStatus $status;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(['blogArticle:read', 'blogArticle:write'])]
    private string $slug;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[Groups(['blogArticle:read', 'blogArticle:write'])]
    private string $coverPictureRef;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    public function setAuthorId(?int $authorId): self
    {
        $this->authorId = $authorId;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getPublicationDate(): DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(?DateTimeInterface $publicationDate): self
    {
        $this->publicationDate = $publicationDate;
        return $this;
    }

    public function getCreationDate(): DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function setKeywords(?array $keywords): self
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function getStatus(): BlogArticleStatus
    {
        return $this->status;
    }

    public function setStatus(?BlogArticleStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getCoverPictureRef(): string
    {
        return $this->coverPictureRef;
    }

    public function setCoverPictureRef(?string $coverPictureRef): self
    {
        $this->coverPictureRef = $coverPictureRef;
        return $this;
    }
}
