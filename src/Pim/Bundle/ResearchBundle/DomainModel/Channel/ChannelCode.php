<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Channel;

class ChannelCode
{
    /** @var string */
    private $value;

    /**
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function createFromString(string $value): self
    {
        return new self($value);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
