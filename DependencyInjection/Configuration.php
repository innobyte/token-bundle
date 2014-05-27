<?php

namespace Innobyte\TokenBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
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
