<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Persistence\InMemory;

use Pim\Bundle\ResearchBundle\DomainModel\Channel\Channel;
use Pim\Bundle\ResearchBundle\DomainModel\Channel\ChannelCode;
use Pim\Bundle\ResearchBundle\DomainModel\Channel\ChannelRepository;

class InMemoryChannelRepository implements ChannelRepository, InMemoryRepository
{
    /** @var Channel[] */
    private $storage = [];

    public function withCode(ChannelCode $channelCode): ?Channel
    {
        return $this->storage[$channelCode->getValue()] ?? null;
    }

    public function withCodes(array $channelCodes): array
    {
        $channels = [];
        foreach ($channelCodes as $channelCode) {
            if (isset($this->storage[$channelCode->getValue()])) {
                $channels[] = $this->storage[$channelCode->getValue()];
            }
        }

        return $channels;
    }

    public function add($channel): void
    {
        if (!$channel instanceof Channel) {
            throw new \invalidargumentexception('Channel class expected in argument.');
        }
        $this->storage[$channel->code()->getValue()] = $channel;
    }
}
