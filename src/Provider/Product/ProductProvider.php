<?php

namespace App\Provider\Product;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\EnterpriseRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Psr\Log\LoggerInterface;


class ProductProvider implements ProviderInterface
{
    private ProductRepository $productRepository;
    private RequestStack $requestStack;
    private LoggerInterface $logger;
    private EnterpriseRepository $enterpriseRepository;

    public function __construct(ProductRepository $productRepository,RequestStack $requestStack, LoggerInterface $logger,EnterpriseRepository $enterpriseRepository)
    {
        $this->productRepository = $productRepository;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->enterpriseRepository = $enterpriseRepository;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $productId = $uriVariables['id'] ?? null;
        $request = $this->requestStack->getCurrentRequest();
        $enterpriseUuid = $request->headers->get('enterprise_uuid');

        if (null === $productId || null === $enterpriseUuid) {
            throw new BadRequestHttpException("L'ID de produit et l'UUID d'entreprise sont requis");
        }

        $enterprise = $this->enterpriseRepository->findOneBy(['uuid' => $enterpriseUuid]);

        if (!$enterprise) {
            throw new NotFoundHttpException("Entreprise non trouvée");
        }

        try {
            $product = $this->productRepository->findOneBy([
                'id' => $productId,
                'enterprise' => $enterprise,
                'status' => 'AVAILABLE'
            ]);

            if (!$product) {
                throw new NotFoundHttpException("Utilisateur non trouvé ou indisponible");
            }
            return $product;

        } catch (\Exception $e) {
            $this->logger->error("Erreur lors de la récupération du produit", ['exception' => $e, 'productId' => $productId, 'enterpriseUuid' => $enterpriseUuid]);

            return ['error' => "Une erreur s'est produite lors de la récupération du produit", 'details' => $e->getMessage()];
        }
    }
}