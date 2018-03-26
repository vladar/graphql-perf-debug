<?php

namespace Pim\Bundle\ResearchBundle\Infrastructure\DI\Symfony\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PimResearchExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('../repositories.yml');
        $loader->load('../controllers.yml');
        $loader->load('../types.yml');
        $loader->load('../loaders.yml');

        //if ('in_memory' === $container->getParameter('kernel.environment')) {
        //    $loader->load('../in_memory_repositories.yml');
        //}

        if ('behat' === $container->getParameter('kernel.environment')) {
            $loader->load('../Test/EndToEnd/fixture_loaders.yml');
        }
    }
}
