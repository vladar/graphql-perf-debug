<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Pim\Bundle\ResearchBundle\DomainModel\Attribute\AttributeCode;
use Pim\Bundle\ResearchBundle\DomainModel\Attribute\AttributeRepository;
use Pim\Bundle\ResearchBundle\DomainModel\Category\CategoryCode;
use Pim\Bundle\ResearchBundle\DomainModel\Category\CategoryRepository;
use Pim\Bundle\ResearchBundle\DomainModel\Channel\ChannelCode;
use Pim\Bundle\ResearchBundle\DomainModel\Channel\ChannelRepository;
use Pim\Bundle\ResearchBundle\DomainModel\Currency\CurrencyCode;
use Pim\Bundle\ResearchBundle\DomainModel\Currency\CurrencyRepository;
use Pim\Bundle\ResearchBundle\DomainModel\Family\FamilyCode;
use Pim\Bundle\ResearchBundle\DomainModel\Family\FamilyRepository;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleCode;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleRepository;
use Pim\Bundle\ResearchBundle\DomainModel\Product\ProductIdentifier;
use Pim\Bundle\ResearchBundle\DomainModel\Product\ProductRepository;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Types;

class QueryType extends ObjectType
{
    /** @var Types */
    private $types;

    /** @var ProductRepository */
    private $productRepository;

    /** @var FamilyRepository */
    private $familyRepository;

    /** @var LocaleRepository */
    private $localeRepository;

    /** @var CurrencyRepository */
    private $currencyRepository;

    /** @var ChannelRepository */
    private $channelRepository;

    /** @var AttributeRepository */
    private $attributeRepository;

    /** @var CategoryRepository */
    private $categoryRepository;

    public function __construct(
        Types $types,
        ProductRepository $productRepository,
        FamilyRepository $familyRepository,
        LocaleRepository $localeRepository,
        CurrencyRepository $currencyRepository,
        ChannelRepository $channelRepository,
        AttributeRepository $attributeRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->types = $types;
        $this->productRepository = $productRepository;
        $this->familyRepository = $familyRepository;
        $this->localeRepository = $localeRepository;
        $this->currencyRepository = $currencyRepository;
        $this->channelRepository = $channelRepository;
        $this->attributeRepository = $attributeRepository;
        $this->categoryRepository = $categoryRepository;

        $config = [
            'name' => 'query',
            'description' => 'Root query',
            'fields' => [
                'product' => [
                    'type' => $types->get(ProductType::class),
                    'description' => 'Return a product by its identifier',
                    'args' => [
                        'identifier' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => function($root, $args) {
                        return $this->productRepository->withIdentifier(
                            ProductIdentifier::createFromString($args['identifier'])
                        );
                    }
                ],
                'family' => [
                    'type' => $types->get(FamilyType::class),
                    'description' => 'Returns family by its a code',
                    'args' => [
                        'code' => Type::nonNull(Type::string()),
                     ],
                    'resolve' => function($root, $args) {
                        return $this->familyRepository->withCode(FamilyCode::createFromString($args['code']));
                    }
                ],
                'families' => [
                    'type' => Type::listOf($types->get(FamilyType::class)),
                    'description' => 'Returns family by its a code',
                    'args' => [
                        'codes' => Type::listOf(Type::nonNull(Type::string())),
                    ],
                    'resolve' => function($root, $args) {
                        $familyCodes = array_map(function($familyCode) {
                            return FamilyCode::createFromString($familyCode);
                        }, $args['codes']);
                        return $this->familyRepository->withCodes($familyCodes);
                    }
                ],
                'locale' => [
                    'type' => $types->get(LocaleType::class),
                    'description' => 'Returns locale by its a code',
                    'args' => [
                        'code' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => function($root, $args) {
                        return $this->localeRepository->withCode(LocaleCode::createFromString($args['code']));
                    }
                ],
                'currency' => [
                    'type' => $types->get(CurrencyType::class),
                    'description' => 'Returns currency by its a code',
                    'args' => [
                        'code' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => function($root, $args) {
                        return $this->currencyRepository->withCode(CurrencyCode::createFromString($args['code']));
                    }
                ],
                'channel' => [
                    'type' => $types->get(ChannelType::class),
                    'description' => 'Returns channel by its a code',
                    'args' => [
                        'code' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => function($root, $args) {
                        return $this->channelRepository->withCode(ChannelCode::createFromString($args['code']));
                    }
                ],
                'attribute' => [
                    'type' => $types->get(AttributeType::class),
                    'description' => 'Returns attribute by its a code',
                    'args' => [
                        'code' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => function($root, $args) {
                        return $this->attributeRepository->withCode(AttributeCode::createFromString($args['code']));
                    }
                ],
                'category' => [
                    'type' => $types->get(CategoryType::class),
                    'description' => 'Returns category by its a code',
                    'args' => [
                        'code' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => function($root, $args) {
                        return $this->categoryRepository->withCode(CategoryCode::createFromString($args['code']));
                    }
                ],
            ]
        ];
        parent::__construct($config);
    }
}
