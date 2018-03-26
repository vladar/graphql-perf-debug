<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\DI\Symfony\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterTypePass implements CompilerPassInterface
{
    const DEFAULT_PRIORITY = 100;

    const TYPES = 'pim_research.infrastructure.delivery.api.graphql.types';

    const GRAPHQL_TYPE_TAG = 'pim_research.infrastructure.delivery.api.graphql.type';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::TYPES)) {
            throw new \LogicException('GraphQL type registry service not defined.');
        }

        $registry = $container->getDefinition(self::TYPES);

        $types = $container->findTaggedServiceIds(self::GRAPHQL_TYPE_TAG, $container);
        foreach ($types as $serviceId => $type) {
            $registry->addMethodCall('register', [new Reference($serviceId)]);
        }
    }
}
