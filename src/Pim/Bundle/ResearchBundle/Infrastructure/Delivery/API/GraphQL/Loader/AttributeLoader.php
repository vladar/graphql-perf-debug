<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Loader;

use Overblog\PromiseAdapter\Adapter\WebonyxGraphQLSyncPromiseAdapter;
use Pim\Bundle\ResearchBundle\DomainModel\Attribute\AttributeCode;
use Pim\Bundle\ResearchBundle\DomainModel\Attribute\AttributeRepository;

class AttributeLoader
{
    /** @var WebonyxGraphQLSyncPromiseAdapter */
    private $promiseAdapter;

    /** @var AttributeRepository */
    private $repository;

    public function __construct(WebonyxGraphQLSyncPromiseAdapter $promiseAdapter, AttributeRepository $repository)
    {
        $this->promiseAdapter = $promiseAdapter;
        $this->repository = $repository;
    }

    public function load(array $attributeCodes)
    {
        $attributes = $this->repository->withCodes($attributeCodes);

        return $this->promiseAdapter->getWebonyxPromiseAdapter()->all($attributes);
    }

    public function key(AttributeCode $attributeCode)
    {
        return $attributeCode->getValue();
    }
}
