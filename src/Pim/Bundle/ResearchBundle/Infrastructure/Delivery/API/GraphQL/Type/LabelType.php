<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Overblog\DataLoader\DataLoader;
use Pim\Bundle\ResearchBundle\DomainModel\Common\Label;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleRepository;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Types;

class LabelType extends ObjectType
{
    public function __construct(Types $types, DataLoader $localeLoader)
    {
        $config = [
            'name' => 'label',
            'description' => 'Label',
            'fields' => function() use ($types) {
                return [
                    'locale' => $types->get(LocaleType::class),
                    'value' => Type::string()
                ];
            },
            'resolveField' => function(Label $label, $args, $context, ResolveInfo $info) use ($localeLoader) {
                switch ($info->fieldName) {
                    case 'locale':
                        return $localeLoader->load($label->localeCode());
                    case 'value':
                        return $label->value();
                    default:
                        return null;
                }
            }
        ];
        parent::__construct($config);
    }
}
