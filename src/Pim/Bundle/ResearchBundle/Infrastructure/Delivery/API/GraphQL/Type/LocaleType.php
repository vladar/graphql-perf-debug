<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pim\Bundle\ResearchBundle\DomainModel\Attribute\Attribute;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\Locale;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Types;

class LocaleType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'locale',
            'description' => 'Locale',
            'fields' => function() {
                return [
                    'code' => Type::string(),
                    'enabled' => Type::boolean()
                ];
            },
            'resolveField' => function(Locale $locale, $args, $context, ResolveInfo $info) {
                switch ($info->fieldName) {
                    case 'code':
                        return $locale->code()->getValue();
                    case 'enabled':
                        return $locale->enabled();
                    default:
                        return null;
                }
            }
        ];
        parent::__construct($config);
    }
}
