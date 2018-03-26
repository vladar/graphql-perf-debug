<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Currency;

interface CurrencyRepository
{
    public function withCode(CurrencyCode $currencyCode): ?Currency;

    public function withCodes(array $currencyCodes): array;
}
