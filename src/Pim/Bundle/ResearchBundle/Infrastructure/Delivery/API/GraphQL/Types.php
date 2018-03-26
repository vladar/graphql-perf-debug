<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL;

use GraphQL\Type\Definition\Type;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\Exception\UnknownGraphQLType;

class Types
{
    /** @var array */
    private $types;

    public function get(string $fqcnType): Type
    {
        if(!isset($this->types[$fqcnType])) {
            throw UnknownGraphQLType::unknownType($fqcnType);
        }

        return $this->types[$fqcnType];
    }

    /**
     * Register a type class by its FQCN.
     *
     * As types are lazy loaded to avoid circular dependencies between them,
     * it's a proxy that is passed into the registry.
     *
     * Therefore, we have to get the FQCN of the parent class.
     *
     * @param Type $type
     */
    public function register(Type $type): void
    {
        $this->types[get_class($type)] = $type;
    }
}
