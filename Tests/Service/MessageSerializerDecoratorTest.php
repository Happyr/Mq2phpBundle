<?php

namespace Happyr\Mq2phpBundle\Tests\Service;

use Happyr\Mq2phpBundle\Service\MessageSerializerDecorator;

class MessageSerializerDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testWrapAndSerialize()
    {
        $inner = $this->getMockBuilder('SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopSerializer')
            ->getMock();
        $inner->method('wrapAndSerialize')
            ->willReturnArgument(0);

        $service = new MessageSerializerDecorator($inner, ['foo' => 'bar', 'baz' => 'biz']);
        $result = $service->wrapAndSerialize('data');

        $lines = explode("\n", $result);
        $this->assertEquals('foo: bar', $lines[0]);
        $this->assertEquals('baz: biz', $lines[1]);
        $this->assertEquals('', $lines[2]);
        $this->assertEquals('data', $lines[3]);
    }
}
