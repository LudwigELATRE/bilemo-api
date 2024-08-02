<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\ProductRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductProvider implements ProviderInterface
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $id = $uriVariables['id'] ?? null;
        $enterpriseName = $uriVariables['enterpriseName'] ?? null;

        dd($enterpriseName);

        if (!$id || !$enterpriseName) {
            throw new NotFoundHttpException('Product not found.');
        }

        $product = $this->productRepository->findOneByIdAndEnterpriseName($id, $enterpriseName);

        if (!$product) {
            throw new NotFoundHttpException('Product not found.');
        }

        return $product;
    }
}