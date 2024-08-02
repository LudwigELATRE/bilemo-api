<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserProvider implements ProviderInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $id = $uriVariables['id'] ?? null;
        $enterpriseName = $uriVariables['enterpriseName'] ?? null;
        dd($enterpriseName);
        if (!$id || !$enterpriseName) {
            throw new NotFoundHttpException('Product not found.');
        }

        $user = $this->userRepository->findOneByIdAndEnterpriseName($id, $enterpriseName);

        if (!$product) {
            throw new NotFoundHttpException('Product not found.');
        }

        return $user;
    }
}