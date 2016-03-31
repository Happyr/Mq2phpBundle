<?php

namespace Happyr\Mq2phpBundle\Consumer;

use Happyr\Mq2phpBundle\Event\PreHandleMessage;
use SimpleBus\Serialization\Envelope\Serializer\MessageInEnvelopSerializer;
use SimpleBus\Message\Bus\MessageBus;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer as SimpleBusSerializedEnvelopeConsumer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Use this consumer to easily implement an asynchronous message consumer.
 */
class ExtendableEnvelopeConsumer implements SimpleBusSerializedEnvelopeConsumer
{
    /**
     * @var MessageInEnvelopSerializer
     */
    private $messageInEnvelopeSerializer;

    /**
     * @var MessageBus
     */
    private $messageBus;

    /**
     * @var EventDispatcherInterface
     */
    private $dispathcer;

    /**
     * @param MessageInEnvelopSerializer $messageInEnvelopeSerializer
     * @param MessageBus                 $messageBus
     * @param EventDispatcherInterface   $dispathcer
     */
    public function __construct(
        MessageInEnvelopSerializer $messageInEnvelopeSerializer,
        MessageBus $messageBus,
        EventDispatcherInterface $dispathcer
    ) {
        $this->messageInEnvelopeSerializer = $messageInEnvelopeSerializer;
        $this->messageBus = $messageBus;
        $this->dispathcer = $dispathcer;
    }

    /**
     * @param string $serializedEnvelope
     */
    public function consume($serializedEnvelope)
    {
        // Unserialize
        $envelope = $this->messageInEnvelopeSerializer->unwrapAndDeserialize($serializedEnvelope);

        // Tell the world
        $this->dispathcer->dispatch(PreHandleMessage::NAME, new PreHandleMessage($envelope));

        // Handle the message
        $this->messageBus->handle($envelope->message());
    }
}
