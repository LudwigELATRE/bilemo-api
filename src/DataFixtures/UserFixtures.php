<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\EnterpriseRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    private EnterpriseRepository $enterpriseRepository;

    public function __construct(EnterpriseRepository $enterpriseRepository)
    {
        $this->enterpriseRepository = $enterpriseRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $enterprise = $this->enterpriseRepository->findOneBy(['name' => 'bilemo']);

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFirstname('firstname ' . $i);
            $user->setLastname('lastname ' . $i);
            $user->setEmail('user' . $i . '@bilemo.com');
            $user->setPassword('password');
            $user->setRoles(['ROLE_USER']);
            $user->setStatus('AVAILABLE');
            $user->setEnterprise($enterprise);
            $manager->persist($user);
        }

        $manager->flush();
    }
}