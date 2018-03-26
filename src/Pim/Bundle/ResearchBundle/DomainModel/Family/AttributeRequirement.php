<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Family;

use Pim\Bundle\ResearchBundle\DomainModel\Attribute\AttributeCode;
use Pim\Bundle\ResearchBundle\DomainModel\Channel\ChannelCode;

class AttributeRequirement
{
    /** @var ChannelCode */
    private $channelCode;

    /** @var AttributeCode[] */
    private $attributeCodes;

    /**
     * @param ChannelCode     $channelCode
     * @param AttributeCode[] $attributeCodes
     */
    public function __construct(ChannelCode $channelCode, array $attributeCodes)
    {
        $this->channelCode = $channelCode;
        $this->attributeCodes = $attributeCodes;

        $this->attributeCodes = (function(AttributeCode ...$attributeCode) {
            return $attributeCode;
        })(...$attributeCodes);
    }

    /**
     * @param ChannelCode     $channelCode
     * @param AttributeCode[] $attributeCodes
     *
     * @return AttributeRequirement
     */
    public static function createFromChannelCode(ChannelCode $channelCode, array $attributeCodes): self
    {
        return new self($channelCode, $attributeCodes);
    }

    public function channelCode(): ChannelCode
    {
        return $this->channelCode;
    }

    public function requiredAttributeCodes(): array
    {
        return $this->attributeCodes;
    }
}
