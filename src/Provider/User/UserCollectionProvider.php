<?php

namespace App\Provider\User;

use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\Operation;
use App\Repository\EnterpriseRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;


class UserCollectionProvider implements ProviderInterface
{
    private UserRepository $userRepository;
    private RequestStack $requestStack;
    private EnterpriseRepository $enterpriseRepository;
    private LoggerInterface $logger;

    public function __construct(UserRepository $userRepository, RequestStack $requestStack, EnterpriseRepository $enterpriseRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->requestStack = $requestStack;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->logger = $logger;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?array
    {
        $request = $this->requestStack->getCurrentRequest();
        $enterpriseUuid = $request->headers->get('enterprise_uuid');

        $enterprise = $this->enterpriseRepository->findOneBy(['uuid' => $enterpriseUuid]);

        if (!$enterprise) {
            throw new NotFoundHttpException("Entreprise non trouvée");
        }

        try {
            $users = $this->userRepository->findAllByEnterpriseId($enterprise->getId());
            $this->logger->info('Utilisateurs récupérés', ['users' => $users]);
            return $users;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération des utilisateurs', ['exception' => $e]);
            return ['error' => "Une erreur s'est produite lors de la récupération des utilisateurs", 'details' => $e->getMessage()];        }
    }
}