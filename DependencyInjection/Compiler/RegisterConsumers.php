<?php

namespace Happyr\Mq2phpBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Register the message bus argument to our consumer.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class RegisterConsumers implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->replaceArgumentWithReference($container, 'happyr.mq2php.extendable_command_envelope_consumer', 'simple_bus.asynchronous.command_bus');
        $this->replaceArgumentWithReference($container, 'happyr.mq2php.extendable_event_envelope_consumer', 'simple_bus.asynchronous.event_bus');
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $serviceId
     * @param string           $referenceId
     */
    private function replaceArgumentWithReference(ContainerBuilder $container, $serviceId, $referenceId)
    {
        if (!$container->hasDefinition($serviceId)) {
            return;
        }

        // If there is not $referenceId the $service has no use
        if (!$container->hasDefinition($referenceId)) {
            $container->removeDefinition($serviceId);

            return;
        }

        $container->getDefinition($serviceId)->replaceArgument(1, new Reference($referenceId));
    }
}
