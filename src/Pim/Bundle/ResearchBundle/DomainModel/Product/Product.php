<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Product;

use Pim\Bundle\ResearchBundle\DomainModel\Category\CategoryCode;
use Pim\Bundle\ResearchBundle\DomainModel\Family\FamilyCode;

class Product
{
    /** @var ProductIdentifier */
    private $identifier;

    /** @var \DateTimeInterface */
    private $created;

    /** @var \DateTimeInterface */
    private $updated;

    /** @var bool */
    private $enabled;

    /** @var FamilyCode */
    private $familyCode;

    /** @var CategoryCode[] */
    private $categoryCodes;

    public function __construct(
        ProductIdentifier $identifier,
        \DateTimeInterface $created,
        \DateTimeInterface $updated,
        bool $enabled,
        ?FamilyCode $familyCode,
        array $categoryCodes
    ) {
        $this->identifier = $identifier;
        $this->created = $created;
        $this->updated = $updated;
        $this->enabled = $enabled;
        $this->familyCode = $familyCode;

        $this->categoryCodes = (function(CategoryCode ...$categoryCode) {
            return $categoryCode;
        })(...$categoryCodes);
    }

    public function identifier(): ProductIdentifier
    {
        return $this->identifier;
    }

    public function created(): \DateTimeInterface
    {
        return $this->created;
    }

    public function updated(): \DateTimeInterface
    {
        return $this->updated;
    }

    public function isEnable(): bool
    {
        return $this->enabled;
    }

    public function familyCode(): ?FamilyCode
    {
        return $this->familyCode;
    }

    public function categoryCodes(): array
    {
        return $this->categoryCodes;
    }
}
