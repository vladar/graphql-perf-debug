<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Channel;

interface ChannelRepository
{
    public function withCode(ChannelCode $channelCode): ?Channel;

    public function withCodes(array $channelCodes): array;
}
