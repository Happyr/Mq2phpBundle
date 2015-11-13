<?php

namespace Happyr\DeferredEventSimpleBusBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('happyr_deferred_event_simple_bus');

        $root
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->scalarNode('command_queue')->defaultValue('asynchronous_commands')->end()
                ->scalarNode('event_queue')->defaultValue('asynchronous_events')->end()
                ->arrayNode('message_headers')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('php_bin')->defaultNull()->end()
                        ->scalarNode('dispatch_path')->defaultNull()->end()
                        ->scalarNode('fastcgi_host')->cannotBeEmpty()->defaultValue('localhost')->end()
                        ->integerNode('fastcgi_port')->cannotBeEmpty()->defaultValue(9000)->end()
                    ->end()
                    ->end()
            ->end();

        return $treeBuilder;
    }
}
