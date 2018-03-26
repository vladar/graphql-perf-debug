<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Attribute;

class Attribute
{
    /** @var AttributeCode */
    private $code;

    /** @var string */
    private $type;

    /** @var bool */
    private $localizable;

    /** @var bool */
    private $scopable;

    /** @var string */
    private $group;

    /** @var bool */
    private $unique;

    /** @var bool */
    private $useableAsGridFilter;

    /** @var string[] */
    private $allowedExtensions;

    /** @var string */
    private $metricFamily;

    /** @var string */
    private $defaultMetricUnit;

    /** @var string */
    private $referenceDataName;

    private $availableLocale;

    private $maxCharacters;

    private $validationRule;

    private $validationRegexp;

    private $wysiwygEnabled;

    private $minimumNumber;

    private $maximumNumber;

    private $decimalAllowed;

    private $negativeAllowed;

    private $minimumDate;

    private $maximumDate;

    private $maxFileSize;

    private $minimumInputLenght;

    private $sortOder;

    /** @var \DateTimeInterface */
    protected $dateMin;

    /** @var \DateTimeInterface */
    protected $dateMax;

    public function __construct(
        AttributeCode $code,
        string $type,
        bool $localizable,
        bool $scopable
    ) {
        $this->code = $code;
        $this->type = $type;
        $this->localizable = $localizable;
        $this->scopable = $scopable;
    }

    public function code(): AttributeCode
    {
        return $this->code;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function localizable(): bool
    {
        return $this->localizable;
    }

    public function scopable(): bool
    {
        return $this->scopable;
    }
}
