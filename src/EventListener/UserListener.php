<?php

namespace App\EventListener;

use App\Entity\Enterprise;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class UserListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onUserPost(ViewEvent $event): void
    {
        $request = $event->getRequest();
        $method = $request->getMethod();

        if ($method !== 'POST' || !$event->getControllerResult() instanceof User) {
            return;
        }

        $enterpriseUuid = $request->headers->get('enterprise_uuid');
        if (!$enterpriseUuid) {
            throw new BadRequestHttpException(' Entreprise_uuid manquante dans les en-têtes de requête.');
        }

        $enterprise = $this->entityManager->getRepository(Enterprise::class)->findOneBy(['uuid' => $enterpriseUuid]);
        if (!$enterprise) {
            throw new BadRequestHttpException(' Entreprise introuvable.');
        }

        $user = $event->getControllerResult();
        $hash = password_hash($user->getPassword(), PASSWORD_DEFAULT);
        $user->setPassword($hash);
        $user->setRoles(['ROLE_USER']);
        $user->setEnterprise($enterprise);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onUserPost', 10],
        ];
    }
}
