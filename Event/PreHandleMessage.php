<?php

namespace Happyr\Mq2phpBundle\Event;

use SimpleBus\Serialization\Envelope\Envelope;
use Symfony\Component\EventDispatcher\Event;

class PreHandleMessage extends Event
{
    const NAME = 'happyr.mq2php.pre_handle_message';

    /**
     * @var Envelope
     */
    private $envelope;

    /**
     * @param Envelope $envelope
     */
    public function __construct(Envelope $envelope)
    {
        $this->envelope = $envelope;
    }

    /**
     * @return Envelope
     */
    public function getEnvelope()
    {
        return $this->envelope;
    }
}
