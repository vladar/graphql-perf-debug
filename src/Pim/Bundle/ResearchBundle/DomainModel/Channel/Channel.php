<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Channel;

use Pim\Bundle\ResearchBundle\DomainModel\Currency\CurrencyCode;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleCode;

class Channel
{
    /** @var ChannelCode */
    private $code;

    /** @var array */
    private $localeCodes;

    /** @var array */
    private $currencyCodes;

    /** @var ChannelLabel[] */
    private $labels;

    public function __construct(ChannelCode $code, array $localeCodes, array $currencyCodes, array $labels)
    {
        $this->code = $code;
        $this->localeCodes = (function(LocaleCode ...$localeCode) {
            return $localeCode;
        })(...$localeCodes);

        $this->currencyCodes = (function(CurrencyCode ...$currencyCode) {
            return $currencyCode;
        })(...$currencyCodes);

        $this->labels = (function(ChannelLabel ...$label) {
            return $label;
        })(...$labels);
    }

    public function code(): ChannelCode
    {
        return $this->code;
    }

    public function localeCodes(): array
    {
        return $this->localeCodes;
    }

    public function currencyCodes(): array
    {
        return $this->currencyCodes;
    }

    public function labels(): array
    {
        return $this->labels;
    }
}
