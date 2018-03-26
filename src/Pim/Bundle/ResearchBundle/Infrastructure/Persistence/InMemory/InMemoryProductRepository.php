<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Persistence\InMemory;

use Pim\Bundle\ResearchBundle\DomainModel\Product\Product;
use Pim\Bundle\ResearchBundle\DomainModel\Product\ProductIdentifier;
use Pim\Bundle\ResearchBundle\DomainModel\Product\ProductRepository;

class InMemoryProductRepository implements ProductRepository, InMemoryRepository
{
    /** @var Product[] */
    private $storage;

    public function withIdentifier(ProductIdentifier $productIdentifier): ?Product
    {
        return $this->storage[$productIdentifier->getValue()] ?? null;
    }

    public function add($product): void
    {
        if (!$product instanceof Product) {
            throw new \invalidargumentexception('Product class expected in argument.');
        }
        $this->storage[$product->identifier()->getValue()] = $product;
    }
}
