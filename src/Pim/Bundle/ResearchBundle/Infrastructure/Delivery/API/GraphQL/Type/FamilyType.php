<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Overblog\DataLoader\DataLoader;
use Pim\Bundle\ResearchBundle\DomainModel\Family\Family;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Types;

class FamilyType extends ObjectType
{
    public function __construct(Types $types, DataLoader $attributeDataLoader) {
        $config = [
            'name' => 'family',
            'description' => 'Family',
            'fields' => function() use ($types) {
                return [
                    'code' => Type::string(),
                    'created' => Type::string(),
                    'updated' => Type::string(),
                    'attributes' => Type::listOf($types->get(AttributeType::class)),
                    'attribute_as_label' => $types->get(AttributeType::class),
                    'attribute_as_image' => $types->get(AttributeType::class),
                    'attribute_requirements' => Type::listOf($types->get(AttributeRequirementType::class)),
                    'labels' => Type::listOf($types->get(LabelType::class)),
                ];
            },
            'resolveField' => function(Family $family, $args, $context, ResolveInfo $info) use ($attributeDataLoader) {
                switch ($info->fieldName) {
                    case 'code':
                        return $family->code()->getValue();
                    case 'created':
                        return $family->created()->format(\DateTime::ISO8601);
                    case 'updated':
                        return $family->updated()->format(\DateTime::ISO8601);
                    case 'attributes':
                        return $attributeDataLoader->loadMany($family->attributeCodes());
                    case 'attribute_as_label':
                        return $family->hasAttributeAsLabel() ?
                            $attributeDataLoader->load($family->attributeAsLabelCode()) : null;
                    case 'attribute_as_image':
                        return $family->hasAttributeAsImage() ?
                            $attributeDataLoader->load($family->attributeAsImageCode()) : null;
                    case 'attribute_requirements':
                        return $family->attributeRequirements();
                    case 'labels':
                        return $family->labels();
                    default:
                        return null;
                }
            }
        ];
        parent::__construct($config);
    }
}
