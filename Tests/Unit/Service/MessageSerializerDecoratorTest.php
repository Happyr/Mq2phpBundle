<?php

namespace Happyr\Mq2phpBundle\Tests\Unit\Service;

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

        $array = json_decode($result, true);
        $this->assertEquals('foo', $array['headers'][0]['key']);
        $this->assertEquals('bar', $array['headers'][0]['value']);
        $this->assertEquals('baz', $array['headers'][1]['key']);
        $this->assertEquals('biz', $array['headers'][1]['value']);
        $this->assertEquals('data', $array['body']);
    }
}
