<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\Exception;

use Throwable;

class UnknownGraphQLType extends \LogicException
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function unknownType(string $type)
    {
        return new static(sprintf('Type "%s" is unknown.', $type));
    }
}
