<?php

namespace Happyr\Mq2phpBundle\Service;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SimpleBus\Asynchronous\Consumer\SerializedEnvelopeConsumer;

/**
 * This class delegates a message to the CommandConsumer or EventConsumer depending on the queue name.
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
        $this->log('info', sprintf('Consuming data from queue: %s', $queueName));

        if ($queueName === $this->eventQueueName) {
            $this->doConsume($queueName, $message, $this->eventConsumer);
            $this->log('info', sprintf('Data from queue %s was consumed by the event consumer', $queueName));
        } elseif ($queueName === $this->commandQueueName) {
            $this->doConsume($queueName, $message, $this->commandConsumer);
            $this->log('info', sprintf('Data from queue %s was consumed by the command consumer', $queueName));
        }
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    private function log($level, $message, array $context = [])
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Consume a message and make sure we log errors
     *
     * @param string $queueName
     * @param mixed $message
     * @param SerializedEnvelopeConsumer $consumer
     * @throws \Exception
     */
    private function doConsume($queueName, $message, SerializedEnvelopeConsumer $consumer)
    {
        try {
            $consumer->consume($message);
        } catch (\Exception $e) {
            $this->log(
                'error',
                sprintf('Tried to handle message from queue %s but failed', $queueName),
                [
                    'exception' => $e,
                    'message' => $message,
                ]
            );

            throw $e;
        }
    }
}
