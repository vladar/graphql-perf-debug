<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Category;

interface CategoryRepository
{
    public function withCode(CategoryCode $channelCode): ?Category;

    public function withCodes(array $channelCodes): array;
}
