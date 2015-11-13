<?php

namespace Happyr\DeferredEventSimpleBusBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Tobias Nyholm
 */
class UpdateSimpleBusAlias implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('happyr_deferred_event_simple_bus_enabled')) {
            return;
        }

        $alias = 'simple_bus.asynchronous.message_serializer';
        $happyrMessageSerializer = 'happyr.deferred_event_simple_bus.service.message_serializer';
        $simpleBusMessageSerializerId = $container->getAlias($alias)->__toString();

        $definition = $container->getDefinition($happyrMessageSerializer);
        $definition->replaceArgument(0, new Reference($simpleBusMessageSerializerId));

        $container->setAlias($alias, $happyrMessageSerializer);
    }
}
