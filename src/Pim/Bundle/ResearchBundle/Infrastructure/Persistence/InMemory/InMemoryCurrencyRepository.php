<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Persistence\InMemory;

use Pim\Bundle\ResearchBundle\DomainModel\Currency\Currency;
use Pim\Bundle\ResearchBundle\DomainModel\Currency\CurrencyCode;
use Pim\Bundle\ResearchBundle\DomainModel\Currency\CurrencyRepository;

class InMemoryCurrencyRepository implements CurrencyRepository, InMemoryRepository
{
    /** @var Currency[] */
    private $storage = [];

    public function withCode(CurrencyCode $currencyCode): ?Currency
    {
        return $this->storage[$currencyCode->getValue()] ?? null;
    }

    public function withCodes(array $currencyCodes): array
    {
        $currencies = [];
        foreach ($currencyCodes as $currencyCode) {
            if (isset($this->storage[$currencyCode->getValue()])) {
                $currencies[] = $this->storage[$currencyCode->getValue()];
            }
        }

        return $currencies;
    }

    public function add($currency): void
    {
        if (!$currency instanceof Currency) {
            throw new \invalidargumentexception('Currency class expected in argument.');
        }
        $this->storage[$currency->code()->getValue()] = $currency;
    }
}
