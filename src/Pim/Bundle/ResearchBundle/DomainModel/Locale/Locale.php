<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Locale;

class Locale
{
    /** @var LocaleCode */
    private $code;

    /** @var boolean */
    private $enabled;

    public function __construct(LocaleCode $code, bool $enabled) {
        $this->code = $code;
        $this->enabled = $enabled;
    }

    public function code(): LocaleCode
    {
        return $this->code;
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }
}
