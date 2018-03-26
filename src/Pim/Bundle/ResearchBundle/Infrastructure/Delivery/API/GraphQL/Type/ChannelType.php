<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Overblog\DataLoader\DataLoader;
use Pim\Bundle\ResearchBundle\DomainModel\Channel\Channel;
use Pim\Bundle\ResearchBundle\DomainModel\Currency\CurrencyRepository;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleRepository;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Types;

class ChannelType extends ObjectType
{
    public function __construct(Types $types, DataLoader $localeDataLoader, CurrencyRepository $currencyRepository)
    {
        $config = [
            'name' => 'channel',
            'description' => 'Channel',
            'fields' => function() use ($types) {
                return [
                    'code' => Type::string(),
                    'locales' => Type::listOf($types->get(LocaleType::class)),
                    'currencies' => Type::listOf($types->get(CurrencyType::class)),
                    'labels' => Type::listOf($types->get(LabelType::class)),
                ];
            },
            'resolveField' => function(Channel $channel, $args, $context, ResolveInfo $info) use ($localeDataLoader, $currencyRepository) {
                switch ($info->fieldName) {
                    case 'code':
                        return $channel->code()->getValue();
                    case 'locales':
                        return $localeDataLoader->loadMany($channel->localeCodes());
                    case 'currencies':
                        return $currencyRepository->withCodes($channel->currencyCodes());
                    case 'labels':
                        return $channel->labels();
                    default:
                        return null;
                }
            }
        ];
        parent::__construct($config);
    }
}
