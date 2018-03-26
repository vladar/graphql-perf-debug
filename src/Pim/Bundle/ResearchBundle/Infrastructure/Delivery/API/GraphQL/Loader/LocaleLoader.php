<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Loader;

use Overblog\PromiseAdapter\Adapter\WebonyxGraphQLSyncPromiseAdapter;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleCode;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleRepository;

class LocaleLoader
{
    /** @var WebonyxGraphQLSyncPromiseAdapter */
    private $promiseAdapter;

    /** @var LocaleRepository */
    private $repository;

    public function __construct(WebonyxGraphQLSyncPromiseAdapter $promiseAdapter, LocaleRepository $repository)
    {
        $this->promiseAdapter = $promiseAdapter;
        $this->repository = $repository;
    }

    public function load(array $attributeCodes)
    {
        $attributes = $this->repository->withCodes($attributeCodes);

        return $this->promiseAdapter->getWebonyxPromiseAdapter()->all($attributes);
    }

    public function key(LocaleCode $attributeCode)
    {
        return $attributeCode->getValue();
    }
}
