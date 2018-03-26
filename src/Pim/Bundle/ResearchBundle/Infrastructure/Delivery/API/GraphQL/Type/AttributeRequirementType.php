<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Overblog\DataLoader\DataLoader;
use Pim\Bundle\ResearchBundle\DomainModel\Family\AttributeRequirement;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Types;

class AttributeRequirementType extends ObjectType
{
    public function __construct(
        Types $types,
        DataLoader $channelDataloader,
        DataLoader $attributeDataLoader
    ) {
        $config = [
            'name' => 'family_requirement',
            'description' => 'Family requirement',
            'fields' => function() use ($types) {
                return [
                    'channel' => $types->get(ChannelType::class),
                    'attributes' => Type::listOf($types->get(AttributeType::class)),
                ];
            },
            'resolveField' => function(AttributeRequirement $attributeRequirement, $args, $context, ResolveInfo $info)
            use ($channelDataloader, $attributeDataLoader) {
                switch ($info->fieldName) {
                    case 'channel':
                        return $channelDataloader->load($attributeRequirement->channelCode());
                    case 'attributes':
                        return $attributeDataLoader->loadMany($attributeRequirement->requiredAttributeCodes());
                    default:
                        return null;
                }
            }
        ];
        parent::__construct($config);
    }
}
