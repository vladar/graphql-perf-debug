<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Pim\Bundle\ResearchBundle\DomainModel\Attribute\AttributeRepository;
use Pim\Bundle\ResearchBundle\DomainModel\Category\Category;
use Pim\Bundle\ResearchBundle\DomainModel\Category\CategoryRepository;
use Pim\Bundle\ResearchBundle\DomainModel\Family\Family;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Types;

class CategoryType extends ObjectType
{
    public function __construct(Types $types, CategoryRepository $categoryRepository)
    {
        $config = [
            'name' => 'category',
            'description' => 'Category',
            'fields' => function() use ($types) {
                return [
                    'code' => Type::string(),
                    'parent' => $types->get(CategoryType::class),
                    'labels' => Type::listOf($types->get(LabelType::class)),
                ];
            },
            'resolveField' => function(Category $category, $args, $context, ResolveInfo $info) use ($categoryRepository) {
                switch ($info->fieldName) {
                    case 'code':
                        return $category->code()->getValue();
                    case 'parent':
                        return $category->isRoot() ? null : $categoryRepository->withCode($category->parentCode());
                    case 'labels':
                        return $category->labels();
                    default:
                        return null;
                }
            }
        ];
        parent::__construct($config);
    }
}
