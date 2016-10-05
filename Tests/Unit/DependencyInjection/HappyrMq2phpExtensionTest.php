<?php

namespace Happyr\Mq2phpBundle\Tests\Unit\DependencyInjection;

use Happyr\Mq2phpBundle\DependencyInjection\HappyrMq2phpExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class HappyrMq2phpExtensionTest extends AbstractExtensionTestCase
{
    protected function getMinimalConfiguration()
    {
        $this->setParameter('kernel.bundles', ['SimpleBusAsynchronousBundle'=>true]);
        return ['enabled'=>true];
    }

    public function testServicesRegisteredAfterLoading()
    {
        $this->load();

        $this->assertContainerBuilderHasService('happyr.mq2php.message_serializer', 'Happyr\Mq2phpBundle\Service\MessageSerializerDecorator');
        $this->assertContainerBuilderHasService('happyr.mq2php.consumer_wrapper', 'Happyr\Mq2phpBundle\Service\ConsumerWrapper');
    }

    protected function getContainerExtensions()
    {
        return array(
            new HappyrMq2phpExtension(),
        );
    }
}
