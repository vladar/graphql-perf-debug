<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Persistence\InMemory;

interface InMemoryRepository
{
    /**
     * @param $entity
     *
     * @throws \InvalidArgumentException
     */
    public function add($entity): void;
}
