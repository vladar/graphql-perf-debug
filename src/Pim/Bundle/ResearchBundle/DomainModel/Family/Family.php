<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Family;

use Pim\Bundle\ResearchBundle\DomainModel\Attribute\AttributeCode;

class Family
{
    /** @var FamilyCode */
    private $code;

    /** @var \DateTimeInterface */
    private $created;

    /** @var \DateTimeInterface */
    private $updated;

    /** @var AttributeCode */
    private $attributeAsLabelCode;

    /** @var AttributeCode */
    private $attributeAsImageCode;

    /** @var AttributeCode[] */
    private $attributeCodes = [];

    /** @var AttributeRequirement[] */
    private $attributeRequirements = [];

    /** @var FamilyLabel[] */
    private $labels = [];

    public function __construct(
        FamilyCode $code,
        \DateTimeInterface $created,
        \DateTimeInterface $updated,
        ?AttributeCode $attributeAsLabelCode,
        ?AttributeCode $attributeAsImageCode,
        array $attributeCodes,
        array $attributeRequirements,
        array $labels
    ) {
        $this->code = $code;
        $this->created = $created instanceof \DateTimeImmutable ? $created : \DateTimeImmutable::createFromMutable($created);
        $this->updated = $updated instanceof \DatetimeImmutable ? $updated : \DateTimeImmutable::createFromMutable($updated);
        $this->attributeAsLabelCode = $attributeAsLabelCode;
        $this->attributeAsImageCode = $attributeAsImageCode;

        $this->attributeCodes = (function(AttributeCode ...$attributeCode) {
            return $attributeCode;
        })(...$attributeCodes);

        $this->attributeRequirements = (function(AttributeRequirement ...$attributeRequirement) {
            return $attributeRequirement;
        })(...$attributeRequirements);

        $this->labels = (function(FamilyLabel ...$label) {
            return $label;
        })(...$labels);
    }

    public function code(): FamilyCode
    {
        return $this->code;
    }

    public function created(): \DateTimeInterface
    {
        return $this->created;
    }

    public function updated(): \DateTimeInterface
    {
        return $this->updated;
    }

    public function attributeCodes(): array
    {
        return $this->attributeCodes;
    }

    public function attributeRequirements(): array
    {
        return $this->attributeRequirements;
    }

    public function attributeAsLabelCode(): ?AttributeCode
    {
        return $this->attributeAsLabelCode;
    }

    public function hasAttributeAsLabel(): bool
    {
        return null !== $this->attributeAsLabelCode;
    }

    public function attributeAsImageCode(): ?AttributeCode
    {
        return $this->attributeAsImageCode;
    }

    public function hasAttributeAsImage(): bool
    {
        return null !== $this->attributeAsImageCode;
    }

    public function labels(): array
    {
        return $this->labels;
    }
}
