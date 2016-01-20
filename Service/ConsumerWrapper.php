<?php

namespace Happyr\DeferredEventSimpleBusBundle\Service;

use Happyr\DeferredEventSimpleBusBundle\Traits\LoggerTrait;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;

class ConsumerWrapper
{
    use LoggerTrait;

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
        if ($queueName === $this->eventQueueName) {
            $this->eventConsumer->consume($message);
        } elseif ($queueName === $this->commandQueueName) {
            $this->commandConsumer->consume($message);
        }

        $this->log('debug', 'Consumed '.$queueName.'.');
    }
}