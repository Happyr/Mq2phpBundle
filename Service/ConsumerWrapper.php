<?php

namespace Happyr\DeferredEventSimpleBusBundle\Service;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;

/**
 * This class deligates a message to the CommandConsumer or EventConsumer depending on the queue name.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ConsumerWrapper implements LoggerAwareInterface
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
     * @var LoggerInterface
     */
    private $logger;

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

        $this->log('info', 'Consumed '.$queueName.'.');
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $level
     * @param $message
     */
    private function log($level, $message)
    {
        if ($this->logger) {
            $this->log($level, $message);
        }
    }
}
