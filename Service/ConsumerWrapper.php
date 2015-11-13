<?php

namespace Happyr\DeferredEventSimpleBusBundle\Service;

use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;

class ConsumerWrapper
{
    /**
     * @var SerializedEnvelopeConsumer
     */
    private $commandConsumer;

    /**
     * @var SerializedEnvelopeConsumer
     */
    private $eventConsumer;

    /**
     * @var string
     */
    private $commandQueueName;

    /**
     * @var string
     */
    private $eventQueueName;

    /**
     * @param SerializedEnvelopeConsumer $commandConsumer
     * @param SerializedEnvelopeConsumer $eventConsumer
     * @param string                     $commandQueueName
     * @param string                     $eventQueueName
     */
    public function __construct(
        SerializedEnvelopeConsumer $commandConsumer,
        SerializedEnvelopeConsumer $eventConsumer,
        $commandQueueName,
        $eventQueueName
    ) {
        $this->commandConsumer = $commandConsumer;
        $this->eventConsumer = $eventConsumer;
        $this->commandQueueName = $commandQueueName;
        $this->eventQueueName = $eventQueueName;
    }

    /**
     * @param $queueName
     * @param $message
     */
    public function consume($queueName, $message)
    {
        // TODO Dispatch event

        if ($queueName === $this->eventQueueName) {
            $this->eventConsumer->consume($message);
        } elseif ($queueName === $this->commandQueueName) {
            $this->commandConsumer->consume($message);
        }

        // TODO log this
    }
}
