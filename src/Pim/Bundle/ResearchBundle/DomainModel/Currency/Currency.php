<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Currency;

class Currency
{
    /** @var CurrencyCode */
    private $code;

    /** @var boolean */
    private $enabled;

    public function __construct(CurrencyCode $code, bool $enabled) {
        $this->code = $code;
        $this->enabled = $enabled;
    }

    public function code(): CurrencyCode
    {
        return $this->code;
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }
}
