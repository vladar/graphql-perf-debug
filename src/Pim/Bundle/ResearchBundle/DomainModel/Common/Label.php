<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Common;

use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleCode;

class Label
{
    /** @var LocaleCode */
    private $localeCode;

    /** @var string */
    private $value;

    public function __construct(LocaleCode $localeCode, string $value)
    {
        $this->localeCode = $localeCode;
        $this->value = $value;
    }

    public static function createFromLocaleCode(LocaleCode $localeCode, string $label): Label {
        return new static($localeCode, $label);
    }

    public function localeCode(): LocaleCode
    {
        return $this->localeCode;
    }

    public function value(): string
    {
        return $this->value;
    }
}
