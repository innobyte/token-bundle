<?php

namespace Innobyte\TokenBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class InnobyteTokenExtension
 * Loads and manages bundle configuration
 *
 * @package Innobyte\TokenBundle\DependencyInjection
 *
 * @codeCoverageIgnore
 *
 * @author Sorin Dumitrescu <sorin.dumitrescu@innobyte.com>
 */
class InnobyteTokenExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container
            ->getDefinition('innobyte_token.token')
            ->replaceArgument(0, new Reference(sprintf('doctrine.orm.%s_entity_manager', $config['entity_manager'])))
        ;
    }
}
