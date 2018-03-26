<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Category;

class Category
{
    /** @var CategoryCode */
    private $code;

    /** @var null|CategoryCode */
    private $parentCode;

    /** @var CategoryLabel[] */
    private $labels;

    public function __construct(CategoryCode $code, ?CategoryCode $parentCode, array $labels)
    {
        $this->code = $code;
        $this->parentCode = $parentCode;

        $this->labels = (function(CategoryLabel ...$label) {
            return $label;
        })(...$labels);
    }

    public function code(): CategoryCode
    {
        return $this->code;
    }

    public function parentCode(): ?CategoryCode
    {
        return $this->parentCode;
    }

    public function isRoot(): bool
    {
        return null === $this->parentCode;
    }

    public function labels(): array
    {
        return $this->labels;
    }
}
