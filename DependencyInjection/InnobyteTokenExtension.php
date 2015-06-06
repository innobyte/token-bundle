<?php

namespace Innobyte\TokenBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class InnobyteTokenExtension
 * Loads and manages bundle configuration
 *
 * @package Innobyte\TokenBundle\DependencyInjection
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

        $container->setParameter(
            'innobyte_token.entity_manager_service_name',
            sprintf('doctrine.orm.%s_entity_manager', $config['entity_manager'])
        );

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
