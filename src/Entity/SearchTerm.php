<?php

namespace App\Entity;

use App\Repository\SearchTermRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: SearchTermRepository::class)]
class SearchTerm
{
    #[OA\Property(description: 'The unique identifier of the search term.')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[OA\Property(description: 'Name of the search term.')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[OA\Property(description: 'Number of the negative results.')]
    #[ORM\Column(nullable: true)]
    private ?int $negativeResults = null;

    #[OA\Property(description: 'Number of the positive results.')]
    #[ORM\Column(nullable: true)]
    private ?int $positiveResults = null;

    #[OA\Property(description: 'Search term score based on the positive and negative results.')]
    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2, nullable: true)]
    private ?string $score = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNegativeResults(): ?int
    {
        return $this->negativeResults;
    }

    public function setNegativeResults(?int $negativeResults): self
    {
        $this->negativeResults = $negativeResults;

        return $this;
    }

    public function getPositiveResults(): ?int
    {
        return $this->positiveResults;
    }

    public function setPositiveResults(?int $positiveResults): self
    {
        $this->positiveResults = $positiveResults;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(?string $score): self
    {
        $this->score = $score;

        return $this;
    }
}
