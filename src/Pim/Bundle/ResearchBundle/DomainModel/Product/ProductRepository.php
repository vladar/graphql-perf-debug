<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Product;

interface ProductRepository
{
    public function withIdentifier(ProductIdentifier $productIdentifier): ?Product;
}
