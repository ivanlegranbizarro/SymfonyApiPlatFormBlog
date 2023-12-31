<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post as Store;
use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[UniqueEntity('title')]
#[ApiResource(
  operations: [
    new Get(
      normalizationContext: ['groups' => ['post:read', 'post:read:item']]
    ),
    new GetCollection(
      normalizationContext: ['groups' => ['post:read', 'post:read:collection']]
    ),
    new Store(),
    new Patch(),
  ],
  // normalizationContext: ['groups' => ['post:read']],
  denormalizationContext: ['groups' => ['post:write']],
  paginationItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial', 'body' => 'partial', 'category.name' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['id'])]
class Post
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['post:read'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Assert\NotBlank]
  #[Assert\Length(min: 3, max: 255)]
  #[Groups(['post:read', 'post:write'])]
  private ?string $title = null;

  #[ORM\Column(type: Types::TEXT)]
  #[Assert\NotBlank]
  #[Assert\Length(min: 10, max: 500)]
  #[Groups(['post:read:item', 'post:write'])]
  private ?string $body = null;

  #[ORM\ManyToOne(inversedBy: 'posts')]
  #[ORM\JoinColumn(nullable: false)]
  #[Groups(['post:read', 'post:write'])]
  private ?Category $category = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(string $title): static
  {
    $this->title = $title;

    return $this;
  }

  public function getBody(): ?string
  {
    return $this->body;
  }

  #[Groups(['post:read:collection'])]
  public function getSummary(): ?string
  {
    if (strlen($this->body) > 70) {
      return substr($this->body, 0, 70) . '...';
    }
    return $this->body;
  }
  public function setBody(string $body): static
  {
    $this->body = $body;

    return $this;
  }

  public function getCategory(): ?Category
  {
    return $this->category;
  }

  public function setCategory(?Category $category): static
  {
    $this->category = $category;

    return $this;
  }
}
