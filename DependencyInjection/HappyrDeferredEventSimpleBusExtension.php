<?php

namespace Happyr\DeferredEventSimpleBusBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class HappyrDeferredEventSimpleBusExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('happyr_deferred_event_simple_bus_enabled', $config['enabled']);
        $this->requireBundle('SimpleBusAsynchronousBundle', $container);

        $def = $container->getDefinition('happyr.deferred_event_simple_bus.service.message_serializer');
        $def->replaceArgument(1, $config['message_headers']);

        $def = $container->getDefinition('happyr.deferred_event_simple_bus.consumer_wrapper');
        $def->replaceArgument(2, $config['command_queue'])
            ->replaceArgument(3, $config['event_queue']);
    }

    private function requireBundle($bundleName, ContainerBuilder $container)
    {
        $enabledBundles = $container->getParameter('kernel.bundles');
        if (!isset($enabledBundles[$bundleName])) {
            throw new \LogicException(
                sprintf(
                    'You need to enable "%s" as well',
                    $bundleName
                )
            );
        }
    }
}
