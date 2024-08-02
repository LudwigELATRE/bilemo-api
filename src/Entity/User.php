<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Provider\User\UserCollectionProvider;
use App\Provider\User\UserProvider;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/users',
            outputFormats: ['json' => ['application/json']],
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
            description: 'Get all users',
            normalizationContext: ['groups' => ['user:read']],
            provider: UserCollectionProvider::class,

        ),
        new Get(
            uriTemplate: '/user/{id}',
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
            description: 'Get a user by ID and enterprise UUID',
            normalizationContext: ['groups' => ['user:read']],
            provider: UserProvider::class,
        ),
        new Post(
            uriTemplate: '/user',
            inputFormats: ['json' => ['application/json']],
            outputFormats: ['json' => ['application/json']],
            openapiContext: [
                'tags' => ['User'],
                'order' => 1,
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'example' => [
                                'email' => 'user@example.com',
                                'password' => 'password123',
                                'firstname' => 'John',
                                'lastname' => 'Doe',
                                'date_of_birth' => '2000-01-01',
                                'status' => 'AVAILABLE',
                            ]
                        ]
                    ]
                ],
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
            description: 'Créer un nouvel utilisateur',
            normalizationContext: ['groups' => ['user:read']],
            denormalizationContext: ['groups' => ['user:write']],
        ),
        new Delete(
            uriTemplate: '/user/{id}',
            inputFormats: ['json' => ['application/json']],
            outputFormats: ['json' => ['application/json']],
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
            description: 'Supprimer un utilisateur par ID',
            normalizationContext: ['groups' => ['user:read']],
            denormalizationContext: ['groups' => ['user:write']]
        )
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:write'])]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $lastname = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?\DateTimeImmutable $date_of_birth = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: Enterprise::class)]
    #[Groups(['user:read', 'enterprise:read', 'user:write'])]
    private ?Enterprise $enterprise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeImmutable
    {
        return $this->date_of_birth;
    }

    public function setDateOfBirth(?\DateTimeImmutable $date_of_birth): static
    {
        $this->date_of_birth = $date_of_birth;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
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
