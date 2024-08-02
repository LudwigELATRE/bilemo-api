<?php

namespace App\Provider\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\EnterpriseRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserProvider implements ProviderInterface
{
    private UserRepository $userRepository;
    private RequestStack $requestStack;
    private LoggerInterface $logger;
    private EnterpriseRepository $enterpriseRepository;

    public function __construct(UserRepository $userRepository,RequestStack $requestStack, LoggerInterface $logger,EnterpriseRepository $enterpriseRepository)
    {
        $this->userRepository = $userRepository;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->enterpriseRepository = $enterpriseRepository;
    }

    /**
     * @inheritDoc
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $userId = $uriVariables['id'] ?? null;
        $request = $this->requestStack->getCurrentRequest();
        $enterpriseUuid = $request->headers->get('enterprise_uuid');

        if (null === $userId || null === $enterpriseUuid) {
            throw new BadRequestHttpException("L'ID de produit et l'UUID d'entreprise sont requis");
        }

        $enterprise = $this->enterpriseRepository->findOneBy(['uuid' => $enterpriseUuid]);

        if (!$enterprise) {
            throw new NotFoundHttpException("Entreprise non trouvée");
        }

        try {
            $user = $this->userRepository->findOneBy([
                'id' => $userId,
                'enterprise' => $enterprise,
                'status' => 'AVAILABLE'
            ]);

            if (!$user) {
                throw new NotFoundHttpException("Utilisateur non trouvé ou indisponible");
            }

            return $user;

        } catch (\Exception $e) {
            $this->logger->error("Erreur lors de la récupération du produit", ['exception' => $e, 'productId' => $userId, 'enterpriseUuid' => $enterpriseUuid]);

            return ['error' => "Une erreur s'est produite lors de la récupération du produit", 'details' => $e->getMessage()];
        }
    }
}