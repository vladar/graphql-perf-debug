<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Loader;

use Overblog\PromiseAdapter\Adapter\WebonyxGraphQLSyncPromiseAdapter;
use Pim\Bundle\ResearchBundle\DomainModel\Channel\ChannelCode;
use Pim\Bundle\ResearchBundle\DomainModel\Channel\ChannelRepository;

class ChannelLoader
{
    /** @var WebonyxGraphQLSyncPromiseAdapter */
    private $promiseAdapter;

    /** @var ChannelRepository */
    private $repository;

    public function __construct(WebonyxGraphQLSyncPromiseAdapter $promiseAdapter, ChannelRepository $repository)
    {
        $this->promiseAdapter = $promiseAdapter;
        $this->repository = $repository;
    }

    public function load(array $attributeCodes)
    {
        $attributes = $this->repository->withCodes($attributeCodes);

        return $this->promiseAdapter->getWebonyxPromiseAdapter()->all($attributes);
    }

    public function key(ChannelCode $attributeCode)
    {
        return $attributeCode->getValue();
    }
}
