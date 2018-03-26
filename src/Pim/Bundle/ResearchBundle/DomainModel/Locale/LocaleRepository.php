<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Locale;

interface LocaleRepository
{
    public function withCode(LocaleCode $localeCode): ?Locale;

    public function withCodes(array $localeCodes): array;
}
