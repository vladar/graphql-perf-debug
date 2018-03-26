<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Attribute;

interface AttributeRepository
{
    public function withCode(AttributeCode $attributeCode): ?Attribute;

    /**
     * @param array $attributeCodes
     *
     * @return Attribute[]
     */
    public function withCodes(array $attributeCodes): array;
}
