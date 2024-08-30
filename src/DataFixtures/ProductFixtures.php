<?php

namespace App\DataFixtures;

use App\Entity\Enterprise;
use App\Entity\Product;
use App\Repository\EnterpriseRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    private EnterpriseRepository $enterpriseRepository;

    public function __construct(EnterpriseRepository $enterpriseRepository)
    {
        $this->enterpriseRepository = $enterpriseRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $enterprise = $this->enterpriseRepository->findOneBy(['uuid' => '0c66a703-1e1f-4a73-b3a6-2b735ab652ee']);
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName('Product ' . $i);
            $product->setDescription('Description ' . $i);
            $product->setCreatedAt(new \DateTimeImmutable());
            $product->setUpdatedAt(new \DateTimeImmutable());
            $product->setStatus('AVAILABLE');
            $product->setEnterprise($enterprise);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
