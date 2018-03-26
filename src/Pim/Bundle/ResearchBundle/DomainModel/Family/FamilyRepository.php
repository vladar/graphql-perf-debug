<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Family;

interface FamilyRepository
{
    public function withCode(FamilyCode $familyCode): ?Family;

    public function withCodes(array $familyCodes): array;
}
