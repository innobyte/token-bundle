<?php

namespace Innobyte\TokenBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * Validate and merge configuration from app/config
 *
 * @package Innobyte\TokenBundle\DependencyInjection
 *
 * @codeCoverageIgnore
 *
 * @author Sorin Dumitrescu <sorin.dumitrescu@innobyte.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('innobyte_token');

        $rootNode
            ->children()
                ->scalarNode('entity_manager')
                    ->defaultValue('default')
                    ->info('The name of the entity manager that will handle the Token entity')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
