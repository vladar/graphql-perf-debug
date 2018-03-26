<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Product;

use Pim\Bundle\ResearchBundle\DomainModel\Attribute\Attribute;
use Pim\Bundle\ResearchBundle\DomainModel\Channel\ChannelCode;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleCode;

interface ValueFactoryInterface
{
    /**
     * @param Attribute   $attribute
     * @param ChannelCode $channelCode
     * @param LocaleCode  $localeCode
     * @param mixed       $data
     *
     * @return
     */
    public function create(Attribute $attribute, ChannelCode $channelCode, LocaleCode $localeCode, $data);
}
