<?php

namespace Liuggio\HelpDeskBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('liuggio_help_desk')->children();
        $rootNode
            ->scalarNode('object_manager')->defaultValue('doctrine.orm.default_entity_manager')->end()

            ->arrayNode('class')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('user')->isRequired()->end()
            ->scalarNode('ticket')->defaultValue('Application\\Liuggio\\HelpDeskBundle\\Entity\\Ticket')->end()
            ->scalarNode('comment')->defaultValue('Application\\Liuggio\\HelpDeskBundle\\Entity\\Comment')->end()
            ->scalarNode('category')->defaultValue('Application\\Liuggio\\HelpDeskBundle\\Entity\\Category')->end()
            ->end()
            ->end()

            ->arrayNode('email')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('sender')->isRequired()->end()
            ->scalarNode('subject_prefix')->defaultValue('[help desk]')->end()
            ->end()
            ->end();

        return $treeBuilder;
    }

}
