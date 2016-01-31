<?php

namespace Happyr\DeferredEventSimpleBusBundle\DependencyInjection\Configurator;

use Happyr\DeferredEventSimpleBusBundle\Service\HeaderAwareInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * A configurator that will configure the MessageSerialiserDecorator with the mandatory headers.
 *
 * @author Tobias Nyholm
 */
class MessageHeaderConfigurator
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface kernel
     */
    private $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param HeaderAwareInterface $service
     */
    public function configure(HeaderAwareInterface $service)
    {
        //try to set default php bin
        if ($service->getHeader('php_bin') === null) {
            if (defined(PHP_BINARY)) {
                //since php 5.4
                $service->setHeader('php_bin', PHP_BINARY);
            } else {
                $service->setHeader('php_bin', PHP_BINDIR.'/php');
            }
        }

        //try to set default dispatch_path
        if ($service->getHeader('dispatch_path') === null) {
            $service->setHeader(
                'dispatch_path',
                $this->kernel->locateResource('@HappyrDeferredEventSimpleBusBundle/Resources/bin/dispatch-message.php')
            );
        }
    }
}
