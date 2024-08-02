<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\StatusProduct;
use App\Provider\Product\ProductCollectionProvider;
use App\Provider\Product\ProductProvider;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Parameter;


#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/products',
            outputFormats: ['json' => ['application/json']],
            requirements: ['enterprise_uuid' => '[0-9a-fA-F\-]{36}'],
            openapiContext: [
                'parameters' => [
                    [
                        'name' => 'enterprise_uuid',
                        'in' => 'header',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'format' => 'uuid',
                        ],
                        'description' => 'Enterprise UUID',
                    ],
                ],
            ],
            paginationEnabled: false,
            description: 'Get all products with Enterprise UUID passed in the header',
            normalizationContext: ['groups' => ['product:read']],
            provider: ProductCollectionProvider::class,
        ),
        new Get(
            uriTemplate: '/product/{id}',
            openapiContext: [
                'parameters' => [
                    [
                        'name' => 'enterprise_uuid',
                        'in' => 'header',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'format' => 'uuid',
                        ],
                        'description' => 'Enterprise UUID',
                    ],
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [
                            'type' => 'integer',
                        ],
                        'description' => 'Product ID',
                    ],
                ],
            ],
            paginationEnabled: false,
            description: 'Get a Product by ID with Enterprise UUID passed in the header',
            normalizationContext: ['groups' => ['product:read']],
            provider: ProductProvider::class
        )
    ]
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['product:read'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['product:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['product:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read'])]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: Enterprise::class)]
    #[Groups(['product:read', 'enterprise:read'])]
    private ?Enterprise $enterprise = null;

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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, StatusProduct::getValues())) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;

        return $this;
    }

    public function getEnterprise(): ?Enterprise
    {
        return $this->enterprise;
    }

    public function setEnterprise(?Enterprise $enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }
}
