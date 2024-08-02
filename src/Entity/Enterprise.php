<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Repository\EnterpriseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EnterpriseRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/enterprise',
            inputFormats: ['json' => ['application/json']],
            outputFormats: ['json' => ['application/json']],
            openapiContext: [
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'example' => [
                                'name' => 'My New Enterprise'
                            ]
                        ]
                    ]
                ]
            ],
            description: 'Create a new enterprise',
            normalizationContext: ['groups' => ['enterprise:read']],
            denormalizationContext: ['groups' => ['enterprise:write']]
        )
    ]
)]
class Enterprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['enterprise:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['enterprise:read', 'enterprise:write'])]
    #[Assert\NotBlank(message: 'The name should not be blank.')]
    #[Assert\Length(
        min: 2,
        max: 15,
        minMessage: 'The name must be at least {{ limit }} characters long',
        maxMessage: 'The name cannot be longer than {{ limit }} characters'
    )]
    private ?string $name = null;

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
}
