<?php

namespace App\Provider\Product;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\EnterpriseRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;


class ProductCollectionProvider implements ProviderInterface
{
    private ProductRepository $productRepository;
    private RequestStack $requestStack;
    private EnterpriseRepository $enterpriseRepository;
    private LoggerInterface $logger;

    public function __construct(ProductRepository $productRepository, RequestStack $requestStack, EnterpriseRepository $enterpriseRepository, LoggerInterface $logger)
    {
        $this->productRepository = $productRepository;
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
            $products = $this->productRepository->findAllByEnterpriseId($enterprise->getId());
            $this->logger->info('Produits récupérés', ['products' => $products]);
            return $products;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération des produits', ['exception' => $e]);
            return ['error' => "Une erreur s'est produite lors de la recuperation des produits", 'details' => $e->getMessage()];
        }
    }
}
